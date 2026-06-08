<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // NUEVO: primero creamos roles y permisos.
        // Debe ir ANTES de crear usuarios porque luego asignamos rol al admin.
        // Si los roles no existen al hacer syncRoles(), Spatie lanzaría un error.
        $this->call(RolesAndPermissionsSeeder::class);

        // updateOrCreate busca un usuario por email:
        //   - Si existe → actualiza los campos del segundo array.
        //   - Si no existe → crea un usuario nuevo con ambos arrays combinados.
        // Esto hace que el seeder sea idempotente (seguro de correr múltiples veces).
        $admin = User::updateOrCreate(
            // Condición de búsqueda: buscar por este email.
            ['email' => 'admin@admin.com'],
            // Datos a crear o actualizar.
            [
                'name' => 'Administrador',
                // 'password' se hashea automáticamente por el cast 'hashed' del modelo.
                'password' => 'password',
                // email_verified_at con valor = email ya verificado, puede entrar al panel.
                // Sin este campo, Fortify redirige a /email/verify en cada login.
                'email_verified_at' => now(),
            ],
        );

        // NUEVO: asignamos el rol 'super-admin' al usuario administrador.
        // syncRoles() reemplaza TODOS los roles actuales por exactamente los que se pasan.
        // Diferencia con assignRole(): assignRole() agrega, syncRoles() reemplaza.
        // Usamos syncRoles() para garantizar que al re-seedear no acumule roles duplicados.
        $admin->syncRoles('super-admin');
    }
}
