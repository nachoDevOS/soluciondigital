<script setup lang="ts">
// TableEmpty.vue — Fila de estado vacío
// Se usa cuando el array de datos está vacío.
// Recibe "colspan" para que la celda ocupe todas las columnas de la tabla.
// Recibe "message" como texto personalizable.

import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

const props = defineProps<{
    class?: HTMLAttributes['class'];
    // colspan: número de columnas que ocupa la celda vacía.
    // Debe coincidir con la cantidad de <TableHead> que tiene la tabla.
    colspan?: number;
    // message: texto del estado vacío. Se puede sobreescribir con <slot>.
    message?: string;
}>();
</script>

<template>
    <tr>
        <!--
            :colspan="colspan ?? 99" → ocupa todas las columnas.
            Si no se pasa colspan, usa 99 como fallback (más que suficiente).
            py-14 → bastante espacio vertical para que no se vea apretado.
            text-center text-muted-foreground → texto centrado y apagado.
        -->
        <td
            :colspan="colspan ?? 99"
            :class="cn('px-5 py-14 text-center text-sm text-muted-foreground', props.class)"
        >
            <!--
                <slot> permite personalizar el contenido desde el padre:
                <TableEmpty :colspan="4">
                    <MyIcon /> No se encontraron resultados.
                </TableEmpty>
                Si no hay slot, muestra el prop message o un texto por defecto.
            -->
            <slot>{{ message ?? 'No hay registros.' }}</slot>
        </td>
    </tr>
</template>
