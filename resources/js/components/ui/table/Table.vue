<script setup lang="ts">
// Table.vue — Contenedor principal de la tabla
// El componente raíz que envuelve toda la tabla con la card visual.

import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

const props = defineProps<{
    class?: HTMLAttributes['class'];
    wrapperClass?: HTMLAttributes['class'];
}>();
</script>

<template>
    <!--
        Contenedor card de la tabla.

        rounded-xl                → esquinas redondeadas consistentes con el resto del admin
        border border-border      → borde usando token del tema (adapta light/dark)
        bg-card                   → fondo de card del tema
        overflow-hidden           → las esquinas redondeadas cortan el contenido del thead

        shadow — efecto multicapa para profundidad:
          shadow-sm               → sombra base sutil
          ring-1 ring-border/50   → borde interior semitransparente (efecto de profundidad)
    -->
    <div
        :class="cn(
            'w-full overflow-hidden rounded-xl border border-border bg-card shadow-sm ring-1 ring-border/50',
            wrapperClass
        )"
    >
        <!-- overflow-x-auto: scroll horizontal en pantallas pequeñas sin romper el layout -->
        <div class="overflow-x-auto">
            <table :class="cn('w-full text-sm', props.class)">
                <slot />
            </table>
        </div>
    </div>
</template>
