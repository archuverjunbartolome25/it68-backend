<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up()
{
    Schema::table('sales_orders', function (Blueprint $table) {
        $table->integer('qty_6L')->default(0)->after('qty_1L'); // adjust position if needed
    });
}

public function down()
{
    Schema::table('sales_orders', function (Blueprint $table) {
        $table->dropColumn('qty_6L');
    });
}

};
