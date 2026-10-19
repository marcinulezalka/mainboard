#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Skrypt wydania wersji zgodnej z SemVer, niezależny od frameworka.
 *
 * Jedyna implementacja reguły podbijania wersji — plik jest kopiowany bez zmian
 * do każdego projektu tworzonego przez Smartboard, gdzie Laravel/artisan nie musi
 * istnieć, dlatego nie ma żadnych zależności poza PHP.
 *
 * Czyta wpisy z sekcji „## [Unreleased]” w CHANGELOG.md (format Keep a Changelog),
 * ustala wymagane podbicie wersji i — o ile nie jest to MAJOR bez --major-confirm —
 * zapisuje nową wersję do VERSION oraz zamienia „[Unreleased]” na datowaną sekcję
 * wydania, z nową pustą sekcją „[Unreleased]” nad nią.
 *
 * Reguła podbicia (wygrywa najwyższa wśród niepustych sekcji):
 * - MAJOR — sekcja „Removed” albo wpis oznaczony „**BREAKING**” / „BREAKING:”
 *   na początku linii (np. „- **BREAKING** Usunięto parametr X”),
 * - MINOR — „Added”, „Deprecated”,
 * - PATCH — „Changed”, „Fixed”, „Security”.
 *
 * Data wydania liczona jest w strefie RELEASE_TIMEZONE (zmienna środowiskowa),
 * domyślnie Europe/Warsaw.
 *
 * Użycie: php tools/version-release.php [--major-confirm]
 */

/**
 * Wyjątek oznaczający sytuację, w której wydanie nie może zostać wykonane.
 *
 * Komunikat jest przeznaczony dla użytkownika i trafia bezpośrednio na STDERR.
 */
final class ReleaseException extends RuntimeException {}

/**
 * Rodzaj podbicia wersji wraz z mapowaniem sekcji Keep a Changelog na to podbicie.
 */
enum ReleaseBump: string
{
    case Patch = 'patch';
    case Minor = 'minor';
    case Major = 'major';

    /**
     * Sekcje Keep a Changelog i podbicie, którego wymaga każdy wpis w danej sekcji.
     *
     * @var array<string, ReleaseBump>
     */
    public const array SECTIONS = [
        'Added' => self::Minor,
        'Changed' => self::Patch,
        'Deprecated' => self::Minor,
        'Removed' => self::Major,
        'Fixed' => self::Patch,
        'Security' => self::Patch,
    ];

    /**
     * Zwraca podbicie wymagane przez sekcję o podanym tytule.
     *
     * @param  string  $title  Tytuł nagłówka „###”, np. „Added”.
     * @return ReleaseBump|null Podbicie albo null, gdy sekcja nie należy do Keep a Changelog.
     */
    public static function forSection(string $title): ?self
    {
        return self::SECTIONS[$title] ?? null;
    }

    /**
     * Zwraca wyższe z dwóch podbić; null traktowane jest jako brak podbicia.
     *
     * @param  ReleaseBump|null  $other  Podbicie do porównania.
     * @return ReleaseBump Wyższe podbicie.
     */
    public function max(?self $other): self
    {
        return $other !== null && $other->rank() > $this->rank() ? $other : $this;
    }

    /**
     * Waga podbicia używana do porównań.
     */
    private function rank(): int
    {
        return match ($this) {
            self::Patch => 1,
            self::Minor => 2,
            self::Major => 3,
        };
    }
}

/**
 * Wersja w formacie MAJOR.MINOR.PATCH (bez prefiksu „v” i sufiksów pre-release).
 */
final readonly class SemanticVersion
{
    private const string PATTERN = '/^(0|[1-9]\d*)\.(0|[1-9]\d*)\.(0|[1-9]\d*)$/';

    public function __construct(
        public int $major,
        public int $minor,
        public int $patch,
    ) {}

    /**
     * Tworzy wersję z tekstu, np. zawartości pliku VERSION.
     *
     * @param  string  $value  Tekst wersji, np. „1.4.0”.
     * @return SemanticVersion Odczytana wersja.
     *
     * @throws ReleaseException Gdy tekst nie jest poprawną wersją MAJOR.MINOR.PATCH.
     */
    public static function parse(string $value): self
    {
        if (! preg_match(self::PATTERN, $value, $matches)) {
            throw new ReleaseException(
                "Plik VERSION zawiera „{$value}” — oczekiwano formatu MAJOR.MINOR.PATCH (np. 1.4.0), bez prefiksu „v” i sufiksów pre-release."
            );
        }

        return new self((int) $matches[1], (int) $matches[2], (int) $matches[3]);
    }

    /**
     * Zwraca nową wersję podbitą o wskazany poziom.
     *
     * @param  ReleaseBump  $bump  Poziom podbicia.
     * @return SemanticVersion Nowa wersja; niższe składowe są zerowane.
     */
    public function bump(ReleaseBump $bump): self
    {
        return match ($bump) {
            ReleaseBump::Major => new self($this->major + 1, 0, 0),
            ReleaseBump::Minor => new self($this->major, $this->minor + 1, 0),
            ReleaseBump::Patch => new self($this->major, $this->minor, $this->patch + 1),
        };
    }

    public function __toString(): string
    {
        return "{$this->major}.{$this->minor}.{$this->patch}";
    }
}

/**
 * Wynik zaplanowanego wydania: poprzednia i nowa wersja oraz nowa treść CHANGELOG.md.
 */
final readonly class ReleasePlan
{
    public function __construct(
        public SemanticVersion $from,
        public SemanticVersion $to,
        public ReleaseBump $bump,
        public string $changelog,
    ) {}
}

const RELEASE_UNRELEASED_TITLE = '[Unreleased]';
const RELEASE_DEFAULT_TIMEZONE = 'Europe/Warsaw';
const RELEASE_BREAKING_PATTERN = '/^[ \t]*[-*][ \t]+(?:\*\*BREAKING\*\*|BREAKING(?: CHANGE)?:)/m';

/**
 * Buduje wyrażenie regularne dopasowujące linię nagłówka Markdown.
 *
 * @param  int  $level  Poziom nagłówka (2 dla „##”, 3 dla „###”).
 * @param  string  $titlePattern  Fragment regex dopasowujący tytuł (może zawierać grupę przechwytującą).
 * @return string Wyrażenie regularne w trybie wielolinijkowym; koniec linii (LF lub CRLF)
 *                nie wchodzi w dopasowanie, więc podmiana nagłówka go zachowuje.
 */
function heading_pattern(int $level, string $titlePattern): string
{
    return '/^'.str_repeat('#', $level).'[ \t]+'.$titlePattern.'[ \t]*(?=\r?$)/m';
}

/**
 * Dzieli tekst Markdown według nagłówków danego poziomu.
 *
 * @param  string  $markdown  Dzielony tekst.
 * @param  int  $level  Poziom nagłówka (2 dla „##”, 3 dla „###”).
 * @return array{0: string, 1: array<string, string>} Tekst przed pierwszym nagłówkiem
 *                                                    oraz mapa tytuł nagłówka → treść pod nim.
 */
function split_by_heading(string $markdown, int $level): array
{
    $parts = preg_split(heading_pattern($level, '(.+?)'), $markdown, -1, PREG_SPLIT_DELIM_CAPTURE);
    $preamble = (string) array_shift($parts);
    $sections = [];

    foreach (array_chunk($parts, 2) as [$title, $body]) {
        $sections[$title] = ($sections[$title] ?? '').$body;
    }

    return [$preamble, $sections];
}

/**
 * Zwraca treść sekcji „## [Unreleased]”.
 *
 * @param  string  $changelog  Pełna treść CHANGELOG.md.
 * @return string Treść między nagłówkiem „## [Unreleased]” a kolejnym nagłówkiem „##”.
 *
 * @throws ReleaseException Gdy w changelogu brakuje sekcji „## [Unreleased]”.
 */
function extract_unreleased(string $changelog): string
{
    [, $sections] = split_by_heading($changelog, 2);

    return $sections[RELEASE_UNRELEASED_TITLE]
        ?? throw new ReleaseException('W CHANGELOG.md brakuje sekcji „## '.RELEASE_UNRELEASED_TITLE.'”.');
}

/**
 * Ustala podbicie wersji wymagane przez wpisy z sekcji „[Unreleased]”.
 *
 * @param  string  $unreleased  Treść sekcji „[Unreleased]”.
 * @return ReleaseBump|null Najwyższe wymagane podbicie albo null, gdy żadna sekcja nie ma wpisów.
 *
 * @throws ReleaseException Gdy wpisy leżą poza nagłówkiem „###” albo nagłówek nie należy do Keep a Changelog.
 */
function decide_bump(string $unreleased): ?ReleaseBump
{
    [$loose, $sections] = split_by_heading($unreleased, 3);

    if (trim($loose) !== '') {
        throw new ReleaseException('Wpisy w „'.RELEASE_UNRELEASED_TITLE.'” muszą leżeć pod nagłówkiem „### <sekcja>”.');
    }

    $bump = null;

    foreach ($sections as $title => $body) {
        $sectionBump = ReleaseBump::forSection($title) ?? throw new ReleaseException(
            "Nieznana sekcja „### {$title}”. Dozwolone: ".implode(', ', array_keys(ReleaseBump::SECTIONS)).'.'
        );

        if (trim($body) === '') {
            continue;
        }

        $entryBump = preg_match(RELEASE_BREAKING_PATTERN, $body) === 1 ? ReleaseBump::Major : $sectionBump;
        $bump = $entryBump->max($bump);
    }

    return $bump;
}

/**
 * Zamienia „## [Unreleased]” na datowaną sekcję wydania z nową, pustą sekcją „[Unreleased]” nad nią.
 *
 * @param  string  $changelog  Pełna treść CHANGELOG.md.
 * @param  SemanticVersion  $version  Wydawana wersja.
 * @param  DateTimeImmutable  $releasedAt  Moment wydania (liczy się data).
 * @return string Nowa treść CHANGELOG.md, z zachowanym stylem końców linii.
 *
 * @throws ReleaseException Gdy podmiana nagłówka się nie powiodła.
 */
function promote_unreleased(string $changelog, SemanticVersion $version, DateTimeImmutable $releasedAt): string
{
    $eol = str_contains($changelog, "\r\n") ? "\r\n" : "\n";
    $heading = '## '.RELEASE_UNRELEASED_TITLE;

    $promoted = preg_replace(
        heading_pattern(2, preg_quote(RELEASE_UNRELEASED_TITLE, '/')),
        $heading.$eol.$eol."## [{$version}] - {$releasedAt->format('Y-m-d')}",
        $changelog,
        1,
    );

    return $promoted ?? throw new ReleaseException('Nie udało się przenieść wpisów z „'.RELEASE_UNRELEASED_TITLE.'”.');
}

/**
 * Planuje wydanie na podstawie treści changelogu i bieżącej wersji — bez żadnych operacji wejścia/wyjścia.
 *
 * @param  string  $changelog  Pełna treść CHANGELOG.md.
 * @param  string  $currentVersion  Bieżąca wersja (zawartość pliku VERSION bez białych znaków).
 * @param  DateTimeImmutable  $releasedAt  Moment wydania.
 * @param  bool  $majorConfirmed  Czy użytkownik potwierdził podbicie MAJOR.
 * @return ReleasePlan|null Plan wydania albo null, gdy w „[Unreleased]” nie ma żadnych wpisów.
 *
 * @throws ReleaseException Gdy wersja lub changelog są niepoprawne albo podbicie MAJOR nie zostało potwierdzone.
 */
function plan_release(string $changelog, string $currentVersion, DateTimeImmutable $releasedAt, bool $majorConfirmed): ?ReleasePlan
{
    $from = SemanticVersion::parse($currentVersion);
    $bump = decide_bump(extract_unreleased($changelog));

    if ($bump === null) {
        return null;
    }

    if ($bump === ReleaseBump::Major && ! $majorConfirmed) {
        throw new ReleaseException('Wykryto breaking change (bump MAJOR). Uruchom z --major-confirm, aby zastosować.');
    }

    $to = $from->bump($bump);

    return new ReleasePlan($from, $to, $bump, promote_unreleased($changelog, $to, $releasedAt));
}

/**
 * Zwraca bieżący moment w strefie czasowej wydań.
 *
 * @return DateTimeImmutable Teraz, w strefie z RELEASE_TIMEZONE albo domyślnej Europe/Warsaw.
 *
 * @throws ReleaseException Gdy RELEASE_TIMEZONE zawiera nieznaną strefę czasową.
 */
function release_now(): DateTimeImmutable
{
    $timezone = getenv('RELEASE_TIMEZONE') ?: RELEASE_DEFAULT_TIMEZONE;

    try {
        return new DateTimeImmutable('now', new DateTimeZone($timezone));
    } catch (Exception) {
        throw new ReleaseException("Nieznana strefa czasowa w RELEASE_TIMEZONE: „{$timezone}”.");
    }
}

/**
 * Odczytuje plik tekstowy.
 *
 * @param  string  $path  Ścieżka do pliku.
 * @return string Zawartość pliku.
 *
 * @throws ReleaseException Gdy pliku nie ma albo nie da się go odczytać.
 */
function read_file(string $path): string
{
    $contents = is_file($path) ? file_get_contents($path) : false;

    return $contents !== false ? $contents : throw new ReleaseException("Nie można odczytać pliku {$path}.");
}

/**
 * Zapisuje kilka plików tak, aby żaden nie został zapisany częściowo.
 *
 * Najpierw zapisuje wszystkie treści do plików tymczasowych obok docelowych,
 * a dopiero gdy wszystkie się udadzą, podmienia je atomowym rename().
 *
 * @param  array<string, string>  $contents  Mapa ścieżka → nowa treść pliku.
 *
 * @throws ReleaseException Gdy zapis lub podmiana któregokolwiek pliku się nie powiodły.
 */
function write_files_atomically(array $contents): void
{
    $temporary = [];

    foreach ($contents as $path => $content) {
        $temporaryPath = $path.'.release-'.getmypid();

        if (file_put_contents($temporaryPath, $content) === false) {
            array_map('unlink', $temporary);

            throw new ReleaseException("Nie można zapisać pliku {$path}.");
        }

        if (is_file($path)) {
            chmod($temporaryPath, fileperms($path) & 0777);
        }

        $temporary[$path] = $temporaryPath;
    }

    foreach ($temporary as $path => $temporaryPath) {
        if (! rename($temporaryPath, $path)) {
            throw new ReleaseException("Nie można podmienić pliku {$path}.");
        }
    }
}

/**
 * Wykonuje wydanie w katalogu projektu: czyta CHANGELOG.md i VERSION, planuje wydanie i zapisuje wynik.
 *
 * @param  string  $projectRoot  Katalog główny projektu z plikami CHANGELOG.md i VERSION.
 * @param  bool  $majorConfirmed  Czy użytkownik potwierdził podbicie MAJOR.
 * @param  DateTimeImmutable|null  $releasedAt  Moment wydania; null oznacza teraz (zob. release_now()).
 * @return array{0: int, 1: string} Kod wyjścia procesu i komunikat dla użytkownika.
 */
function version_release(string $projectRoot, bool $majorConfirmed, ?DateTimeImmutable $releasedAt = null): array
{
    $changelogPath = "{$projectRoot}/CHANGELOG.md";
    $versionPath = "{$projectRoot}/VERSION";

    try {
        $plan = plan_release(
            changelog: read_file($changelogPath),
            currentVersion: trim(read_file($versionPath)),
            releasedAt: $releasedAt ?? release_now(),
            majorConfirmed: $majorConfirmed,
        );

        if ($plan === null) {
            return [0, 'Sekcja '.RELEASE_UNRELEASED_TITLE." nie ma wpisów — nic do wydania.\n"];
        }

        write_files_atomically([
            $changelogPath => $plan->changelog,
            $versionPath => $plan->to."\n",
        ]);

        return [0, "Wersja {$plan->from} → {$plan->to} ({$plan->bump->value}).\n"];
    } catch (ReleaseException $exception) {
        return [1, $exception->getMessage()."\n"];
    }
}

if (PHP_SAPI === 'cli' && realpath($argv[0] ?? '') === __FILE__) {
    $options = array_slice($argv, 1);
    $unknown = array_diff($options, ['--major-confirm']);

    [$exitCode, $message] = $unknown !== []
        ? [1, 'Nieznana opcja: '.implode(' ', $unknown).". Użycie: php tools/version-release.php [--major-confirm]\n"]
        : version_release(
            projectRoot: dirname(__DIR__),
            majorConfirmed: in_array('--major-confirm', $options, true),
        );

    fwrite($exitCode === 0 ? STDOUT : STDERR, $message);

    exit($exitCode);
}
