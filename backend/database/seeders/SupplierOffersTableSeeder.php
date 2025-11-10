<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierOffersTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('supplier_offers')->insert([
            [
                'id' => 1,
                'supplier_id' => 1,
                'rawmat_id' => 1,
                'unit' => 'pieces',
                'price' => 2.10,
                'created_at' => '2025-11-02 23:31:20',
                'updated_at' => '2025-11-08 22:54:32',
            ],
            [
                'id' => 2,
                'supplier_id' => 1,
                'rawmat_id' => 2,
                'unit' => 'pieces',
                'price' => 2.25,
                'created_at' => '2025-11-02 23:44:37',
                'updated_at' => '2025-11-02 23:44:37',
            ],
            [
                'id' => 3,
                'supplier_id' => 2,
                'rawmat_id' => 3,
                'unit' => 'pieces',
                'price' => 2.35,
                'created_at' => '2025-11-02 23:47:39',
                'updated_at' => '2025-11-02 23:47:39',
            ],
            [
                'id' => 4,
                'supplier_id' => 1,
                'rawmat_id' => 4,
                'unit' => 'pieces',
                'price' => 4.05,
                'created_at' => '2025-11-02 23:48:11',
                'updated_at' => '2025-11-02 23:48:11',
            ],
            [
                'id' => 5,
                'supplier_id' => 1,
                'rawmat_id' => 5,
                'unit' => 'pieces',
                'price' => 23.00,
                'created_at' => '2025-11-02 23:48:36',
                'updated_at' => '2025-11-02 23:48:36',
            ],
            [
                'id' => 6,
                'supplier_id' => 1,
                'rawmat_id' => 6,
                'unit' => 'pieces',
                'price' => 0.50,
                'created_at' => '2025-11-02 23:48:57',
                'updated_at' => '2025-11-02 23:48:57',
            ],
            [
                'id' => 7,
                'supplier_id' => 2,
                'rawmat_id' => 7,
                'unit' => 'pieces',
                'price' => 0.49,
                'created_at' => '2025-11-02 23:49:14',
                'updated_at' => '2025-11-02 23:49:14',
            ],
            [
                'id' => 8,
                'supplier_id' => 1,
                'rawmat_id' => 8,
                'unit' => 'pieces',
                'price' => 3.00,
                'created_at' => '2025-11-02 23:49:34',
                'updated_at' => '2025-11-02 23:49:34',
            ],
            [
                'id' => 9,
                'supplier_id' => 3,
                'rawmat_id' => 9,
                'unit' => 'rolls',
                'price' => 7960.00,
                'created_at' => '2025-11-02 23:50:02',
                'updated_at' => '2025-11-02 23:50:02',
            ],
            [
                'id' => 10,
                'supplier_id' => 4,
                'rawmat_id' => 10,
                'unit' => 'rolls',
                'price' => 5960.00,
                'created_at' => '2025-11-02 23:50:16',
                'updated_at' => '2025-11-02 23:50:16',
            ],
            [
                'id' => 11,
                'supplier_id' => 5,
                'rawmat_id' => 11,
                'unit' => 'pieces',
                'price' => 320.00,
                'created_at' => '2025-11-02 23:50:30',
                'updated_at' => '2025-11-02 23:50:30',
            ],
            [
                'id' => 12,
                'supplier_id' => 6,
                'rawmat_id' => 12,
                'unit' => 'pieces',
                'price' => 2337.00,
                'created_at' => '2025-11-02 23:50:45',
                'updated_at' => '2025-11-02 23:50:45',
            ],
        ]);
    }
}
