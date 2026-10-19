# Struktura projektu

```
simplysmart/
├── public/              document root, punkt wejścia: public/index.php
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

## Co trafia na serwer

Tylko kod aplikacji. `tools/`, `docs/` i `.claude/` mają w `.gitattributes` regułę `export-ignore`.
Szczegóły: [wdrozenie.md](wdrozenie.md).
