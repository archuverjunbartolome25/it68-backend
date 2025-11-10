<?php

// database/migrations/YYYY_MM_DD_add_customer_id_to_sales_orders_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            // Change this line to allow null values
            $table->foreignId('customer_id')->nullable()->constrained()->cascadeOnDelete();
            // This line is now safe to run because the column can be null initially
            $table->dropColumn('customer_name');
        });
    }

    public function down(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
            $table->string('customer_name')->nullable();
        });
    }
};