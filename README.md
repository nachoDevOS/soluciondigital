# plantilla-vue

Plantilla de inicio completa con **sitio público** y **panel de administración**, construida con Laravel + Vue 3 + Inertia.js. Sin librerías de componentes externas — puro Vue con Tailwind CSS.

---

## Requisitos previos

Antes de instalar el proyecto necesitas tener estos programas en tu máquina:

| Herramienta | Versión mínima | Cómo verificar |
|---|---|---|
| PHP | 8.3 | `php --version` |
| Composer | 2.x | `composer --version` |
| Node.js | 18.x | `node --version` |
| npm | 9.x | `npm --version` |
| SQLite | cualquiera | `sqlite3 --version` |

> SQLite viene incluido en PHP por defecto en la mayoría de sistemas. No necesitas instalar un servidor de base de datos adicional para desarrollo local.

### Instalar PHP 8.3 (si no lo tienes)

**Ubuntu / Debian:**
```bash
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.3 php8.3-cli php8.3-sqlite3 php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip
```

**macOS (con Homebrew):**
```bash
brew install php@8.3
```

**Windows:**
Descarga e instala desde [windows.php.net/download](https://windows.php.net/download) (versión Thread Safe).

### Instalar Composer

```bash
# Linux / macOS
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
```

O descárgalo desde [getcomposer.org](https://getcomposer.org).

### Instalar Node.js 18+

Descarga desde [nodejs.org](https://nodejs.org) o usa un gestor de versiones:

```bash
# Con nvm (recomendado)
nvm install 20
nvm use 20
```

---

## Stack del proyecto

| Tecnología | Versión | Función |
|---|---|---|
| Laravel | ^13.7 | Backend, rutas, autenticación |
| PHP | ^8.3 | Lenguaje del servidor |
| Inertia.js | ^3.0 | Puente Laravel ↔ Vue (sin API REST) |
| Vue | ^3.5 | Interfaz de usuario |
| TypeScript | ^5.2 | Tipado estático en el frontend |
| Tailwind CSS | ^4.1 | Estilos utility-first |
| Vite | ^8.0 | Bundler y servidor de desarrollo |
| Laravel Fortify | ^1.34 | Autenticación completa |
| SQLite | — | Base de datos (local, sin servidor) |

---

## Instalación paso a paso

### 1. Clonar el repositorio

```bash
git clone <url-del-repositorio> mi-proyecto
cd mi-proyecto
```

### 2. Instalar dependencias PHP

```bash
composer install
```

### 3. Instalar dependencias de Node

```bash
npm install
```

### 4. Crear el archivo de configuración

```bash
cp .env.example .env
```

### 5. Generar la clave de la aplicación

```bash
php artisan key:generate
```

### 6. Crear la base de datos SQLite

```bash
touch database/database.sqlite
```

### 7. Ejecutar las migraciones

Esto crea todas las tablas necesarias (usuarios, sesiones, caché, etc.):

```bash
php artisan migrate
```

### 8. Compilar los assets del frontend

```bash
npm run build
```

### 9. Iniciar el servidor de desarrollo

Este comando arranca en paralelo el servidor Laravel, el queue worker, el visor de logs (Pail) y el servidor Vite:

```bash
composer run dev
```

Abre el navegador en **http://localhost:8000**

---

## Estructura de la aplicación

```
Sitio público   →  http://localhost:8000/
Acerca de       →  http://localhost:8000/about
Contacto        →  http://localhost:8000/contact

Login           →  http://localhost:8000/login
Registro        →  http://localhost:8000/register

Panel admin     →  http://localhost:8000/admin/dashboard
Usuarios        →  http://localhost:8000/admin/users
Configuración   →  http://localhost:8000/settings/profile
```

---

## Comandos útiles durante el desarrollo

```bash
# Arrancar todo el entorno de desarrollo
composer run dev

# Ejecutar los tests
composer test

# Linting y formato del código PHP (Pint)
composer lint

# Linting del frontend (ESLint)
npm run lint

# Formato del frontend (Prettier)
npm run format

# Verificación de tipos TypeScript
npm run types:check
```

---

## Crear un usuario administrador

Puedes crear un usuario desde Tinker para probar el panel:

```bash
php artisan tinker
```

```php
\App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
    'email_verified_at' => now(),
]);
```

O simplemente regístrate desde la interfaz en `/register`.

---

## Variables de entorno importantes

| Variable | Descripción | Valor por defecto |
|---|---|---|
| `APP_NAME` | Nombre mostrado en la UI | `Laravel` |
| `APP_URL` | URL base de la aplicación | `http://localhost` |
| `DB_CONNECTION` | Motor de base de datos | `sqlite` |
| `MAIL_MAILER` | Driver de correo | `log` (no envía, solo loguea) |
| `VITE_APP_NAME` | Nombre en el frontend | igual que `APP_NAME` |

Para usar MySQL en lugar de SQLite, cambia en `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_base_datos
DB_USERNAME=usuario
DB_PASSWORD=contraseña
```
"# soluciondigital" 
# veterinaria
# soluciondigital
