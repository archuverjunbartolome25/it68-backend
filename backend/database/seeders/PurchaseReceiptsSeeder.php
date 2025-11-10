<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseReceiptsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('purchase_receipts')->insert([
            [
                'id' => 1,
                'purchase_order_id' => 1,
                'purchase_order_item_id' => 1,
                'po_number' => 'PO-1762102282082',
                'item_name' => 'Plastic Bottle (350ml)',
                'quantity_received' => 250,
                'received_date' => '2025-11-03 00:51:57',
                'created_at' => '2025-11-03 00:51:57',
                'updated_at' => '2025-11-03 00:51:57',
                'image_path' => null,
            ],
            [
                'id' => 2,
                'purchase_order_id' => 2,
                'purchase_order_item_id' => 2,
                'po_number' => 'PO-1762169848814',
                'item_name' => 'Plastic Bottle (350ml)',
                'quantity_received' => 50000,
                'received_date' => '2025-11-03 19:39:55',
                'created_at' => '2025-11-03 19:39:55',
                'updated_at' => '2025-11-03 19:39:55',
                'image_path' => null,
            ],
            [
                'id' => 3,
                'purchase_order_id' => 2,
                'purchase_order_item_id' => 3,
                'po_number' => 'PO-1762169848814',
                'item_name' => 'Plastic Bottle (500ml)',
                'quantity_received' => 25000,
                'received_date' => '2025-11-03 19:39:56',
                'created_at' => '2025-11-03 19:39:56',
                'updated_at' => '2025-11-03 19:39:56',
                'image_path' => null,
            ],
            [
                'id' => 4,
                'purchase_order_id' => 2,
                'purchase_order_item_id' => 2,
                'po_number' => 'PO-1762169848814',
                'item_name' => 'Plastic Bottle (350ml)',
                'quantity_received' => 50000,
                'received_date' => '2025-11-03 19:41:17',
                'created_at' => '2025-11-03 19:41:17',
                'updated_at' => '2025-11-03 19:41:17',
                'image_path' => null,
            ],
            [
                'id' => 5,
                'purchase_order_id' => 2,
                'purchase_order_item_id' => 3,
                'po_number' => 'PO-1762169848814',
                'item_name' => 'Plastic Bottle (500ml)',
                'quantity_received' => 25000,
                'received_date' => '2025-11-03 19:41:18',
                'created_at' => '2025-11-03 19:41:18',
                'updated_at' => '2025-11-03 19:41:18',
                'image_path' => null,
            ],
            [
                'id' => 6,
                'purchase_order_id' => 3,
                'purchase_order_item_id' => 4,
                'po_number' => 'PO-1762180484692',
                'item_name' => 'Plastic Bottle (350ml)',
                'quantity_received' => 500,
                'received_date' => '2025-11-03 22:36:54',
                'created_at' => '2025-11-03 22:36:54',
                'updated_at' => '2025-11-03 22:36:54',
                'image_path' => null,
            ],
            [
                'id' => 7,
                'purchase_order_id' => 3,
                'purchase_order_item_id' => 4,
                'po_number' => 'PO-1762180484692',
                'item_name' => 'Plastic Bottle (350ml)',
                'quantity_received' => 500,
                'received_date' => '2025-11-03 22:37:38',
                'created_at' => '2025-11-03 22:37:38',
                'updated_at' => '2025-11-03 22:37:38',
                'image_path' => null,
            ],
            [
                'id' => 8,
                'purchase_order_id' => 4,
                'purchase_order_item_id' => 5,
                'po_number' => 'PO-1762242680745',
                'item_name' => 'Plastic Bottle (350ml)',
                'quantity_received' => 50,
                'received_date' => '2025-11-04 15:52:05',
                'created_at' => '2025-11-04 15:52:05',
                'updated_at' => '2025-11-04 15:52:05',
                'image_path' => null,
            ],
            [
                'id' => 9,
                'purchase_order_id' => 4,
                'purchase_order_item_id' => 5,
                'po_number' => 'PO-1762242680745',
                'item_name' => 'Plastic Bottle (350ml)',
                'quantity_received' => 50,
                'received_date' => '2025-11-04 15:53:08',
                'created_at' => '2025-11-04 15:53:08',
                'updated_at' => '2025-11-04 15:53:08',
                'image_path' => null,
            ],
            [
                'id' => 10,
                'purchase_order_id' => 5,
                'purchase_order_item_id' => 6,
                'po_number' => 'PO-1762270221965',
                'item_name' => 'Plastic Bottle (350ml)',
                'quantity_received' => 100,
                'received_date' => '2025-11-04 23:30:47',
                'created_at' => '2025-11-04 23:30:47',
                'updated_at' => '2025-11-04 23:30:47',
                'image_path' => null,
            ],
            // Add remaining entries (11-19) similarly...
        ]);
    }
}
