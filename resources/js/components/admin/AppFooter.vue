<template>
    <!--
        mt-auto  → empuja el footer al fondo del flex-col del SidebarInset,
                   incluso cuando el contenido de la página es corto.
        border-t → línea separadora sutil con el token del tema.
        shrink-0 → no se comprime si hay poco espacio vertical.
    -->
    <footer class="mt-auto shrink-0 border-t border-border/50 bg-background/50">
        <div class="flex flex-col items-center justify-between gap-2 px-4 py-3 sm:flex-row sm:px-6">
            <!-- Izquierda: nombre + año -->
            <p class="text-xs text-muted-foreground/70">
                &copy; {{ year }} <span class="font-medium text-muted-foreground">Solución Digital</span>. Todos los derechos reservados.
            </p>
            <!-- Derecha: versión del stack -->
            <p class="text-xs text-muted-foreground/50">
                Laravel · Inertia.js · Vue 3
            </p>
        </div>
    </footer>
</template>

<script setup lang="ts">
const year = new Date().getFullYear();
</script>
