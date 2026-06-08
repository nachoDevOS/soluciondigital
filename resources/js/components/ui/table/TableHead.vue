<script setup lang="ts">
// TableHead.vue — Celda de encabezado <th>

import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

const props = defineProps<{ class?: HTMLAttributes['class'] }>();
</script>

<template>
    <!--
        px-5 py-3.5           → padding consistente (mismo horizontal que TableCell)
        text-left             → alineación izquierda por defecto
        text-xs               → fuente pequeña para labels de columna
        font-semibold         → negrita media
        uppercase             → mayúsculas (estilo profesional de tabla)
        tracking-widest       → letras muy separadas — mejora legibilidad en uppercase
        text-muted-foreground → color apagado del tema
        whitespace-nowrap     → no rompe en dos líneas
        select-none           → no se puede seleccionar el texto del encabezado accidentalmente
    -->
    <th
        :class="cn(
            'px-3 py-2.5 sm:px-5 sm:py-3.5 text-left text-xs font-semibold uppercase tracking-widest text-muted-foreground whitespace-nowrap select-none',
            props.class
        )"
    >
        <slot />
    </th>
</template>
