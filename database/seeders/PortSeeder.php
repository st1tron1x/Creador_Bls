<?php

namespace Database\Seeders;

use App\Models\Port;
use Illuminate\Database\Seeder;

class PortSeeder extends Seeder
{
    public function run(): void
    {
        $ports = [
            ['code' => 'BVT', 'name' => 'Buenaventura'],
            ['code' => 'CTG', 'name' => 'Cartagena'],
            ['code' => 'BAQ', 'name' => 'Barranquilla'],
            ['code' => 'SMR', 'name' => 'Santa Marta'],
            ['code' => 'TBO', 'name' => 'Turbo'],
        ];

        foreach ($ports as $port) {
            Port::firstOrCreate(
                ['code' => $port['code']],
                ['name' => $port['name'], 'last_bl_number' => 0]
            );
        }
    }
}
