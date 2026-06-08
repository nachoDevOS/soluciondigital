<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Muestra la lista de roles con sus permisos y conteo de usuarios.
     *
     * Inertia::render() es como return view() de Laravel normal, pero en lugar de
     * renderizar Blade, le pasa los datos a un componente Vue como "props".
     * La página Vue recibe $roles como defineProps({ roles: [...] }).
     */
    public function index(): Response
    {
        // Role::with('permissions') → carga cada rol junto con sus permisos en una sola query
        // (eager loading). Sin 'with', haría una query extra por cada rol (problema N+1).
        // ->get() → ejecuta la query y retorna una colección de objetos Role.
        $roles = Role::with('permissions')->get();

        // ->map() transforma cada objeto Role en un array simple con solo los datos
        // que necesita el frontend. Enviamos solo lo necesario, no el objeto Eloquent completo.
        $rolesData = $roles->map(function (Role $role) {
            return [
                // ID del rol en la base de datos.
                'id' => $role->id,

                // Nombre del rol (ej: 'super-admin', 'admin', 'editor').
                'name' => $role->name,

                // ->pluck('name') convierte la colección de objetos Permission a un array
                // de strings con solo los nombres. Ej: ['dashboard.view', 'users.view']
                'permissions' => $role->permissions->pluck('name')->sort()->values(),

                // Contamos cuántos usuarios tienen este rol.
                // User::role($role->name) es un scope de Spatie que filtra por rol.
                // ->count() retorna el número como entero.
                'users_count' => User::role($role->name)->count(),
            ];
        });

        // Renderiza el componente Vue en resources/js/pages/admin/roles/Index.vue
        // y le pasa $rolesData como prop 'roles'.
        return Inertia::render('admin/roles/Index', [
            'roles' => $rolesData,
        ]);
    }
}
