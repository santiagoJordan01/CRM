<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_leads', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_cliente', 255);
            $table->string('cedula', 30)->nullable();
            $table->string('email', 255);
            $table->string('celular_cliente', 20);
            $table->string('campania', 255);
            $table->string('producto', 255);
            $table->string('canal', 100)->nullable();
            $table->string('genero', 50)->nullable();
            $table->foreignId('departamento_id')->nullable()->constrained('departamentos')->nullOnDelete();
            $table->foreignId('municipio_id')->nullable()->constrained('municipios')->nullOnDelete();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('monto_referido', 30)->nullable();
            $table->string('ingreso_referido', 30)->nullable();
            $table->text('observaciones')->nullable();
            $table->string('status', 30)->default('pendiente');
            $table->foreignId('converted_cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('converted_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_leads');
    }
};
