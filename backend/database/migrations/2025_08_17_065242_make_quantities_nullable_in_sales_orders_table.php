<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->integer('qty_350ml')->nullable()->change();
            $table->integer('qty_500ml')->nullable()->change();
            $table->integer('qty_1L')->nullable()->change();
            $table->integer('qty_6L')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->integer('qty_350ml')->nullable(false)->change();
            $table->integer('qty_500ml')->nullable(false)->change();
            $table->integer('qty_1L')->nullable(false)->change();
            $table->integer('qty_6L')->nullable(false)->change();
        });
    }
};