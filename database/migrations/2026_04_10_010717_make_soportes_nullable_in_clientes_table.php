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
        Schema::table('clientes', function (Blueprint $table) {
        $table->string('soporte_1')->nullable()->change();
        $table->string('soporte_2')->nullable()->change();
        $table->string('soporte_3')->nullable()->change();
        $table->string('soporte_4')->nullable()->change();
        $table->string('soporte_5')->nullable()->change();
        $table->string('soporte_6')->nullable()->change();
        // También para los soportes de mesa de control si existen
        $table->string('mesa_soporte_1')->nullable()->change();
        $table->string('mesa_soporte_2')->nullable()->change();
        $table->string('mesa_soporte_3')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('soporte_1')->nullable(false)->change();
        $table->string('soporte_2')->nullable(false)->change();
        $table->string('soporte_3')->nullable(false)->change();
        $table->string('soporte_4')->nullable(false)->change();
        $table->string('soporte_5')->nullable(false)->change();
        $table->string('soporte_6')->nullable(false)->change();
        $table->string('mesa_soporte_1')->nullable(false)->change();
        $table->string('mesa_soporte_2')->nullable(false)->change();
        $table->string('mesa_soporte_3')->nullable(false)->change();
        });
    }
};
