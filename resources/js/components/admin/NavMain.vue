<script setup lang="ts">
// ─────────────────────────────────────────────────────────────
// NavMain.vue — Grupo de items de navegación del sidebar
// ─────────────────────────────────────────────────────────────
// Este componente recibe un label (título del grupo) y una lista de items.
// Se usa múltiples veces en AppSidebar.vue para crear secciones distintas.
// Ejemplo:
//   <NavMain label="Principal"  :items="[dashboard]" />
//   <NavMain label="Seguridad"  :items="[usuarios, roles]" />
// ─────────────────────────────────────────────────────────────

// Link: componente de Inertia.js que navega entre páginas SIN recargar el navegador.
// Es como un <a href> pero más inteligente: solo actualiza lo que cambió en la página.
import { Link } from '@inertiajs/vue3';

// Componentes de Reka UI que forman la estructura visual del sidebar:
//   SidebarGroup       → contenedor que agrupa varios items (equivale a una <section>)
//   SidebarGroupLabel  → texto de encabezado del grupo ("Principal", "Seguridad", etc.)
//   SidebarMenu        → lista <ul> estilizada de items
//   SidebarMenuButton  → botón clickeable de cada item (maneja hover, activo, icono-solo)
//   SidebarMenuItem    → contenedor <li> de un solo item
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';

// useCurrentUrl: composable que detecta la URL actual del navegador.
// isCurrentUrl('/admin/dashboard') → true si estamos en esa página, false si no.
import { useCurrentUrl } from '@/composables/useCurrentUrl';

// NavItem: tipo TypeScript con la forma de un item del menú.
// Tiene: title (texto visible), href (URL destino), icon (componente Lucide), isActive (opcional).
import type { NavItem } from '@/types';

// defineProps: declara las "entradas" que este componente espera recibir del padre.
// En Vue, props son como parámetros de función: el padre pasa datos, el hijo los usa.
//
//   label?: string    → texto del encabezado del grupo. El ? lo hace opcional.
//                       Si no se pasa, no se muestra el encabezado.
//   items: NavItem[]  → array de items a mostrar en este grupo (obligatorio).
//
// Uso desde el padre: <NavMain label="Seguridad" :items="securityItems" />
defineProps<{
    label?: string;
    items: NavItem[];
}>();

// Extraemos isCurrentUrl del composable para usarlo en el template.
const { isCurrentUrl } = useCurrentUrl();
</script>

<template>
    <!--
        SidebarGroup: contenedor del grupo completo.
        px-2 py-0 → padding horizontal pequeño, sin padding vertical (cada item lo maneja).
    -->
    <SidebarGroup class="px-2 py-0">

        <!--
            v-if="label": solo renderiza el encabezado si se pasó el prop label.
            En Vue, v-if elimina el elemento del DOM si la condición es false (no solo lo oculta).

            SidebarGroupLabel: texto de sección del sidebar.
            Se oculta automáticamente cuando el sidebar está en modo icono (solo muestra íconos).
            Clases:
              text-xs          → texto pequeño (0.75rem)
              font-semibold    → negrita media
              uppercase        → todo en mayúsculas
              tracking-widest  → letras muy separadas (estilo encabezado de sección)
              text-sidebar-foreground/50 → color apagado al 50% de opacidad
              mb-1             → margen inferior pequeño para separar del primer item
        -->
        <SidebarGroupLabel
            v-if="label"
            class="text-xs font-semibold uppercase tracking-widest text-sidebar-foreground/50 mb-1"
        >
            {{ label }}
        </SidebarGroupLabel>

        <!--
            SidebarMenu: lista contenedora de items (<ul>).
            gap-0.5 → espacio de 2px entre cada item.
        -->
        <SidebarMenu class="gap-0.5">

            <!--
                v-for="item in items": bucle de Vue — repite el bloque por cada item del array.
                :key="item.title": clave única para que Vue actualice el DOM eficientemente.
                                   Debe ser único dentro del bucle.
                relative: permite usar position:absolute en los hijos (el indicador izquierdo).
            -->
            <SidebarMenuItem
                v-for="item in items"
                :key="item.title"
                class="relative"
            >
                <!--
                    INDICADOR DE ITEM ACTIVO — Línea de color a la izquierda.
                    v-if="isCurrentUrl(item.href)": solo aparece en el item de la página actual.

                    Clases de posicionamiento y estilo:
                      absolute          → se posiciona respecto al padre (relative)
                      left-0            → pegado al borde izquierdo
                      top-1/2           → a la mitad de la altura del item
                      -translate-y-1/2  → se sube la mitad de su propia altura (centrado exacto)
                      h-5               → altura de la línea (1.25rem)
                      w-[3px]           → ancho fino (3 píxeles)
                      rounded-r-full    → esquinas redondeadas solo en el lado derecho
                      bg-primary        → color teal del tema
                      transition-all duration-300 → animación suave al aparecer
                -->
                <div
                    v-if="isCurrentUrl(item.href)"
                    class="absolute left-0 top-1/2 -translate-y-1/2 h-5 w-[3px] rounded-r-full bg-primary transition-all duration-300"
                />

                <!--
                    SidebarMenuButton: botón visual del item de navegación.

                    as-child         → no renderiza su propio HTML, le pasa estilos/comportamiento
                                       al hijo directo (el componente Link de Inertia).
                    :is-active       → comunica al componente si este item está activo.
                                       Cambia el fondo y color de texto automáticamente.
                    :tooltip         → texto que aparece al hover cuando el sidebar
                                       está colapsado en modo icono (solo muestra íconos).

                    Clases de efectos añadidos:
                      group/btn                           → crea un "grupo" nombrado "btn" para
                                                            que los hijos reaccionen al hover del botón
                      transition-all duration-200         → todas las propiedades animan en 200ms
                      hover:translate-x-0.5               → se mueve 2px a la derecha al hacer hover
                      group-data-[collapsible=icon]:hover:translate-x-0 → cancela el slide cuando
                                                            el sidebar está en modo icono (evita desalineación)
                      data-[active=true]:font-semibold    → texto en negrita cuando está activo
                      rounded-md                          → esquinas redondeadas (4px)
                -->
                <SidebarMenuButton
                    as-child
                    :is-active="isCurrentUrl(item.href)"
                    :tooltip="item.title"
                    class="group/btn transition-all duration-200 hover:translate-x-0.5 group-data-[collapsible=icon]:hover:translate-x-0 data-[active=true]:font-semibold rounded-md"
                >
                    <!--
                        Link de Inertia: navega a item.href sin recargar el navegador.
                        Internamente hace una petición XHR y solo actualiza los componentes
                        que cambiaron, como una SPA (Single Page Application).
                        flex items-center gap-2 → alinea ícono y texto en fila horizontal.
                    -->
                    <Link :href="item.href" class="flex items-center gap-2">

                        <!--
                            component :is="item.icon": renderiza dinámicamente el ícono Lucide
                            que viene como prop. Si item.icon = LayoutGrid, renderiza ese ícono.
                            Si item.icon = Users, renderiza ese otro. Es un componente dinámico.

                            :class con array: Vue acepta un array para combinar clases estáticas
                            con condicionales.

                            Clases base (siempre):
                              size-4              → 1rem × 1rem
                              shrink-0            → no se comprime en espacios pequeños
                              transition-colors duration-200 → el color anima suavemente

                            Clases condicionales (ternario de JavaScript a ? b : c):
                              Si activo:    text-primary (color teal del tema)
                              Si inactivo:  text-sidebar-foreground/60 (apagado al 60%)
                                            + group-hover/btn:text-sidebar-foreground
                                            (se ilumina al hacer hover en el botón padre)
                        -->
                        <component
                            :is="item.icon"
                            :class="[
                                'size-4 shrink-0 transition-colors duration-200',
                                isCurrentUrl(item.href)
                                    ? 'text-primary'
                                    : 'text-sidebar-foreground/60 group-hover/btn:text-sidebar-foreground',
                            ]"
                        />

                        <!--
                            Texto del item de navegación.
                            El ternario cambia la opacidad del texto según si está activo.
                        -->
                        <span
                            :class="[
                                'transition-colors duration-200',
                                isCurrentUrl(item.href)
                                    ? 'text-sidebar-foreground'
                                    : 'text-sidebar-foreground/70 group-hover/btn:text-sidebar-foreground',
                            ]"
                        >
                            {{ item.title }}
                        </span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
