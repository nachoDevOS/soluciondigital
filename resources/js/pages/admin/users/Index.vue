<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import type { User } from '@/types';

// Importamos todos los componentes de tabla desde el barrel export.
// Cada componente aplica el estilo estándar del tema automáticamente.
import {
    Table,
    TableBody,
    TableCell,
    TableEmpty,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

type PaginatedUsers = {
    data: User[];
    current_page: number;
    last_page: number;
    total: number;
    from: number;
    to: number;
    links: Array<{ url: string | null; label: string; active: boolean }>;
};

const { users } = defineProps<{
    users: PaginatedUsers;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/admin/dashboard' },
            { title: 'Usuarios', href: '/admin/users' },
        ],
    },
});

function formatDate(dateStr: string) {
    return new Date(dateStr).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}

function paginationLabel(label: string) {
    return label.replace('&laquo;', '‹').replace('&raquo;', '›');
}
</script>

<template>
    <Head title="Usuarios" />

    <div class="flex flex-col gap-6 p-4 sm:p-6">

        <!-- Encabezado: flex-wrap para que el botón baje en pantallas pequeñas -->
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-foreground">Usuarios</h1>
                <p class="mt-1 text-sm text-muted-foreground">
                    {{ users.total }} {{ users.total === 1 ? 'usuario registrado' : 'usuarios registrados' }}
                </p>
            </div>
        </div>

        <!--
            Table: contenedor visual (card con border, bg-card, shadow, overflow hidden).
            TableHeader: <thead> con bg-muted/60 y borde inferior.
            TableHead: <th> con estilo uppercase, tracking-wide, text-muted-foreground.
            TableBody: <tbody> con divide-y divide-border entre filas.
            TableRow: <tr> con hover:bg-muted/40 y transition.
            TableCell: <td> con padding y text-foreground estándar.
            TableEmpty: fila de estado vacío centrada.
        -->
        <Table>
            <TableHeader>
                <tr>
                    <TableHead>Nombre</TableHead>
                    <TableHead>Email</TableHead>
                    <TableHead>Registro</TableHead>
                    <TableHead>Estado</TableHead>
                </tr>
            </TableHeader>

            <TableBody>
                <TableRow v-for="user in users.data" :key="user.id">

                    <!-- Nombre: text-foreground (color principal, negrita) -->
                    <TableCell class="font-medium">
                        {{ user.name }}
                    </TableCell>

                    <!-- Email: text-muted-foreground (secundario, apagado) -->
                    <TableCell class="text-muted-foreground">
                        {{ user.email }}
                    </TableCell>

                    <!-- Fecha: text-muted-foreground -->
                    <TableCell class="text-muted-foreground">
                        {{ formatDate(user.created_at) }}
                    </TableCell>

                    <!-- Estado: badge semántico (verde/amarillo) — color intencional, no de tema -->
                    <TableCell>
                        <span
                            class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                            :class="
                                user.email_verified_at
                                    ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                    : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'
                            "
                        >
                            {{ user.email_verified_at ? 'Verificado' : 'Sin verificar' }}
                        </span>
                    </TableCell>
                </TableRow>

                <!-- TableEmpty: se muestra si no hay usuarios -->
                <TableEmpty
                    v-if="users.data.length === 0"
                    :colspan="4"
                    message="No hay usuarios registrados aún."
                />
            </TableBody>
        </Table>

        <!-- Paginación -->
        <div
            v-if="users.last_page > 1"
            class="flex flex-col items-center justify-between gap-3 sm:flex-row"
        >
            <p class="text-sm text-muted-foreground">
                Mostrando {{ users.from }} – {{ users.to }} de {{ users.total }}
            </p>
            <div class="flex items-center gap-1">
                <template v-for="(link, i) in users.links" :key="i">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="inline-flex h-8 min-w-[2rem] items-center justify-center rounded-lg px-2 text-sm transition-colors"
                        :class="[
                            link.active
                                ? 'bg-primary font-semibold text-primary-foreground'
                                : 'text-muted-foreground hover:bg-muted',
                        ]"
                    >
                        {{ paginationLabel(link.label) }}
                    </Link>
                    <span
                        v-else
                        class="inline-flex h-8 min-w-[2rem] cursor-not-allowed items-center justify-center rounded-lg px-2 text-sm text-muted-foreground/40"
                    >
                        {{ paginationLabel(link.label) }}
                    </span>
                </template>
            </div>
        </div>
    </div>
</template>
