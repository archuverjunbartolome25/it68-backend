<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQuantitiesToSalesOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_orders', 'qty_350ml')) {
                $table->integer('qty_350ml')->default(0);
            }
            if (!Schema::hasColumn('sales_orders', 'qty_500ml')) {
                $table->integer('qty_500ml')->default(0);
            }
            if (!Schema::hasColumn('sales_orders', 'qty_1L')) {
                $table->integer('qty_1L')->default(0);
            }
            if (!Schema::hasColumn('sales_orders', 'quantities')) {
                $table->jsonb('quantities')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropColumn(['qty_350ml', 'qty_500ml', 'qty_1L', 'quantities']);
        });
    }
}

