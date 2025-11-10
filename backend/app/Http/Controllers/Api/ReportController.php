<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;


class ReportController extends Controller
{
public function salesReport(Request $request)
{
    // BOM / unit cost per bottle
    $UNIT_COGS = [
        '350ml' => 2.89,
        '500ml' => 3.29,
        '1L'    => 3.79,
        '6L'    => 5.00,
    ];

    // Pieces per case
    $PCS_PER_CASE = [
        '350ml' => 24,
        '500ml' => 24,
        '1L'    => 12,
        '6L'    => 1,
    ];

    $salesOrders = DB::table('sales_orders as so')
        ->leftJoin('customers as c', 'so.customer_id', '=', 'c.id')
        ->select(
            'so.id',
            'so.location',
            'so.date',
            'so.products',
            'so.delivery_date',
            'so.date_delivered',
            'so.status',
            'so.order_type',
            'so.amount as total_sales',
            'so.quantities',
            'c.name as customer_name'
        )
        ->orderBy('so.date', 'desc')
        ->get()
        ->map(function ($order) use ($UNIT_COGS, $PCS_PER_CASE) {
            $qty = json_decode($order->quantities, true) ?? [];
            $totalQty = array_sum(array_map(fn($v) => (int)$v, $qty));

            $cogs = 0;
            foreach ($qty as $product => $cases) {
                $unitCost = $UNIT_COGS[$product] ?? 0;
                $piecesPerCase = $PCS_PER_CASE[$product] ?? 1;

                // COGS = unit cost * number of pieces in a case * number of cases sold
                $cogs += $unitCost * $piecesPerCase * (int)$cases;
            }

            $totalSales = (float)$order->total_sales;
            $profit = $totalSales - $cogs;

            $order->total_qty = $totalQty;
            $order->cogs = round($cogs, 2);
            $order->profit = round($profit, 2);

            return $order;
        });

    return response()->json($salesOrders);
}

public function salesReportPDF(Request $request)
{
    $UNIT_COGS = [
        '350ml' => 2.89,
        '500ml' => 3.29,
        '1L'    => 3.79,
        '6L'    => 5.00,
    ];

    $PCS_PER_CASE = [
        '350ml' => 24,
        '500ml' => 24,
        '1L'    => 12,
        '6L'    => 1,
    ];

    // Filters from request
    $reportType = $request->query('reportType', 'All'); // Daily / Weekly / Monthly / Yearly
    $filterValue = $request->query('filterValue'); // e.g., 2025-11-05, 2025-45 (week), 2025-11 (month)
    $statusFilter = $request->query('status', 'All'); // Pending / Delivered / All

    $query = DB::table('sales_orders as so')
        ->leftJoin('customers as c', 'so.customer_id', '=', 'c.id')
        ->select(
            'so.id',
            'so.location',
            'so.date',
            'so.products',
            'so.delivery_date',
            'so.date_delivered',
            'so.order_type',
            'so.status',
            'so.amount as total_sales',
            'so.quantities',
            'c.name as customer_name'
        );

    // ✅ Apply date filter
    if ($filterValue) {
        switch ($reportType) {
            case 'Daily':
                $query->whereDate('so.date', $filterValue);
                break;

            case 'Weekly':
                [$year, $week] = explode('-W', $filterValue);
                $weekStart = new DateTime();
                $weekStart->setISODate($year, $week);
                $weekEnd = clone $weekStart;
                $weekEnd->modify('+6 days');
                $query->whereBetween('so.date', [
                    $weekStart->format('Y-m-d'),
                    $weekEnd->format('Y-m-d')
                ]);
                break;

            case 'Monthly':
                [$year, $month] = explode('-', $filterValue);
                $query->whereYear('so.date', $year)
                      ->whereMonth('so.date', $month);
                break;

            case 'Yearly':
                $query->whereYear('so.date', $filterValue);
                break;
        }
    }

    // ✅ Apply status filter
    if ($statusFilter !== 'All') {
        if ($statusFilter === 'Pending') {
            $query->whereNull('so.date_delivered');
        } elseif ($statusFilter === 'Delivered') {
            $query->whereNotNull('so.date_delivered');
        }
    }

    $salesOrders = $query->orderBy('so.date', 'desc')->get()
        ->map(function ($order) use ($UNIT_COGS, $PCS_PER_CASE) {
            $qty = json_decode($order->quantities, true) ?? [];
            $totalQty = array_sum(array_map(fn($v) => (int)$v, $qty));

            $cogs = 0;
            foreach ($qty as $product => $cases) {
                $unitCost = $UNIT_COGS[$product] ?? 0;
                $piecesPerCase = $PCS_PER_CASE[$product] ?? 1;
                $cogs += $unitCost * $piecesPerCase * (int)$cases;
            }

            $profit = (float)$order->total_sales - $cogs;

            $order->total_qty = $totalQty;
            $order->cogs = round($cogs, 2);
            $order->profit = round($profit, 2);

            return $order;
        });

    // ✅ Generate filename
    $fileName = $filterValue
        ? "Sales_Report_{$reportType}_" . str_replace('-', '', $filterValue) . ".pdf"
        : "Sales_Report_All.pdf";

    $pdf = Pdf::loadView('pdfs.sales_report_pdf', [
        'salesOrders' => $salesOrders,
        'selectedDate' => $filterValue,
        'reportType' => $reportType,
        'generatedAt' => Carbon::now()->format('F j, Y, g:i A'),
    ])->setPaper('a4', 'landscape');

    return $pdf->download($fileName);
}

public function inventoryReportPDF(Request $request)
{
    $type = $request->query('type', 'All');
    $dateNow = Carbon::now()->format('mdY');

    // ✅ Fetch inventory data depending on type
    if ($type === 'Raw Material') {
        $data = DB::table('inventory_rawmats')->orderBy('item')->get();
    } elseif ($type === 'Finished Goods') {
        $data = DB::table('inventories')->orderBy('item')->get();
    } else {
        $data = DB::table('inventories')
            ->select('item', 'unit', 'unit_cost', 'quantity', 'quantity_pcs', 'low_stock_alert', 'updated_at')
            ->union(
                DB::table('inventory_rawmats')
                    ->select('item', 'unit', 'unit_cost', 'quantity', 'quantity_pcs', 'low_stock_alert', 'updated_at')
            )
            ->get();
    }

    // ✅ Set filename dynamically
    if ($type !== 'All') {
        $fileName = "Inventory_Report_" . str_replace(' ', '_', $type) . "_{$dateNow}.pdf";
    } else {
        $fileName = "Inventory_Report.pdf";
    }

    $pdf = Pdf::loadView('pdfs.inventory_report_pdf', [
        'inventoryData' => $data,
        'selectedType' => $type,
        'generatedAt' => Carbon::now()->format('F j, Y, g:i A'),
    ])->setPaper('a4', 'landscape');

    return $pdf->download($fileName);
}
// 🗑️ DISPOSAL REPORT PDF
public function disposalReportPDF(Request $request)
{
    $query = DB::table('disposals');

    // Filter by status
    if ($request->status) {
        $query->where('status', $request->status);
    }

    // Filter by report type & filter value
    $reportType = $request->reportType ?? 'All';
    $filterValue = $request->filterValue;

    if ($reportType === 'Daily' && $filterValue) {
        $query->whereDate('disposal_date', $filterValue);
    }

    if ($reportType === 'Weekly' && $filterValue) {
        [$year, $week] = explode('-W', $filterValue);
        $weekStart = new DateTime();
        $weekStart->setISODate($year, $week);
        $weekEnd = clone $weekStart;
        $weekEnd->modify('+6 days');
        $query->whereBetween('disposal_date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')]);
    }

    if ($reportType === 'Monthly' && $filterValue) {
        [$year, $month] = explode('-', $filterValue);
        $query->whereYear('disposal_date', $year)->whereMonth('disposal_date', $month);
    }

    if ($reportType === 'Yearly' && $filterValue) {
        $query->whereYear('disposal_date', $filterValue);
    }

    // Optional search filter
    if ($request->search) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('item', 'ILIKE', "%$search%")
              ->orWhere('reason', 'ILIKE', "%$search%");
        });
    }

    // Fetch filtered results
    $disposals = $query->orderBy('id', 'desc')->get();

    // Generate PDF
    $pdf = Pdf::loadView('pdfs.disposal_report_pdf', [
        'disposals' => $disposals,
        'generatedAt' => Carbon::now()->format('F j, Y, g:i A'),
    ])->setPaper('a4', 'landscape');

    $fileName = 'Disposal_Report_' . now()->format('Ymd') . '.pdf';

    return $pdf->download($fileName);
}
public function returnToVendorReportPDF(Request $request)
{
    $query = DB::table('return_to_vendor as r')
        ->leftJoin('customers as c', 'r.customer_id', '=', 'c.id')
        ->select('r.*', 'c.name as customer_name');

    $reportType = $request->reportType ?? 'All';
    $filterValue = $request->filterValue;

    // Apply filtering by report type
    if ($filterValue) {
        switch ($reportType) {
            case 'Daily':
                $query->whereDate('r.date_returned', $filterValue);
                break;

            case 'Weekly':
                [$year, $week] = explode('-W', $filterValue);
                $weekStart = new DateTime();
                $weekStart->setISODate($year, $week);
                $weekEnd = clone $weekStart;
                $weekEnd->modify('+6 days');
                $query->whereBetween('r.date_returned', [
                    $weekStart->format('Y-m-d'),
                    $weekEnd->format('Y-m-d')
                ]);
                break;

            case 'Monthly':
                [$year, $month] = explode('-', $filterValue);
                $query->whereYear('r.date_returned', $year)
                      ->whereMonth('r.date_returned', $month);
                break;

            case 'Yearly':
                $query->whereYear('r.date_returned', $filterValue);
                break;
        }
    }

    // Optional status filter
    if ($request->status) {
        $query->where('r.status', $request->status);
    }

    // Optional search filter
    if ($request->search) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('r.rtv_number', 'ILIKE', "%$search%")
              ->orWhere('r.status', 'ILIKE', "%$search%");
        });
    }

    // Fetch filtered results
    $returns = $query->orderBy('r.id', 'desc')->get();

    // Generate PDF
    $pdf = Pdf::loadView('pdfs.return_to_vendor_report', [
        'returns' => $returns,
        'selectedDate' => $filterValue,
        'reportType' => $reportType,
        'generatedAt' => Carbon::now()->format('F j, Y, g:i A'),
    ])->setPaper('a4', 'landscape');

    $fileName = 'ReturnToVendor_Report_' . now()->format('Ymd') . '.pdf';

    return $pdf->download($fileName);
}
public function purchaseOrderReportPDF(Request $request)
{
    $status = $request->query('status', 'All'); // Filter by status if provided
    $date = $request->query('date'); // Filter by date if provided
    $dateNow = Carbon::now()->format('mdY');

    // Fetch purchase orders
    $query = DB::table('purchase_orders')
        ->select(
            'id',
            'po_number',
            'order_date',
            'expected_date',
            'status',
            'amount',
            'supplier_name'
        );

    // Apply status filter
    if ($status !== 'All') {
        $query->where('status', $status);
    }

    // Apply date filter
    if ($date) {
        $query->whereDate('order_date', $date);
    }

    $purchaseOrders = $query->orderBy('order_date', 'desc')->get();

    // Fetch items for each PO
    $purchaseOrders = $purchaseOrders->map(function ($po) {
        $items = DB::table('purchase_order_items')
            ->where('purchase_order_id', $po->id)
            ->select('item_name', 'quantity', 'received_quantity')
            ->get();
        $po->items = $items;
        return $po;
    });

    // Generate filename
    $fileName = "Purchase_Order_Report_" 
        . ($status !== 'All' ? str_replace(' ', '_', $status) : 'All') 
        . "_{$dateNow}.pdf";

    // Generate PDF using Blade view
    $pdf = Pdf::loadView('pdfs.purchase_order_report', [
        'purchaseOrders' => $purchaseOrders,
        'status' => $status,
        'selectedDate' => $date,
        'generatedAt' => Carbon::now()->format('F j, Y, g:i A'),
    ])->setPaper('a4', 'landscape');

    return $pdf->download($fileName);
}

}
