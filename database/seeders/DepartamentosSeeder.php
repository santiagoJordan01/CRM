<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Departamento;

class DepartamentosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departamentos = [
            ['id' => 1, 'nombre' => 'AMAZONAS', 'cod_dane' => '91'],
            ['id' => 2, 'nombre' => 'ANTIOQUIA', 'cod_dane' => '05'],
            ['id' => 3, 'nombre' => 'ARAUCA', 'cod_dane' => '81'],
            ['id' => 4, 'nombre' => 'ARCHIPIÉLAGO DE SAN ANDRÉS, PROVIDENCIA Y SANTA CATALINA', 'cod_dane' => '88'],
            ['id' => 5, 'nombre' => 'ATLÁNTICO', 'cod_dane' => '08'],
            ['id' => 6, 'nombre' => 'BOGOTÁ D.C.', 'cod_dane' => '11'],
            ['id' => 7, 'nombre' => 'BOLÍVAR', 'cod_dane' => '13'],
            ['id' => 8, 'nombre' => 'BOYACÁ', 'cod_dane' => '15'],
            ['id' => 9, 'nombre' => 'CALDAS', 'cod_dane' => '17'],
            ['id' => 10, 'nombre' => 'CAQUETÁ', 'cod_dane' => '18'],
            ['id' => 11, 'nombre' => 'CASANARE', 'cod_dane' => '85'],
            ['id' => 12, 'nombre' => 'CAUCA', 'cod_dane' => '19'],
            ['id' => 13, 'nombre' => 'CESAR', 'cod_dane' => '20'],
            ['id' => 14, 'nombre' => 'CHOCÓ', 'cod_dane' => '27'],
            ['id' => 15, 'nombre' => 'CÓRDOBA', 'cod_dane' => '23'],
            ['id' => 16, 'nombre' => 'CUNDINAMARCA', 'cod_dane' => '25'],
            ['id' => 17, 'nombre' => 'GUAINÍA', 'cod_dane' => '94'],
            ['id' => 18, 'nombre' => 'GUAVIARE', 'cod_dane' => '95'],
            ['id' => 19, 'nombre' => 'HUILA', 'cod_dane' => '41'],
            ['id' => 20, 'nombre' => 'LA GUAJIRA', 'cod_dane' => '44'],
            ['id' => 21, 'nombre' => 'MAGDALENA', 'cod_dane' => '47'],
            ['id' => 22, 'nombre' => 'META', 'cod_dane' => '50'],
            ['id' => 23, 'nombre' => 'NARIÑO', 'cod_dane' => '52'],
            ['id' => 24, 'nombre' => 'NORTE DE SANTANDER', 'cod_dane' => '54'],
            ['id' => 25, 'nombre' => 'PUTUMAYO', 'cod_dane' => '86'],
            ['id' => 26, 'nombre' => 'QUINDÍO', 'cod_dane' => '63'],
            ['id' => 27, 'nombre' => 'RISARALDA', 'cod_dane' => '66'],
            ['id' => 28, 'nombre' => 'SANTANDER', 'cod_dane' => '68'],
            ['id' => 29, 'nombre' => 'SUCRE', 'cod_dane' => '70'],
            ['id' => 30, 'nombre' => 'TOLIMA', 'cod_dane' => '73'],
            ['id' => 31, 'nombre' => 'VALLE DEL CAUCA', 'cod_dane' => '76'],
            ['id' => 32, 'nombre' => 'VAUPÉS', 'cod_dane' => '97'],
            ['id' => 33, 'nombre' => 'VICHADA', 'cod_dane' => '99'],
        ];
        
        foreach ($departamentos as $departamento) {
            Departamento::updateOrCreate(
                ['cod_dane' => $departamento['cod_dane']],
                ['nombre' => $departamento['nombre']]
            );
        }
    }
}
