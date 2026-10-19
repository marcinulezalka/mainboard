# Changelog

Format oparty na [Keep a Changelog](https://keepachangelog.com/pl/1.1.0/).

## [Unreleased]

### Added

- `CONTRIBUTING.md` z zasadami wpisów w `CHANGELOG.md` i wydawania wersji.
- Licencja MIT: plik `LICENSE` i sekcja „Licencja” w README.
- Dokumentacja w `docs/`: struktura projektu, wdrożenie na hosting współdzielony oraz pakiety zewnętrzne (aktualne zależności, licencje, zasady dodawania).
- `composer.json` paczki `simplysmart/mainboard` (PHP ^8.4, bez zależności, autoload PSR-4: `Simplysmart\Mainboard\` → `src/`), `.gitignore` wykluczający pliki środowiska lokalnego (`.env*`, cały katalog `infrastructure/` z konfiguracją vhosta, PHP-FPM i SSL, katalogi IDE), `.editorconfig` i `.gitattributes`.
- Skille AI w jednym miejscu, `.claude/skills/`, wersjonowane w repozytorium; `.gitignore` pomija tylko lokalne pliki sesji AI: `.claude/settings.local.json` i `CLAUDE.local.md`.
- Wpis w `### Changed` oznaczony `**BREAKING**` (lub `BREAKING:`) na początku linii wymusza podbicie MAJOR w `tools/version-release.php`.
- Identyfikacja marki w `brand/`: logo (SVG i PNG na jasne i ciemne tło), sygnet „S” w stylu kroju Mole (PNG z przezroczystym tłem, na jasne i ciemne tło), kolory i zasady użycia w `brand/README.md`; katalog wykluczony z paczek (`export-ignore`).
- Struktura assetów w `public/assets/` (CSS, grafiki, fonty) i style oparte na tokenach marki w `tokens.css`, z motywem ciemnym.
- Strona startowa pokazuje logo i korzysta ze stylów marki; adresy assetów zawierają wersję z pliku `VERSION`, więc po wydaniu przeglądarki pobierają nowe pliki.
- Strefa czasowa daty wydania w `tools/version-release.php` ustawiana zmienną `RELEASE_TIMEZONE` (domyślnie `Europe/Warsaw`).

### Changed

- Standardy kodu: PSR-4 i PER Coding Style pilnowany przez Laravel Pint z presetem `per` (`pint.json`, `composer lint` / `composer format`); `laravel/pint` w `require-dev`.
- Mainboard jest paczką, a nie projektem: usunięto katalog `infrastructure/` (vhost Apache, pula PHP-FPM, certyfikaty SSL) i domenę lokalną `mainboard.local`; pakiet rozwijany jest w `packages/simplysmart/mainboard` projektu głównego i podpinany przez repozytorium Composera typu `path`.
- Wersję pakietu wydaje wspólny przybornik `simplysmart/tools` (https://github.com/marcinulezalka/tools) z projektu głównego: `vendor/bin/version-release --root=packages/simplysmart/mainboard`; własny katalog `tools/` pakietu usunięty.
- Instrukcja wydawania wersji przeniesiona do `CONTRIBUTING.md`; plik `USAGE.md` (jednorazowa aktywacja vhosta i nieaktualne kroki wdrożenia) usunięty.
- Katalog `scripts/` przemianowany na `tools/` (nazwy `bin`/`scripts` bywają zarezerwowane lub blokowane na hostingach współdzielonych); `.gitattributes` wyklucza `tools/` z paczek (`export-ignore`).
- README skrócone do opisu, wymagań, szybkiego startu i spisu dokumentacji; szczegóły przeniesione do `docs/`, które `.gitattributes` również wyklucza z paczek.
- `tools/version-release.php`: sekcja `### Deprecated` podbija MINOR zamiast PATCH (zgodnie z SemVer).
- Repozytorium przemianowane z `simplysmart` na `mainboard` (`https://github.com/marcinulezalka/mainboard`): adresy w `composer.json`, katalog projektu `mainboard/`, domena lokalna `mainboard.local`, paczka `git archive` jako `mainboard.zip`.

### Fixed

- `tools/version-release.php` nie tworzy pustego wydania, gdy `[Unreleased]` ma same puste nagłówki.
- `tools/version-release.php` odrzuca niepoprawny plik `VERSION` (np. `v1.2.3`, `1.2.3-beta.1`) czytelnym komunikatem — wcześniej `v1.2.3` po cichu stawało się `0.2.4`, a sufiks pre-release kończył się błędem krytycznym PHP.
- `tools/version-release.php` nie traktuje każdego wystąpienia słowa „breaking” (np. „line-breaking”) jako zmiany łamiącej.
- `tools/version-release.php` odrzuca nieznane sekcje (`### Dodano`) i wpisy bez nagłówka zamiast liczyć je jako PATCH, zgłasza brak sekcji `[Unreleased]` i nieznane opcje.
- `tools/version-release.php` zapisuje `VERSION` i `CHANGELOG.md` atomowo i zachowuje końce linii CRLF.
- Nazwa projektu „simplysmart” pisana małą literą w README i na stronie startowej.

## [0.1.0] - 2026-10-06

### Added

- `CONTRIBUTING.md` z zasadami wpisów w `CHANGELOG.md` i wydawania wersji.
- Initial scaffold wygenerowany przez Smartboard (2026-10-06).
