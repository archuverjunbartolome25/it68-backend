<?php

namespace App\Http\Controllers;

use App\Models\InventoryActivityLog;
use Illuminate\Http\Request;

class InventoryActivityLogController extends Controller
{
    /**
     * Display a list of inventory activity logs.
     * Optional query parameters:
     * - module
     * - type
     * - employee_id
     */
    public function index(Request $request)
    {
        $query = InventoryActivityLog::query();

        // Optional filters
        if ($request->has('module')) {
            $query->where('module', $request->module);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $logs = $query->orderBy('processed_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'count' => $logs->count(),
            'data' => $logs,
        ]);
    }
}
