<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

public function up()
{
    Schema::table('purchase_receipts', function (Blueprint $table) {
        $table->string('image_path')->nullable()->after('received_date');
    });
}

public function down()
{
    Schema::table('purchase_receipts', function (Blueprint $table) {
        $table->dropColumn('image_path');
    });
}

};
