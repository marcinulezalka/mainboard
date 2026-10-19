# simplysmart

Modułowa płyta główna (mainboard) dla aplikacji simplysmart: podłączanie modułów, przestrzenie robocze do zarządzania i więcej.

Paczka Composer: `simplysmart/mainboard`, namespace `Simplysmart\Mainboard\`.

> Projekt na wczesnym etapie: `public/index.php` to na razie placeholder wygenerowany przez
> [Smartboard](https://smartboard.local).

## Wymagania

- PHP ^8.4
- Laravel 13 (`illuminate/support` ^13.0); provider `Simplysmart\Mainboard\MainboardServiceProvider` rejestruje się sam (auto-discovery)
- Pakiety zewnętrzne: zob. [docs/zaleznosci.md](docs/zaleznosci.md)

## Dokumentacja

| Dokument | Zawartość |
| --- | --- |
| [Struktura projektu](docs/struktura.md) | katalogi i ich rola |
| [Wdrożenie](docs/wdrozenie.md) | hosting współdzielony: co wysyłać, a czego nie |
| [Pakiety zewnętrzne](docs/zaleznosci.md) | zależności Composer, licencje, zasady dodawania |
| [Marka](brand/README.md) | logo, sygnet, kolory, zasady użycia |
| [CONTRIBUTING.md](CONTRIBUTING.md) | zasady rozwoju i wydawania wersji |
| [CHANGELOG.md](CHANGELOG.md) | historia zmian |

Pełny spis: [docs/README.md](docs/README.md).

## Licencja

[MIT](LICENSE) — © 2026 Marcin Ulezalka. Możesz używać kodu również komercyjnie, pod warunkiem
dołączenia informacji o prawach autorskich i treści licencji. Wiążąca jest angielska treść pliku
[LICENSE](LICENSE).
