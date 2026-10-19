# Contributing — simplysmart

Zasady rozwijania projektu i wydawania kolejnych wersji.

## Pakiety zewnętrzne

Zanim dodasz zależność do `composer.json`, sprawdź zasady w
[docs/zaleznosci.md](docs/zaleznosci.md): licencja zgodna z MIT, `require` / `require-dev`, `composer audit`.

## Wydanie wersji

Wpisy zmian dodawaj w `CHANGELOG.md` pod `## [Unreleased]`, w sekcjach
`### Added`, `### Changed`, `### Deprecated`, `### Removed`, `### Fixed`, `### Security`.
Zmianę łamiącą oznacz na początku wpisu: `- **BREAKING** …`.

```bash
php tools/version-release.php                  # PATCH / MINOR
php tools/version-release.php --major-confirm  # gdy wymagany MAJOR (Removed lub **BREAKING**)
```

Skrypt podbija `VERSION` i zamienia `[Unreleased]` na datowaną sekcję wydania.
Reguła: `Removed` lub `**BREAKING**` → MAJOR, `Added` / `Deprecated` → MINOR,
`Changed` / `Fixed` / `Security` → PATCH. Data w strefie `RELEASE_TIMEZONE`
(domyślnie `Europe/Warsaw`).
