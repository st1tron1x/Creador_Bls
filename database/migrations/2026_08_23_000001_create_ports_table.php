<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ports', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique(); // BVT, CTG, BAQ, SMR, TBO
            $table->string('name');               // Buenaventura, Cartagena, ...
            $table->unsignedInteger('last_bl_number')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ports');
    }
};
