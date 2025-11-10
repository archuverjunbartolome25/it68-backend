<?php

namespace App\Http\Controllers\Api;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\Inventory;
use App\Models\InventoryRawmat;
use App\Models\PurchaseReceipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class PurchaseOrderController extends Controller
{
    public function index()
    {
        return response()->json(PurchaseOrder::with('items')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'po_number'      => 'required|unique:purchase_orders',
            'supplier_name'  => 'required|string',
            'order_date'     => 'required|date',
            'expected_date'  => 'required|date',
            'status'         => 'required|string',
            'amount'         => 'required|numeric'
        ]);

        $order = PurchaseOrder::create($request->only([
            'po_number', 'supplier_name', 'order_date', 'expected_date', 'status', 'amount'
        ]));

        return response()->json($order, 201);
    }

    public function destroy($id)
    {
        $order = PurchaseOrder::findOrFail($id);
        $order->items()->delete();
        $order->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }

    public function generateDeliveryNote($id)
    {
        $order = PurchaseOrder::with('items')->findOrFail($id);

        if (!in_array(strtolower(trim($order->status)), ['completed', 'partially received'])) {
            abort(403, 'Delivery note only available after some items are received.');
        }

        $receivedItems = $order->items->filter(fn($item) => $item->received_quantity > 0);

        $pdf = Pdf::loadView('pdfs.delivery_note', [
            'order' => $order,
            'items' => $receivedItems
        ]);

        return $pdf->download('delivery_note_' . $order->po_number . '.pdf');
    }

    public function update(Request $request, $id)
    {
        $order = PurchaseOrder::findOrFail($id);

        $request->validate([
            'supplier_name' => 'required|string',
            'order_date'    => 'required|date',
            'expected_date' => 'required|date',
            'status'        => 'required|string',
            'amount'        => 'required|numeric'
        ]);

        $order->update($request->only([
            'supplier_name', 'order_date', 'expected_date', 'status', 'amount'
        ]));

        return response()->json([
            'message' => 'Purchase order updated successfully',
            'order'   => $order
        ]);
    }

public function receiveItems(Request $request, $id)
{
    $request->validate([
        'item_id'  => 'required|exists:purchase_order_items,id',
        'quantity' => 'required|integer|min:1',
    ]);

    DB::beginTransaction();

    try {
        $order = PurchaseOrder::with('items')->findOrFail($id);
        $item  = $order->items()->where('id', $request->item_id)->firstOrFail();

        $qty = (int) $request->quantity;
        $remaining = ($item->quantity ?? 0) - ($item->received_quantity ?? 0);

        if ($qty > $remaining) {
            DB::rollBack();
            return response()->json([
                'error' => "Cannot receive more than remaining quantity ({$remaining})."
            ], 422);
        }

        // ✅ Default conversion
        $conversion = 1;
        $receivedQty = $qty;

        // ✅ Update received quantity in PO item
        $item->received_quantity = ($item->received_quantity ?? 0) + $qty;
        $item->save();

        // ✅ Record transaction in receipts table
        DB::table('purchase_receipts')->insert([
            'purchase_order_id'      => $order->id,
            'purchase_order_item_id' => $item->id,
            'po_number'              => $order->po_number,
            'item_name'              => $item->item_name,
            'quantity_received'      => $qty,
            'received_date'          => now(),
            'created_at'             => now(),
            'updated_at'             => now(),
        ]);

        $employeeId = $order->employee_id ?? ($request->employee_id ?? auth()->user()->employeeID ?? 'UNKNOWN');

        // ✅ Check if item exists in raw materials first
        $rawMat = InventoryRawmat::whereRaw('LOWER(item) = ?', [strtolower($item->item_name)])->first();

        if ($rawMat) {
            // 🔹 Update existing raw material
            $conversion = $rawMat->conversion ?? 1;
            $receivedQty = $qty * $conversion;

            $rawMat->quantity += $qty;
            $rawMat->quantity_pieces += $receivedQty;
            $rawMat->save();

            // ✅ Log raw material receipt
            \App\Models\InventoryActivityLog::create([
                'employee_id' => $employeeId,
                'module'      => 'Purchase Order',
                'type'        => 'Raw Materials',
                'item_name'   => $item->item_name,
                'quantity'    => $receivedQty,
                'processed_at'=> now(),
            ]);
        } else {
            // 🔹 Otherwise, treat it as finished goods
            $finished = Inventory::firstOrCreate(
                ['item' => $item->item_name],
                ['unit' => 'pcs', 'quantity' => 0, 'quantity_pcs' => 0, 'low_stock_alert' => 0]
            );

            $finished->quantity += $qty;
            $finished->quantity_pcs += $receivedQty;
            $finished->save();

            // ✅ Log finished goods receipt
            \App\Models\InventoryActivityLog::create([
                'employee_id' => $employeeId,
                'module'      => 'Purchase Order',
                'type'        => 'Finished Goods',
                'item_name'   => $item->item_name,
                'quantity'    => $receivedQty,
                'processed_at'=> now(),
            ]);
        }

        // ✅ Update Purchase Order Status
        $order->load('items');
        $totalOrdered  = $order->items->sum('quantity');
        $totalReceived = $order->items->sum('received_quantity');

        if ($totalReceived === 0) {
            $order->status = 'Pending';
        } elseif ($totalReceived < $totalOrdered) {
            $order->status = 'Partially Received';
        } else {
            $order->status = 'Completed';
        }

        $order->save();

        DB::commit();

        return response()->json([
            'message' => 'Items received and logged successfully.',
            'order'   => $order->load('items')
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('receiveItems error: ' . $e->getMessage());
        return response()->json([
            'error'   => 'Server error while receiving items.',
            'details' => $e->getMessage(),
        ], 500);
    }
}

    /** ✅ Dashboard counts */
    public function getPendingCount()
    {
        $count = PurchaseOrder::where('status', 'Pending')->count();
        return response()->json(['count' => $count]);
    }

    public function getPartialCount()
    {
        $count = PurchaseOrder::where('status', 'Partially Received')->count();
        return response()->json(['count' => $count]);
    }

    public function getCompletedCount()
    {
        $count = PurchaseOrder::where('status', 'Completed')->count();
        return response()->json(['count' => $count]);
    }

    /** ✅ Received items history */
    public function getAllReceivedItems()
    {
        $items = DB::table('purchase_receipts')
            ->join('purchase_orders', 'purchase_receipts.purchase_order_id', '=', 'purchase_orders.id')
            ->select(
                'purchase_receipts.id',
                'purchase_orders.po_number',
                'purchase_orders.supplier_name',
                'purchase_receipts.item_name',
                'purchase_receipts.quantity_received',
                'purchase_receipts.received_date'
            )
            ->orderBy('purchase_receipts.received_date', 'desc')
            ->get();

        return response()->json($items);
    }
}
