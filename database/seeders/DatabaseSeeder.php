<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\PortSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PortSeeder::class,
            RolePermissionSeeder::class,
        ]);

        // User::factory(10)->create();

        // Un usuario de prueba por cada rol -- todos con password "password" (default de UserFactory)
        $adminUser = User::factory()->create([
            'name' => 'Admin Prueba',
            'email' => 'test@example.com',
        ]);
        $adminUser->assignRole('admin');

        $gerenciaUser = User::factory()->create([
            'name' => 'Gerente Prueba',
            'email' => 'gerencia@test.com',
        ]);
        $gerenciaUser->assignRole('gerencia');

        $coordUser = User::factory()->create([
            'name' => 'Coordinador Prueba',
            'email' => 'coordinador@test.com',
        ]);
        $coordUser->assignRole('coordinacion');

        $auxUser = User::factory()->create([
            'name' => 'Auxiliar Prueba',
            'email' => 'auxiliar@test.com',
        ]);
        $auxUser->assignRole('auxiliar');
    }
}