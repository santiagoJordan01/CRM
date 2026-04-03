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
          Schema::create('municipios', function (Blueprint $table) {
        $table->id();
        $table->foreignId('departamento_id')->constrained('departamentos')->onDelete('cascade');
        $table->string('nombre', 100);
        $table->string('cod_dane', 10);
        $table->timestamps();
        
        $table->index('departamento_id');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('municipios');
    }
};
