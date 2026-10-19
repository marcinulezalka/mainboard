# Pakiety zewnętrzne (third-party)

## Aktualne zależności

| Pakiet | Wersja | Licencja | Sekcja | Do czego |
| --- | --- | --- | --- | --- |
| `laravel/pint` | `^1.27` | MIT | `require-dev` | formatowanie kodu w stylu PER Coding Style (`composer lint` / `composer format`) |

Stan sprawdzisz poleceniem:

```bash
composer licenses   # pakiety z vendor/ i ich licencje
composer show -t    # drzewo zależności
```

## Zasady dodawania pakietu

1. **Tylko przez Composer.** Nie kopiuj cudzego kodu do repozytorium. Jeśli to konieczne,
   opisz go w sekcji [Kod skopiowany do repozytorium](#kod-skopiowany-do-repozytorium).
2. **Licencja zgodna z MIT.** Dozwolone są MIT, BSD-2/3-Clause, Apache-2.0 i ISC.
   Licencje copyleft (GPL, AGPL, LGPL) narzuciłyby swoje warunki użytkownikom paczki, więc wymagają
   osobnej decyzji.
3. **Właściwa sekcja.** `require` dla kodu potrzebnego w działaniu, `require-dev` dla narzędzi
   (testy, analiza statyczna, formatowanie).
4. **Zakres wersji z `^`** (np. `^11.0`). To paczka biblioteczna, więc `composer.lock` nie jest
   w repozytorium (`.gitignore`), a zbyt wąski zakres blokowałby projekty, które ją instalują.
5. **Bezpieczeństwo.** Po dodaniu lub aktualizacji uruchom `composer audit`.
6. **Dokumentacja.** Dopisz wiersz do tabeli powyżej i wpis w `CHANGELOG.md`: nowy pakiet w
   `### Added`, a zmiana wymaganej wersji w `### Changed`.

## Licencje i informacja o autorach

Pakiety instalowane przez Composer mają własne pliki licencji w `vendor/` i nie są częścią
repozytorium, więc nie trzeba dołączać ich treści.

## Kod skopiowany do repozytorium

Brak. Każdy fragment cudzego kodu wklejony do projektu trzeba tu wpisać: źródło, autor,
licencja i ścieżka w projekcie. Plik musi też zachować oryginalną informację o prawach
autorskich.
