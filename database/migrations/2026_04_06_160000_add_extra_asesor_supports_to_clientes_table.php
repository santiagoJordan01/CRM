<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('soporte_4', 255)->nullable()->after('soporte_3');
            $table->string('soporte_5', 255)->nullable()->after('soporte_4');
            $table->string('soporte_6', 255)->nullable()->after('soporte_5');
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn([
                'soporte_4',
                'soporte_5',
                'soporte_6',
            ]);
        });
    }
};
