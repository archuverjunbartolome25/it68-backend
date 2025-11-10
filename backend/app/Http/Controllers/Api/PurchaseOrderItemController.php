<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseOrderItem;
use App\Models\InventoryRawmat;
use Illuminate\Support\Facades\Log;

class PurchaseOrderItemController extends Controller
{
    /** Store a new purchase order item */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'purchase_order_id' => 'required|integer|exists:purchase_orders,id',
            'item_name'         => 'required|string|max:255',
            'item_type'         => 'required|in:raw,finished',
            'quantity'          => 'required|integer|min:1',
            'received_quantity' => 'nullable|integer|min:0',
            'rawmat_id'         => 'nullable|integer|exists:inventory_rawmats,id', // ✅ include rawmat_id
        ]);

        $item = PurchaseOrderItem::create($validated);

        return response()->json([
            'message' => 'Item saved successfully',
            'item'    => $item
        ], 201);
    }

    /** Update received quantity and adjust raw material stock */
    public function update(Request $request, $id)
    {
        $item = PurchaseOrderItem::findOrFail($id);

        $request->validate([
            'received_quantity' => 'required|integer|min:0',
        ]);

        $item->update($request->only(['received_quantity']));

        // ✅ Try to match raw material by ID (more reliable than name)
        if (!empty($item->rawmat_id)) {
            $rawmat = InventoryRawmat::find($item->rawmat_id);
        } else {
            // fallback if no rawmat_id (legacy data)
            $rawmat = InventoryRawmat::where('item', $item->item_name)->first();
        }

        if ($rawmat) {
            $conversion = $rawmat->conversion ?? 1;
            $addedQty = $item->received_quantity * $conversion;

            $rawmat->quantity += $addedQty;
            $rawmat->save();

            Log::info("✅ Raw material '{$rawmat->item}' updated by {$addedQty} units (Conversion: {$conversion}).");
        } else {
            Log::warning("⚠️ No raw material found for item '{$item->item_name}'. Quantity not added.");
        }

        return response()->json([
            'message' => 'Item updated and raw material quantity adjusted successfully',
            'item'    => $item
        ]);
    }

    /** Get all items for a given purchase order */
    public function getByPurchaseOrder($purchaseOrderId)
    {
        $items = PurchaseOrderItem::where('purchase_order_id', $purchaseOrderId)->get();
        return response()->json($items);
    }

    /** Get all received items */
    public function getAllReceivedItems()
    {
        $items = PurchaseOrderItem::where('received_quantity', '>', 0)
            ->join('purchase_orders', 'purchase_order_items.purchase_order_id', '=', 'purchase_orders.id')
            ->select(
                'purchase_order_items.id',
                'purchase_orders.po_number',
                'purchase_orders.supplier_name',
                'purchase_order_items.item_name',
                'purchase_order_items.received_quantity as quantity_received',
                'purchase_order_items.updated_at as received_date',
                'purchase_order_items.image_path'
            )
            ->orderBy('purchase_order_items.updated_at', 'desc')
            ->get()
            ->map(function ($item) {
                $item->image_url = $item->image_path ? asset('storage/' . $item->image_path) : null;
                return $item;
            });

        return response()->json($items);
    }
}
