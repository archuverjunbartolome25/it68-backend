<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupplierOffer;

class SupplierOfferController extends Controller
{
public function index()
{
    $offers = SupplierOffer::with('rawMaterial')->get()
        ->map(function($offer) {
            return [
                'id' => $offer->id,
                'supplier_id' => $offer->supplier_id,
                'rawmat_id' => $offer->rawmat_id,
                'material_name' => $offer->rawMaterial->item ?? null,
                'unit' => $offer->unit,
                'price' => $offer->price,
            ];
        });

    return response()->json($offers);
}

    public function store(Request $request, $supplierId)
    {
        $validated = $request->validate([
            'rawmat_id' => 'required|integer|exists:inventory_rawmats,id',
            'unit' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        $offer = SupplierOffer::create([
            'supplier_id' => $supplierId,
            'rawmat_id' => $validated['rawmat_id'],
            'unit' => $validated['unit'],
            'price' => $validated['price'],
        ]);

        return response()->json([
            'message' => 'Offer added successfully',
            'data' => $offer->load('rawMaterial'),
        ]);
    }

public function update(Request $request, $supplierId, $rawmatId)
{
    // Ensure rawmatId is numeric
    if (!is_numeric($rawmatId)) {
        return response()->json(['message' => 'Invalid raw material ID'], 400);
    }

    $rawmatId = (int) $rawmatId;

    $validated = $request->validate([
        'price' => 'required|numeric|min:0',
    ]);

    $offer = SupplierOffer::where('supplier_id', $supplierId)
        ->where('rawmat_id', $rawmatId)
        ->first();

    if (!$offer) {
        return response()->json(['message' => 'Offer not found'], 404);
    }

    $offer->price = $validated['price'];
    $offer->save();

    return response()->json([
        'message' => 'Offer updated successfully',
        'data' => $offer->load('rawMaterial'),
    ]);
}

}
