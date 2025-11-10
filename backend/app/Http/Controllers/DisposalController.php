<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Disposal;
use Barryvdh\DomPDF\Facade\Pdf;


class DisposalController extends Controller
{
    /**
     * 🧾 Get all disposal records (latest first)
     */
    public function index()
    {
        $disposals = Disposal::orderByDesc('disposal_date')->get();

        return response()->json([
            'message' => 'Disposal records fetched successfully.',
            'data'    => $disposals
        ]);
    }

    /**
     * 🔍 Show single disposal record
     */
    public function show($id)
    {
        $disposal = Disposal::find($id);

        if (!$disposal) {
            return response()->json(['message' => 'Disposal not found'], 404);
        }

        return response()->json($disposal);
    }

    /**
     * ➕ Store a new disposal entry (default status = Pending)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'disposal_date' => 'required|date',
            'employee_id'   => 'required|string',
            'item_type'     => 'required|string',
            'item'          => 'required|string',
            'quantity'      => 'required|numeric|min:1',
            'reason'        => 'nullable|string|max:255',
        ]);

        // ✅ Auto-generate disposal_number
        $latest = Disposal::latest('id')->first();
        $nextNumber = $latest ? ((int)substr($latest->disposal_number, -4)) + 1 : 1;
        $disposalNumber = 'DIS-' . now()->format('Ymd') . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $disposal = Disposal::create([
            'disposal_number' => $disposalNumber,
            'disposal_date'   => $validated['disposal_date'],
            'employee_id'     => $validated['employee_id'],
            'item_type'       => $validated['item_type'],
            'item'            => $validated['item'],
            'quantity'        => $validated['quantity'],
            'reason'          => $validated['reason'] ?? null,
            'status'          => 'Pending',
        ]);

        return response()->json([
            'message' => 'Disposal recorded successfully.',
            'data'    => $disposal
        ], 201);
    }

public function markDisposed($id)
{
    $disposal = Disposal::find($id);

    if (!$disposal) {
        return response()->json(['message' => 'Disposal record not found.'], 404);
    }

    // Prevent double logging if already disposed
    if ($disposal->status === 'Disposed') {
        return response()->json(['message' => 'Disposal already marked as Disposed.'], 400);
    }

    $disposal->update([
        'status'        => 'Disposed',
        'disposed_date' => now()->toDateString(),
        'disposed_time' => now()->toTimeString(),
    ]);

    // ✅ Log disposal in inventory activity log
    \App\Models\InventoryActivityLog::create([
        'employee_id'  => $disposal->employee_id,
        'module'       => 'Disposal',
        'type'         => $disposal->item_type, // "Raw Materials" or "Finished Goods"
        'item_name'    => $disposal->item,
        'quantity'     => $disposal->quantity,
        'processed_at' => now(),
    ]);

    return response()->json([
        'message' => 'Disposal marked as Disposed and logged successfully.',
        'data'    => $disposal
    ]);
}

    /**
     * 🗑️ Delete multiple disposal records
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids');
        $type = $request->input('item_type'); // "Finished Goods" or "Raw Materials"

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['message' => 'No IDs provided.'], 400);
        }

        if (!$type) {
            return response()->json(['message' => 'Item type not specified.'], 400);
        }

        $deleted = Disposal::whereIn('id', $ids)
            ->where('item_type', $type)
            ->delete();

        return response()->json([
            'message' => $deleted
                ? 'Selected disposal records deleted successfully.'
                : 'No matching records found.',
        ]);
    }
        public function generatePDF(Request $request)
    {
        $query = Disposal::query();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->search) {
            $query->where('item_name', 'like', '%' . $request->search . '%');
        }

        $disposals = $query->get();

        $pdf = Pdf::loadView('reports.disposals', compact('disposals'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('disposals_report.pdf');
    }
}
