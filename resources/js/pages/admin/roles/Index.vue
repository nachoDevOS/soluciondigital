<script setup lang="ts">
// ─────────────────────────────────────────────────────────────
// pages/admin/roles/Index.vue — Gestión de Roles y Permisos
// ─────────────────────────────────────────────────────────────

// Head: modifica el <title> de la pestaña del navegador desde Vue.
import { Head, useForm } from '@inertiajs/vue3';
// useForm: hook de Inertia para manejar formularios.
//   - Rastrea estado de carga (form.processing)
//   - Maneja errores de validación del backend (form.errors.campo)
//   - Envía datos vía XHR sin recargar la página

// computed, ref: herramientas reactivas de Vue.
//   ref()      → crea una variable reactiva. Cuando cambia, Vue actualiza el DOM automáticamente.
//   computed() → crea un valor derivado que se recalcula cuando cambian sus dependencias.
import { computed, ref } from 'vue';

// Íconos de Lucide Vue para los botones y cards.
import { Lock, Pencil, Plus, ShieldCheck, Users, X } from 'lucide-vue-next';

// Componentes de Reka UI para el modal (Dialog):
//   Dialog          → contenedor lógico del modal (no renderiza HTML propio)
//   DialogContent   → el panel blanco flotante del modal con animaciones
//   DialogHeader    → zona del título del modal
//   DialogTitle     → el título en sí
//   DialogFooter    → zona de los botones de acción
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

// Checkbox: casilla de verificación de Reka UI con estilos del tema.
import { Checkbox } from '@/components/ui/checkbox';

// Composable para verificar permisos del usuario logueado.
// hasPermission('roles.edit') → true si puede crear/editar roles.
import { usePermissions } from '@/composables/usePermissions';

// ─────────────────────────────────────────────
// TIPOS TYPESCRIPT
// ─────────────────────────────────────────────
// Describe la forma de un objeto Role que viene del backend.
type Role = {
    id: number;
    name: string;
    permissions: string[];
    users_count: number;
};

// ─────────────────────────────────────────────
// PROPS — datos que llegan desde RoleController
// ─────────────────────────────────────────────
// defineProps declara qué datos espera esta página de Inertia.
// roles          → array de roles existentes con sus permisos y conteo de usuarios
// allPermissions → array de TODOS los permisos del sistema (para los checkboxes del modal)
const { roles, allPermissions } = defineProps<{
    roles: Role[];
    allPermissions: string[];
}>();

// ─────────────────────────────────────────────
// BREADCRUMBS
// ─────────────────────────────────────────────
defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/admin/dashboard' },
            { title: 'Roles', href: '/admin/roles' },
        ],
    },
});

// ─────────────────────────────────────────────
// PERMISOS DEL USUARIO ACTUAL
// ─────────────────────────────────────────────
const { hasPermission } = usePermissions();
// canEdit → true solo para 'super-admin'. Muestra u oculta botones de crear/editar.
const canEdit = computed(() => hasPermission('roles.edit'));

// ─────────────────────────────────────────────
// ESTADO DEL MODAL
// ─────────────────────────────────────────────
// ref(false) crea una variable reactiva inicializada en false.
// Cuando cambia a true, Vue renderiza el modal. Cuando vuelve a false, lo oculta.
const showModal = ref(false);

// editingRole guarda el rol que se está editando, o null si es modo "crear nuevo".
// ref<Role | null>(null) → TypeScript sabe que puede ser un Role o null.
const editingRole = ref<Role | null>(null);

// Computed que decide si el modal está en modo "editar" o "crear".
// isEditMode → true si hay un rol seleccionado para editar.
const isEditMode = computed(() => editingRole.value !== null);

// ─────────────────────────────────────────────
// FORMULARIO DE INERTIA
// ─────────────────────────────────────────────
// useForm() crea un objeto reactivo con los campos del formulario.
// form.name        → valor del campo "nombre del rol"
// form.permissions → array de permisos seleccionados (checkboxes)
// form.processing  → true mientras el request está en curso (para deshabilitar el botón)
// form.errors      → objeto con errores de validación del backend { name: '...', ... }
const form = useForm({
    name: '',
    permissions: [] as string[],
});

// ─────────────────────────────────────────────
// FUNCIONES DEL MODAL
// ─────────────────────────────────────────────

// Abre el modal en modo "crear nuevo rol".
// Limpia el formulario antes de abrir para no mostrar datos del modal anterior.
function openCreate() {
    editingRole.value = null;  // sin rol seleccionado = modo crear
    form.reset();              // limpia name y permissions
    form.clearErrors();        // limpia errores de validación anteriores
    showModal.value = true;
}

// Abre el modal en modo "editar rol existente".
// Pre-carga el formulario con los permisos actuales del rol.
function openEdit(role: Role) {
    editingRole.value = role;

    // form.reset() limpia, luego asignamos manualmente los valores del rol.
    // spread operator [...role.permissions] crea una copia del array para no mutar el original.
    form.reset();
    form.permissions = [...role.permissions];
    form.clearErrors();
    showModal.value = true;
}

// Cierra el modal y resetea el estado.
function closeModal() {
    showModal.value = false;
    editingRole.value = null;
    form.reset();
    form.clearErrors();
}

// Envía el formulario. La lógica cambia según si es crear o editar.
function submitForm() {
    if (isEditMode.value && editingRole.value) {
        // MODO EDITAR: PUT /admin/roles/{id}
        // useForm.put() envía los datos con método HTTP PUT.
        // onSuccess: cierra el modal si el servidor respondió sin errores.
        form.put(`/admin/roles/${editingRole.value.id}`, {
            onSuccess: () => closeModal(),
        });
    } else {
        // MODO CREAR: POST /admin/roles
        form.post('/admin/roles', {
            onSuccess: () => closeModal(),
        });
    }
}

// ─────────────────────────────────────────────
// HELPERS PARA CHECKBOXES
// ─────────────────────────────────────────────

// Verifica si un permiso está marcado en el formulario.
// form.permissions es el array de permisos seleccionados.
// .includes(perm) → true si el permiso está en el array.
function isChecked(perm: string): boolean {
    return form.permissions.includes(perm);
}

// Alterna un permiso en el formulario al hacer click en su checkbox.
// Si ya estaba → lo quita. Si no estaba → lo agrega.
function togglePermission(perm: string) {
    const idx = form.permissions.indexOf(perm);
    if (idx === -1) {
        // No estaba → agregar al array
        form.permissions.push(perm);
    } else {
        // Ya estaba → quitar del array (splice elimina 1 elemento en posición idx)
        form.permissions.splice(idx, 1);
    }
}

// Selecciona/deselecciona todos los permisos de un módulo de una sola vez.
// modulePerms → array de permisos que pertenecen a ese módulo.
function toggleModule(modulePerms: string[]) {
    // allChecked → true si TODOS los permisos del módulo están seleccionados.
    const allChecked = modulePerms.every((p) => form.permissions.includes(p));

    if (allChecked) {
        // Si todos están marcados → desmarcar todos los del módulo.
        // filter() retorna un nuevo array excluyendo los del módulo.
        form.permissions = form.permissions.filter((p) => !modulePerms.includes(p));
    } else {
        // Si alguno no está marcado → marcar todos los del módulo.
        // Set elimina duplicados (por si alguno ya estaba marcado).
        form.permissions = [...new Set([...form.permissions, ...modulePerms])];
    }
}

// ─────────────────────────────────────────────
// HELPERS VISUALES
// ─────────────────────────────────────────────

// Agrupa los permisos por módulo para mostrarlos en secciones.
// Ejemplo: ['dashboard.view', 'users.view', 'users.edit']
//   → { dashboard: ['dashboard.view'], users: ['users.view', 'users.edit'] }
// Nota: guardamos el string completo ('users.view') no solo la acción,
//       para poder usar directamente en togglePermission().
function groupPermissions(permissions: string[]): Record<string, string[]> {
    return permissions.reduce(
        (groups, perm) => {
            // 'users.view'.split('.')[0] → 'users' (nombre del módulo)
            const module = perm.split('.')[0];
            if (!groups[module]) groups[module] = [];
            groups[module].push(perm);  // guardamos el permiso completo
            return groups;
        },
        {} as Record<string, string[]>,
    );
}

// Extrae solo la acción de un permiso para mostrarlo en la badge.
// 'users.view'.split('.')[1] → 'view'
function getAction(perm: string): string {
    return perm.split('.')[1] ?? perm;
}

// Configuración visual (colores + etiqueta) por nombre de rol.
const roleConfig: Record<string, { color: string; label: string }> = {
    'super-admin': {
        color: 'bg-red-100 text-red-700 border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800/30',
        label: 'Super Administrador',
    },
    admin: {
        color: 'bg-blue-100 text-blue-700 border-blue-200 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800/30',
        label: 'Administrador',
    },
    editor: {
        color: 'bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800/30',
        label: 'Editor',
    },
};

function getRoleConfig(name: string) {
    return roleConfig[name] ?? {
        color: 'bg-gray-100 text-gray-700 border-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700',
        label: name,
    };
}

// Colores de las badges de acción en las cards.
const actionColors: Record<string, string> = {
    view:   'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-400',
    edit:   'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
    delete: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
};

function getActionColor(action: string): string {
    return actionColors[action] ?? 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400';
}

// Suma de usuarios en todos los roles (para el subtítulo del header).
const totalUsers = computed(() => roles.reduce((sum, r) => sum + r.users_count, 0));

// Permisos agrupados por módulo para mostrar en el modal.
// Siempre se basa en allPermissions (todos los del sistema), no en los del rol.
const allPermissionsGrouped = computed(() => groupPermissions(allPermissions));
</script>

<template>
    <Head title="Roles y Permisos" />

    <div class="flex flex-col gap-6 p-4">

        <!-- ─── ENCABEZADO ─────────────────────────────────── -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Roles y Permisos
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ roles.length }} roles · {{ totalUsers }} usuarios en total
                </p>
            </div>

            <!--
                Botón "Nuevo Rol": solo visible si el usuario tiene 'roles.edit'.
                v-if="canEdit" → se elimina del DOM si canEdit es false.
                No es solo visual: un admin (sin roles.edit) no lo ve en absoluto.
            -->
            <button
                v-if="canEdit"
                type="button"
                class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white shadow-sm transition-all hover:bg-primary/90 hover:shadow-md active:scale-95"
                @click="openCreate"
            >
                <!-- Plus: ícono de + de Lucide -->
                <Plus class="size-4" />
                Nuevo Rol
            </button>

            <!--
                Badge "Solo lectura": visible solo cuando NO puede editar.
                v-else → se muestra si v-if fue false (complementario).
            -->
            <div
                v-else
                class="flex items-center gap-1.5 rounded-full border border-gray-200 bg-gray-50 px-3 py-1.5 dark:border-gray-700 dark:bg-gray-800"
            >
                <Lock class="size-3.5 text-gray-400" />
                <span class="text-xs text-gray-500 dark:text-gray-400">Solo lectura</span>
            </div>
        </div>

        <!-- ─── GRID DE CARDS ──────────────────────────────── -->
        <!--
            grid-cols-1 → 1 columna en móvil
            md:grid-cols-2 → 2 en tablet
            xl:grid-cols-3 → 3 en desktop grande
        -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
            <div
                v-for="role in roles"
                :key="role.id"
                class="flex flex-col gap-4 rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition-shadow hover:shadow-md dark:border-gray-700 dark:bg-gray-800"
            >
                <!-- Cabecera de la card: ícono + nombre + conteo de usuarios + botón editar -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <!--
                            Círculo de ícono con color según el rol.
                            getRoleConfig(role.name).color → clases de color del mapa.
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
                            <h3 class="font-semibold text-gray-900 dark:text-white">
                                {{ getRoleConfig(role.name).label }}
                            </h3>
                            <span class="text-xs text-gray-400 dark:text-gray-500">
                                {{ role.name }}
                            </span>
                        </div>
                    </div>

                    <!-- Lado derecho: conteo de usuarios + botón editar -->
                    <div class="flex items-center gap-2">
                        <!-- Badge de conteo de usuarios -->
                        <div class="flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-1 dark:bg-gray-700">
                            <Users class="size-3.5 text-gray-500 dark:text-gray-400" />
                            <span class="text-xs font-medium text-gray-600 dark:text-gray-300">
                                {{ role.users_count }}
                            </span>
                        </div>

                        <!--
                            Botón "Editar permisos": solo visible para super-admin.
                            v-if="canEdit" → oculto si no tiene el permiso roles.edit.
                            @click="openEdit(role)" → abre el modal pre-cargado con este rol.
                        -->
                        <button
                            v-if="canEdit"
                            type="button"
                            class="flex size-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 transition-all hover:border-primary/50 hover:bg-primary/5 hover:text-primary dark:border-gray-600 dark:bg-gray-700 dark:text-gray-400 dark:hover:border-primary/50 dark:hover:bg-primary/10 dark:hover:text-primary"
                            title="Editar permisos"
                            @click="openEdit(role)"
                        >
                            <Pencil class="size-3.5" />
                        </button>
                    </div>
                </div>

                <!-- Permisos agrupados por módulo -->
                <div class="border-t border-gray-100 pt-3 dark:border-gray-700">
                    <div v-if="role.permissions.length === 0" class="text-sm text-gray-400 dark:text-gray-500">
                        Sin permisos asignados.
                    </div>
                    <div v-else class="flex flex-col gap-2">
                        <!--
                            Object.entries() convierte el objeto agrupado a array de pares.
                            v-for descompone: [module, perms] = ['users', ['users.view', 'users.edit']]
                        -->
                        <div
                            v-for="[module, perms] in Object.entries(groupPermissions(role.permissions))"
                            :key="module"
                            class="flex items-center gap-2"
                        >
                            <!-- Nombre del módulo -->
                            <span class="w-20 shrink-0 text-xs font-medium capitalize text-gray-500 dark:text-gray-400">
                                {{ module }}
                            </span>
                            <!-- Badges de acción -->
                            <div class="flex flex-wrap gap-1">
                                <span
                                    v-for="perm in perms"
                                    :key="perm"
                                    :class="[
                                        'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium',
                                        getActionColor(getAction(perm)),
                                    ]"
                                >
                                    {{ getAction(perm) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mensaje vacío -->
        <div
            v-if="roles.length === 0"
            class="rounded-xl border border-dashed border-gray-200 bg-gray-50 py-16 text-center dark:border-gray-700 dark:bg-gray-800/50"
        >
            <ShieldCheck class="mx-auto mb-3 size-10 text-gray-300 dark:text-gray-600" />
            <p class="text-sm text-gray-400 dark:text-gray-500">No hay roles configurados.</p>
        </div>
    </div>

    <!-- ─── MODAL CREAR / EDITAR ───────────────────────────── -->
    <!--
        Dialog de Reka UI: componente lógico que controla si el modal está abierto o cerrado.
        :open="showModal"        → controlamos la visibilidad desde nuestra variable reactiva.
        @update:open="..."       → cuando el usuario cierra el modal (click en X o Escape),
                                   Reka UI emite este evento. Lo usamos para cerrar limpiamente.
    -->
    <Dialog :open="showModal" @update:open="(val) => !val && closeModal()">

        <!--
            DialogContent: el panel flotante del modal.
            max-w-lg → ancho máximo de 512px (permite ver los checkboxes cómodamente).
        -->
        <DialogContent class="max-w-lg">

            <!-- Título del modal: cambia según si es crear o editar -->
            <DialogHeader>
                <DialogTitle class="flex items-center gap-2">
                    <ShieldCheck class="size-5 text-primary" />
                    <!--
                        Ternario: si isEditMode → "Editar: nombre", si no → "Nuevo Rol".
                        editingRole!.name → el ! le dice a TypeScript que sabemos que no es null aquí.
                    -->
                    {{ isEditMode ? `Editar: ${editingRole!.name}` : 'Nuevo Rol' }}
                </DialogTitle>
            </DialogHeader>

            <!-- Contenido del formulario -->
            <form class="flex flex-col gap-5" @submit.prevent="submitForm">

                <!--
                    Campo "Nombre": solo se muestra en modo CREAR.
                    En modo editar, el nombre del rol no se puede cambiar.
                    v-if="!isEditMode" → desaparece cuando editingRole tiene valor.
                -->
                <div v-if="!isEditMode" class="flex flex-col gap-1.5">
                    <label
                        for="role-name"
                        class="text-sm font-medium text-gray-700 dark:text-gray-300"
                    >
                        Nombre del rol
                    </label>
                    <input
                        id="role-name"
                        v-model="form.name"
                        type="text"
                        placeholder="ej: supervisor"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-500"
                    />
                    <!--
                        form.errors.name → mensaje de error que viene del backend (validación Laravel).
                        Solo se muestra si hay error en ese campo.
                    -->
                    <p v-if="form.errors.name" class="text-xs text-red-500">
                        {{ form.errors.name }}
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">
                        Solo minúsculas, números y guiones. Ej: <code class="font-mono">mi-rol</code>
                    </p>
                </div>

                <!-- ─── CHECKBOXES DE PERMISOS ───────────────── -->
                <div class="flex flex-col gap-3">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Permisos
                    </span>

                    <!--
                        max-h-72 overflow-y-auto → el área de checkboxes tiene altura máxima
                        con scroll para no agrandar demasiado el modal si hay muchos permisos.
                        pr-1 → padding derecho para que la scrollbar no tape contenido.
                    -->
                    <div class="flex max-h-72 flex-col gap-3 overflow-y-auto pr-1">

                        <!--
                            Un bloque por cada módulo de permisos.
                            allPermissionsGrouped → computed que agrupa todos los permisos del sistema.
                            Object.entries() → array de pares [modulo, [permisos del modulo]]
                        -->
                        <div
                            v-for="[module, perms] in Object.entries(allPermissionsGrouped)"
                            :key="module"
                            class="rounded-lg border border-gray-200 p-3 dark:border-gray-700"
                        >
                            <!-- Cabecera del módulo con checkbox "seleccionar todos" -->
                            <div class="mb-2 flex items-center gap-2">
                                <!--
                                    Checkbox de módulo completo.
                                    :checked → true si TODOS los permisos del módulo están seleccionados.
                                    perms.every() → retorna true solo si todos cumplen la condición.
                                    @click.prevent → previene el comportamiento default del click
                                    para poder controlarlo manualmente con toggleModule().
                                -->
                                <Checkbox
                                    :id="`module-${module}`"
                                    :checked="perms.every((p) => isChecked(p))"
                                    @click.prevent="toggleModule(perms)"
                                />
                                <label
                                    :for="`module-${module}`"
                                    class="cursor-pointer text-sm font-semibold capitalize text-gray-800 dark:text-gray-200"
                                >
                                    {{ module }}
                                </label>
                            </div>

                            <!-- Checkboxes individuales de cada permiso del módulo -->
                            <div class="flex flex-col gap-2 pl-6">
                                <div
                                    v-for="perm in perms"
                                    :key="perm"
                                    class="flex items-center gap-2"
                                >
                                    <!--
                                        Checkbox individual de permiso.
                                        :checked="isChecked(perm)" → refleja si está en form.permissions.
                                        @click.prevent + togglePermission → lo agrega/quita del array.
                                    -->
                                    <Checkbox
                                        :id="perm"
                                        :checked="isChecked(perm)"
                                        @click.prevent="togglePermission(perm)"
                                    />
                                    <label
                                        :for="perm"
                                        class="cursor-pointer text-sm text-gray-600 dark:text-gray-400"
                                    >
                                        <!-- Mostramos la acción con badge de color -->
                                        <span
                                            :class="[
                                                'rounded-full px-2 py-0.5 text-xs font-medium',
                                                getActionColor(getAction(perm)),
                                            ]"
                                        >
                                            {{ getAction(perm) }}
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ─── BOTONES DEL MODAL ─────────────────────── -->
                <DialogFooter class="flex gap-2">
                    <!--
                        Botón cancelar: cierra el modal sin enviar nada.
                        type="button" → previene que este botón submita el formulario.
                    -->
                    <button
                        type="button"
                        class="flex-1 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
                        @click="closeModal"
                    >
                        Cancelar
                    </button>

                    <!--
                        Botón de submit: envía el formulario.
                        :disabled="form.processing" → se deshabilita mientras el request está en curso.
                        Esto previene doble-clic accidental mientras espera respuesta del servidor.
                        form.processing → true automáticamente desde que se envía hasta que el servidor responde.
                    -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="flex-1 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white transition-all hover:bg-primary/90 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <!--
                            Texto del botón cambia según el estado:
                            - "Guardando..." mientras procesa el request
                            - "Crear Rol" en modo crear (cuando no está procesando)
                            - "Guardar Cambios" en modo editar
                        -->
                        {{ form.processing ? 'Guardando...' : isEditMode ? 'Guardar Cambios' : 'Crear Rol' }}
                    </button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
