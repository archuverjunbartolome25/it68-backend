<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Optional: clear existing records before inserting
        DB::table('inventories')->truncate();

        $now = Carbon::now();

        DB::table('inventories')->insert([
            [
                'id' => 1,
                'item' => '350ml',
                'unit' => 'case',
                'quantity' => 20000,
                'quantity_pcs' => 48000,
                'unit_cost' => 130.00,
                'low_stock_alert' => 1000,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'item' => '500ml',
                'unit' => 'case',
                'quantity' => 10000,
                'quantity_pcs' => 24000,
                'unit_cost' => 155.00,
                'low_stock_alert' => 1500,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'item' => '1L',
                'unit' => 'case',
                'quantity' => 10000,
                'quantity_pcs' => 12000,
                'unit_cost' => 130.00,
                'low_stock_alert' => 1000,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'item' => '6L',
                'unit' => 'pieces',
                'quantity' => 5000,
                'quantity_pcs' => 5000,
                'unit_cost' => 60.00,
                'low_stock_alert' => 500,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
