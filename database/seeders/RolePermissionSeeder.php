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
            'bl.issue',         // emitir BL (genera número)
            'bl.void',          // (heredado) anular un BL emitido
            'bl.void.request',  // solicitar anulación
            'bl.void.approve',  // aprobar / rechazar anulaciones (además aplica la jerarquía de config/role_ranks.php)
            'bl.view',          // ver / buscar BL
            'users.manage',     // administrar usuarios
            'reports.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        Role::firstOrCreate(['name' => 'admin'])->syncPermissions($permissions);
        Role::firstOrCreate(['name' => 'gerencia'])->syncPermissions($permissions);

        Role::firstOrCreate(['name' => 'coordinacion'])->syncPermissions([
            'bl.create', 'bl.issue', 'bl.void', 'bl.void.request', 'bl.void.approve', 'bl.view', 'reports.view',
        ]);

        // El auxiliar puede solicitar anulación pero nunca aprobarla.
        Role::firstOrCreate(['name' => 'auxiliar'])->syncPermissions([
            'bl.create', 'bl.issue', 'bl.void.request', 'bl.view',
        ]);
    }
}
