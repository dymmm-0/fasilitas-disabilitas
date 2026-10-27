<div align="center">

# AksesKita

### Find places that are easier for everyone to access

A map and verification platform for disability-friendly public facilities, helping people plan trips with clearer accessibility information.

[![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel&logoColor=white)](https://laravel.com/)
[![Vue](https://img.shields.io/badge/Vue-3-42B883?logo=vuedotjs&logoColor=white)](https://vuejs.org/)
[![Vite](https://img.shields.io/badge/Vite-8-646CFF?logo=vite&logoColor=white)](https://vite.dev/)
[![Leaflet](https://img.shields.io/badge/Leaflet-1.9-199900?logo=leaflet&logoColor=white)](https://leafletjs.com/)
[![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)

<br>

[**Explore the app**](#running-the-application) · [**View features**](#features) · [**Start installation**](#installation)

[Versi Bahasa Indonesia](README_ID.md)

</div>

> **Core focus:** provide useful accessibility information before people leave — not make them guess after they arrive.

## Features

| Area | Available |
| --- | --- |
| **Discover** | Map, search, facility list, and category/location filters |
| **Understand** | Facility details with eight accessibility indicators |
| **Contribute** | New facility proposals and community corrections |
| **Verify** | Admin queue, decisions, history, and private supporting evidence |
| **Secure access** | Laravel session login, CSRF, `auth` middleware, and limited public endpoints |
| **Multilingual** | Indonesian and English interface; language choice is saved in the browser |

Accessibility status uses three values: `yes`, `no`, and `unknown`. `unknown` means not yet known — it does not mean that the facility is unavailable.

## Main routes

| Route | Purpose |
| --- | --- |
| `/` | Map, search, filters, and facility list |
| `/fasilitas/:id` | Facility details |
| `/kontribusi` | New facility proposal or data correction |
| `/status` | Check a submission without signing in |
| `/masuk` | Admin login |
| `/admin` | Administration dashboard |
| `/verifikasi` | Verification queue and decisions |
| `/tentang` | About the application |

## Technology

- PHP 8.3+, Laravel 13, PHPUnit 12, Laravel Pint
- Vue 3, Vue Router, Vue I18n for the multilingual interface
- Indonesian (`id`) and English (`en`)
- Vite 8 and Tailwind CSS 4
- Leaflet and OpenStreetMap
- MySQL 8.x

Shared frontend state uses singleton composables. The UI uses CSS classes and design tokens in `resources/css/app.css`. Indonesian is the default language; English translations are maintained in `resources/js/i18n/en.json`. The selected language is stored in the browser and applied to the document `lang` attribute.

## Requirements

- PHP 8.3+
- Composer 2.x
- Node.js 20.19 or 22.12+
- npm 10+
- MySQL 8.x
- Git 2.x

An internet connection is required for fonts during the build, OpenStreetMap tiles, and source-data links.

## Installation

### 1. Install dependencies

```powershell
git clone https://github.com/dymmm-0/fasilitas-disabilitas.git
cd fasilitas-disabilitas
composer install
npm install
Copy-Item .env.example .env
php artisan key:generate
```

### 2. Prepare the database

Create an empty database and update `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fasilitas_disabilitas
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Run migrations and build the frontend

```powershell
php artisan migrate
php artisan db:seed
npm run build
```

The seeder creates an admin account using `SEED_PASSWORD`. The default value is `password`; change it before using the application outside a local machine.

## Running the application

Recommended command:

```powershell
composer run dev
```

This starts the Laravel server, queue worker, and Vite. The application is available at `http://127.0.0.1:8000`.

Alternatively, run these commands in separate terminals:

```powershell
php artisan serve
npm run dev
```

Run `npm run build` before a demo or deployment. Do not use `npm run dev` in production.

## Testing and code style

```powershell
# All backend tests
php artisan test

# A specific test
php artisan test --filter=AksesKitaTest

# Check PHP formatting
vendor/bin/pint --test

# Format PHP
vendor/bin/pint

# Build frontend assets
npm run build
```

Vue component tests, end-to-end tests, a JavaScript linter, and CI are not currently included.

## Directory structure

```text
app/
├── Console/Commands/       # Application commands
├── Http/Controllers/       # API, authentication, submissions, verification
├── Jobs/                   # QueueSubmission
└── Models/                 # Facility, Submission, User, and related models

database/
├── factories/
├── migrations/
└── seeders/

resources/
├── css/app.css             # Design tokens and UI classes
├── js/components/          # Vue components
├── js/composables/         # Shared state
├── js/data/                # Labels and data helpers
├── js/i18n/                # Indonesian and English translations
├── js/router/              # SPA routes
└── js/views/               # Application pages

routes/                     # API and web routes
public/images/hero/         # Local hero slider photos
storage/app/private/        # Private submission evidence
TemplateDesignWeb/          # Read-only design reference
tests/                      # Backend tests
```

## Security and data notes

- `/admin` and `/verifikasi` are protected by the `auth` middleware; frontend route guards are not the only access control.
- Submission evidence is stored on the `local` disk, not in `public/`.
- The public submission-status endpoint does not expose `contact`, `payload`, summaries, or evidence.
- `unknown` means unknown, not unavailable.
- Facility verification decisions do not change accessibility indicators without evidence.
- Do not commit `.env`, credentials, or generated build artifacts.
- Use a new migration for schema changes; do not edit migrations that have already run.

## License

The project license has not been decided. The repository does not contain a `LICENSE` file; the `MIT` value in `composer.json` comes from the Laravel skeleton and is not a project license statement.

---

<div align="center">
<sub>AksesKita — clear accessibility information before you leave.</sub>
</div>
