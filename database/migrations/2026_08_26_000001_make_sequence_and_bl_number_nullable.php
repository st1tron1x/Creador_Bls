<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bills_of_lading', function (Blueprint $table) {
            $table->unsignedInteger('sequence_number')->nullable()->change();
            $table->string('bl_number', 20)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('bills_of_lading', function (Blueprint $table) {
            $table->unsignedInteger('sequence_number')->nullable(false)->change();
            $table->string('bl_number', 20)->nullable(false)->change();
        });
    }
};
