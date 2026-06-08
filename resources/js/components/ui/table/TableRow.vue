<script setup lang="ts">
// TableRow.vue — Fila <tr> del cuerpo de la tabla

import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

const props = defineProps<{
    class?: HTMLAttributes['class'];
    // clickable: true → cursor pointer para filas que llevan a un detalle/vista
    clickable?: boolean;
}>();
</script>

<template>
    <!--
        Efecto hover premium:

        group                      → permite que los hijos (TableCell) reaccionen al hover
                                     del <tr> con "group-hover:..."

        transition-colors duration-150 → animación suave de 150ms

        hover:bg-primary/[0.04]    → fondo teal EXTREMADAMENTE sutil al hacer hover
                                     El valor [0.04] = 4% de opacidad — casi imperceptible
                                     pero suficiente para indicar la fila seleccionada
                                     Sin ser intrusivo como un bg-primary/10 más fuerte

        relative                   → necesario para el pseudo-elemento ::after (línea izquierda)

        Nota sobre la línea izquierda de acento:
        No se puede hacer directamente con Tailwind en <tr> porque las tablas
        HTML no soportan border-left bien con border-collapse.
        En cambio, el primer TableCell puede recibir "group-hover:border-l-2 group-hover:border-primary"
        si se quiere ese efecto. Lo dejamos como opción del padre.
    -->
    <tr
        :class="cn(
            'group relative transition-colors duration-150 hover:bg-primary/[0.04]',
            props.clickable && 'cursor-pointer',
            props.class
        )"
    >
        <slot />
    </tr>
</template>
