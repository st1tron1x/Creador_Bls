<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');                  // "Trade & Business S.A.S.", "Global Trade Business Corp"
            $table->string('short_name', 30);         // "Trade & Business", "Global Trade Business"
            $table->string('numbering_scheme', 20);   // 'port_based' (Trade) | 'company_based' (Global)
            $table->string('fixed_prefix')->nullable(); // el FMC OTI No. de Global, solo aplica si numbering_scheme = company_based
            $table->string('pdf_template');           // 'trade' | 'global' -- qué plantilla Blade usar
            $table->string('logo_path')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
