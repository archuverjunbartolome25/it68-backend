<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('item'); // Name of item
            $table->string('unit'); // Unit (e.g., '1L', 'box', etc.)
            $table->integer('quantity')->default(0); // Quantity
            $table->date('date'); // Purchase date
            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down(): void {
        Schema::dropIfExists('inventory');
    }
};
