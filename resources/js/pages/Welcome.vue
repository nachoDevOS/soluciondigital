<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

const { canRegister } = defineProps<{
    canRegister: boolean;
}>();

const features = [
    {
        icon: '🔐',
        title: 'Autenticación completa',
        description:
            'Login, registro, 2FA, recuperación y verificación de email incluidos vía Laravel Fortify.',
    },
    {
        icon: '⚙️',
        title: 'Panel de administración',
        description:
            'Dashboard, gestión de usuarios y configuración de perfil listos para usar.',
    },
    {
        icon: '⚡',
        title: 'Vue 3 + Inertia.js',
        description: 'SPA sin necesidad de API REST. Las rutas las maneja Laravel de forma nativa.',
    },
    {
        icon: '🎨',
        title: 'Tailwind CSS v4',
        description: 'Estilos utility-first con modo oscuro configurado desde el primer día.',
    },
];
</script>

<template>
    <Head title="Inicio" />

    <!-- Hero -->
    <section class="flex flex-col items-center justify-center px-4 py-24 text-center sm:py-36">
        <div class="mx-auto max-w-3xl">
            <span class="mb-6 inline-flex items-center rounded-full border border-gray-200 bg-white px-3 py-1 text-xs font-medium text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                Laravel + Vue 3 + Inertia.js
            </span>
            <h1 class="mt-4 text-5xl font-bold tracking-tight text-gray-900 sm:text-6xl dark:text-white">
                Plantilla lista para<br />
                <span class="text-gray-500">producción</span>
            </h1>
            <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-400">
                Starter kit completo con sitio público y panel de administración. Sin componentes de
                terceros, solo Vue puro con Tailwind.
            </p>
            <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                <Link
                    v-if="canRegister"
                    href="/register"
                    class="rounded-xl bg-gray-900 px-6 py-3 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-gray-700 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100"
                >
                    Crear cuenta
                </Link>
                <Link
                    href="/login"
                    class="rounded-xl border border-gray-300 px-6 py-3 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
                >
                    Iniciar sesión →
                </Link>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="border-t border-gray-200 bg-gray-50 px-4 py-20 dark:border-gray-800 dark:bg-gray-900">
        <div class="mx-auto max-w-7xl">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
                    Todo lo que necesitas para empezar
                </h2>
                <p class="mt-4 text-gray-600 dark:text-gray-400">
                    Una base sólida para tu próximo proyecto web.
                </p>
            </div>
            <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="feature in features"
                    :key="feature.title"
                    class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800"
                >
                    <div class="text-4xl">{{ feature.icon }}</div>
                    <h3 class="mt-4 text-base font-semibold text-gray-900 dark:text-white">
                        {{ feature.title }}
                    </h3>
                    <p class="mt-2 text-sm leading-relaxed text-gray-600 dark:text-gray-400">
                        {{ feature.description }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="px-4 py-24">
        <div class="mx-auto max-w-2xl text-center">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">¿Listo para construir?</h2>
            <p class="mt-4 text-gray-600 dark:text-gray-400">
                Clona el repositorio y empieza tu aplicación hoy mismo.
            </p>
            <div class="mt-10 flex flex-wrap justify-center gap-4">
                <Link
                    v-if="canRegister"
                    href="/register"
                    class="rounded-xl bg-gray-900 px-6 py-3 text-sm font-semibold text-white transition-colors hover:bg-gray-700 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100"
                >
                    Crear cuenta gratis
                </Link>
                <Link
                    href="/about"
                    class="rounded-xl border border-gray-300 px-6 py-3 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
                >
                    Saber más
                </Link>
            </div>
        </div>
    </section>
</template>
