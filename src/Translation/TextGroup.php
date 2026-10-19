<?php

declare(strict_types=1);

namespace Simplysmart\Mainboard\Translation;

/**
 * Przestrzenie kluczy helperów text_*: pierwszy segment klucza po namespace paczki.
 *
 * Nowa rodzina tekstów (np. eksport do PDF) to nowy przypadek tu i jeden helper w src/helpers.php.
 */
enum TextGroup: string
{
    /** Powtarzalne akcje interfejsu: zapisz, usuń, dodaj (text_action). */
    case Actions = 'actions';

    /** Nazwy kolumn i atrybutów modeli danych (text_data). */
    case Data = 'data';

    /** Etykiety plików eksportu: CSV, Excel, PDF (text_file). */
    case Files = 'files';

    /** Treści wiadomości e-mail: tematy, powitania, stopki (text_email). */
    case Emails = 'emails';
}
