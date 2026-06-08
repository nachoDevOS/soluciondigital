<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Public\ContactController;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────
// SITIO PÚBLICO — accesible sin login
// ─────────────────────────────────────────────

// Route::inertia() es un shortcut para rutas que solo renderizan una página Vue
// sin lógica en controlador. Equivale a: Route::get('/', fn() => inertia('public/Welcome'))
Route::inertia('/', 'public/Welcome')->name('home');
Route::inertia('/about', 'public/About')->name('about');

Route::get('/contact', fn () => inertia('public/Contact'))->name('contact');

// El formulario de contacto hace POST a este endpoint → ContactController::store
// valida los campos y retorna un flash toast al frontend.
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// ─────────────────────────────────────────────
// PANEL DE ADMINISTRACIÓN
// ─────────────────────────────────────────────

// Grupo con middleware base:
//   auth     → el usuario debe estar logueado (si no, redirige a /login)
//   verified → el usuario debe haber verificado su email (si no, redirige a /email/verify)
// prefix('admin') → todas las rutas del grupo tienen URL /admin/...
// name('admin.')  → los nombres de ruta tienen prefijo admin. (ej: admin.dashboard)
Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // middleware('permission:dashboard.view') → NUEVO: Spatie verifica que el usuario
        // tenga el permiso 'dashboard.view' asignado (directo o a través de su rol).
        // Si no tiene el permiso, Spatie lanza un 403 Forbidden automáticamente.
        Route::get('dashboard', [DashboardController::class, 'index'])
            ->middleware('permission:dashboard.view')
            ->name('dashboard');

        // Solo usuarios con permiso 'users.view' pueden acceder a la lista de usuarios.
        // Un 'editor' no verá esta ruta — solo 'admin' y 'super-admin'.
        Route::get('users', [UserController::class, 'index'])
            ->middleware('permission:users.view')
            ->name('users');
    });

// Fortify redirige a /dashboard después del login. Esta línea lo reenvía al panel real.
Route::redirect('/dashboard', '/admin/dashboard');

// Rutas de configuración de perfil/seguridad/apariencia (auth requerido, sin prefijo admin).
require __DIR__.'/settings.php';
