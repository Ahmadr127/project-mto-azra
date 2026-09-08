# AGENTS.md

## Stack
- Laravel 12 / PHP ^8.2 (verified 8.3), Vite 7 + Tailwind 4 (`@tailwindcss/vite`), `barryvdh/laravel-dompdf` for MCU PDF.
- DB: PostgreSQL in dev (`.env` `DB_CONNECTION=pgsql` `DB_DATABASE=db_mto`); **tests use `sqlite :memory:`** via `phpunit.xml:25-26` — no Postgres needed for `php artisan test`.
- Cache/session/queue = `database` in dev, `array`/`sync` in testing (`phpunit.xml`).

## Commands
```bash
composer install
npm install
cp .env.example .env && php artisan key:generate
# Edit .env DB_* for Postgres (default pgsql, not sqlite). Ensure DB db_mto exists.
php artisan migrate --seed          # seeds RolePermissionSeeder -> OrganizationTypeSeeder -> OrganizationUnitSeeder -> Mcu*Seeders -> DemoSeeder, then admin user
php artisan serve                   # http://localhost:8000
npm run dev                         # vite only
composer run dev                    # concurrently: serve + queue:listen --tries=1 + vite (requires `concurrently` via npm)
composer run test                   # = php artisan config:clear && php artisan test
php artisan test --filter=TestName  # single test
vendor/bin/pint                     # formatter (laravel/pint ^1.24, no pint.json — defaults)
npm run build                       # vite build -> public/build (gitignored)
php artisan queue:listen --tries=1  # queue required in dev when using composer run dev
```

## Architecture
- **Routes split:** `routes/web.php` (auth + dashboard + `permission:*` groups) `require`s `routes/master.php` (6× `mcu-*` resources) and `routes/mcu.php` (patients, mcu-registrations, `mcu-examinations/*` lab/radiology/anamnesis/physical/doctor, `mcu-resumes` + pdf). All under `auth` middleware; `/` redirects to `/login`.
- **AuthZ:** `AppServiceProvider:boot` aliases `permission` -> `App\Http\Middleware\CheckPermission` (not in `bootstrap/app.php:13-15` which is empty). Middleware checks `Auth::user()->hasPermission($name)` -> `User->role->permissions` via `role_permission` pivot. Permissions: `manage_users`, `manage_roles`, `manage_permissions`, `manage_organization_types`, `manage_organization_units`, `view_dashboard` (see `RolePermissionSeeder.php`).
- **Models:** `app/Models/` — `User` (fillable `role_id`, `organization_unit_id`, `nik/username`), `Role`, `Permission`, `OrganizationType/Unit`, `Patient`, `McuRegistration`, `McuPackage`+`McuPackageItem`, `McuLab/Radiology/Anamnesis/PhysicalExam/MedicalAction` and exam pivots (`McuExamLab` etc.) + `McuPhysicalExamResult`. Relations are standard Eloquent; no custom casts beyond `password hashed`.
- **Frontend:** Blade + Tailwind. Entrypoints `resources/css/app.css`, `resources/js/app.js` (`vite.config.js:8`). Layout `resources/views/layouts/app.blade.php` loads `@vite`, `css/sidebar.css`, `js/sidebar.js` + `js/navigation.js` (AJAX nav + Alpine). Views grouped by feature in `resources/views/{auth,users,roles,permissions,mcu,organization-*}`.
- **Seed order matters:** `DatabaseSeeder.php:17-26` creates admin `admin@example.com` / `rsazra` (not `password` as README claims) with `admin` role (all permissions). Demo patients prefixed `DEMO-RM-*`.

## Conventions / Gotchas
- **Do not move `permission` middleware registration** to `bootstrap/app.php` without keeping `AppServiceProvider::boot` alias — routes depend on string `permission:*`.
- **EditorConfig:** 4 spaces, LF, trim trailing whitespace (except `.md`), 2 spaces for yaml. Pint uses defaults.
- **Env:** `.env` is required; `SESSION_DRIVER=database` + `CACHE_STORE=database` need migrations. Tests override to `array`. If Postgres not running, `migrate --seed` fails — create DB first or temporarily switch to sqlite for quick checks.
- **Vite:** `tailwindcss()` plugin is required; `public/build` and `public/hot` are gitignored. Run `npm run dev` alongside `php artisan serve` during UI work or use `composer run dev`.
- **PDF:** `McuResumeController@printPdf` uses `barryvdh/laravel-dompdf` — dompdf config in `config/dompdf.php`, requires fully rendered Blade HTML.
- **Tests:** Only `tests/Feature/ExampleTest.php` + `tests/Unit/ExampleTest.php` exist; Feature test `GET /` expects 200 but app redirects to `/login` (302) — will fail until updated. Use `RefreshDatabase` trait for DB tests (not included by default).
- **No CI / pre-commit / opencode.json** — no lint/typecheck gate beyond Pint.

## Verification Order
1. `vendor/bin/pint --test` (if touching PHP)
2. `php artisan test` (sqlite in-memory, fast)
3. `npm run build` (check Vite/Tailwind)
