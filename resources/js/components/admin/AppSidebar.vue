<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { LayoutGrid, Users } from 'lucide-vue-next';
import NavMain from '@/components/admin/NavMain.vue';
import NavUser from '@/components/admin/NavUser.vue';
import AppLogo from '@/components/AppLogo.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
// NUEVO: importamos el composable que creamos para verificar permisos.
import { usePermissions } from '@/composables/usePermissions';
import type { NavItem } from '@/types';

// Extraemos hasPermission del composable.
// hasPermission() lee los permisos del usuario desde $page.props.auth.permissions
// (que fueron enviados por HandleInertiaRequests.php en cada request).
const { hasPermission } = usePermissions();

// NUEVO: mainNavItems ahora es un computed en lugar de un array estático.
// computed() hace que Vue recalcule la lista automáticamente si cambian los permisos
// (por ejemplo, si el usuario cambia de rol en tiempo real vía Inertia).
//
// Antes era: const mainNavItems: NavItem[] = [{ Dashboard }, { Usuarios }]
// Ahora cada item solo se agrega si el usuario tiene el permiso correspondiente.
//
// Resultado: un usuario 'editor' solo ve "Dashboard".
//            un usuario 'admin' ve "Dashboard" y "Usuarios".
//            un usuario 'super-admin' ve todo.
const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [];

    // Solo agrega "Dashboard" al menú si el usuario tiene el permiso 'dashboard.view'.
    // Este permiso lo tienen: editor, admin, super-admin.
    if (hasPermission('dashboard.view')) {
        items.push({ title: 'Dashboard', href: '/admin/dashboard', icon: LayoutGrid });
    }

    // Solo agrega "Usuarios" al menú si el usuario tiene el permiso 'users.view'.
    // Este permiso lo tienen: admin, super-admin. (editor NO lo tiene)
    if (hasPermission('users.view')) {
        items.push({ title: 'Usuarios', href: '/admin/users', icon: Users });
    }

    // Cuando se agreguen nuevas secciones al admin, seguir este mismo patrón:
    // if (hasPermission('nuevo-modulo.view')) {
    //     items.push({ title: 'Nuevo Módulo', href: '/admin/nuevo', icon: IconName });
    // }

    return items;
});
</script>

<template>
    <!-- Sidebar = barra lateral del admin. Tiene header, body y footer. -->
    <Sidebar collapsible="icon" variant="inset">
        <!-- Header del sidebar: logo / acceso al dashboard. -->
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link href="/admin/dashboard">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <!-- Body del sidebar: navegacion principal filtrada por permisos. -->
        <!-- mainNavItems es computed → Vue re-renderiza si cambian los permisos. -->
        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <!-- Footer del sidebar: usuario y menu de cuenta. -->
        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
