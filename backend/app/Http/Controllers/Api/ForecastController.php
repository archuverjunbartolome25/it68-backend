<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ForecastController extends Controller
{
    /**
     * Return historical sales data aggregated by date for ARIMA forecasting.
     */
    public function historicalSales()
    {
        try {
            $sales = DB::table('sales_orders')
                ->select(
                    'date',
                    DB::raw('COALESCE(SUM("qty_350ml"),0) AS qty_350ml'),
                    DB::raw('COALESCE(SUM("qty_500ml"),0) AS qty_500ml'),
                    DB::raw('COALESCE(SUM("qty_1L"),0) AS qty_1L'),
                    DB::raw('COALESCE(SUM("qty_6L"),0) AS qty_6L')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return response()->json($sales);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch historical sales data.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

 public function forecast()
{
    try {
        // 1. Fetch historical sales (grouped by date) and sum all quantity columns
        $sales = DB::table('sales_orders')
            ->select(
                DB::raw('DATE(date) as date'),
                DB::raw('
                    COALESCE(SUM("qty_350ml"),0) +
                    COALESCE(SUM("qty_500ml"),0) +
                    COALESCE(SUM("qty_1L"),0) +
                    COALESCE(SUM("qty_6L"),0) AS total_qty
                ')
            )
            ->groupBy(DB::raw('DATE(date)'))
            ->orderBy('date', 'asc')
            ->get();

        // Convert to array for forecasting
        $historicalData = $sales->map(function ($row) {
            return [
                'date' => $row->date,
                'qty'  => (int) $row->total_qty
            ];
        });

        // 2. Simple forecast (average of last 7 days)
        $last7 = collect($historicalData)->take(-7)->pluck('qty');
        $avg = $last7->count() > 0 ? round($last7->avg()) : 0;

        // Predict next 30 days
        $forecast = [];
        $start = now()->addDay(); // start tomorrow

        for ($i = 0; $i < 30; $i++) {
            $forecast[] = [
                'date' => $start->copy()->addDays($i)->format('Y-m-d'),
                'predicted_qty' => $avg
            ];
        }

        return response()->json([
            'forecast' => $forecast
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Failed to generate forecast',
            'message' => $e->getMessage()
        ], 500);
    }
}
}
