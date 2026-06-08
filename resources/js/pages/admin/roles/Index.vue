<script setup lang="ts">
// ─────────────────────────────────────────────────────────────
// pages/admin/roles/Index.vue — Página de Roles y Permisos
// ─────────────────────────────────────────────────────────────
// Esta página muestra todos los roles del sistema con sus permisos asignados
// y la cantidad de usuarios que tienen cada rol.
//
// Los datos llegan desde RoleController::index() a través de Inertia.
// El controlador pasa: [{ id, name, permissions: string[], users_count }]
// ─────────────────────────────────────────────────────────────

// Head: componente de Inertia que modifica el <head> del HTML.
// Permite cambiar el <title> de la pestaña del navegador desde Vue.
import { Head } from '@inertiajs/vue3';

// computed: función reactiva de Vue que calcula un valor derivado de otros datos.
import { computed } from 'vue';

// Íconos de Lucide Vue para el diseño de las cards.
import { ShieldCheck, Users, Lock } from 'lucide-vue-next';

// ─────────────────────────────────────────────
// TIPO TYPESCRIPT
// ─────────────────────────────────────────────
// TypeScript usa "types" para describir la forma de los datos.
// Aquí describimos cómo es un objeto Role que viene del backend.
type Role = {
    id: number;           // ID numérico del rol en la base de datos
    name: string;         // Nombre del rol: 'super-admin', 'admin', 'editor'
    permissions: string[]; // Array de nombres de permisos: ['dashboard.view', 'users.view', ...]
    users_count: number;  // Cuántos usuarios tienen este rol
};

// ─────────────────────────────────────────────
// PROPS (datos que recibe esta página del backend)
// ─────────────────────────────────────────────
// defineProps declara qué datos espera esta página de Inertia.
// Inertia los envía como "props" desde el controlador PHP.
// RoleController::index() pasa 'roles' → aquí lo recibimos.
const { roles } = defineProps<{
    roles: Role[];
}>();

// ─────────────────────────────────────────────
// CONFIGURACIÓN DEL LAYOUT Y BREADCRUMBS
// ─────────────────────────────────────────────
// defineOptions permite configurar opciones del componente que no van en setup.
// 'layout' con 'breadcrumbs' le dice a AppLayout qué mostrar en la barra superior.
// Resultado: Dashboard > Seguridad > Roles
defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/admin/dashboard' },
            { title: 'Roles', href: '/admin/roles' },
        ],
    },
});

// ─────────────────────────────────────────────
// HELPERS DE VISUALIZACIÓN
// ─────────────────────────────────────────────

// Mapa de configuración visual por rol.
// Define el color de la badge/card y el ícono según el nombre del rol.
// Es un objeto JavaScript normal donde la clave es el nombre del rol.
const roleConfig: Record<string, { color: string; label: string }> = {
    'super-admin': {
        // Clases Tailwind para color rojo/danger → rol más poderoso
        color: 'bg-red-100 text-red-700 border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800/30',
        label: 'Super Administrador',
    },
    admin: {
        // Clases Tailwind para color azul → administrador estándar
        color: 'bg-blue-100 text-blue-700 border-blue-200 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800/30',
        label: 'Administrador',
    },
    editor: {
        // Clases Tailwind para color verde → acceso básico
        color: 'bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800/30',
        label: 'Editor',
    },
};

// Función que retorna la configuración visual de un rol por su nombre.
// Si el rol no está en el mapa (rol personalizado), devuelve un color gris por defecto.
function getRoleConfig(name: string) {
    return roleConfig[name] ?? {
        color: 'bg-gray-100 text-gray-700 border-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700',
        label: name,
    };
}

// Función que agrupa los permisos por módulo para mostrarlos organizados.
// Ejemplo: ['dashboard.view', 'users.view', 'users.edit'] →
//   { dashboard: ['view'], users: ['view', 'edit'] }
//
// Record<string, string[]> → tipo TypeScript para "objeto con claves string y valores string[]"
function groupPermissions(permissions: string[]): Record<string, string[]> {
    // reduce() es un método de arrays que "acumula" un resultado recorriendo cada elemento.
    return permissions.reduce(
        (groups, perm) => {
            // Cada permiso tiene formato 'modulo.accion'. split('.') lo divide en array.
            // Ejemplo: 'users.view'.split('.') → ['users', 'view']
            const [module, action] = perm.split('.');

            // Si el módulo no existe en el objeto, lo inicializamos con un array vacío.
            if (!groups[module]) groups[module] = [];

            // Agregamos la acción al módulo correspondiente.
            groups[module].push(action);

            return groups;
        },
        {} as Record<string, string[]>, // valor inicial del acumulador: objeto vacío
    );
}

// Mapa de colores para las badges de permisos individuales (por acción).
// Permite que 'view' siempre sea azul, 'edit' naranja, 'delete' rojo, etc.
const actionColors: Record<string, string> = {
    view:   'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-400',
    edit:   'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
    delete: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
};

// Función que retorna las clases de color para una acción.
// Si la acción no está en el mapa, devuelve color gris.
function getActionColor(action: string): string {
    return actionColors[action] ?? 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400';
}

// Conteo total de usuarios en el sistema (suma de todos los roles).
// computed() recalcula automáticamente si cambia 'roles'.
// roles.reduce() suma todos los users_count de cada rol.
const totalUsers = computed(() =>
    roles.reduce((sum, role) => sum + role.users_count, 0),
);
</script>

<template>
    <!--
        Head: cambia el título de la pestaña del navegador.
        Inertia actualiza esto en cada navegación sin recargar la página.
    -->
    <Head title="Roles y Permisos" />

    <!--
        Contenedor principal de la página.
        flex flex-col gap-6 → columna vertical con espacio entre secciones.
        p-4 → padding interno.
    -->
    <div class="flex flex-col gap-6 p-4">

        <!-- ─── ENCABEZADO DE LA PÁGINA ─────────────────────── -->
        <div class="flex items-start justify-between">
            <div>
                <!-- Título principal -->
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Roles y Permisos
                </h1>
                <!-- Subtítulo con conteo total de usuarios -->
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    <!-- roles.length → cantidad de roles, totalUsers → suma de todos -->
                    {{ roles.length }} roles configurados · {{ totalUsers }} usuarios en total
                </p>
            </div>

            <!--
                Badge de solo lectura: avisa que los roles no se editan desde aquí.
                Se muestra en la esquina superior derecha del encabezado.
            -->
            <div class="flex items-center gap-1.5 rounded-full border border-gray-200 bg-gray-50 px-3 py-1.5 dark:border-gray-700 dark:bg-gray-800">
                <!-- Lock: ícono de candado de Lucide -->
                <Lock class="size-3.5 text-gray-400" />
                <span class="text-xs text-gray-500 dark:text-gray-400">Solo lectura</span>
            </div>
        </div>

        <!-- ─── GRID DE CARDS DE ROLES ───────────────────────── -->
        <!--
            grid: layout en cuadrícula.
            grid-cols-1 → 1 columna en móvil
            md:grid-cols-2 → 2 columnas en tablet (768px+)
            xl:grid-cols-3 → 3 columnas en desktop grande (1280px+)
            gap-4 → espacio entre cards
        -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">

            <!--
                v-for="role in roles": bucle que crea una card por cada rol.
                :key="role.id": identificador único para el virtual DOM de Vue.
            -->
            <div
                v-for="role in roles"
                :key="role.id"
                class="flex flex-col gap-4 rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition-shadow hover:shadow-md dark:border-gray-700 dark:bg-gray-800"
            >
                <!-- ─── CABECERA DE LA CARD ─────────────────── -->
                <div class="flex items-center justify-between">

                    <!-- Ícono + nombre del rol -->
                    <div class="flex items-center gap-3">
                        <!--
                            Círculo de ícono con fondo de color según el rol.
                            getRoleConfig(role.name).color → clases de color del mapa.
                            split(' ').slice(0,3).join(' ') → toma solo las 3 primeras clases
                            (bg, text, border) para el fondo del círculo, ignora dark:.
                        -->
                        <div
                            :class="[
                                'flex size-10 items-center justify-center rounded-full border',
                                getRoleConfig(role.name).color,
                            ]"
                        >
                            <ShieldCheck class="size-5" />
                        </div>

                        <div>
                            <!-- Nombre legible del rol (del mapa roleConfig) -->
                            <h3 class="font-semibold text-gray-900 dark:text-white">
                                {{ getRoleConfig(role.name).label }}
                            </h3>
                            <!-- Nombre técnico del rol en formato badge pequeño -->
                            <span class="text-xs text-gray-400 dark:text-gray-500">
                                {{ role.name }}
                            </span>
                        </div>
                    </div>

                    <!-- Conteo de usuarios con este rol -->
                    <div class="flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-1 dark:bg-gray-700">
                        <Users class="size-3.5 text-gray-500 dark:text-gray-400" />
                        <span class="text-xs font-medium text-gray-600 dark:text-gray-300">
                            <!--
                                role.users_count → número de usuarios.
                                Ternario para pluralizar: 1 "usuario", 2+ "usuarios".
                            -->
                            {{ role.users_count }} {{ role.users_count === 1 ? 'usuario' : 'usuarios' }}
                        </span>
                    </div>
                </div>

                <!-- ─── PERMISOS AGRUPADOS POR MÓDULO ──────── -->
                <!--
                    Separador visual entre la cabecera y los permisos.
                    border-t border-gray-100 → línea sutil.
                -->
                <div class="border-t border-gray-100 pt-3 dark:border-gray-700">

                    <!--
                        Verificamos si el rol tiene permisos antes de mostrar la sección.
                        role.permissions.length === 0 → si no hay permisos, mostramos mensaje vacío.
                    -->
                    <div v-if="role.permissions.length === 0" class="text-sm text-gray-400 dark:text-gray-500">
                        Sin permisos asignados.
                    </div>

                    <!--
                        v-else → se renderiza si el v-if de arriba fue false (sí hay permisos).
                        flex flex-col gap-2 → columna con espacio entre módulos.
                    -->
                    <div v-else class="flex flex-col gap-2">

                        <!--
                            Object.entries(groupPermissions(role.permissions)):
                            - groupPermissions() agrupa los permisos por módulo.
                            - Object.entries() convierte el objeto a array de pares [clave, valor].
                            - Ejemplo: [['dashboard', ['view']], ['users', ['view', 'edit']]]
                            - v-for descompone cada par en [module, actions].
                        -->
                        <div
                            v-for="[module, actions] in Object.entries(groupPermissions(role.permissions))"
                            :key="module"
                            class="flex items-center gap-2"
                        >
                            <!-- Nombre del módulo (ej: "dashboard", "users") con capitalización -->
                            <span class="w-20 shrink-0 text-xs font-medium capitalize text-gray-500 dark:text-gray-400">
                                {{ module }}
                            </span>

                            <!-- Contenedor de badges de acciones del módulo -->
                            <div class="flex flex-wrap gap-1">
                                <!--
                                    Una badge por cada acción del módulo.
                                    Ejemplo: "view", "edit", "delete"
                                    getActionColor(action) → clases de color según la acción.
                                -->
                                <span
                                    v-for="action in actions"
                                    :key="action"
                                    :class="[
                                        'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium',
                                        getActionColor(action),
                                    ]"
                                >
                                    {{ action }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--
            Mensaje si no hay roles (caso extremo — el seeder siempre crea al menos 3).
            v-if="roles.length === 0" → solo visible si no hay ningún rol en el sistema.
        -->
        <div
            v-if="roles.length === 0"
            class="rounded-xl border border-dashed border-gray-200 bg-gray-50 py-16 text-center dark:border-gray-700 dark:bg-gray-800/50"
        >
            <ShieldCheck class="mx-auto mb-3 size-10 text-gray-300 dark:text-gray-600" />
            <p class="text-sm text-gray-400 dark:text-gray-500">No hay roles configurados.</p>
            <p class="mt-1 text-xs text-gray-300 dark:text-gray-600">
                Ejecuta <code class="font-mono">php artisan db:seed</code> para crearlos.
            </p>
        </div>
    </div>
</template>
