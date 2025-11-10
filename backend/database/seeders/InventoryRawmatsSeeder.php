<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventoryRawmatsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('inventory_rawmats')->insert([
            [
                'id' => 1,
                'item' => 'Plastic Bottle (350ml)',
                'unit' => 'pieces',
                'quantity' => 200000,
                'conversion' => 1,
                'quantity_pieces' => 200000,
                'low_stock_alert' => 150000,
                'created_at' => $now,
                'updated_at' => $now,
                'price' => 2.10,
            ],
            [
                'id' => 2,
                'item' => 'Plastic Bottle (500ml)',
                'unit' => 'pieces',
                'quantity' => 300000,
                'conversion' => 1,
                'quantity_pieces' => 300000,
                'low_stock_alert' => 150000,
                'created_at' => $now,
                'updated_at' => $now,
                'price' => 2.25,
            ],
            [
                'id' => 3,
                'item' => 'Plastic Bottle (500ml)',
                'unit' => 'pieces',
                'quantity' => 250000,
                'conversion' => 2,
                'quantity_pieces' => 250000,
                'low_stock_alert' => 150000,
                'created_at' => $now,
                'updated_at' => $now,
                'price' => 2.35,
            ],
            [
                'id' => 4,
                'item' => 'Plastic Bottle (1L)',
                'unit' => 'pieces',
                'quantity' => 150000,
                'conversion' => 1,
                'quantity_pieces' => 150000,
                'low_stock_alert' => 150000,
                'created_at' => $now,
                'updated_at' => $now,
                'price' => 4.05,
            ],
            [
                'id' => 5,
                'item' => 'Plastic Gallon (6L)',
                'unit' => 'pieces',
                'quantity' => 100000,
                'conversion' => 1,
                'quantity_pieces' => 100000,
                'low_stock_alert' => 150000,
                'created_at' => $now,
                'updated_at' => $now,
                'price' => 23.00,
            ],
            [
                'id' => 6,
                'item' => 'Blue Plastic Cap',
                'unit' => 'pieces',
                'quantity' => 300000,
                'conversion' => 1,
                'quantity_pieces' => 300000,
                'low_stock_alert' => 150000,
                'created_at' => $now,
                'updated_at' => $now,
                'price' => 0.50,
            ],
            [
                'id' => 7,
                'item' => 'Blue Plastic Cap',
                'unit' => 'pieces',
                'quantity' => 400000,
                'conversion' => 2,
                'quantity_pieces' => 400000,
                'low_stock_alert' => 150000,
                'created_at' => $now,
                'updated_at' => $now,
                'price' => 0.49,
            ],
            [
                'id' => 8,
                'item' => 'Blue Plastic Cap (6L)',
                'unit' => 'pieces',
                'quantity' => 150000,
                'conversion' => 1,
                'quantity_pieces' => 150000,
                'low_stock_alert' => 150000,
                'created_at' => $now,
                'updated_at' => $now,
                'price' => 3.00,
            ],
            [
                'id' => 9,
                'item' => 'Label',
                'unit' => 'rolls',
                'quantity' => 20,
                'conversion' => 10,
                'quantity_pieces' => 20000,
                'low_stock_alert' => 400000,
                'created_at' => $now,
                'updated_at' => $now,
                'price' => 7960.00,
            ],
            [
                'id' => 10,
                'item' => 'Label',
                'unit' => 'rolls',
                'quantity' => 10,
                'conversion' => 10,
                'quantity_pieces' => 20000,
                'low_stock_alert' => 200000,
                'created_at' => $now,
                'updated_at' => $now,
                'price' => 5960.00,
            ],
            [
                'id' => 11,
                'item' => 'Stretchfilm',
                'unit' => 'pieces',
                'quantity' => 100,
                'conversion' => 1,
                'quantity_pieces' => 100,
                'low_stock_alert' => 50,
                'created_at' => $now,
                'updated_at' => $now,
                'price' => 320.00,
            ],
            [
                'id' => 12,
                'item' => 'Shrinkfilm',
                'unit' => 'pieces',
                'quantity' => 120,
                'conversion' => 1,
                'quantity_pieces' => 120,
                'low_stock_alert' => 50,
                'created_at' => $now,
                'updated_at' => $now,
                'price' => 2337.00,
            ],
        ]);
    }
}
