<?php

declare(strict_types=1);

namespace Simplysmart\Mainboard\Contracts;

use Simplysmart\Mainboard\Translation\TextGroup;

/**
 * Tłumaczenia kaskadowe bez języka zapasowego: paczka strefy → mainboard → skeleton → klucz.
 *
 * Brak tłumaczenia w bieżącym języku zwraca klucz zamiast tekstu w innym języku, więc od razu
 * widać, czy brakuje tłumaczenia, czy klucz jest błędny.
 */
interface TextTranslator
{
    /**
     * Tłumaczy klucz (opcjonalnie z namespace paczki, np. "zone::dashboard.title").
     *
     * @param  array<string, mixed>  $replace  wartości podstawiane za :placeholder
     */
    public function text(string $key, array $replace = [], ?string $locale = null): string;

    /**
     * Tłumaczy klucz w przestrzeni grupy, np. Actions + "save" → "actions.save".
     *
     * @param  array<string, mixed>  $replace  wartości podstawiane za :placeholder
     */
    public function group(TextGroup $group, string $key, array $replace = [], ?string $locale = null): string;

    /**
     * Tłumaczy nazwę kolumny modelu: najpierw "data.<model>.<kolumna>", potem wspólne "data.<kolumna>".
     *
     * @param  object|string  $model  instancja, nazwa klasy albo nazwa modelu (także "zone::Model")
     * @param  array<string, mixed>  $replace  wartości podstawiane za :placeholder
     */
    public function data(object|string $model, string $column, array $replace = [], ?string $locale = null): string;
}
