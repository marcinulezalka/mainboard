# Struktura projektu

```
simplysmart/
├── brand/               źródła marki: logo, sygnet, kolory (zob. brand/README.md)
├── public/              document root, punkt wejścia: public/index.php, assety (zob. niżej)
├── src/                 kod paczki, namespace Simplysmart\Mainboard\ (PSR-4)
├── tools/               narzędzia deweloperskie, np. version-release.php
├── docs/                dokumentacja
├── .claude/skills/      skille AI (Claude Code, GitHub Copilot)
├── CHANGELOG.md         historia zmian (Keep a Changelog)
├── CONTRIBUTING.md      zasady rozwoju i wydawania wersji
├── VERSION              bieżąca wersja (SemVer), aktualizowana przez tools/version-release.php
├── composer.json        metadane paczki simplysmart/mainboard, autoload PSR-4
└── LICENSE              licencja MIT
```

## Dlaczego `tools/`, a nie `scripts/` lub `bin/`

Na hostingach współdzielonych nazwy `bin`, `cgi-bin` i `scripts` bywają zarezerwowane przez
panel albo blokowane przez reguły WAF. `tools/` nie koliduje z żadnym popularnym panelem.

## Assety

Źródła marki (`brand/`) i assety serwowane przeglądarce (`public/`) są rozdzielone: `brand/`
trzyma wzorce w pełnej rozdzielczości, `public/` tylko zoptymalizowane pochodne.

```
public/
├── favicon.ico, favicon.svg     favicony w katalogu głównym: przeglądarki szukają ich bez <link>
├── apple-touch-icon.png
├── site.webmanifest
└── assets/
    ├── css/                     tokens.css → base.css → app.css
    ├── js/
    ├── img/
    │   ├── brand/               logo.svg (currentColor), ikony z manifestu
    │   └── ui/                  ikony i ilustracje interfejsu
    └── fonts/                   tylko woff2
```

Katalogi bez plików (`js/`, `img/ui/`, `fonts/`) zakładamy przy pierwszym pliku.

- **CSS bez bundlera.** Wartości marki (kolory, fonty, odstępy) są wyłącznie w `tokens.css`,
  komponenty używają zmiennych semantycznych (`--ss-color-text`), więc motyw ciemny zmienia się
  w jednym miejscu. Arkusze linkujemy osobno w `<head>`, bez `@import`.
- **Cache busting.** Adresy assetów dostają `?v=` z pliku `VERSION`, więc po wydaniu wersji
  przeglądarki pobierają nowe pliki.
- **Bundler w przyszłości.** Gdy dojdzie Vite lub esbuild, źródła trafią do `resources/`,
  a wynik builda do `public/assets/build/`.

## Co trafia na serwer

Tylko kod aplikacji i `public/`. `brand/`, `tools/`, `docs/` i `.claude/` mają w `.gitattributes` regułę `export-ignore`.
Szczegóły: [wdrozenie.md](wdrozenie.md).
