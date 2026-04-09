<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('tasa_ea', 60)->nullable()->after('observacion_mesa_control');
            $table->string('numero_credito', 120)->nullable()->after('tasa_ea');
            $table->string('oficina_radicacion', 255)->nullable()->after('numero_credito');
            $table->string('financiera_1', 255)->nullable()->after('oficina_radicacion');
            $table->string('financiera_2', 255)->nullable()->after('financiera_1');
            $table->string('financiera_3', 255)->nullable()->after('financiera_2');
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn([
                'tasa_ea',
                'numero_credito',
                'oficina_radicacion',
                'financiera_1',
                'financiera_2',
                'financiera_3',
            ]);
        });
    }
};
