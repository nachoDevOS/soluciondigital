# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Caveman Mode
- Ingeniero senior. Ultra-terso, técnico, directo.
- Prohibido: introducciones, relleno, agradecimientos.
- Formato: código y comandos primero.
- Sin info suficiente: pregunta breve. Con info: ejecuta.

## Stack

Laravel 13 + Inertia.js v3 + Vue 3 (TypeScript) + Tailwind CSS v4 + Laravel Fortify + Reka UI (headless). Sin librerías de UI con estilos (no PrimeVue, Vuetify, etc.).

## Comandos

```bash
composer setup      # primera instalación
composer run dev    # Laravel + queue + logs + Vite en paralelo
composer test       # config:clear + pint:check + pest
composer lint       # Pint auto-corrige
composer ci:check   # lint + format + types + tests completo

npm run lint        # ESLint auto-corrige
npm run format      # Prettier auto-corrige
npm run types:check # vue-tsc sin emitir

./vendor/bin/pest tests/Feature/Auth/LoginTest.php  # test único
```

## Arquitectura

### Secciones

| Sección | URL | Middleware | Layout |
|---|---|---|---|
| Público | `/`, `/about`, `/contact` | — | `PublicLayout.vue` |
| Admin | `/admin/*` | `auth + verified` | `AppLayout.vue` |
| Auth | `/login`, etc. | — | `AuthLayout.vue` |
| Settings | `/settings/*` | `auth` | `AppLayout` + `settings/Layout.vue` |

### Layouts (`resources/js/layouts/`)

```
AppLayout.vue          ← sidebar colapsable (Reka UI) + breadcrumbs + Toaster
AuthLayout.vue         ← centrado, con logo, sin sidebar
PublicLayout.vue       ← navbar + footer público (pure Vue + Tailwind)
settings/Layout.vue    ← anidado dentro de AppLayout, nav lateral de settings
```

### Asignación automática de layouts (`app.ts`)

```
Welcome / public/*   → PublicLayout
auth/*               → AuthLayout
settings/*           → [AppLayout, settings/Layout]
(resto)              → AppLayout
```

### Rutas (`routes/web.php`)

```
GET  /               → Welcome.vue
GET  /about          → public/About.vue
GET  /contact        → public/Contact.vue
POST /contact        → ContactController@store
GET  /admin/dashboard → admin/Dashboard.vue    (auth+verified)
GET  /admin/users     → admin/users/Index.vue  (auth+verified)
/settings/*           → routes/settings.php
```

Fortify gestiona login, 2FA, reset y verificación. **Registro desactivado** — usuarios solo via `php artisan db:seed`.
Dev: `admin@admin.com` / `password`.

### Agregar sección al admin

1. Ruta en `routes/web.php` bajo grupo `admin.`
2. Controlador en `app/Http/Controllers/Admin/`
3. Página en `resources/js/pages/admin/`
4. Item en `mainNavItems` de `AppSidebar.vue`

### Tipos (`resources/js/types/index.ts`)

Todos los tipos en un solo archivo: `User`, `Auth`, `BreadcrumbItem`, `NavItem`, `FlashToast`, `Appearance`.
`global.d.ts` y `vue-shims.d.ts` son declaraciones de módulo — no tocar.

### Flash toasts

```php
return back()->with(['toast' => ['type' => 'success', 'message' => 'OK']]);
// tipos: success | info | warning | error
```

`lib/flashToast.ts` escucha el evento `flash` de Inertia y lo pasa a `vue-sonner`.

### Rutas tipadas

`resources/js/routes/` — auto-generado por Wayfinder. No editar manualmente.

## Convenciones

**PHP:** PSR-4 (`App\` → `app/`), Pint, Pest. SQLite local, MySQL producción. Session/queue/cache usan driver `database` — correr `migrate` antes de `dev`.

**Frontend:** alias `@/` → `resources/js/`. `import type` obligatorio para tipos. `cn()` (`clsx` + `tailwind-merge`) para clases condicionales. `components/ui/` excluido de ESLint.
