import type { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

// Usuario y autenticación
export type User = {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    [key: string]: unknown;
};

// Auth es el objeto que llega en $page.props.auth desde HandleInertiaRequests.php.
// Está disponible en cualquier componente Vue con: usePage().props.auth
export type Auth = {
    // El usuario logueado. Coincide con los campos del modelo User de Laravel
    // (excluyendo los campos hidden: password, 2FA secrets, remember_token).
    user: User;

    // NUEVO: array de nombres de roles del usuario.
    // Ejemplo: ['super-admin'] o ['admin'] o ['editor']
    // Viene de $user->getRoleNames() en el middleware.
    // Array vacío [] si no hay usuario logueado.
    roles: string[];

    // NUEVO: array con TODOS los permisos del usuario (directos + heredados del rol).
    // Ejemplo: ['dashboard.view', 'users.view', 'users.edit', 'settings.view']
    // Viene de $user->getAllPermissions()->pluck('name') en el middleware.
    // Array vacío [] si no hay usuario logueado.
    permissions: string[];
};

export type TwoFactorConfigContent = {
    title: string;
    description: string;
    buttonText: string;
};

// Navegación (sidebar y breadcrumbs)
export type BreadcrumbItem = {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
};

export type NavItem = {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
};

// UI y apariencia
export type Appearance = 'light' | 'dark' | 'system';
export type ResolvedAppearance = 'light' | 'dark';
export type AppVariant = 'header' | 'sidebar';

export type FlashToast = {
    type: 'success' | 'info' | 'warning' | 'error';
    message: string;
};
