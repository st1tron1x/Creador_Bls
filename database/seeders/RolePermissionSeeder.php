<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'bl.create',   // crear / editar BL en borrador
            'bl.issue',    // emitir BL (genera número + PDF)
            'bl.void',     // anular un BL emitido
            'bl.view',     // ver / buscar BL
            'users.manage',
            'reports.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $gerencia = Role::firstOrCreate(['name' => 'gerencia']);
        $gerencia->syncPermissions($permissions); // todos los permisos

        $coordinacion = Role::firstOrCreate(['name' => 'coordinacion']);
        $coordinacion->syncPermissions([
            'bl.create', 'bl.issue', 'bl.void', 'bl.view', 'reports.view',
        ]);

        $auxiliar = Role::firstOrCreate(['name' => 'auxiliar']);
        $auxiliar->syncPermissions([
            'bl.create', 'bl.issue', 'bl.view',
        ]);
    }
}
