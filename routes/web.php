<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Public\ContactController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

// ─── Sitio público ────────────────────────────────────────────────────────────

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::inertia('/about', 'public/About')->name('about');

Route::get('/contact', fn () => inertia('public/Contact'))->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// ─── Panel de administración ──────────────────────────────────────────────────

Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('users', [UserController::class, 'index'])->name('users');
    });

// Redirige /dashboard → /admin/dashboard por compatibilidad
Route::redirect('/dashboard', '/admin/dashboard');

// ─── Configuración de usuario ─────────────────────────────────────────────────

require __DIR__.'/settings.php';
