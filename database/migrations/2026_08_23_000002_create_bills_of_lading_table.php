<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bills_of_lading', function (Blueprint $table) {
            $table->id();

            // Identificación / numeración
            $table->foreignId('port_id')->constrained('ports'); // define el prefijo (BVT, CTG, ...)
            $table->unsignedInteger('sequence_number');         // el número dentro del puerto (0001, 0002, ...)
            $table->string('bl_number', 20)->unique();          // ej. BVT-0007, ya formateado
            $table->enum('status', ['borrador', 'emitido', 'anulado'])->default('borrador');

            // Casillas del formato
            $table->string('country_origin')->nullable();
            $table->string('shipper_ref')->nullable();
            $table->text('shipper')->nullable();
            $table->text('consignee')->nullable();
            $table->text('notify_party')->nullable();
            $table->string('pre_carriage_by')->nullable();
            $table->string('place_of_receipt')->nullable();
            $table->string('ocean_vessel_voy_no')->nullable();
            $table->string('port_of_discharge')->nullable();
            $table->string('place_of_delivery')->nullable();
            $table->string('final_destination')->nullable();
            $table->string('freight_payable_at')->nullable();
            $table->string('number_of_original_bsl')->nullable();
            $table->text('temperature_control_instructions')->nullable();
            $table->enum('freight_type', ['prepaid', 'collect'])->nullable();
            $table->decimal('freight_total', 12, 2)->nullable();
            $table->text('freight_and_charges')->nullable();
            $table->text('excess_value_declaration')->nullable();
            $table->date('on_board_date')->nullable();
            $table->string('delivery_agent')->nullable();

            // Trazabilidad / auditoría
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamp('issued_at')->nullable();
            $table->foreignId('voided_by')->nullable()->constrained('users');
            $table->timestamp('voided_at')->nullable();
            $table->text('voided_reason')->nullable();

            $table->timestamps();

            $table->unique(['port_id', 'sequence_number']); // refuerza que no se repita el consecutivo por puerto
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bills_of_lading');
    }
};
