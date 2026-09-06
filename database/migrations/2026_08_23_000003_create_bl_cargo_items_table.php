<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bl_cargo_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_of_lading_id')->constrained('bills_of_lading')->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0); // orden de la fila en la tabla de mercancía

            $table->text('marks_and_numbers')->nullable();     // marcas + contenedor/sello
            $table->string('container_seal_no')->nullable();
            $table->string('no_of_pkgs')->nullable();
            $table->text('description_of_goods')->nullable();
            $table->string('gross_weight')->nullable();
            $table->string('measurement_cbm')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bl_cargo_items');
    }
};
