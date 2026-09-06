<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bills_of_lading', function (Blueprint $table) {
            // Texto libre: número/referencia que a veces asigna la naviera (formato variable),
            // distinto del consecutivo interno por puerto (bl_number).
            $table->string('bill_of_loading_ref')->nullable()->after('shipper_ref');
        });
    }

    public function down(): void
    {
        Schema::table('bills_of_lading', function (Blueprint $table) {
            $table->dropColumn('bill_of_loading_ref');
        });
    }
};
