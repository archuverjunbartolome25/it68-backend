<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DisposalProduct;
use Illuminate\Support\Str; // for UUID

class DisposalProductController extends Controller
{
    public function index()
    {
        return DisposalProduct::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'disposal_date' => 'required|date',
            'employee_id' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.item_type' => 'required|string',
            'items.*.item' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // Generate one unique batch ID for this transaction
        $batchId = Str::uuid()->toString();

        $created = [];

        foreach ($validated['items'] as $itemData) {
            $created[] = DisposalProduct::create([
                'disposal_batch_id' => $batchId,
                'disposal_date' => $validated['disposal_date'],
                'employee_id' => $validated['employee_id'],
                'item_type' => $itemData['item_type'],
                'item' => $itemData['item'],
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
        return DisposalProduct::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $product = DisposalProduct::findOrFail($id);

        $validated = $request->validate([
            'disposal_date' => 'sometimes|date',
            'item_type' => 'sometimes|string',
            'item' => 'sometimes|string',
            'quantity' => 'sometimes|integer|min:1',
            'employee_id' => 'required|string',
        ]);

        $product->update($validated);

        return response()->json($product, 200);
    }

    public function destroy($id)
    {
        $product = DisposalProduct::findOrFail($id);
        $product->delete();

        return response()->json(null, 204);
    }
    public function destroyMultiple(Request $request)
{
    $ids = $request->input('ids', []);

    if (empty($ids)) {
        return response()->json(['message' => 'No IDs provided.'], 400);
    }

    DisposalProduct::whereIn('id', $ids)->delete();

    return response()->json([
        'message' => 'Selected disposal products deleted successfully.',
        'deleted_ids' => $ids
    ], 200);
}

}
