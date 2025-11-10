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
    Schema::create('inventory_activity_logs', function (Blueprint $table) {
        $table->id();
        $table->string('employee_id');
        $table->string('module'); // "Sales Order", "Production Output", etc.
        $table->string('type'); // "Finished Good" or "Raw Material"
        $table->string('item_name');
        $table->integer('quantity'); // + or - value
        $table->timestamp('processed_at')->useCurrent();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_activity_logs');
    }
};
