<script setup lang="ts">
// TableHeader.vue — Encabezado <thead> de la tabla
// El estilo más llamativo: fondo degradado + línea de acento teal en la parte inferior.

import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

const props = defineProps<{ class?: HTMLAttributes['class'] }>();
</script>

<template>
    <!--
        Estilo del encabezado:

        bg-muted/80                  → fondo levemente más oscuro que la card
        [&_tr]:border-b-2            → borde inferior de 2px (más grueso que un borde normal)
        [&_tr]:border-primary/30     → color teal al 30% de opacidad — acento sutil pero visible
                                       Crea una línea divisoria que conecta el header con el cuerpo

        El acento teal en el borde inferior:
        - Es lo suficientemente sutil para no distraer
        - Comunica visualmente "aquí empiezan los datos"
        - Refuerza el color primario del tema sin usarlo en exceso
    -->
    <thead
        :class="cn(
            'bg-muted/80 [&_tr]:border-b-2 [&_tr]:border-primary/30',
            props.class
        )"
    >
        <slot />
    </thead>
</template>
