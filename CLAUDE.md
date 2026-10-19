# Package: simplysmart/mainboard

## Context & Responsibility
Standalone Laravel Composer package (`illuminate/support` ^13): the modular mainboard for simplysmart applications — connecting modules, management workspaces. Developed inside the skeleton at `packages/simplysmart/mainboard/` (symlinked into its `vendor/`), but it is its own Git repository and must work without the skeleton.

## Architectural Boundaries
- **No application coupling**: Never reference skeleton classes (`App\...`) or other packages' concrete classes. Depend on contracts; receive dependencies through constructor injection or the container.
- **Contracts**: All contracts/interfaces of this package MUST reside in `src/Contracts/` (`Simplysmart\Mainboard\Contracts\*`). Bind their implementations in `MainboardServiceProvider::register()`.
- **Service Provider**: `src/MainboardServiceProvider.php`, auto-discovered via `extra.laravel.providers` in `composer.json`. Load package resources in `boot()`: migrations (`loadMigrationsFrom`), views and Blade components (`loadViewsFrom`), config (`mergeConfigFrom`), routes. Keep these files inside the package, never in the skeleton.
- **Assets**: Static CSS without a bundler in `public/assets/` (see `docs/struktura.md`); brand sources in `brand/`.

## Development Rules
- **Strict Typing**: Every PHP file starts with `declare(strict_types=1);` (enforced by Pint).
- **Coding Standards**: PHP ^8.4, PSR-4 (`Simplysmart\Mainboard\` → `src/`), PER Coding Style via Laravel Pint `per` preset (`pint.json`).
- **Dependencies**: Follow `docs/zaleznosci.md` (MIT-compatible licences, `^` ranges, update the table and `CHANGELOG.md`). `composer.lock` is not committed.
- **Changelog**: Record user-visible changes in `CHANGELOG.md` under `## [Unreleased]` (Keep a Changelog; see `CONTRIBUTING.md`).

## Testing & Quality
Run in this directory before finishing:
- `composer lint` — style and strict types (`composer format` fixes them).
- `composer analyse` — PHPStan at level `max` (`phpstan.neon`). Fix the cause; no ignores or baseline.
- No package test suite yet; integration with the skeleton is covered by the skeleton's `tests/Feature/PackageIntegrationTest.php` (`composer test` in the skeleton root).

## Releasing
From the skeleton root: `vendor/bin/version-release --root=packages/simplysmart/mainboard` (add `--dry-run` to preview without changing files). It really releases — never run it without `--dry-run` just to test.
