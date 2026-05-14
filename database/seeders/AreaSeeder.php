<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Area;

class AreaSeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Informática',
            'RRHH',
            'Contabilidad',
            'Gerencia Municipal',
            'Fiscalización',
            'Seguridad Ciudadana',
        ];

        foreach ($names as $name) {
            Area::firstOrCreate(['name' => $name], ['is_active' => true]);
        }
    }
}
