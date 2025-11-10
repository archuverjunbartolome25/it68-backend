<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InventoryRawMat;

class InventoryRawMatsController extends Controller
{
    /**
     * 🧾 Get all raw materials with supplier details
     */
public function index()
{
    $rawMats = InventoryRawmat::with(['supplierOffers.supplier'])
        ->get()
        ->map(function ($item) {
            // 🧮 Compute display quantity
            if (strtolower($item->item) === 'label') {
                $item->display_quantity = $item->in_quantity * 20000;
            } else {
                $item->display_quantity = $item->quantity;
            }

            // 🏭 Get supplier info
            if ($item->supplierOffers->isNotEmpty()) {
                $bestOffer = $item->supplierOffers->sortBy('price')->first();

                // ✅ use `name` instead of `supplier_name`
                $item->supplier_name = optional($bestOffer->supplier)->name ?? '—';
                $item->unit_cost = $bestOffer->price ?? null;
            } else {
                $item->supplier_name = '—';
                $item->unit_cost = null;
            }

            return $item;
        })
        ->values();

    return response()->json($rawMats);
}

    /**
     * ✏️ Update raw material info
     */
    public function update(Request $request, $id)
    {
        $item = InventoryRawMat::find($id);

        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        $validated = $request->validate([
            'item' => 'sometimes|string',
            'unit' => 'sometimes|string',
        ]);

        $item->update($validated);

        return response()->json([
            'message' => 'Raw material updated successfully',
            'item' => $item,
        ]);
    }

    /**
     * 📦 Receive (add) raw material stock
     */
    public function receiveItem(Request $request)
    {
        $validated = $request->validate([
            'item' => 'required|string',
            'unit' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);

        $item = InventoryRawMat::firstOrCreate(
            ['item' => $validated['item']],
            [
                'unit' => $validated['unit'],
                'quantity' => 0,
                'in_quantity' => 0,
                'out_quantity' => 0,
                'low_stock_alert' => 0,
            ]
        );

        $item->in_quantity += $validated['quantity'];
        $item->quantity += $validated['quantity'];
        $item->save();

        return response()->json([
            'message' => 'Raw material inventory updated successfully',
            'data' => $item,
        ]);
    }

    /**
     * ➕ Add a new raw material
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item' => 'required|string',
            'unit' => 'required|string',
            'quantity' => 'required|integer|min:0',
        ]);

        $validated['in_quantity'] = $validated['quantity'];
        $validated['out_quantity'] = 0;
        $validated['low_stock_alert'] = $validated['low_stock_alert'] ?? 0;

        $item = InventoryRawMat::create($validated);

        return response()->json([
            'message' => 'Raw material added successfully',
            'data' => $item,
        ]);
    }

    /**
     * ➕ Add raw material with conversion and supplier
     */
    public function add(Request $request)
    {
        $validated = $request->validate([
            'item' => 'required|string|max:255',
            'unit' => 'required|string|max:255',
            'conversion' => 'required|numeric|min:1',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'unit_price' => 'nullable|numeric|min:0',
            'low_stock_alert' => 'nullable|numeric|min:0',
        ]);

        $rawMat = InventoryRawMat::create([
            'item' => $validated['item'],
            'unit' => $validated['unit'],
            'conversion' => $validated['conversion'],
            'quantity' => 0,
            'quantity_pieces' => 0,
            'in_quantity' => 0,
            'out_quantity' => 0,
            'supplier_id' => $validated['supplier_id'] ?? null,
            'unit_cost' => $validated['unit_price'] ?? null,
            'low_stock_alert' => $validated['low_stock_alert'] ?? 10,
        ]);

        if (!empty($validated['supplier_id']) && !empty($validated['unit_price'])) {
            \App\Models\SupplierOffer::create([
                'supplier_id' => $validated['supplier_id'],
                'rawmat_id' => $rawMat->id,
                'unit' => $validated['unit'],
                'price' => $validated['unit_price'],
            ]);
        }

        return response()->json([
            'message' => 'Raw material added successfully',
            'data' => $rawMat->load(['supplier', 'supplierOffers.supplier']),
        ], 201);
    }

    /**
     * ➖ Deduct quantity from stock
     */
    public function deduct(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        $item = InventoryRawMat::findOrFail($id);
        $availableStock = $item->in_quantity - $item->out_quantity;

        if ($availableStock < $request->quantity) {
            return response()->json(['message' => 'Not enough stock to deduct'], 400);
        }

        $item->out_quantity += $request->quantity;
        $item->quantity = $item->in_quantity - $item->out_quantity;
        $item->save();

        return response()->json([
            'message' => 'Quantity deducted successfully',
            'item' => $item,
        ]);
    }

    /**
     * ⚠️ Update low stock alert
     */
    public function updateAlert(Request $request, $id)
    {
        $request->validate(['low_stock_alert' => 'required|integer|min:1']);

        $item = InventoryRawMat::findOrFail($id);
        $item->low_stock_alert = $request->low_stock_alert;
        $item->save();

        return response()->json(['message' => 'Alert quantity updated successfully']);
    }

    /**
     * 🔍 Get all suppliers linked to a raw material
     */
    public function getSuppliers($id)
    {
        $rawmat = InventoryRawMat::with('supplierOffers.supplier')->find($id);

        if (!$rawmat) {
            return response()->json(['message' => 'Raw material not found'], 404);
        }

        return response()->json($rawmat->supplierOffers);
    }
}
