<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Use raw statement to avoid requiring doctrine/dbal for a simple nullability change
        DB::statement('ALTER TABLE `clientes` MODIFY `soporte_3` VARCHAR(255) NULL');
    }

    public function down(): void
    {
        // Revert to NOT NULL (may fail if NULL values exist)
        DB::statement('ALTER TABLE `clientes` MODIFY `soporte_3` VARCHAR(255) NOT NULL');
    }
};
