<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class RawMaterialSupplierController extends Controller
{
    public function getByProduct($product)
    {
        // ✅ Define the BOM (Bill of Materials)
        $bomMap = [
            '350ml' => ['Plastic Bottle (350ml)', 'Blue Plastic Cap', 'Label'],
            '500ml' => ['Plastic Bottle (500ml)', 'Blue Plastic Cap', 'Label'],
            '1L'    => ['Plastic Bottle (1L)', 'Blue Plastic Cap', 'Label'],
            '6L'    => ['Plastic Gallon (6L)', 'Blue Plastic Cap (6L)', 'Label'],
        ];

        if (!isset($bomMap[$product])) {
            return response()->json(['message' => 'Unknown product'], 404);
        }

        $materials = $bomMap[$product];
        $result = [];

        foreach ($materials as $mat) {
            // ✅ Fetch all suppliers offering this raw material
            $suppliers = DB::select("
                SELECT s.id, s.name
                FROM suppliers s
                JOIN supplier_offers so ON s.id = so.supplier_id
                JOIN inventory_rawmats rm ON rm.id = so.rawmat_id
                WHERE rm.item = ?
            ", [$mat]);

            // ✅ Only include if multiple suppliers exist
            if (count($suppliers) > 1) {
                $result[] = [
                    'material' => $mat,
                    'suppliers' => $suppliers,
                ];
            }
        }

        return response()->json($result);
    }
}
