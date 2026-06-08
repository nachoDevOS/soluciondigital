<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

// Este middleware se ejecuta en CADA request de Inertia (navegación SPA y carga inicial).
// Su función es "compartir" datos globales con todas las páginas Vue sin tener que
// pasarlos manualmente desde cada controlador.
class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    // Vista Blade raíz que carga el HTML inicial con el div #app donde monta Vue.
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    // Retorna la versión del manifest de Vite. Inertia la usa para detectar si los
    // assets cambiaron y forzar recarga completa de página automáticamente.
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    // Todo lo que retorne este método estará disponible en TODAS las páginas Vue
    // como props globales, accesibles con: usePage().props
    public function share(Request $request): array
    {
        // Obtenemos el usuario autenticado una sola vez para no hacer múltiples queries.
        // Si no hay sesión activa, $user es null.
        $user = $request->user();

        return [
            // parent::share() incluye el flash de Inertia (errores de validación, etc.)
            ...parent::share($request),

            // Nombre de la app desde config/app.php → usado en <title> y el sidebar.
            'name' => config('app.name'),

            // Bloque auth: datos del usuario logueado disponibles en Vue como $page.props.auth
            'auth' => [
                // El objeto User completo (sin campos hidden como password).
                'user' => $user,

                // NUEVO: array con los nombres de roles del usuario.
                // Ejemplo: ['super-admin'] o ['admin', 'editor']
                // getRoleNames() viene del trait HasRoles de Spatie.
                // Si no hay usuario (guest), retorna array vacío [].
                'roles' => $user ? $user->getRoleNames() : [],

                // NUEVO: array con TODOS los permisos del usuario (directos + heredados por rol).
                // Ejemplo: ['dashboard.view', 'users.view', 'users.edit', ...]
                // getAllPermissions() viene del trait HasRoles de Spatie.
                // pluck('name') convierte la colección de objetos Permission a array de strings.
                // Si no hay usuario (guest), retorna array vacío [].
                'permissions' => $user ? $user->getAllPermissions()->pluck('name') : [],
            ],

            // Estado del sidebar: true = abierto, false = colapsado.
            // Se lee desde cookie para recordar la preferencia del usuario entre visitas.
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
