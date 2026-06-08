<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

// #[Fillable] define qué columnas se pueden asignar en masa (User::create([...]) o fill([...])).
// Esto previene "mass assignment" — nadie puede meter columnas no listadas aquí.
#[Fillable(['name', 'email', 'password'])]

// #[Hidden] excluye estas columnas cuando el modelo se serializa a JSON (ej: respuesta Inertia).
// Así el password y secrets de 2FA nunca llegan al frontend ni a APIs externas.
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    // HasFactory        → permite usar User::factory() en tests y seeders.
    // HasRoles          → NUEVO: agrega métodos de Spatie: assignRole(), hasRole(), hasPermissionTo(),
    //                     getRoleNames(), getAllPermissions(), syncRoles(), etc.
    //                     También crea las relaciones roles() y permissions() en el modelo.
    // Notifiable        → permite enviar notificaciones (email, SMS, etc.) al usuario.
    // TwoFactorAuth...  → agrega soporte de 2FA de Laravel Fortify (QR, recovery codes).
    use HasFactory, HasRoles, Notifiable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // email_verified_at se guarda como string en DB, se lee como objeto Carbon (fecha).
            'email_verified_at' => 'datetime',
            // password se hashea automáticamente al asignarlo: $user->password = 'texto' → bcrypt.
            'password' => 'hashed',
            // Fecha en que el usuario confirmó su dispositivo 2FA.
            'two_factor_confirmed_at' => 'datetime',
        ];
    }
}
