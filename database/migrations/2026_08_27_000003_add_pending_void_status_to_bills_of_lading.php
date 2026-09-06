<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Postgres implementa el enum() de Laravel como un CHECK constraint sobre una columna string.
        // Hay que reemplazar ese constraint para permitir el nuevo valor.
        DB::statement('ALTER TABLE bills_of_lading DROP CONSTRAINT IF EXISTS bills_of_lading_status_check');
        DB::statement("ALTER TABLE bills_of_lading ADD CONSTRAINT bills_of_lading_status_check CHECK (status IN ('borrador','emitido','anulado','anulacion_solicitada'))");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE bills_of_lading DROP CONSTRAINT IF EXISTS bills_of_lading_status_check');
        DB::statement("ALTER TABLE bills_of_lading ADD CONSTRAINT bills_of_lading_status_check CHECK (status IN ('borrador','emitido','anulado'))");
    }
};
