<script setup lang="ts">
// ─────────────────────────────────────────────────────────────
// AppSidebarHeader.vue — Barra superior del panel de administración
// ─────────────────────────────────────────────────────────────
// Este componente NO es el header dentro del sidebar.
// Es la barra horizontal que aparece ARRIBA DEL CONTENIDO PRINCIPAL,
// al lado derecho del sidebar. Contiene:
//   1. El botón hamburguesa (SidebarTrigger) para abrir/cerrar el sidebar
//   2. Las migas de pan (breadcrumbs) que muestran en qué sección estás
// ─────────────────────────────────────────────────────────────

// Breadcrumbs: componente que muestra la ruta de navegación actual.
// Ejemplo: "Dashboard > Usuarios > Juan Pérez"
import Breadcrumbs from '@/components/admin/Breadcrumbs.vue';

// SidebarTrigger: botón hamburguesa que abre/cierra el sidebar.
// Al hacer click llama a toggleSidebar() del contexto de Reka UI.
import { SidebarTrigger } from '@/components/ui/sidebar';

// BreadcrumbItem: tipo TypeScript → { title: string, href: string }
import type { BreadcrumbItem } from '@/types';
import { useAppearance } from '@/composables/useAppearance';
import { Moon, Sun } from 'lucide-vue-next';
import { computed } from 'vue';

const { appearance, updateAppearance } = useAppearance();
const isDark = computed(() =>
    appearance.value === 'dark' ||
    (appearance.value === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches),
);
function toggleTheme() {
    updateAppearance(isDark.value ? 'light' : 'dark');
}

// withDefaults + defineProps: declara los props que recibe este componente
// con valores por defecto si el padre no los pasa.
//
// breadcrumbs?: BreadcrumbItem[] → array opcional de migas de pan.
// Si el padre no pasa breadcrumbs, el default es un array vacío [].
// El padre lo usa así: <AppSidebarHeader :breadcrumbs="[{title:'Dashboard',href:'/admin/dashboard'}]" />
withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItem[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);
</script>

<template>
    <!--
        <header>: etiqueta HTML semántica para cabeceras de sección.

        Clases explicadas:
          flex                  → layout en fila (flexbox)
          h-16                  → altura fija de 4rem (64px)
          shrink-0              → no se comprime aunque no haya espacio vertical
          items-center          → centra los hijos verticalmente dentro del header
          gap-2                 → espacio de 0.5rem entre hijos
          px-6 md:px-4          → padding horizontal: 1.5rem en móvil, 1rem en desktop
          transition-[width,height] ease-linear → anima suavemente cuando el sidebar
                                    se colapsa y el header se hace más pequeño
          group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 → cuando el sidebar
                                    está en modo icono, el header se reduce a h-12 (3rem).
                                    Esto es magia de Tailwind + data attributes de Reka UI:
                                    el sidebar-wrapper (elemento padre) tiene un data-attribute
                                    que Tailwind puede leer para cambiar estilos de hijos.

        Visual añadido:
          border-b border-sidebar-border/70 → línea separadora sutil en la parte inferior
          bg-background/80                  → fondo semi-transparente (efecto frosted glass ligero)
          backdrop-blur-sm                  → desenfoque de lo que hay detrás (frosted glass)
    -->
    <header
        class="flex h-16 shrink-0 items-center justify-between gap-2 border-b border-sidebar-border/70 bg-background/80 backdrop-blur-sm px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
    >
        <!--
            Contenedor flex de los elementos de la izquierda: trigger + breadcrumbs.
            items-center → alineación vertical centrada.
            gap-2        → espacio entre el trigger y los breadcrumbs.
        -->
        <!-- min-w-0: permite que flex-children se compriman y no desborden en pantallas pequeñas -->
        <div class="flex min-w-0 items-center gap-2">

            <SidebarTrigger
                class="-ml-1 shrink-0 hover:bg-sidebar-accent/60 transition-colors duration-200 rounded-md"
            />

            <!--
                hidden sm:flex: en móvil (< 640px) se ocultan los breadcrumbs para
                no aplastar el header. En sm+ vuelven a aparecer.
                min-w-0: permite que el contenedor flex se comprima si no hay espacio.
            -->
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <div class="hidden sm:flex items-center gap-2 min-w-0">
                    <div class="h-4 w-px shrink-0 bg-sidebar-border/50" />
                    <!-- overflow-hidden min-w-0: el breadcrumb trunca si el texto es muy largo -->
                    <div class="min-w-0 overflow-hidden">
                        <Breadcrumbs :breadcrumbs="breadcrumbs" />
                    </div>
                </div>
            </template>
        </div>

        <button
            type="button"
            :title="isDark ? 'Cambiar a claro' : 'Cambiar a oscuro'"
            class="inline-flex items-center justify-center w-8 h-8 rounded-full border border-sidebar-border/50 bg-sidebar-accent/30 text-muted-foreground hover:bg-sidebar-accent hover:text-foreground transition-colors duration-200"
            @click="toggleTheme"
        >
            <Sun v-if="isDark" class="w-4 h-4" />
            <Moon v-else class="w-4 h-4" />
        </button>
    </header>
</template>
