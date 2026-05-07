<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class RolesAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Crear roles
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'tecnico', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'usuario', 'guard_name' => 'web']);

        // 2) Crear admin inicial
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrador TI',
                'password' => Hash::make('Admin#2026!'),
            ]
        );

        // 3) Asignar rol admin
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }
    }
}