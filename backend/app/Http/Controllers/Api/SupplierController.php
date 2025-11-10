<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\SupplierOffer;

class SupplierController extends Controller
{
    public function index()
    {
        return response()->json(Supplier::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'tin' => 'nullable|string|unique:suppliers,tin',    
        ]);

        $supplier = Supplier::create($validated);

        return response()->json([
            'message' => 'Supplier added successfully',
            'data' => $supplier
        ]);
    }

public function getOffers($id)
{
    $offers = SupplierOffer::with('rawMaterial')
        ->where('supplier_id', $id)
        ->get();

    if ($offers->isEmpty()) {
        return response()->json([]);
    }

    return response()->json($offers->map(function ($offer) {
        return [
            'id' => $offer->id,
            'supplier_id' => $offer->supplier_id,
            'rawmat_id' => $offer->rawmat_id,
            'material_name' => $offer->rawMaterial->item ?? 'N/A',
            'unit' => $offer->unit ?? $offer->rawMaterial->unit ?? 'N/A',
            'price' => $offer->price,
        ];
    }));
}

}
