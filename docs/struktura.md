# Struktura projektu

```
mainboard/
├── brand/               źródła marki: logo, sygnet, kolory (zob. brand/README.md)
├── public/              document root, punkt wejścia: public/index.php, assety (zob. niżej)
├── src/                 kod paczki, namespace Simplysmart\Mainboard\ (PSR-4)
├── docs/                dokumentacja
├── .claude/skills/      skille AI (Claude Code, GitHub Copilot)
├── CHANGELOG.md         historia zmian (Keep a Changelog)
├── CONTRIBUTING.md      zasady rozwoju i wydawania wersji
├── VERSION              bieżąca wersja (SemVer), aktualizowana przez simplysmart/tools (zob. CONTRIBUTING.md)
├── composer.json        metadane paczki simplysmart/mainboard, autoload PSR-4
└── LICENSE              licencja MIT
```

## Assety

Źródła marki (`brand/`) i assety serwowane przeglądarce (`public/`) są rozdzielone: `brand/`
trzyma wzorce w pełnej rozdzielczości, `public/` tylko zoptymalizowane pochodne.

```
public/
└── assets/
    ├── css/                     tokens.css → base.css → app.css
    ├── js/
    ├── img/
    │   ├── brand/               logo.svg (currentColor)
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

Tylko kod aplikacji i `public/`. `brand/`, `docs/` i `.claude/` mają w `.gitattributes` regułę `export-ignore`.
Szczegóły: [wdrozenie.md](wdrozenie.md).
