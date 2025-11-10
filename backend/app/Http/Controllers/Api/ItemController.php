<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index()
    {
        return Item::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'size' => 'required|string',
            'quantity' => 'required|integer',
        ]);

        return Item::create($validated);
    }

    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string',
            'size' => 'required|string',
            'quantity' => 'required|integer',
        ]);

        $item->update($validated);

        return response()->json(['message' => 'Updated successfully']);
    }

    public function destroy($id)
    {
        Item::destroy($id);

        return response()->json(['message' => 'Deleted']);
    }
}
