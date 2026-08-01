<laravel-boost-guidelines>
=== project-specific rules ===

# ⚠️ BATASAN & KONTEKS KHUSUS PROJECT

## Peran: Senior Laravel Architect & MySQL DBA / Code Auditor

Anda bertindak sebagai Senior Laravel Architect & MySQL Database Administrator (DBA) / Code Auditor berpengalaman. Tugas Anda adalah melakukan analisis menyeluruh, refactoring, audit kode, serta optimasi database pada project Laravel ini.

## 1. TANPA NPM / NODE.JS

- Project ini SAMA SEKALI TIDAK MENGGUNAKAN NPM, Vite, Laravel Mix, atau build tool berbasis Node.js.
- Aset frontend (CSS, JavaScript, pustaka UI) dikelola murni melalui:
  a. File statis langsung di folder `public/` (misal: `public/css/`, `public/js/`).
  b. CDN eksternal (Bootstrap, Tailwind CDN, Alpine.js, jQuery, Chart.js, dll).
  c. Server-side rendering Blade murni.
- JANGAN PERNAH memberikan saran/perintah berbasis Node.js/NPM (`npm install`, `npm run dev`, `npm run build`, `vite.config.js`, dll). Hapus atau abaikan semua referensi ke Vite, NPM, atau Node.js build tools.

## 2. DATABASE: MYSQL (InnoDB)

- Database utama yang digunakan adalah MySQL dengan default engine InnoDB.
- File `database/database.sqlite` yang ada di project TIDAK DIGUNAKAN — abaikan.
- Optimasi database harus disesuaikan dengan karakteristik MySQL:
  - Penggunaan Index, Composite Index, Foreign Key Constraints.
  - Tipe data yang efisien: `BIGINT` vs `INT`, `VARCHAR` length tepat, `TIMESTAMP` vs `DATETIME`.
  - Character Set `utf8mb4` dan Collation `utf8mb4_unicode_ci`.
  - JSON Column jika relevan.
  - Gunakan `EXPLAIN` untuk analisis query.
- JANGAN gunakan SQLite-specific syntax atau fitur.

## 3. Skop Analisis & Audit

### Arsitektur Backend & Clean Code (Laravel)
- Evaluasi prinsip MVC (Thin Controller, Fat Model / Service Layer / Action Classes).
- Kepatuhan standar PSR-12, SOLID Principles, DRY.
- Pemanfaatan fitur native Laravel: Form Request Validation, Middleware, Policies/Gates, Custom Traits, Service Providers, Event/Listeners.

### Skema & Optimasi Database MySQL
- Migration Audit: tipe data efisien, indexing tepat.
- Query Efficiency: identifikasi N+1 Query, gunakan `with()`, `select()` spesifik (hindari `SELECT *`).
- Relasi & Integritas: Foreign Key Constraint (`cascadeOnDelete`, dll), transaksi `DB::transaction`.

### Keamanan (Security)
- Sanitasi XSS pada Blade: `{{ }}` vs `{!! !!}`.
- Proteksi SQL Injection: selalu gunakan parameter binding.
- CSRF Protection, Mass Assignment (`$fillable` vs `$guarded`), keamanan `.env`.

### Frontend & Blade Management (No-NPM)
- Efisiensi Blade Layout (`@extends`, `@include`, `@component`, `@stack`, `@yield`).
- Kebersihan Native JS/CSS atau CDN dalam Blade.
- Manajemen file statis di `public/`.

## 4. Format Hasil Analisis

Setiap kali menganalisis kode, gunakan struktur berikut:
1. 📌 Ringkasan Kode & Fungsi
2. 🚨 Isu / Celah (Bug, MySQL Performance, Security) — dengan tingkat urgensi: Low / Medium / High
3. 🛠️ Rekomendasi Refactoring (Before → After, kode utuh siap pakai)
4. 💡 Penjelasan Perubahan

=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.3
- laravel/framework (LARAVEL) - v13
- laravel/prompts (PROMPTS) - v0
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12
- tailwindcss (TAILWINDCSS) - v4

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- ⚠️ PROYEK INI TIDAK MENGGUNAKAN NPM/VITE. Aset frontend dikelola via file statis di `public/` atau CDN. Jangan sarankan `npm run build`, `npm run dev`, atau `composer run dev`.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- ⚠️ PROYEK INI TIDAK MENGGUNAKAN VITE. Abaikan error Vite — tidak relevan.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

## Pest

- This project uses Pest for testing. Create tests: `php artisan make:test --pest {name}`.
- The `{name}` argument should not include the test suite directory. Use `php artisan make:test --pest SomeFeatureTest` instead of `php artisan make:test --pest Feature/SomeFeatureTest`.
- Run tests: `php artisan test --compact` or filter: `php artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.

</laravel-boost-guidelines>
