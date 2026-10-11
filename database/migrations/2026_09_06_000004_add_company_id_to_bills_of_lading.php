<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bills_of_lading', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained();
        });

        // --- Backfill: todo lo que ya existe pertenece a Trade & Business ---
        $tradeCompanyId = DB::table('companies')->insertGetId([
            'name' => 'Trade & Business S.A.S.',
            'short_name' => 'Trade & Business',
            'numbering_scheme' => 'port_based',
            'fixed_prefix' => null,
            'pdf_template' => 'trade',
            'logo_path' => 'images/logo-trade-business.png',
            'address' => 'Av 4 Norte No. 37an-29 La Flora, Cali - Colombia',
            'phone' => '(602) 4896901',
            'email' => 'dayana.ordonez@tradebusiness.com.co',
            'active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Todos los BL existentes pasan a pertenecer a Trade & Business
        DB::table('bills_of_lading')->update(['company_id' => $tradeCompanyId]);

        // Una fila en bl_sequences por cada puerto existente, preservando su consecutivo actual
        $ports = DB::table('ports')->get();
        foreach ($ports as $port) {
            DB::table('bl_sequences')->insert([
                'company_id' => $tradeCompanyId,
                'label' => $port->code,
                'last_number' => $port->last_bl_number,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Todos los usuarios existentes quedan asociados a Trade & Business
        $userIds = DB::table('users')->pluck('id');
        foreach ($userIds as $userId) {
            DB::table('company_user')->insert([
                'company_id' => $tradeCompanyId,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Schema::table('bills_of_lading', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('bills_of_lading', function (Blueprint $table) {
            $table->dropConstrainedForeignId('company_id');
        });
    }
};
