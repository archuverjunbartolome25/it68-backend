<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionOutputController extends Controller
{
    /** List all production outputs */
    public function index()
    {
        return DB::table('production_outputs')
            ->select('production_date', 'batch_number', 'qty_350ml', 'qty_500ml', 'qty_1l', 'qty_6l')
            ->orderByDesc('production_date')
            ->get();
    }

    /** Store new production output */
public function store(Request $request)
{
    $request->validate([
        'products' => 'required|array',
        'products.*.product_name' => 'required|string',
        'products.*.quantity_pcs' => 'required|integer|min:1',
        'products.*.selected_suppliers' => 'nullable|array',
        'batch_number' => 'nullable|string',
    ]);

    $mapping = [
        '350ml' => ['Plastic Bottle (350ml)', 'Blue Plastic Cap', 'Label'],
        '500ml' => ['Plastic Bottle (500ml)', 'Blue Plastic Cap', 'Label'],
        '1L'    => ['Plastic Bottle (1L)', 'Blue Plastic Cap', 'Label'],
        '6L'    => ['Plastic Gallon (6L)', 'Blue Plastic Cap (6L)', 'Label'],
    ];

    $unitMap = [
        '350ml' => 'case',
        '500ml' => 'case',
        '1L'    => 'case',
        '6L'    => 'pieces',
    ];

    $pcsPerCase = [
        '350ml' => 24,
        '500ml' => 24,
        '1L'    => 12,
        '6L'    => 1,
    ];

    $rawMaterialConversions = [
        'Label' => 20000, // 1 roll = 20,000 labels
        'Blue Plastic Cap' => 1,
        'Blue Plastic Cap (6L)' => 1,
        'Plastic Bottle (350ml)' => 1,
        'Plastic Bottle (500ml)' => 1,
        'Plastic Bottle (1L)' => 1,
        'Plastic Gallon (6L)' => 1,
    ];

    $employeeId = $request->employee_id ?? auth()->user()->employeeID ?? 'UNKNOWN';

    DB::beginTransaction();

    try {
        foreach ($request->products as $prod) {
            $productName = trim($prod['product_name']);
            $quantityPcs = (int) $prod['quantity_pcs'];
            $selectedSuppliers = $prod['selected_suppliers'] ?? [];
            $unit = $unitMap[$productName] ?? 'pcs';
            $piecesPerCase = $pcsPerCase[$productName] ?? 1;

            $batchNumber = $request->batch_number ?? 'BATCH-' . now()->format('YmdHis');

            // Insert production output
            DB::table('production_outputs')->insert([
                'employee_id'    => $employeeId, // <--- add this
                'product_name' => $productName,
                'quantity' => floor($quantityPcs / $piecesPerCase),
                'quantity_pcs' => $quantityPcs,
                'qty_350ml' => $productName === '350ml' ? $quantityPcs : 0,
                'qty_500ml' => $productName === '500ml' ? $quantityPcs : 0,
                'qty_1l' => $productName === '1L' ? $quantityPcs : 0,
                'qty_6l' => $productName === '6L' ? $quantityPcs : 0,
                'batch_number' => $batchNumber,
                'production_date' => now(),
                'selected_suppliers' => json_encode($selectedSuppliers),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update or create finished goods inventory
            $inventory = DB::table('inventories')
                ->whereRaw('LOWER(item) = ?', [strtolower($productName)])
                ->where('unit', $unit)
                ->first();

            if ($inventory) {
                DB::table('inventories')->where('id', $inventory->id)->update([
                    'quantity' => floor(($inventory->quantity_pcs + $quantityPcs) / $piecesPerCase),
                    'quantity_pcs' => $inventory->quantity_pcs + $quantityPcs,
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('inventories')->insert([
                    'item' => $productName,
                    'unit' => $unit,
                    'quantity' => floor($quantityPcs / $piecesPerCase),
                    'quantity_pcs' => $quantityPcs,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

        // Log finished goods addition
        \App\Models\InventoryActivityLog::create([
            'employee_id' => $employeeId,
            'module'      => 'Production Output',
            'type'        => 'Finished Goods', // <-- changed
            'item_name'   => $productName,
            'quantity'    => $quantityPcs,
            'processed_at'=> now(),
        ]);


            // Deduct raw materials
            foreach ($mapping[$productName] ?? [] as $rawItem) {
                $supplierName = $selectedSuppliers[$rawItem] ?? DB::table('suppliers')
                    ->join('inventory_rawmats', 'suppliers.id', '=', 'inventory_rawmats.supplier_id')
                    ->where('inventory_rawmats.item', $rawItem)
                    ->value('suppliers.name');

                if (!$supplierName) continue;

                $supplier = DB::table('suppliers')->where('name', $supplierName)->first();
                if (!$supplier) continue;

                $raw = DB::table('inventory_rawmats')
                    ->where('item', $rawItem)
                    ->where('supplier_id', $supplier->id)
                    ->first();

                if (!$raw) continue;

                $conversion = $rawMaterialConversions[$rawItem] ?? 1;

                if ($rawItem === 'Label') {
                    $usedRolls = $quantityPcs / $conversion;

                    DB::table('inventory_rawmats')->where('id', $raw->id)->update([
                        'quantity' => max(0, ($raw->quantity ?? 0) - floor($usedRolls)),
                        'quantity_pieces' => max(0, ($raw->quantity_pieces ?? 0) - $quantityPcs),
                        'updated_at' => now(),
                    ]);

                    $usedQty = $quantityPcs;
                } else {
                    $usedQty = $quantityPcs * $conversion;

                    DB::table('inventory_rawmats')->where('id', $raw->id)->update([
                        'quantity' => max(0, ($raw->quantity ?? 0) - floor($usedQty / $conversion)),
                        'quantity_pieces' => max(0, ($raw->quantity_pieces ?? 0) - $usedQty),
                        'updated_at' => now(),
                    ]);
                }

                // Log raw material deduction
                \App\Models\InventoryActivityLog::create([
                    'employee_id' => $employeeId,
                    'module'      => 'Production Output',
                    'type'        => 'Raw Materials', // <-- changed
                    'item_name'   => $rawItem,
                    'quantity'    => $rawItem === 'Label' ? $quantityPcs : $usedQty,
                    'processed_at'=> now(),
                ]);
            }
        }

        DB::commit();
        return response()->json(['message' => 'Production output recorded successfully.']);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

    /** Delete multiple production outputs by date */
    public function destroyMany(Request $request)
    {
        $validated = $request->validate(['dates' => 'required|array', 'dates.*' => 'date']);
        DB::table('production_outputs')->whereIn('production_date', $validated['dates'])->delete();
        return response()->json(['message' => 'Selected production outputs deleted successfully']);
    }

    /** Show production details per date (only selected suppliers) */
/** Show production details for a specific batch (only selected suppliers) */
/** Show production details for a specific batch (with correct label pricing) */
public function showDetails($batch_number)
{
    // ✅ Fetch all production outputs with this batch number
    $productions = DB::table('production_outputs')
        ->where('batch_number', $batch_number)
        ->get();

    if ($productions->isEmpty()) {
        return response()->json([]);
    }

    // ✅ Map product → required raw materials
    $mapping = [
        '350ml' => ['Plastic Bottle (350ml)', 'Blue Plastic Cap', 'Label'],
        '500ml' => ['Plastic Bottle (500ml)', 'Blue Plastic Cap', 'Label'],
        '1L'    => ['Plastic Bottle (1L)', 'Blue Plastic Cap', 'Label'],
        '6L'    => ['Plastic Gallon (6L)', '6L Cap', 'Label'],
    ];

    $labelsPerRoll = 20000;
    $result = [];

    foreach ($productions as $prod) {
        $selectedSuppliers = json_decode($prod->selected_suppliers, true) ?? [];
        $materials = [];

        foreach ($mapping[$prod->product_name] ?? [] as $mat) {
            // ✅ Determine supplier (multi or single)
            $supplier = $selectedSuppliers[$mat] ?? null;

            if (!$supplier) {
                $defaultSupplier = DB::table('supplier_offers')
                    ->join('suppliers', 'suppliers.id', '=', 'supplier_offers.supplier_id')
                    ->join('inventory_rawmats', 'inventory_rawmats.id', '=', 'supplier_offers.rawmat_id')
                    ->where('inventory_rawmats.item', $mat)
                    ->select('suppliers.name')
                    ->first();

                if ($defaultSupplier) {
                    $supplier = $defaultSupplier->name;
                }
            }

            if (!$supplier) continue;

            // ✅ Get supplier’s unit offer price
            $offer = DB::table('supplier_offers')
                ->join('suppliers', 'suppliers.id', '=', 'supplier_offers.supplier_id')
                ->join('inventory_rawmats', 'inventory_rawmats.id', '=', 'supplier_offers.rawmat_id')
                ->where('inventory_rawmats.item', $mat)
                ->where('suppliers.name', $supplier)
                ->select('supplier_offers.price as unit_price')
                ->first();

            $unitPrice = $offer->unit_price ?? 0;

            // ✅ Adjust label price (roll → per piece)
            if ($mat === 'Label' && $unitPrice > 0) {
                $unitPrice = $unitPrice / $labelsPerRoll;
            }

            $materials[] = [
                'material'   => $mat,
                'supplier'   => $supplier,
                'qty'        => $prod->quantity_pcs,
                'unit_price' => round($unitPrice, 6),
                'total'      => round($unitPrice * $prod->quantity_pcs, 2),
            ];
        }

        if (!empty($materials)) {
            // ✅ Group by supplier (keep full material details)
            $grouped = collect($materials)
                ->groupBy('supplier')
                ->map(function ($items, $supplier) {
                    return [
                        'supplier' => $supplier,
                        'materials' => $items->values(), // <-- Keep full info here
                    ];
                })
                ->values();

            $result[] = [
                'product_name' => $prod->product_name,
                'batch_number' => $prod->batch_number,
                'materials_grouped' => $grouped,
            ];
        }
    }

    return response()->json($result);
}

    /** Get raw materials and supplier options for a product */
    public function getRawMaterialsByProduct($product)
    {
        $mapping = [
            '350ml' => ['Plastic Bottle (350ml)', 'Blue Plastic Cap', 'Label'],
            '500ml' => ['Plastic Bottle (500ml)', 'Blue Plastic Cap', 'Label'],
            '1L'    => ['Plastic Bottle (1L)', 'Blue Plastic Cap', 'Label'],
            '6L'    => ['Plastic Gallon (6L)', '6L Cap', 'Label'],
        ];

        $materials = [];
        foreach ($mapping[$product] ?? [] as $mat) {
            $suppliers = DB::table('supplier_offers')
                ->join('suppliers', 'suppliers.id', '=', 'supplier_offers.supplier_id')
                ->join('inventory_rawmats', 'inventory_rawmats.id', '=', 'supplier_offers.rawmat_id')
                ->where('inventory_rawmats.item', $mat)
                ->pluck('suppliers.name');

            $materials[] = [
                'name' => $mat,
                'multi_supplier' => $suppliers->count() > 1,
                'suppliers' => $suppliers,
            ];
        }

        return response()->json($materials);
    }
}
