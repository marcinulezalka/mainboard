<?php

declare(strict_types=1);

namespace Simplysmart\Mainboard;

use Illuminate\Support\ServiceProvider;

/**
 * Punkt integracji paczki z aplikacją Laravel (auto-discovery przez extra.laravel.providers).
 *
 * Tu rejestrujemy wiązania kontraktów z src/Contracts/ oraz zasoby paczki: migracje
 * (loadMigrationsFrom), widoki (loadViewsFrom), konfigurację (mergeConfigFrom) i trasy.
 */
final class MainboardServiceProvider extends ServiceProvider
{
    /**
     * Rejestruje wiązania kontraktów paczki w kontenerze.
     */
    public function register(): void {}

    /**
     * Ładuje zasoby paczki po zarejestrowaniu wszystkich providerów.
     */
    public function boot(): void {}
}
