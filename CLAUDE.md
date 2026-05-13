# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Caveman Mode (System Instructions)
- Eres un ingeniero senior de sistemas.
- Estilo de comunicación: Ultra-terso, técnico, directo.
- Prohibido: Introducciones, agradecimientos, relleno ("Aquí tienes...", "Espero que esto ayude...").
- Formato: Prioriza bloques de código, comandos de terminal y soluciones técnicas inmediatas.
- Contexto: Si no hay información suficiente, pregunta brevemente. Si la hay, ejecuta.

## Stack

Laravel 13 + Inertia.js v3 + Vue 3 (TypeScript) + Tailwind CSS v4, con Laravel Fortify para autenticación y Reka UI para primitivos headless. Sin librerías de componentes con diseño preestablecido (no PrimeVue, Vuetify, etc.).

## Comandos

**Primera instalación** (instala dependencias, genera key, migra, compila assets):
```bash
composer setup
```

**Iniciar todos los servicios de desarrollo** (Laravel, queue, Pail logs, Vite — en paralelo):
```bash
composer run dev
```

**Ejecutar tests** (limpia config, verifica Pint, corre Pest):
```bash
composer test
```

**Ejecutar un único archivo de tests:**
```bash
./vendor/bin/pest tests/Feature/Auth/LoginTest.php
```

**Linting PHP** (Laravel Pint):
```bash
composer lint        # auto-corrige
composer lint:check  # solo verifica
```

**Linting y formato del frontend:**
```bash
npm run lint         # ESLint auto-corrige
npm run lint:check   # ESLint solo verifica
npm run format       # Prettier auto-corrige
npm run format:check # Prettier solo verifica
npm run types:check  # vue-tsc comprobación de tipos
```

**CI completo** (todos los linters + tipos + tests):
```bash
composer ci:check
```

---

## Arquitectura

### Dos secciones bien diferenciadas

El proyecto está dividido en:

| Sección | URL prefix | Middleware | Layout asignado |
|---|---|---|---|
| **Sitio público** | `/`, `/about`, `/contact` | ninguno | `PublicLayout` |
| **Panel de admin** | `/admin/*` | `auth + verified` | `AdminLayout` (sidebar) |
| **Autenticación** | `/login`, `/register`, etc. | ninguno | `AuthLayout` |
| **Configuración** | `/settings/*` | `auth` | `AdminLayout` + `SettingsLayout` (anidado) |

### Asignación automática de layouts (`resources/js/app.ts`)

Los layouts se asignan según el nombre del componente de página:

```
Welcome       → PublicLayout
public/*      → PublicLayout
auth/*        → AuthLayout
settings/*    → [AdminLayout, SettingsLayout]
(cualquier otro, incluido admin/*) → AdminLayout
```

### Rutas (`routes/web.php`)

```
GET  /                    → Welcome.vue          (público)
GET  /about               → public/About.vue     (público)
GET  /contact             → public/Contact.vue   (público)
POST /contact             → ContactController    (público)

GET  /admin/dashboard     → admin/Dashboard.vue  (auth+verified)
GET  /admin/users         → admin/users/Index.vue (auth+verified)
GET  /dashboard           → redirect /admin/dashboard

/settings/*               → routes/settings.php
```

Laravel Fortify gestiona login, 2FA, reset de contraseña y verificación de email. El **registro público está desactivado** (`Features::registration()` no está en `config/fortify.php`): los usuarios se crean solo desde admin o con `php artisan db:seed`.

Credenciales del seeder de desarrollo: `admin@admin.com` / `password`.

### Estructura de páginas

```
resources/js/pages/
  Welcome.vue               ← Landing page (sitio público)
  public/
    About.vue               ← Página "Acerca de"
    Contact.vue             ← Formulario de contacto
  admin/
    Dashboard.vue           ← Dashboard con stats de DB
    users/
      Index.vue             ← Listado paginado de usuarios
  auth/                     ← Gestionado por Fortify (no modificar)
  settings/                 ← Perfil, seguridad, apariencia
```

### Controladores PHP

```
app/Http/Controllers/
  Admin/
    DashboardController.php  ← stats (User::count, etc.)
    UserController.php       ← User::latest()->paginate(15)
  Public/
    ContactController.php    ← valida y responde con flash toast
  Settings/
    ProfileController.php    ← existente
    SecurityController.php   ← existente
```

### Layouts disponibles

- **`PublicLayout.vue`** — Navbar responsivo (logo, links, botones de auth), slot de contenido, footer. No depende de Reka UI; pure Vue + Tailwind.
- **`AppLayout.vue`** (alias AdminLayout) — Delegado a `layouts/app/AppSidebarLayout.vue`, que incluye sidebar colapsable (Reka UI), header con breadcrumbs y Toaster.
- **`AuthLayout.vue`** — Delegado a `layouts/auth/AuthSimpleLayout.vue`, centrado, con logo.
- **`layouts/settings/Layout.vue`** — Layout anidado dentro de AdminLayout con nav lateral de settings.

### Navegación del admin (`AppSidebar.vue`)

Los items del sidebar se definen como array `mainNavItems: NavItem[]` en `resources/js/components/AppSidebar.vue`. Para agregar una nueva sección al panel:

1. Añadir la ruta en `routes/web.php` bajo el grupo `admin.`
2. Crear el controlador en `app/Http/Controllers/Admin/`
3. Crear la página en `resources/js/pages/admin/`
4. Añadir el item al array `mainNavItems` en `AppSidebar.vue`

### Rutas tipadas (Wayfinder)

`resources/js/routes/` contiene archivos TypeScript **auto-generados** por `@laravel/vite-plugin-wayfinder`. Nunca editar estos archivos manualmente — se regeneran al arrancar `npm run dev` o `npm run build`.

Las rutas nuevas del admin (`/admin/*`) generarán sus helpers tipados automáticamente al ejecutar el servidor de desarrollo.

### Props compartidas de Inertia

Todas las páginas reciben estas props (tipadas en `resources/js/types/global.d.ts`):

- `auth.user` — usuario autenticado o null
- `name` — nombre de la aplicación
- `sidebarOpen` — estado del sidebar

### Flash toasts

El servidor envía toasts a través del mecanismo flash de Inertia. El shape esperado es:

```php
return back()->with([
    'toast' => ['type' => 'success', 'message' => 'Operación completada.'],
]);
```

Tipos válidos: `success | info | warning | error`. El listener en `resources/js/lib/flashToast.ts` lo captura y lo pasa a `vue-sonner`.

### Apariencia / modo oscuro

El tema se persiste en `localStorage` y se replica en una cookie (para SSR). El composable `useAppearance` expone `appearance`, `resolvedAppearance` y `updateAppearance`. `initializeTheme()` se llama en `app.ts` antes del mount para evitar flash.

### Convenciones PHP

- Autoloading PSR-4: `App\` → `app/`, `Tests\` → `tests/`
- Linting: Laravel Pint (config en `pint.json`)
- Tests: Pest con `pestphp/pest-plugin-laravel`; en `tests/Feature/` y `tests/Unit/`
- Base de datos local: SQLite (`database/database.sqlite`) por defecto; producción usa MySQL
- Session, queue y cache usan driver `database` — asegurarse de correr `php artisan migrate` antes de `composer run dev`

### Convenciones frontend

- Alias `@/` → `resources/js/`
- TypeScript estricto; usar `import type` para tipos (regla ESLint activa)
- `resources/js/components/ui/` — componentes headless (Reka UI), excluidos de ESLint
- `resources/js/routes/` — auto-generado por Wayfinder, excluido de ESLint
- Tailwind: usar `clsx` + `tailwind-merge` (helper `cn`) para clases condicionales
