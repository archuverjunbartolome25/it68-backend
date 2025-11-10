<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\InventoryRawmat;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index()
    {
        return response()->json(Inventory::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item' => 'required|string',
            'unit' => 'required|string',
            'quantity' => 'required|integer|min:0',
        ]);

        $item = Inventory::create($validated);

        return response()->json([
            'message' => 'Item added successfully',
            'data' => $item
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $item = Inventory::findOrFail($id);
        $item->quantity = $validated['quantity'];
        $item->save();

        return response()->json(['message' => 'Inventory updated successfully']);
    }

    /**
     * Deduct items from both finished goods and raw materials
     */
public function deduct(Request $request)
{
    $type = $request->input('type'); // "Finished Goods" or "Raw Materials"
    $itemName = $request->input('item');
    $quantity = (int) $request->input('quantity'); // pieces to deduct

    if (!$type || !$itemName || !$quantity) {
        return response()->json(['error' => 'Missing required fields.'], 400);
    }

    // 🧩 Map frontend codes to actual DB item names
    $itemMap = [
        '350ml_raw' => '350ml',
        '500ml_raw' => '500ml',
        '1L_raw'    => '1L',
        '6L_raw'    => '6L',
        'Cap'       => 'Cap',
        '6L Cap'    => '6L Cap',
        'Label'     => 'Label',
        'Shrinkfilm'=> 'Shrinkfilm',
        'Stretchfilm'=> 'Stretchfilm',
    ];

    $searchItem = $itemMap[$itemName] ?? $itemName;

    // ⚙️ Conversion rules: pieces per case or roll
    $piecesPerUnit = [
        '350ml' => 24,
        '500ml' => 24,
        '1L'    => 12,
        '6L'    => 1,
        'Label' => 20000, // 🧾 1 roll = 20,000 pcs
    ];

    DB::beginTransaction();
    try {
        if ($type === 'Finished Goods') {
            $inventory = Inventory::where('item', $searchItem)->first();

            if (!$inventory) {
                throw new \Exception("Item '{$itemName}' not found in finished goods inventory.");
            }

            $pcsPerUnit = $piecesPerUnit[$searchItem] ?? 1;

            if ($inventory->quantity_pcs < $quantity) {
                throw new \Exception("Insufficient stock for '{$itemName}' (finished goods).");
            }

            // 🧮 Deduct pieces
            $inventory->quantity_pcs -= $quantity;

            // 🔹 Recalculate cases and leftover pieces properly
            $inventory->quantity = intdiv($inventory->quantity_pcs, $pcsPerUnit);
            $remainingPieces = $inventory->quantity_pcs % $pcsPerUnit;

            // Keep both consistent
            $inventory->quantity_pcs = ($inventory->quantity * $pcsPerUnit) + $remainingPieces;

            $inventory->save();
        } 
        elseif ($type === 'Raw Materials') {
            $raw = InventoryRawmat::where('item', $searchItem)->first();

            if (!$raw) {
                throw new \Exception("Item '{$itemName}' not found in raw materials inventory.");
            }

            $pcsPerUnit = $piecesPerUnit[$searchItem] ?? 1;

            if ($raw->quantity_pieces < $quantity) {
                throw new \Exception("Insufficient stock for '{$itemName}' (raw materials).");
            }

            // 🧮 Deduct pieces
            $raw->quantity_pieces -= $quantity;

            // 🔹 Recalculate rolls or units
            $raw->quantity = intdiv($raw->quantity_pieces, $pcsPerUnit);
            $remainingPieces = $raw->quantity_pieces % $pcsPerUnit;

            $raw->quantity_pieces = ($raw->quantity * $pcsPerUnit) + $remainingPieces;

            $raw->save();
        } 
        else {
            throw new \Exception("Invalid item type: '{$type}'");
        }

        DB::commit();
        return response()->json([
            'message' => "✅ Successfully deducted {$quantity} pcs from {$searchItem}.",
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => $e->getMessage()], 400);
    }
}


    public function receiveItem(Request $request)
    {
        $validated = $request->validate([
            'item' => 'required|string',
            'unit' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'quantity_pcs' => 'nullable|integer|min:0',
        ]);

        // Find or create new item by name
        $item = Inventory::firstOrCreate(
            ['item' => $validated['item']],
            ['unit' => $validated['unit'], 'quantity' => 0, 'quantity_pcs' => 0]
        );

        $item->quantity += $validated['quantity'];
        $item->quantity_pcs += $validated['quantity_pcs'] ?? 0;
        $item->save();

        return response()->json([
            'message' => 'Inventory updated successfully',
            'data' => $item
        ]);
    }

    public function addQuantity(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        $inventory = Inventory::findOrFail($id);
        $inventory->quantity += $request->quantity;
        $inventory->save();

        return response()->json([
            'message' => 'Quantity updated successfully',
            'data' => $inventory
        ]);
    }

    public function updateAlert(Request $request, $id)
    {
        $request->validate(['low_stock_alert' => 'required|integer|min:1']);
        $item = Inventory::findOrFail($id);
        $item->low_stock_alert = $request->low_stock_alert;
        $item->save();

        return response()->json(['message' => 'Alert quantity updated successfully']);
    }

public function inventoryByYear(Request $request)
{
    $year = $request->query('year', now()->year);

    $data = Inventory::selectRaw('EXTRACT(MONTH FROM created_at) as month, SUM(quantity_pcs) as total_quantity')
        ->whereRaw('EXTRACT(YEAR FROM created_at) = ?', [$year])
        ->groupBy('month')
        ->orderBy('month')
        ->get();

    return response()->json($data);
}
public function updatePrice(Request $request, $id)
{
    // Validate that unit_cost is present and numeric
    $request->validate([
        'unit_cost' => 'required|numeric|min:0',
    ]);

    $item = Inventory::find($id);

    if (!$item) {
        return response()->json(['message' => 'Item not found'], 404);
    }

    $item->unit_cost = $request->unit_cost;
    $item->save();

    return response()->json([
        'message' => 'Price updated successfully',
        'data' => $item
    ]);
}

}
