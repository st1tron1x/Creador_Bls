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
            'bl.create',        // crear / editar BL en borrador
            'bl.issue',         // emitir BL (genera número + PDF)
            'bl.view',          // ver / buscar / descargar BL (abierto a todos los roles)
            'bl.void.request',  // solicitar anulación (abierto a todos, incluido auxiliar)
            'bl.void.approve',  // aprobar/rechazar una solicitud de anulación
            'users.manage',
            'reports.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions($permissions); // todos los permisos, por encima de gerencia

        $gerencia = Role::firstOrCreate(['name' => 'gerencia']);
        $gerencia->syncPermissions([
            'bl.create', 'bl.issue', 'bl.view', 'bl.void.request', 'bl.void.approve',
            'users.manage', 'reports.view',
        ]);

        $coordinacion = Role::firstOrCreate(['name' => 'coordinacion']);
        $coordinacion->syncPermissions([
            'bl.create', 'bl.issue', 'bl.view', 'bl.void.request', 'bl.void.approve', 'reports.view',
        ]);

        $auxiliar = Role::firstOrCreate(['name' => 'auxiliar']);
        $auxiliar->syncPermissions([
            'bl.create', 'bl.issue', 'bl.view', 'bl.void.request',
        ]);
    }
}
