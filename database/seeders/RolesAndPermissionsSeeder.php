<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

// Este seeder crea todos los roles y permisos del sistema.
// Se llama desde DatabaseSeeder y es seguro correrlo múltiples veces
// (usa firstOrCreate → no duplica registros).
class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Spatie guarda roles y permisos en caché para mejorar rendimiento.
        // Antes de crear/modificar permisos hay que limpiar esa caché,
        // si no, los cambios no se reflejan hasta que expire sola.
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ─────────────────────────────────────────────
        // PERMISOS
        // ─────────────────────────────────────────────
        // Convención de nombres: 'recurso.accion'
        // Esto facilita agruparlos por módulo en la UI si se necesita.
        $permissions = [
            'dashboard.view',   // Ver el panel principal con estadísticas
            'users.view',       // Ver la lista de usuarios
            'users.edit',       // Editar datos de un usuario
            'users.delete',     // Eliminar un usuario
            'settings.view',    // Acceder a la configuración de perfil/seguridad
            'roles.view',       // Ver la lista de roles y sus permisos
            'roles.edit',       // Modificar permisos de un rol (solo super-admin)
        ];

        // firstOrCreate: si el permiso ya existe en DB no lo duplica, si no existe lo crea.
        // Importante para poder correr el seeder múltiples veces sin error.
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ─────────────────────────────────────────────
        // ROL: editor
        // ─────────────────────────────────────────────
        // Rol de solo lectura básica. Solo puede ver el dashboard.
        // Caso de uso: empleado que necesita ver métricas pero sin gestionar nada.
        $editor = Role::firstOrCreate(['name' => 'editor']);
        // syncPermissions reemplaza TODOS los permisos del rol con exactamente estos.
        // A diferencia de givePermissionTo(), syncPermissions garantiza que no queden
        // permisos huérfanos si se corre el seeder de nuevo con permisos distintos.
        $editor->syncPermissions(['dashboard.view']);

        // ─────────────────────────────────────────────
        // ROL: admin
        // ─────────────────────────────────────────────
        // Administrador con acceso a gestión de usuarios pero SIN poder eliminarlos.
        // Caso de uso: gerente que puede ver y editar usuarios pero no borrarlos.
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions([
            'dashboard.view',
            'users.view',
            'users.edit',
            'settings.view',
            'roles.view',       // admin puede VER roles pero no editarlos
            // Nota: 'users.delete' y 'roles.edit' NO están aquí — solo super-admin los tiene.
        ]);

        // ─────────────────────────────────────────────
        // ROL: super-admin
        // ─────────────────────────────────────────────
        // Acceso total. Recibe TODOS los permisos del sistema.
        // Al usar Permission::all() aquí, si en el futuro se agregan nuevos permisos
        // al seeder y se vuelve a correr, super-admin los recibe automáticamente.
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdmin->syncPermissions(Permission::all());
    }
}
