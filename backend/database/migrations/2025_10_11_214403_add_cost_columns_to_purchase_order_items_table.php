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
    Schema::table('purchase_order_items', function (Blueprint $table) {
        $table->decimal('unit_cost', 12, 2)->default(0);
        $table->decimal('total_amount', 12, 2)->default(0);
    });
}

public function down()
{
    Schema::table('purchase_order_items', function (Blueprint $table) {
        $table->dropColumn(['unit_cost', 'total_amount']);
    });
}

};
