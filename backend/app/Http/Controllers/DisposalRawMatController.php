<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DisposalRawMat;
use Illuminate\Support\Str; // for UUID

class DisposalRawMatController extends Controller
{
    public function index()
    {
        return DisposalRawMat::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'disposal_date' => 'required|date',
            'employee_id' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.item' => 'required|string',
            'items.*.item_type' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // Generate one unique batch ID
        $batchId = Str::uuid()->toString();

        $created = [];

        foreach ($validated['items'] as $itemData) {
            $created[] = DisposalRawMat::create([
                'disposal_batch_id' => $batchId,
                'disposal_date' => $validated['disposal_date'],
                'employee_id' => $validated['employee_id'],
                'item' => $itemData['item'],
                'item_type' => $itemData['item_type'],
                'quantity' => $itemData['quantity'],
            ]);
        }

        return response()->json([
            'batch_id' => $batchId,
            'items' => $created,
        ], 201);
    }

    public function show($id)
    {
        return DisposalRawMat::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $rawmat = DisposalRawMat::findOrFail($id);
        $rawmat->update($request->all());
        return response()->json($rawmat, 200);
    }

    public function destroy($id)
    {
        $rawmat = DisposalRawMat::findOrFail($id);
        $rawmat->delete();
        return response()->json(null, 204);
    }
    public function destroyMultiple(Request $request)
{
    $ids = $request->input('ids', []);

    if (empty($ids)) {
        return response()->json(['message' => 'No IDs provided.'], 400);
    }

    DisposalRawMat::whereIn('id', $ids)->delete();

    return response()->json([
        'message' => 'Selected disposal raw materials deleted successfully.',
        'deleted_ids' => $ids
    ], 200);
}

}
