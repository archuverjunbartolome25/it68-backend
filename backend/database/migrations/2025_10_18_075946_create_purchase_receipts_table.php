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
    Schema::create('purchase_receipts', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('purchase_order_id');
        $table->unsignedBigInteger('purchase_order_item_id');
        $table->string('po_number');
        $table->string('item_name');
        $table->integer('quantity_received');
        $table->timestamp('received_date')->useCurrent();
        $table->timestamps();

        $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade');
        $table->foreign('purchase_order_item_id')->references('id')->on('purchase_order_items')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_receipts');
    }
};
