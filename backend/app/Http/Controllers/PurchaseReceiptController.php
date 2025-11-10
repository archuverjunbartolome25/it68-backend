<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseReceiptController extends Controller
{
    public function index()
    {
        $receipts = DB::table('purchase_receipts')
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

        return response()->json($receipts);
    }
}
