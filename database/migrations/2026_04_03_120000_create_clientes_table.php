<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('campania', 255);
            $table->string('producto', 255);
            $table->string('cedula', 30);
            $table->string('genero', 50);
            $table->string('email', 255);
            $table->foreignId('departamento_id')->constrained('departamentos')->cascadeOnDelete();
            $table->foreignId('municipio_id')->constrained('municipios')->cascadeOnDelete();
            $table->string('perfil', 255);
            $table->string('empresa', 255);
            $table->date('fecha_vinculacion');
            $table->string('canal', 100);
            $table->unsignedInteger('plazo');
            $table->string('ingreso_principal', 30);
            $table->string('tipo_cliente', 100);
            $table->string('destino', 255);
            $table->string('nombre_cliente', 255);
            $table->date('fecha_nacimiento');
            $table->string('sector', 100);
            $table->string('nit_empresa', 30);
            $table->string('tipo_contrato', 255);
            $table->string('monto_filtrado', 30);
            $table->string('celular_cliente', 20);
            $table->string('otros_ingresos', 30);
            $table->text('observaciones');
            $table->string('status', 40)->default('Viable');
            $table->string('sub_status', 80)->default('Pendiente Radicar');
            $table->string('recordatorio', 255)->default('Recordatorio / Tarea');
            $table->string('soporte_1', 255);
            $table->string('soporte_2', 255);
            $table->string('soporte_3', 255);
            $table->timestamps();

            $table->index('cedula');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
