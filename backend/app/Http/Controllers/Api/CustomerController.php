<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Include 'status' in the data returned
        return Customer::select(
            'id',
            'name',
            'billing_address',
            'shipping_address',
            'bank_details',
            'tin',
            'status' 
        )->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'billing_address' => 'nullable|string|max:255',
            'shipping_address' => 'nullable|string|max:255',
            'bank_details' => 'nullable|string|max:255',
            'tin' => 'nullable|string|max:255',
            'status' => 'required|in:Active,Inactive', 
        ]);

        $customer = Customer::create($validated);

        return response()->json($customer, 201);
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'billing_address' => 'nullable|string|max:255',
            'shipping_address' => 'nullable|string|max:255',
            'bank_details' => 'nullable|string|max:255',
            'tin' => 'nullable|string|max:255',
            'status' => 'required|in:Active,Inactive', // Added status
        ]);

        $customer->update($validated);

        return response()->json($customer);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $customer->delete();

            return response()->json(['message' => 'Customer deleted successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete customer',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete customers
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('customer_ids', []);

        if (empty($ids)) {
            return response()->json(['message' => 'No customer IDs provided'], 400);
        }

        Customer::whereIn('id', $ids)->delete();

        return response()->json(['message' => 'Customers deleted successfully']);
    }

    public function show($id)
{
    $customer = Customer::findOrFail($id);

    return response()->json($customer);
}
}
