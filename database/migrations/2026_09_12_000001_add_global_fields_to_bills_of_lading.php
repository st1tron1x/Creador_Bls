<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bills_of_lading', function (Blueprint $table) {
            // Trade & Business obtiene el puerto de carga vía port_id (relación).
            // Global no usa un catálogo de puertos -- lo escribe libre, por eso:
            $table->foreignId('port_id')->nullable()->change();
            $table->string('port_of_loading_text')->nullable()->after('port_id');

            // Campos exclusivos del formato de Global (quedan NULL para Trade & Business)
            $table->text('domestic_routing')->nullable();
            $table->text('export_references')->nullable();
            $table->text('cargo_delivery_contact')->nullable();
            $table->text('forwarding_agent_references')->nullable();
            $table->string('exporting_carrier')->nullable();
            $table->string('on_carriage_by')->nullable();
            $table->string('net_weight')->nullable();
            $table->decimal('total_charges', 12, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('bills_of_lading', function (Blueprint $table) {
            $table->dropColumn([
                'port_of_loading_text', 'domestic_routing', 'export_references',
                'cargo_delivery_contact', 'forwarding_agent_references',
                'exporting_carrier', 'on_carriage_by', 'net_weight', 'total_charges',
            ]);
        });
    }
};
