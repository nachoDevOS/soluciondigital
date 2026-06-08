<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

const {
    stats = { totalUsers: 0, newUsersThisMonth: 0, activeSessions: 0 },
} = defineProps<{
    stats?: {
        totalUsers: number;
        newUsersThisMonth: number;
        activeSessions: number;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Dashboard', href: '/admin/dashboard' }],
    },
});

const statCards = [
    {
        label: 'Total usuarios',
        value: stats.totalUsers,
        icon: '👥',
        description: 'usuarios registrados',
    },
    {
        label: 'Nuevos este mes',
        value: stats.newUsersThisMonth,
        icon: '📈',
        description: 'en los últimos 30 días',
    },
    {
        label: 'Sesiones activas',
        value: stats.activeSessions,
        icon: '🌐',
        description: 'sesiones en este momento',
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex flex-col gap-6 p-4 sm:p-6">
        <!-- Encabezado — usa tokens del tema, sin hardcodear gray-* -->
        <div>
            <!-- text-foreground: adapta automáticamente a claro/oscuro via CSS variables -->
            <h1 class="text-2xl font-bold text-foreground">Dashboard</h1>
            <!-- text-muted-foreground: texto secundario apagado en ambos modos -->
            <p class="mt-1 text-sm text-muted-foreground">
                Resumen general del sistema.
            </p>
        </div>

        <!-- Cards de estadísticas -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="stat in statCards"
                :key="stat.label"
                class="flex flex-col gap-2 rounded-xl border border-border bg-card p-6 shadow-sm"
            >
                <div class="flex items-start justify-between">
                    <!-- text-muted-foreground: etiqueta apagada -->
                    <p class="text-sm font-medium text-muted-foreground">
                        {{ stat.label }}
                    </p>
                    <span class="text-2xl">{{ stat.icon }}</span>
                </div>
                <!-- text-foreground: número principal con máximo contraste -->
                <p class="text-3xl font-bold text-foreground">
                    {{ stat.value }}
                </p>
                <!-- text-muted-foreground/70: descripción muy apagada -->
                <p class="text-xs text-muted-foreground/70">
                    {{ stat.description }}
                </p>
            </div>
        </div>

        <!-- Panel de actividad reciente -->
        <div class="rounded-xl border border-border bg-card shadow-sm">
            <div class="border-b border-border px-6 py-4">
                <h2 class="font-semibold text-foreground">Actividad reciente</h2>
            </div>
            <div class="px-6 py-10 text-center">
                <p class="text-sm text-muted-foreground">
                    Agrega aquí gráficos, tablas de actividad reciente u otro contenido relevante.
                </p>
            </div>
        </div>
    </div>
</template>
