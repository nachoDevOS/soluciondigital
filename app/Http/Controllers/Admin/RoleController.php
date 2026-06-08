<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Muestra la lista de roles con sus permisos y conteo de usuarios.
     * También pasa todos los permisos del sistema para los formularios de crear/editar.
     */
    public function index(): Response
    {
        // Carga cada rol con sus permisos en una sola query (eager loading).
        // Sin 'with', haría una query extra por cada rol (problema N+1 de rendimiento).
        $roles = Role::with('permissions')->get();

        $rolesData = $roles->map(function (Role $role) {
            return [
                'id'          => $role->id,
                'name'        => $role->name,
                // pluck('name') convierte colección de objetos Permission a array de strings.
                // sort() ordena alfabéticamente. values() resetea los índices del array.
                'permissions' => $role->permissions->pluck('name')->sort()->values(),
                // User::role() es un scope de Spatie que filtra usuarios por nombre de rol.
                'users_count' => User::role($role->name)->count(),
            ];
        });

        // Pasamos TODOS los permisos del sistema para mostrarlos como checkboxes en los modales.
        // sort() ordena alfabéticamente para que aparezcan en orden consistente.
        $allPermissions = Permission::orderBy('name')->pluck('name');

        return Inertia::render('admin/roles/Index', [
            'roles'          => $rolesData,
            'allPermissions' => $allPermissions,
        ]);
    }

    /**
     * Crea un nuevo rol con los permisos seleccionados.
     *
     * Recibe desde el formulario Vue: { name: string, permissions: string[] }
     */
    public function store(Request $request): RedirectResponse
    {
        // validate() comprueba los datos del request y lanza una excepción si fallan.
        // Inertia captura esa excepción y la devuelve como errores al formulario Vue.
        $validated = $request->validate([
            // name: obligatorio, solo letras minúsculas, números y guiones (formato kebab-case).
            // unique:roles,name → no puede existir ya en la tabla roles.
            'name'          => ['required', 'string', 'regex:/^[a-z0-9\-]+$/', 'max:50', 'unique:roles,name'],
            // permissions: array opcional (un rol puede crearse sin permisos).
            'permissions'   => ['array'],
            // Cada elemento del array debe ser un nombre de permiso existente en la BD.
            'permissions.*' => ['string', 'exists:permissions,name'],
        ], [
            // Mensajes de error personalizados en español.
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.regex'    => 'Solo minúsculas, números y guiones (ej: mi-rol).',
            'name.unique'   => 'Ya existe un rol con ese nombre.',
            'name.max'      => 'El nombre no puede superar 50 caracteres.',
        ]);

        // Crea el rol en la tabla 'roles' de la base de datos.
        $role = Role::create(['name' => $validated['name']]);

        // syncPermissions() asigna exactamente los permisos seleccionados.
        // Si no se mandaron permisos, el array vacío [] limpia los permisos del rol.
        $role->syncPermissions($validated['permissions'] ?? []);

        // Redirige de vuelta a la lista de roles con un toast de éxito.
        // with('toast') es leído por el composable flashToast.ts en el frontend.
        return redirect()->route('admin.roles')
            ->with('toast', ['type' => 'success', 'message' => "Rol '{$role->name}' creado correctamente."]);
    }

    /**
     * Actualiza los permisos de un rol existente.
     * No se permite cambiar el nombre del rol (podría romper lógica que depende del nombre).
     *
     * Recibe desde el formulario Vue: { permissions: string[] }
     * {role} en la URL es el ID del rol → Laravel lo resuelve automáticamente (Route Model Binding).
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'permissions'   => ['array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        // syncPermissions reemplaza TODOS los permisos del rol por los nuevos seleccionados.
        // A diferencia de givePermissionTo(), syncPermissions es idempotente:
        // puedes llamarlo varias veces con el mismo resultado.
        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('admin.roles')
            ->with('toast', ['type' => 'success', 'message' => "Permisos del rol '{$role->name}' actualizados."]);
    }
}
