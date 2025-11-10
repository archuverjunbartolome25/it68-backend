<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        DB::table('customers')->insert([
            [
                'id' => 1,
                'name' => 'Alfamart Lakeside',
                'billing_address' => 'P1 Block 25 Lot 25, Katapatan Subdivision, Barangay Banay-banay, Cabuyao City, Laguna',
                'shipping_address' => 'P1 Block 25 Lot 25, Katapatan Subdivision, Barangay Banay-banay, Cabuyao City, Laguna',
                'bank_details' => 'GCash',
                'tin' => '1244-5658-0000',
                'created_at' => null,
                'updated_at' => '2025-11-04 20:22:18',
                'status' => 'Active',
            ],
            [
                'id' => 2,
                'name' => 'Dali Convenience Store',
                'billing_address' => 'Lakesidenest Subdivision Branch',
                'shipping_address' => 'Lakesidenest Subdivision Branch',
                'bank_details' => 'PayMaya',
                'tin' => '1454-1224-0000',
                'created_at' => '2025-11-04 16:04:02',
                'updated_at' => '2025-11-04 20:21:50',
                'status' => 'Active',
            ],
            [
                'id' => 3,
                'name' => 'Katapatan Grocery',
                'billing_address' => 'Katapatan Subdivision, Brgy. Banay-banay, Cabuyao City, Laguna',
                'shipping_address' => 'Katapatan Subdivision, Brgy. Banay-banay, Cabuyao City, Laguna',
                'bank_details' => 'GCash, Paymaya',
                'tin' => '1564-3212-0000',
                'created_at' => '2025-11-04 20:23:19',
                'updated_at' => '2025-11-04 20:23:19',
                'status' => 'Active',
            ],
            [
                'id' => 4,
                'name' => "Nicolette's Store",
                'billing_address' => 'P1 Block 25 Lot 1 Lakesidenest Subdivision, Barangay Banay-banay, Cabuyao City, Laguna',
                'shipping_address' => 'P1 Block 25 Lot 1 Lakesidenest Subdivision, Barangay Banay-banay, Cabuyao City, Laguna',
                'bank_details' => 'Metro Bank',
                'tin' => '1442-2145-0000',
                'created_at' => '2025-11-04 20:28:49',
                'updated_at' => '2025-11-04 20:28:49',
                'status' => 'Active',
            ],
            [
                'id' => 5,
                'name' => 'Barangay Banay-banay Hall',
                'billing_address' => 'Banay-banay Road, Cabuyao City, Laguna',
                'shipping_address' => 'Banay-banay Road, Cabuyao City, Laguna',
                'bank_details' => 'COD',
                'tin' => '1111-2147-0000',
                'created_at' => '2025-11-04 20:29:48',
                'updated_at' => '2025-11-04 20:29:53',
                'status' => 'Active',
            ],
            [
                'id' => 6,
                'name' => "Grace's Eatery",
                'billing_address' => 'Matulungin St., Katapatan Subdivision, Barangay Banay-banay, Cabuyao City, Laguna',
                'shipping_address' => 'Matulungin St., Katapatan Subdivision, Barangay Banay-banay, Cabuyao City, Laguna',
                'bank_details' => 'COD',
                'tin' => '2197-8283-0000',
                'created_at' => '2025-11-04 20:30:54',
                'updated_at' => '2025-11-04 20:30:58',
                'status' => 'Active',
            ],
        ]);
    }
}
