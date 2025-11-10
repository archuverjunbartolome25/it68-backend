<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function inventoryTrends(Request $request)
    {
        $year = $request->year ?? date('Y');

        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthStr = str_pad($i, 2, '0', STR_PAD_LEFT);
            $months[$monthStr] = [
                'qty_350ml' => 0,
                'qty_500ml' => 0,
                'qty_1l' => 0,
                'qty_6l' => 0,
                'month' => "$year-$monthStr"
            ];
        }

        // Sales trends (columns instead of JSON)
        $salesTrendsRaw = DB::table('sales_orders')
            ->select(
                DB::raw("TO_CHAR(date, 'YYYY-MM') as month"),
                DB::raw("SUM(qty_350ml) as qty_350ml"),
                DB::raw("SUM(qty_500ml) as qty_500ml"),
                DB::raw("SUM(qty_1l) as qty_1l"),
                DB::raw("SUM(qty_6l) as qty_6l")
            )
            ->whereYear('date', $year)
            ->where('status', 'Delivered')
            ->groupBy(DB::raw("TO_CHAR(date, 'YYYY-MM')"))
            ->orderBy('month')
            ->get();

        foreach ($salesTrendsRaw as $row) {
            $monthKey = explode('-', $row->month)[1];
            $months[$monthKey] = (array) $row;
        }
        $salesTrends = array_values($months);

        // Inventory trends
        $monthsInv = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthStr = str_pad($i, 2, '0', STR_PAD_LEFT);
            $monthsInv[$monthStr] = [
                'qty_350ml' => 0,
                'qty_500ml' => 0,
                'qty_1l' => 0,
                'qty_6l' => 0,
                'month' => "$year-$monthStr"
            ];
        }

        $inventoryTrendsRaw = DB::table('inventories')
            ->select(
                DB::raw("TO_CHAR(date, 'YYYY-MM') as month"),
                DB::raw("SUM(qty_350ml) as qty_350ml"),
                DB::raw("SUM(qty_500ml) as qty_500ml"),
                DB::raw("SUM(qty_1l) as qty_1l"),
                DB::raw("SUM(qty_6l) as qty_6l")
            )
            ->whereYear('date', $year)
            ->groupBy(DB::raw("TO_CHAR(date, 'YYYY-MM')"))
            ->orderBy('month')
            ->get();

        foreach ($inventoryTrendsRaw as $row) {
            $monthKey = explode('-', $row->month)[1];
            $monthsInv[$monthKey] = (array) $row;
        }
        $inventoryTrends = array_values($monthsInv);

        return response()->json([
            'salesTrends' => $salesTrends,
            'inventoryTrends' => $inventoryTrends
        ]);
    }
}
