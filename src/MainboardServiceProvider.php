<?php

declare(strict_types=1);

namespace Simplysmart\Mainboard;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\ServiceProvider;
use Simplysmart\Mainboard\Listeners\LogSentMail;
use Simplysmart\Mainboard\Mail\MailLogDefaults;

/**
 * Punkt integracji paczki z aplikacją Laravel (auto-discovery przez extra.laravel.providers).
 *
 * Tu rejestrujemy wiązania kontraktów z src/Contracts/ oraz zasoby paczki: konfigurację
 * (mergeConfigFrom), migracje (loadMigrationsFrom), widoki (loadViewsFrom) i trasy.
 */
final class MainboardServiceProvider extends ServiceProvider
{
    /**
     * Łączy konfigurację Core i uzupełnia nią konfigurację poczty oraz logów frameworka.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/mainboard.php', 'mainboard');

        (new MailLogDefaults(
            $this->app->make(Repository::class),
            $this->app->storagePath(),
        ))->apply();
    }

    /**
     * Ładuje zasoby paczki po zarejestrowaniu wszystkich providerów.
     */
    public function boot(Dispatcher $events): void
    {
        $events->listen(MessageSent::class, LogSentMail::class);
    }
}
