<script setup lang="ts">
// TableCell.vue — Celda <td> del cuerpo de la tabla
// Padding y tipografía estándar. Color text-foreground por defecto.
// Para celdas secundarias (email, fecha) usa class="text-muted-foreground" desde el padre.

import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

const props = defineProps<{ class?: HTMLAttributes['class'] }>();
</script>

<template>
    <!--
        px-5 py-4           → mismo padding horizontal que TableHead para alineación perfecta
        text-sm             → tamaño de fuente estándar para contenido de tabla
        text-foreground     → color principal del tema (negro en light, blanco en dark)
        align-middle        → alineación vertical centrada
        whitespace-nowrap   → el contenido no se parte (override si necesitas wrap)
    -->
    <td :class="cn('px-3 py-3 sm:px-5 sm:py-4 text-sm text-foreground align-middle', props.class)">
        <slot />
    </td>
</template>
