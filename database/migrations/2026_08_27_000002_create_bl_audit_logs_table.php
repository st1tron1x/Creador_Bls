<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bl_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_of_lading_id')->constrained('bills_of_lading')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->string('action', 40); // creado | visto | descargado | anulacion_solicitada | anulacion_aprobada | anulacion_rechazada
            $table->text('detail')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['bill_of_lading_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bl_audit_logs');
    }
};
