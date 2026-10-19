<?php

declare(strict_types=1);

namespace Simplysmart\Mainboard;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Translation\Loader;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\ServiceProvider;
use Simplysmart\Mainboard\Contracts\LocalFormatter;
use Simplysmart\Mainboard\Contracts\TextTranslator;
use Simplysmart\Mainboard\Listeners\LogSentMail;
use Simplysmart\Mainboard\Mail\MailLogDefaults;
use Simplysmart\Mainboard\Localization\IntlLocalFormatter;
use Simplysmart\Mainboard\Translation\CascadeTextTranslator;
use Simplysmart\Mainboard\Translation\CoreTranslationPath;

/**
 * Punkt integracji paczki z aplikacją Laravel (auto-discovery przez extra.laravel.providers).
 *
 * Tu rejestrujemy wiązania kontraktów z src/Contracts/ oraz zasoby paczki: konfigurację
 * (mergeConfigFrom), migracje (loadMigrationsFrom), widoki (loadViewsFrom), tłumaczenia i trasy.
 */
final class MainboardServiceProvider extends ServiceProvider
{
    /**
     * Łączy konfigurację Core, uzupełnia nią konfigurację poczty i logów frameworka,
     * dokłada polskie tłumaczenia frameworka z lang/ paczki i wiąże kontrakty tekstów oraz formatowania.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/mainboard.php', 'mainboard');

        (new MailLogDefaults(
            $this->app->make(Repository::class),
            $this->app->storagePath(),
        ))->apply();

        $this->app->extend('translation.loader', fn(Loader $loader): Loader => (new CoreTranslationPath(
            $this->app->make(Filesystem::class),
            __DIR__ . '/../lang',
            $this->app->langPath(),
        ))->extend($loader));

        $this->app->bind(TextTranslator::class, CascadeTextTranslator::class);
        $this->app->bind(LocalFormatter::class, IntlLocalFormatter::class);
    }

    /**
     * Ładuje zasoby paczki po zarejestrowaniu wszystkich providerów.
     */
    public function boot(Dispatcher $events): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../lang', CascadeTextTranslator::CORE_NAMESPACE);

        $events->listen(MessageSent::class, LogSentMail::class);
    }
}
