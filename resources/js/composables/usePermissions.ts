import { usePage } from '@inertiajs/vue3';
import type { Auth } from '@/types';

// Composable que expone helpers para verificar roles y permisos del usuario logueado
// desde cualquier componente Vue, sin tener que acceder manualmente a $page.props.auth.
//
// Uso:
//   const { hasPermission, hasRole } = usePermissions()
//   if (hasPermission('users.edit')) { ... }
//   if (hasRole('super-admin')) { ... }
export function usePermissions() {
    // usePage() devuelve el objeto global de Inertia con todos los props compartidos.
    // Le pasamos el tipo genérico { auth: Auth } para que TypeScript sepa la forma
    // de los props y nos dé autocompletado y validación de tipos.
    const page = usePage<{ auth: Auth }>();

    // Verifica si el usuario tiene UN permiso específico.
    // Busca el string exacto dentro del array permissions que viene del backend.
    // Ejemplo: hasPermission('users.edit') → true / false
    const hasPermission = (permission: string): boolean => {
        return page.props.auth.permissions.includes(permission);
    };

    // Verifica si el usuario tiene UN rol específico.
    // Ejemplo: hasRole('super-admin') → true / false
    // NOTA: para permisos de UI se recomienda usar hasPermission() en lugar de hasRole(),
    // porque los permisos son más granulares y no cambian si se renombra un rol.
    const hasRole = (role: string): boolean => {
        return page.props.auth.roles.includes(role);
    };

    // Verifica si el usuario tiene AL MENOS UNO de los permisos del array.
    // Útil cuando un elemento de UI requiere cualquiera de varios permisos.
    // Ejemplo: hasAnyPermission(['users.edit', 'users.delete']) → true si tiene alguno
    const hasAnyPermission = (permissions: string[]): boolean => {
        return permissions.some((p) => hasPermission(p));
    };

    // Verifica si el usuario tiene AL MENOS UNO de los roles del array.
    // Ejemplo: hasAnyRole(['admin', 'super-admin']) → true si tiene cualquiera de los dos
    const hasAnyRole = (roles: string[]): boolean => {
        return roles.some((r) => hasRole(r));
    };

    return { hasPermission, hasRole, hasAnyPermission, hasAnyRole };
}
