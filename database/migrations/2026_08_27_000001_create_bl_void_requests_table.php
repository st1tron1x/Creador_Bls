<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bl_void_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_of_lading_id')->constrained('bills_of_lading')->cascadeOnDelete();
            $table->foreignId('requested_by')->constrained('users');
            $table->text('requested_reason');
            $table->timestamp('requested_at');

            $table->enum('status', ['pendiente', 'aprobada', 'rechazada'])->default('pendiente');

            $table->foreignId('resolved_by')->nullable()->constrained('users');
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_note')->nullable(); // motivo de rechazo, si aplica

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bl_void_requests');
    }
};
