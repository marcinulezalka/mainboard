# Marka simplysmart

Pliki źródłowe marki. Nie są serwowane ani wdrażane (`export-ignore`); do serwisu trafiają ich
kopie w `public/` — zob. [docs/struktura.md](../docs/struktura.md#assety).

## Zawartość

| Plik | Do czego |
| --- | --- |
| `logo/simplysmart.svg` | wzorzec logotypu (wektor, czarny). Z niego powstają wszystkie pozostałe wersje |
| `logo/png/simplysmart-on-light-{800,1600,2400}.png` | logo czarne, na jasne tło: maile, dokumenty, prezentacje |
| `logo/png/simplysmart-on-dark-{800,1600,2400}.png` | logo białe, na ciemne tło |
| `icon/simplysmart-mark.svg` | sygnet: drugie „s” z logotypu w kwadratowym kadrze, `fill="currentColor"` |
| `icon/simplysmart-icon.svg` | sygnet na czarnym zaokrąglonym kaflu — źródło faviconów |
| `icon/simplysmart-icon-1024.png` | ikona 1024×1024 (sklepy z aplikacjami, avatary) |

Nazwy `on-light` / `on-dark` mówią o **tle**, na którym logo stoi, nie o kolorze logo.

## Sygnet

Drugie „s” z logotypu (`simply`**`s`**`mart`). W logo nie ma własnego konturu: dolny łuk wyrasta
z „y”, górny przechodzi w „m”. Sygnet zachowuje tę cechę — oba łuki wychodzą poza krawędź kadru,
więc na kaflu litera wtapia się w tło strony, a czarne części kafla same układają się w „S”.
Kadr: prostokąt x 414,5–498,5, y 34–118 w układzie `logo/simplysmart.svg`.

## Kolory

| Nazwa | HEX | Token CSS |
| --- | --- | --- |
| Czerń | `#000000` | `--ss-black` |
| Biel | `#ffffff` | `--ss-white` |

Logo występuje tylko w czerni albo bieli — bez gradientów, cieni i innych kolorów. Pełna lista
tokenów (kolory semantyczne, typografia, odstępy): `public/assets/css/tokens.css`.

## Zasady użycia logo

- Na stronie używaj `public/assets/img/brand/logo.svg` osadzonego inline: ma `fill="currentColor"`,
  więc przyjmuje kolor tekstu i sam przełącza się w motywie ciemnym.
- PNG tylko tam, gdzie SVG nie działa (maile, dokumenty). Wybierz najmniejszy rozmiar, który
  w docelowym miejscu ma co najmniej 2× piksele szerokości wyświetlania.
- Wokół logo zostaw wolne pole co najmniej wysokości kropki nad „i”.
- Nie rozciągaj, nie obracaj i nie zmieniaj proporcji logo.

## Favicony

Pliki w `public/` (`favicon.svg`, `favicon.ico`, `apple-touch-icon.png`) i
`public/assets/img/brand/icon-{192,512}.png` są wygenerowane z sygnetu. Przy zmianie sygnetu
wygeneruj je ponownie z `icon/simplysmart-icon.svg`.
