<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
            ['email' => 'asesor@gmail.com'],
            [
                'name' => 'Asesor CRM',
                'role' => 'asesor',
                'password' => Hash::make('admin123'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'supervisor@gmail.com'],
            [
                'name' => 'Mesa de Control',
                'role' => 'supervisor',
                'password' => Hash::make('admin123'),
            ]
        );

        $this->call([
            DepartamentosSeeder::class,
            MunicipiosSeeder::class,
        ]);
    }
}
