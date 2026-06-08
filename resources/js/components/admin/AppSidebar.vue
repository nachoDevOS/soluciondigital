<script setup lang="ts">
// ─────────────────────────────────────────────────────────────
// AppSidebar.vue — Sidebar principal del panel de administración
// ─────────────────────────────────────────────────────────────
// Estructura visual del sidebar:
//
//   ┌─────────────────────┐
//   │  [Logo]  App Name   │  ← SidebarHeader
//   ├─────────────────────┤
//   │  PRINCIPAL          │
//   │  □ Dashboard        │  ← NavMain (grupo "Principal")
//   │                     │
//   │  SEGURIDAD          │
//   │  👥 Usuarios        │  ← NavMain (grupo "Seguridad")
//   │  🔑 Roles           │
//   ├─────────────────────┤
//   │  [Avatar] Usuario ▾ │  ← SidebarFooter (NavUser)
//   └─────────────────────┘
// ─────────────────────────────────────────────────────────────

// computed: función reactiva de Vue. Se re-ejecuta automáticamente cuando cambian
// sus dependencias. Ideal para listas filtradas que dependen del estado del usuario.
import { computed } from 'vue';

// Link: componente de Inertia que navega entre páginas sin recargar el navegador.
import { Link } from '@inertiajs/vue3';

// Íconos de Lucide Vue. Cada importación es un componente de ícono SVG.
import { LayoutGrid, ShieldCheck, Users } from 'lucide-vue-next';

// Componentes del sidebar.
import NavMain from '@/components/admin/NavMain.vue';
import NavUser from '@/components/admin/NavUser.vue';
import AppLogo from '@/components/AppLogo.vue';

// Componentes de Reka UI para la estructura del sidebar.
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';

// Composable de permisos (creado al integrar Spatie Permission).
// hasPermission('permiso') → true si el usuario logueado tiene ese permiso.
import { usePermissions } from '@/composables/usePermissions';

// NavItem: tipo TypeScript → { title, href, icon, isActive? }
import type { NavItem } from '@/types';

const { hasPermission } = usePermissions();

// ─────────────────────────────────────────────
// GRUPO "PRINCIPAL" — items del área principal del panel
// ─────────────────────────────────────────────
// computed() recalcula la lista automáticamente si cambian los permisos del usuario.
const mainNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [];

    // Dashboard: accesible para editor, admin y super-admin.
    if (hasPermission('dashboard.view')) {
        items.push({
            title: 'Dashboard',
            href: '/admin/dashboard',
            icon: LayoutGrid,
        });
    }

    return items;
});

// ─────────────────────────────────────────────
// GRUPO "SEGURIDAD" — gestión de usuarios y roles
// ─────────────────────────────────────────────
// Este grupo solo aparece si el usuario tiene al menos uno de estos permisos.
// Un 'editor' no tiene ninguno → no ve la sección Seguridad en el sidebar.
const securityNavItems = computed<NavItem[]>(() => {
    const items: NavItem[] = [];

    // Usuarios: accesible para admin y super-admin.
    if (hasPermission('users.view')) {
        items.push({
            title: 'Usuarios',
            href: '/admin/users',
            icon: Users,
        });
    }

    // Roles: accesible para admin y super-admin.
    if (hasPermission('roles.view')) {
        items.push({
            title: 'Roles',
            href: '/admin/roles',
            icon: ShieldCheck,   // ícono de escudo con check → representa seguridad/roles
        });
    }

    return items;
});

// Computed que indica si el grupo "Seguridad" tiene al menos un item visible.
// Lo usamos para no mostrar la sección vacía si el usuario no tiene ningún permiso.
// .length > 0 → true si hay items, false si el array está vacío.
const hasSecurityItems = computed(() => securityNavItems.value.length > 0);
</script>

<template>
    <!--
        Sidebar: componente raíz de Reka UI.

        collapsible="icon" → al colapsarse (click en hamburguesa), el sidebar
                             se reduce a mostrar SOLO íconos sin texto.
                             El tooltip del item muestra el nombre al hacer hover.
        variant="inset"    → el sidebar queda "encajado" en el layout con bordes
                             redondeados y un offset visual (no pegado al borde).
    -->
    <Sidebar collapsible="icon" variant="inset">

        <!--
            ─── HEADER: Logo + nombre de la app ──────────────
            relative overflow-hidden → necesario para contener el gradiente decorativo.
            border-b border-sidebar-border/30 → separador sutil debajo del header.
        -->
        <SidebarHeader class="relative overflow-hidden border-b border-sidebar-border/30">

            <!--
                Gradiente decorativo de fondo. Solo es visual.
                absolute inset-0   → cubre todo el header
                bg-gradient-to-br  → degradado diagonal (arriba-izquierda → abajo-derecha)
                from-primary/10    → empieza con el color teal al 10% de opacidad
                to-transparent     → termina transparente
                pointer-events-none → no intercepta clics del usuario
            -->
            <div class="absolute inset-0 bg-gradient-to-br from-primary/10 to-transparent pointer-events-none" />

            <SidebarMenu class="pb-1">
                <SidebarMenuItem>
                    <!--
                        size="lg" → botón más alto que el default.
                        as-child  → le pasa sus estilos al hijo (Link) sin agregar un HTML extra.
                    -->
                    <SidebarMenuButton
                        size="lg"
                        as-child
                        class="hover:bg-sidebar-accent/50 transition-colors duration-200"
                    >
                        <Link href="/admin/dashboard">
                            <!-- AppLogo se encoge al ícono cuando el sidebar está colapsado. -->
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <!--
            ─── CONTENIDO: Secciones de navegación ───────────
            pt-2 → padding superior para separar del header.
            gap-1 → espacio entre grupos de navegación.
        -->
        <SidebarContent class="pt-2 gap-1">

            <!--
                GRUPO "PRINCIPAL": solo contiene Dashboard por ahora.
                :items="mainNavItems" pasa el computed como prop.
                El : antes de items indica binding dinámico (Vue evalúa la variable, no el string).
            -->
            <NavMain
                label="Principal"
                :items="mainNavItems"
            />

            <!--
                GRUPO "SEGURIDAD": Usuarios + Roles.
                v-if="hasSecurityItems" → solo renderiza este grupo si hay al menos un item visible.
                Evita mostrar un encabezado "SEGURIDAD" vacío para usuarios con pocos permisos.
            -->
            <NavMain
                v-if="hasSecurityItems"
                label="Seguridad"
                :items="securityNavItems"
            />

        </SidebarContent>

        <!--
            ─── FOOTER: Usuario logueado ──────────────────────
            border-t border-sidebar-border/30 → línea separadora encima del footer.
            pt-2 → espacio entre la línea y el contenido.
        -->
        <SidebarFooter class="border-t border-sidebar-border/30 pt-2">
            <!--
                NavUser: muestra avatar + nombre del usuario.
                Al hacer click abre un dropdown con: Perfil, Ajustes, Cerrar sesión.
                Lee el usuario desde $page.props.auth.user (compartido por HandleInertiaRequests).
            -->
            <NavUser />
        </SidebarFooter>
    </Sidebar>

    <!--
        <slot />: espacio donde el componente padre inyecta el contenido de la página.
        En Vue, slot es un "hueco" que el padre rellena.
        AppLayout.vue envuelve el contenido de cada página dentro de <AppSidebar>,
        y ese contenido aparece renderizado aquí.
    -->
    <slot />
</template>
