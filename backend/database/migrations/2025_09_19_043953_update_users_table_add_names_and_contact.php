<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove the old 'name' column
            if (Schema::hasColumn('users', 'name')) {
                $table->dropColumn('name');
            }

            // Add new columns as nullable to avoid errors with existing data
            if (!Schema::hasColumn('users', 'lastname')) {
                $table->string('lastname')->nullable()->after('id');
            }

            if (!Schema::hasColumn('users', 'firstname')) {
                $table->string('firstname')->nullable()->after('lastname');
            }

            if (!Schema::hasColumn('users', 'contact')) {
                $table->string('contact')->nullable()->after('email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Restore old structure if rolling back
            if (!Schema::hasColumn('users', 'name')) {
                $table->string('name')->after('id');
            }

            $table->dropColumn(['lastname', 'firstname', 'contact']);
        });
    }
};
