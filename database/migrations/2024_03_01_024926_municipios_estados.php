<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('municipios_estados_BR', function (Blueprint $table) {
            $table->id();
            $table->string('estado');
            $table->string('uf')->index();
            $table->string('regiao');
            $table->string('municipio')->index();
            $table->boolean('capital');
            $table->index(['uf', 'municipio']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('municipios_estados_BR');
    }
};
