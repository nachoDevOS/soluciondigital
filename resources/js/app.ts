import { createInertiaApp } from '@inertiajs/vue3';
import { initializeTheme } from '@/composables/useAppearance';
import AdminLayout from '@/layouts/AppLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { initializeFlashToast } from '@/lib/flashToast';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    layout: (name) => {
        switch (true) {
            // Sitio público: landing + páginas bajo public/
            case name === 'Welcome':
            case name.startsWith('public/'):
                return PublicLayout;

            // Autenticación: login, registro, etc.
            case name.startsWith('auth/'):
                return AuthLayout;

            // Configuración de usuario: layout anidado
            case name.startsWith('settings/'):
                return [AdminLayout, SettingsLayout];

            // Panel de administración (admin/) y cualquier otra ruta autenticada
            default:
                return AdminLayout;
        }
    },
    progress: {
        color: '#4B5563',
    },
});

// Inicializa light / dark mode antes de que monte la app
initializeTheme();

// Escucha flash data del servidor para mostrar toasts
initializeFlashToast();
