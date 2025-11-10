<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReturnToVendorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('return_to_vendor')->insert([
            [
                'id' => 1,
                'customer_id' => 1,
                'location' => 'P1 Block 25 Lot 25, Katapatan Subdivision, Barangay Banay-banay, Cabuyao City, Laguna',
                'date_ordered' => '2025-11-03',
                'date_returned' => '2025-11-03',
                'qty_350ml' => 1,
                'qty_500ml' => 0,
                'qty_1l' => 0,
                'qty_6l' => 0,
                'created_at' => '2025-11-03 00:40:23',
                'updated_at' => '2025-11-03 00:40:28',
                'rtv_number' => 'RTV-20251102-3369',
                'status' => 'Approved',
            ],
            [
                'id' => 2,
                'customer_id' => 1,
                'location' => 'P1 Block 25 Lot 25, Katapatan Subdivision, Barangay Banay-banay, Cabuyao City, Laguna',
                'date_ordered' => '2025-11-03',
                'date_returned' => '2025-11-03',
                'qty_350ml' => 2,
                'qty_500ml' => 0,
                'qty_1l' => 0,
                'qty_6l' => 0,
                'created_at' => '2025-11-03 19:35:13',
                'updated_at' => '2025-11-03 19:35:24',
                'rtv_number' => 'RTV-20251103-4836',
                'status' => 'Approved',
            ],
            [
                'id' => 3,
                'customer_id' => 1,
                'location' => 'P1 Block 25 Lot 25, Katapatan Subdivision, Barangay Banay-banay, Cabuyao City, Laguna',
                'date_ordered' => '2025-11-03',
                'date_returned' => '2025-11-03',
                'qty_350ml' => 2,
                'qty_500ml' => 0,
                'qty_1l' => 0,
                'qty_6l' => 0,
                'created_at' => '2025-11-03 21:14:29',
                'updated_at' => '2025-11-03 21:14:42',
                'rtv_number' => 'RTV-20251103-4725',
                'status' => 'Approved',
            ],
            [
                'id' => 4,
                'customer_id' => 1,
                'location' => 'P1 Block 25 Lot 25, Katapatan Subdivision, Barangay Banay-banay, Cabuyao City, Laguna',
                'date_ordered' => '2025-11-03',
                'date_returned' => '2025-11-03',
                'qty_350ml' => 1,
                'qty_500ml' => 0,
                'qty_1l' => 0,
                'qty_6l' => 0,
                'created_at' => '2025-11-03 22:32:33',
                'updated_at' => '2025-11-03 22:32:46',
                'rtv_number' => 'RTV-20251103-9599',
                'status' => 'Approved',
            ],
            [
                'id' => 5,
                'customer_id' => 1,
                'location' => 'P1 Block 25 Lot 25, Katapatan Subdivision, Barangay Banay-banay, Cabuyao City, Laguna',
                'date_ordered' => '2025-11-04',
                'date_returned' => '2025-11-04',
                'qty_350ml' => 1,
                'qty_500ml' => 0,
                'qty_1l' => 0,
                'qty_6l' => 0,
                'created_at' => '2025-11-04 15:47:15',
                'updated_at' => '2025-11-04 15:47:46',
                'rtv_number' => 'RTV-20251104-1126',
                'status' => 'Approved',
            ],
        ]);
    }
}
