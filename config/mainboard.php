<?php

declare(strict_types=1);

/**
 * Konfiguracja Core (simplysmart/mainboard), łączona przez MainboardServiceProvider::register().
 *
 * Wartości nadpisuje się zmiennymi środowiska; pliku nie publikuje się do szkieletu.
 */
return [

    'mail' => [

        /*
         * Kanał logów dla poczty: mailer "log" zapisuje tu treść wiadomości, a listener
         * LogSentMail odnotowuje każdą wysyłkę przez prawdziwy transport (SMTP itp.).
         */
        'log_channel' => env('MAIL_LOG_CHANNEL', 'mail'),

        /*
         * Liczba dni przechowywania dziennych plików storage/logs/email-RRRR-MM-DD.log.
         */
        'log_days' => (int) env('LOG_DAILY_DAYS', 14),

        /*
         * Odmowa wysyłki bez szyfrowania: przy schemacie "smtp" (port 587) STARTTLS staje się
         * obowiązkowy zamiast „jeśli się da”. Schemat "smtps" (port 465) i tak szyfruje od początku.
         */
        'require_tls' => (bool) env('MAIL_REQUIRE_TLS', true),

    ],

    'locale' => [

        /*
         * Waluta (kod ISO 4217) dla local_money(), gdy wywołanie nie poda własnej.
         */
        'currency' => env('APP_CURRENCY', 'PLN'),

    ],

];
