<script setup lang="ts">
// TableBody.vue — Envuelve el <tbody> HTML
// divide-y divide-border → línea separadora entre filas usando el token de borde del tema.

import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';

const props = defineProps<{ class?: HTMLAttributes['class'] }>();
</script>

<template>
    <!--
        divide-y divide-border → Tailwind pone border-top en cada fila excepto la primera.
        Usa --border del tema → adapta automáticamente a light/dark.
    -->
    <tbody :class="cn('divide-y divide-border', props.class)">
        <slot />
    </tbody>
</template>
