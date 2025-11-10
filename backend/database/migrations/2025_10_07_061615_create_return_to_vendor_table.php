<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('return_to_vendor', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->string('location')->nullable();
            $table->date('date_ordered')->nullable();
            $table->date('date_returned')->nullable();

            // Quantities
            $table->integer('qty_350ml')->default(0);
            $table->integer('qty_500ml')->default(0);
            $table->integer('qty_1l')->default(0);
            $table->integer('qty_6l')->default(0);

            $table->timestamps();

            // optional foreign key to customers
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_to_vendor');
    }
};
