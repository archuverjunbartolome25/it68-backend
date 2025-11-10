<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
public function run(): void
{
    $this->call([
        DefaultUserSeeder::class,
        InventorySeeder::class,
        InventoryRawmatsSeeder::class,
        CustomerSeeder::class,
        SuppliersTableSeeder::class,
        SupplierOffersTableSeeder::class,
        SalesOrdersTableSeeder::class,
        ReturnToVendorTableSeeder::class,
        PurchaseReceiptsSeeder::class,
        PurchaseOrdersSeeder::class,
        PurchaseOrderItemsSeeder::class,
        ProductionOutputsSeeder::class,
        PersonalAccessTokensSeeder::class,
        InventoryActivityLogsSeeder::class,
        DisposalsSeeder::class,
    ]);
}

}
