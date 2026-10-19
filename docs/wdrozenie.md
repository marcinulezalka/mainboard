# Wdrożenie — hosting współdzielony

## DocumentRoot

Jeśli panel na to pozwala, ustaw katalog domeny na `public/`. Wtedy reszta projektu nie jest
dostępna z sieci.

Jeśli panel wymusza stały katalog (np. `public_html/`), wyślij do niego **tylko zawartość
`public/`**, a resztę kodu aplikacji umieść poziomem wyżej, poza katalogiem publicznym.

## Czego nie wysyłać na serwer

| Ścieżka | Powód |
| --- | --- |
| `brand/` | źródła marki; serwis używa kopii z `public/` |
| `docs/` | dokumentacja |
| `.claude/`, `.git/` | skille AI i historia repozytorium |
| `.env*` | dane środowiska. Na serwerze tworzy się osobny plik |

## Paczka przez `git archive`

`brand/`, `docs/` i `.claude/` mają w `.gitattributes` regułę `export-ignore`, więc paczka ich nie zawiera:

```bash
git archive --format=zip -o mainboard.zip HEAD
```

## Wdrożenie przez FTP

Klient FTP nie czyta `.gitattributes`. Dodaj ścieżki z tabeli powyżej do listy wykluczeń
w kliencie (np. filtry w FileZilla) albo w skrypcie wdrożeniowym.
