<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->text('observacion_mesa_control')->nullable()->after('observaciones');
            $table->foreignId('mesa_control_user_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            $table->timestamp('mesa_control_respondido_at')->nullable()->after('recordatorio');
            $table->string('mesa_soporte_1', 255)->nullable()->after('soporte_3');
            $table->string('mesa_soporte_2', 255)->nullable()->after('mesa_soporte_1');
            $table->string('mesa_soporte_3', 255)->nullable()->after('mesa_soporte_2');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropConstrainedForeignId('mesa_control_user_id');
            $table->dropColumn([
                'observacion_mesa_control',
                'mesa_control_respondido_at',
                'mesa_soporte_1',
                'mesa_soporte_2',
                'mesa_soporte_3',
            ]);
        });
    }
};
