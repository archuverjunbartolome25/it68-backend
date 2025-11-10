<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryRawmatsTable extends Migration
{
    public function up()
    {
        Schema::create('inventory_rawmats', function (Blueprint $table) {
            $table->id(); // id column
            $table->string('item'); // item name
            $table->string('unit'); // unit like pcs, ml, etc
            $table->integer('quantity'); // quantity
            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory_rawmats');
    }
}
