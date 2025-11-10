<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReturnToVendor;
use Illuminate\Support\Facades\DB;

class ReturnToVendorController extends Controller
{
    /**
     * ✅ Get all Return to Vendor records
     */
    public function index()
    {
        $returns = ReturnToVendor::with('customer:id,name')
            ->orderByDesc('date_returned')
            ->get();

        return response()->json($returns);
    }

    /**
     * ✅ Get total count for dashboard
     */
    public function count()
    {
        $count = ReturnToVendor::count();
        return response()->json(['count' => $count]);
    }

    /**
     * ✅ Store new Return to Vendor record
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rtv_number' => 'nullable|string|max:255',
            'customer_id'   => 'required|integer|exists:customers,id',
            'location'      => 'nullable|string',
            'date_ordered'  => 'required|date',
            'date_returned' => 'required|date',
            'quantities'    => 'required|array',
            'status'        => 'nullable|string|in:Pending,Approved',
        ]);

        $q = $validated['quantities'];

        DB::beginTransaction();
        try {
            // ✅ Create Return Record
            $return = ReturnToVendor::create([
                'rtv_number'    => $validated['rtv_number'] ?? null,
                'customer_id'   => $validated['customer_id'],
                'location'      => $validated['location'] ?? '',
                'date_ordered'  => $validated['date_ordered'],
                'date_returned' => $validated['date_returned'],
                'qty_350ml'     => $q['350ml'] ?? 0,
                'qty_500ml'     => $q['500ml'] ?? 0,
                'qty_1l'        => $q['1L'] ?? 0,
                'qty_6l'        => $q['6L'] ?? 0,
                'status'        => $validated['status'] ?? 'Pending',
            ]);

            // ✅ Deduct from finished goods inventory
            foreach (['350ml', '500ml', '1L', '6L'] as $item) {
                $qty = $q[$item] ?? 0;
                if ($qty > 0) {
                    DB::table('inventories')
                        ->where('item', $item)
                        ->decrement('quantity', $qty);
                }
            }

            DB::commit();
            return response()->json([
                'message' => 'Return to Vendor record saved successfully.',
                'data'    => $return
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error'   => 'Failed to save record.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ Delete selected Return to Vendor records
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json(['error' => 'No records selected.'], 400);
        }

        $deleted = ReturnToVendor::whereIn('id', $ids)->delete();

        if ($deleted) {
            return response()->json(['message' => 'Record(s) deleted successfully.']);
        }

        return response()->json(['error' => 'No records were deleted.'], 404);
    }
    public function update(Request $request, $id)
{
    $validated = $request->validate([
        'status' => 'required|string|in:Pending,Approved',
    ]);

    $return = ReturnToVendor::find($id);
    if (!$return) {
        return response()->json(['error' => 'Record not found.'], 404);
    }

    $return->status = $validated['status'];
    $return->save();

    return response()->json(['message' => 'Status updated successfully.']);
}
}
