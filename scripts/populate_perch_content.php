#!/usr/bin/env php
<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This script must be run from the command line." . PHP_EOL);
    exit(1);
}

$argvCopy = $argv;
array_shift($argvCopy);

$dryRun = false;
$pageFilter = null;

foreach ($argvCopy as $arg) {
    if ($arg === '--dry-run') {
        $dryRun = true;
        continue;
    }

    if (str_starts_with_custom($arg, '--page=')) {
        $pageFilter = substr($arg, 7);
        if ($pageFilter === '') {
            $pageFilter = null;
        }
        continue;
    }
}

$rootPath = realpath(__DIR__ . '/..');

if ($rootPath === false) {
    fwrite(STDERR, "Unable to resolve project root path." . PHP_EOL);
    exit(1);
}

if (!$dryRun) {
    bootstrap_server_environment($rootPath);

    $runtimePath = $rootPath . '/perch/runtime.php';
    if (!file_exists($runtimePath)) {
        fwrite(STDERR, "Perch runtime not found at {$runtimePath}." . PHP_EOL);
        exit(1);
    }

    require_once $runtimePath;
}

$pages = [
    [
        'page_path'     => '/index.php',
        'template_path' => $rootPath . '/perch/templates/pages/index.php',
    ],
    [
        'page_path'     => '/company/about-us.php',
        'template_path' => $rootPath . '/perch/templates/pages/company/about-us.php',
    ],
    [
        'page_path'     => '/company/contact-us.php',
        'template_path' => $rootPath . '/perch/templates/pages/company/contact-us.php',
    ],
];

if ($pageFilter !== null) {
    $pages = array_values(array_filter($pages, static function (array $page) use ($pageFilter): bool {
        return $page['page_path'] === $pageFilter;
    }));

    if (empty($pages)) {
        fwrite(STDERR, "No templates matched the provided page filter: {$pageFilter}" . PHP_EOL);
        exit(1);
    }
}

$summary = [
    'seeded' => 0,
    'skipped' => 0,
    'missing' => 0,
];

foreach ($pages as $page) {
    $templatePath = $page['template_path'];
    if (!file_exists($templatePath)) {
        fwrite(STDERR, "Template not found: {$templatePath}" . PHP_EOL);
        $summary['missing']++;
        continue;
    }

    $regions = extract_regions_with_comments($templatePath);

    if (empty($regions)) {
        fwrite(STDOUT, "No Perch regions detected in {$templatePath}." . PHP_EOL);
        continue;
    }

    fwrite(STDOUT, PHP_EOL . "Processing {$page['page_path']} ({$templatePath})" . PHP_EOL);

    foreach ($regions as $regionKey => $comment) {
        $defaultContent = normalise_comment_text($comment);

        if ($defaultContent === '') {
            fwrite(STDOUT, "  - Skipping {$regionKey}: no default content detected" . PHP_EOL);
            $summary['missing']++;
            continue;
        }

        $template = select_template_for_region($regionKey);

        if ($dryRun) {
            fwrite(STDOUT, "  - [Dry run] Would seed {$regionKey} using {$template}" . PHP_EOL);
            $summary['skipped']++;
            continue;
        }

        $result = seed_region_with_default($page['page_path'], $regionKey, $defaultContent, $template);
        if ($result === true) {
            $summary['seeded']++;
        } else {
            $summary['skipped']++;
        }
    }
}

fwrite(STDOUT, PHP_EOL . sprintf(
    "Seeding complete: %d seeded, %d skipped, %d missing defaults." . PHP_EOL,
    $summary['seeded'],
    $summary['skipped'],
    $summary['missing']
));

exit(0);

function bootstrap_server_environment(string $rootPath): void
{
    $_SERVER['DOCUMENT_ROOT'] = $_SERVER['DOCUMENT_ROOT'] ?? $rootPath;
    $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $_SERVER['SERVER_NAME'] = $_SERVER['SERVER_NAME'] ?? 'localhost';
    $_SERVER['SERVER_PROTOCOL'] = $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1';
    $_SERVER['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $_SERVER['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? '/scripts/populate_perch_content.php';
    $_SERVER['SCRIPT_NAME'] = $_SERVER['SCRIPT_NAME'] ?? '/scripts/populate_perch_content.php';
    $_SERVER['SCRIPT_FILENAME'] = $_SERVER['SCRIPT_FILENAME'] ?? $rootPath . '/scripts/populate_perch_content.php';
}

function extract_regions_with_comments(string $templatePath): array
{
    $source = file_get_contents($templatePath);
    if ($source === false) {
        throw new RuntimeException("Unable to read template: {$templatePath}");
    }

    $regions = [];
    $needle = 'perch_content';
    $offset = 0;
    $length = strlen($needle);

    while (($position = strpos($source, $needle, $offset)) !== false) {
        if (is_commented_out($source, $position)) {
            $offset = $position + $length;
            continue;
        }

        $key = extract_region_key($source, $position + $length);
        if ($key === null) {
            $offset = $position + $length;
            continue;
        }

        $afterKeyPosition = find_after_key_position($source, $position + $length);
        if ($afterKeyPosition === null) {
            $offset = $position + $length;
            continue;
        }

        $commentData = locate_inline_comment($source, $afterKeyPosition);
        if ($commentData === null) {
            $offset = $position + $length;
            continue;
        }

        [$commentText, $commentEnd] = $commentData;

        if (!array_key_exists($key, $regions)) {
            $regions[$key] = $commentText;
        }

        $offset = $commentEnd;
    }

    return $regions;
}

function is_commented_out(string $source, int $position): bool
{
    $lineStart = strrpos(substr($source, 0, $position), "\n");
    $lineStart = ($lineStart === false) ? 0 : $lineStart + 1;
    $lineSegment = substr($source, $lineStart, $position - $lineStart);

    return strpos($lineSegment, '//') !== false || strpos($lineSegment, '/*') !== false;
}

function extract_region_key(string $source, int $position): ?string
{
    $openParen = strpos($source, '(', $position);
    if ($openParen === false) {
        return null;
    }

    $cursor = $openParen + 1;
    while (isset($source[$cursor]) && ctype_space($source[$cursor])) {
        $cursor++;
    }

    if (!isset($source[$cursor])) {
        return null;
    }

    $quote = $source[$cursor];
    if ($quote !== "'" && $quote !== '"') {
        return null;
    }

    $cursor++;
    $end = strpos($source, $quote, $cursor);
    if ($end === false) {
        return null;
    }

    return substr($source, $cursor, $end - $cursor);
}

function find_after_key_position(string $source, int $position): ?int
{
    $closing = strpos($source, ')', $position);
    if ($closing === false) {
        return null;
    }

    return $closing + 1;
}

function locate_inline_comment(string $source, int $startPosition): ?array
{
    $commentStart = strpos($source, '<!--', $startPosition);
    if ($commentStart === false) {
        return null;
    }

    $nextRegion = strpos($source, 'perch_content', $startPosition);
    if ($nextRegion !== false && $commentStart > $nextRegion) {
        return null;
    }

    $commentEnd = strpos($source, '-->', $commentStart);
    if ($commentEnd === false) {
        return null;
    }

    $commentText = substr($source, $commentStart + 4, $commentEnd - ($commentStart + 4));

    return [$commentText, $commentEnd + 3];
}

function normalise_comment_text(string $comment): string
{
    $decoded = html_entity_decode($comment, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $stripped = strip_tags($decoded);
    $condensed = preg_replace('/\s+/u', ' ', $stripped);

    return trim($condensed ?? '');
}

function select_template_for_region(string $regionKey): string
{
    $blockSuffixes = [
        '_description',
        '_note',
        '_copy',
        '_details',
        '_message',
        '_bio',
        '_paragraph',
        '_intro',
        '_body',
        '_notice',
    ];

    foreach ($blockSuffixes as $suffix) {
        if (ends_with_custom($regionKey, $suffix)) {
            return 'content/text_block.html';
        }
    }

    $blockFragments = [
        '_subheading',
        '_supporting',
        '_point_',
        '_bullet',
        '_summary',
    ];

    foreach ($blockFragments as $fragment) {
        if (contains_custom($regionKey, $fragment)) {
            return 'content/text_block.html';
        }
    }

    return 'content/text.html';
}

function seed_region_with_default(string $pagePath, string $regionKey, string $content, string $template): bool
{
    try {
        perch_content_create($regionKey, [
            'page'      => $pagePath,
            'template'  => $template,
            'multiple'  => false,
        ]);

        $Pages = new PerchContent_Pages();
        $Page = $Pages->find_or_create($pagePath);

        $Regions = new PerchContent_Regions();
        $Region = $Regions->find_for_page_by_key((int) $Page->id(), $regionKey);

        if (!$Region instanceof PerchContent_Region) {
            fwrite(STDOUT, "  - Failed to resolve region {$regionKey} on {$pagePath}" . PHP_EOL);
            return false;
        }

        $hasItems = $Region->get_item_count() > 0;
        $existingHTML = trim((string) $Region->regionHTML());
        $alreadyPopulated = ($existingHTML !== '' && strpos($existingHTML, 'Undefined content') === false);

        if ($hasItems || $alreadyPopulated) {
            fwrite(STDOUT, "  - Skipping {$regionKey}: region already populated" . PHP_EOL);
            return false;
        }

        if ((int) $Region->regionLatestRev() === 0) {
            $Region->create_new_revision();
        }

        if ($Region->regionTemplate() !== $template) {
            $Region->update(['regionTemplate' => $template]);
        }

        $Item = $Region->add_new_item();
        $Item->update([
            'itemJSON'   => PerchUtil::json_safe_encode(['text' => $content]),
            'itemSearch' => $content,
        ]);

        $Region->publish();
        $Region->index();
        $Region->update(['regionNew' => 0]);

        fwrite(STDOUT, "  - Seeded {$regionKey}" . PHP_EOL);

        return true;
    } catch (Throwable $exception) {
        fwrite(STDOUT, "  - Error seeding {$regionKey}: " . $exception->getMessage() . PHP_EOL);
        return false;
    }
}

function ends_with_custom(string $haystack, string $needle): bool
{
    if ($needle === '') {
        return true;
    }

    if (strlen($needle) > strlen($haystack)) {
        return false;
    }

    return substr($haystack, -strlen($needle)) === $needle;
}

function contains_custom(string $haystack, string $needle): bool
{
    if ($needle === '') {
        return true;
    }

    return strpos($haystack, $needle) !== false;
}

function str_starts_with_custom(string $haystack, string $needle): bool
{
    if ($needle === '') {
        return true;
    }

    if (strlen($needle) > strlen($haystack)) {
        return false;
    }

    return substr($haystack, 0, strlen($needle)) === $needle;
}
