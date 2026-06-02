<?php

/**
 * Seed Vietnamese CMS translations from English source rows.
 *
 * Usage:
 *   php scripts/seed_vi_from_en.php --dry-run
 *   php scripts/seed_vi_from_en.php --only-missing
 *   php scripts/seed_vi_from_en.php --force
 *   php scripts/seed_vi_from_en.php --no-api
 *
 * Default: create missing VI rows and repair corrupted VI (contains "??").
 */

declare(strict_types=1);

use App\Core\Database;
use App\Models\Translation;
use App\Services\ViSeedTranslator;

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
if (file_exists(BASE_PATH . '/.env')) {
    $dotenv->load();
} else {
    $dotenv->safeLoad();
}

$opts = getopt('', ['dry-run', 'only-missing', 'force', 'no-api', 'help']);

if (isset($opts['help'])) {
    echo <<<HELP
Seed Vietnamese translations from English (CMS *_translations tables).

Options:
  --dry-run       Preview changes without writing to DB
  --only-missing  Only insert VI rows that do not exist
  --force         Overwrite all existing VI rows
  --no-api        Dictionary/maps only (no MyMemory API)
  --help          Show this message

Default: insert missing + repair corrupted VI rows.

HELP;
    exit(0);
}

$dryRun = isset($opts['dry-run']);
$onlyMissing = isset($opts['only-missing']);
$force = isset($opts['force']);
$useApi = !isset($opts['no-api']);

$translator = new ViSeedTranslator($useApi);
$pdo = Database::connection();

$entities = [
    [
        'entity' => 'page',
        'table' => 'page_translations',
        'fk' => 'page_id',
        'parent' => ['table' => 'pages', 'slug' => 'slug'],
        'fields' => ['title', 'content', 'seo_title', 'seo_description'],
    ],
    [
        'entity' => 'news',
        'table' => 'news_translations',
        'fk' => 'news_id',
        'parent' => ['table' => 'news', 'slug' => 'slug'],
        'fields' => ['title', 'summary', 'content', 'seo_title', 'seo_description'],
    ],
    [
        'entity' => 'room',
        'table' => 'room_translations',
        'fk' => 'room_id',
        'parent' => ['table' => 'rooms', 'slug' => 'slug'],
        'fields' => ['name', 'description'],
    ],
    [
        'entity' => 'room_category',
        'table' => 'room_category_translations',
        'fk' => 'category_id',
        'parent' => ['table' => 'room_categories', 'slug' => 'slug'],
        'fields' => ['name', 'description'],
    ],
    [
        'entity' => 'banner',
        'table' => 'banner_translations',
        'fk' => 'banner_id',
        'parent' => null,
        'fields' => ['title', 'subtitle', 'button_text'],
    ],
    [
        'entity' => 'menu_item',
        'table' => 'menu_item_translations',
        'fk' => 'menu_item_id',
        'parent' => ['table' => 'menu_items', 'slug' => null],
        'fields' => ['title'],
    ],
];

$stats = ['created' => 0, 'updated' => 0, 'skipped' => 0, 'repaired' => 0];

echo "Vi seed from EN" . ($dryRun ? ' (dry-run)' : '') . "\n";
echo str_repeat('-', 50) . "\n";

foreach ($entities as $cfg) {
    processEntity($pdo, $translator, $cfg, $dryRun, $onlyMissing, $force, $stats);
}

seedSettingsVi($pdo, $dryRun, $stats);

echo str_repeat('-', 50) . "\n";
echo "Created: {$stats['created']}, Updated: {$stats['updated']}, Repaired: {$stats['repaired']}, Skipped: {$stats['skipped']}\n";

if ($dryRun) {
    echo "Dry-run complete — no database changes were made.\n";
}

/**
 * @param array<string, mixed> $cfg
 * @param array<string, int> $stats
 */
function processEntity(
    PDO $pdo,
    ViSeedTranslator $translator,
    array $cfg,
    bool $dryRun,
    bool $onlyMissing,
    bool $force,
    array &$stats
): void {
    $entity = $cfg['entity'];
    $table = $cfg['table'];
    $fk = $cfg['fk'];
    $fields = $cfg['fields'];

    $enRows = $pdo->query(
        "SELECT * FROM {$table} WHERE locale = 'en'"
    )->fetchAll();

    echo "{$entity}: " . count($enRows) . " EN row(s)\n";

    foreach ($enRows as $en) {
        $entityId = (int) $en[$fk];
        $slug = resolveSlug($pdo, $cfg, $entityId, $en);

        $vi = fetchLocaleRow($pdo, $table, $fk, $entityId, 'vi');
        $action = resolveAction($vi, $fields, $onlyMissing, $force);

        if ($action === 'skip') {
            $stats['skipped']++;
            continue;
        }

        $viFields = [];
        foreach ($fields as $field) {
            $source = (string) ($en[$field] ?? '');
            if ($source === '') {
                $viFields[$field] = '';
                continue;
            }
            $viFields[$field] = $translator->translate($source, [
                'entity' => $entity,
                'field' => $field,
                'slug' => $slug,
            ]);
        }

        $wasCorrupted = $vi !== null && rowHasCorruption($vi, $fields);
        $label = $action === 'create' ? 'CREATE' : 'UPDATE';
        if ($wasCorrupted) {
            $label .= ' (repair)';
            $stats['repaired']++;
        }

        echo "  [{$label}] {$entity}#{$entityId}";
        if ($slug !== '') {
            echo " ({$slug})";
        }
        echo ': ' . ($viFields['title'] ?? $viFields['name'] ?? '(no title)') . "\n";

        if ($dryRun) {
            if ($action === 'create') {
                $stats['created']++;
            } else {
                $stats['updated']++;
            }
            continue;
        }

        Translation::save($entity, $entityId, 'vi', $viFields);

        if ($action === 'create') {
            $stats['created']++;
        } else {
            $stats['updated']++;
        }
    }
}

/**
 * @param array<string, mixed> $cfg
 */
function resolveSlug(PDO $pdo, array $cfg, int $entityId, array $enRow): string
{
    $parent = $cfg['parent'] ?? null;
    if ($parent === null) {
        return '';
    }

    $parentTable = $parent['table'];

    if (array_key_exists('slug', $parent) && $parent['slug'] === null) {
        return trim((string) ($enRow['title'] ?? ''));
    }

    $slugCol = $parent['slug'] ?? 'slug';
    $stmt = $pdo->prepare("SELECT {$slugCol} AS slug FROM {$parentTable} WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $entityId]);
    $row = $stmt->fetch();

    return $row ? (string) $row['slug'] : '';
}

function fetchLocaleRow(PDO $pdo, string $table, string $fk, int $entityId, string $locale): ?array
{
    $stmt = $pdo->prepare("SELECT * FROM {$table} WHERE {$fk} = :id AND locale = :locale LIMIT 1");
    $stmt->execute(['id' => $entityId, 'locale' => $locale]);

    $row = $stmt->fetch();

    return $row ?: null;
}

/**
 * @param list<string> $fields
 */
function resolveAction(?array $vi, array $fields, bool $onlyMissing, bool $force): string
{
    if ($force) {
        return $vi === null ? 'create' : 'update';
    }

    if ($onlyMissing) {
        return $vi === null ? 'create' : 'skip';
    }

    if ($vi === null) {
        return 'create';
    }

    if (rowHasCorruption($vi, $fields)) {
        return 'update';
    }

    return 'skip';
}

/**
 * @param list<string> $fields
 */
function rowHasCorruption(array $row, array $fields): bool
{
    foreach ($fields as $field) {
        if (ViSeedTranslator::isCorrupted($row[$field] ?? null)) {
            return true;
        }
    }

    return false;
}

/**
 * @param array<string, int> $stats
 */
function seedSettingsVi(PDO $pdo, bool $dryRun, array &$stats): void
{
    $pairs = [
        'seo_default_title_vi' => 'Shinning Hotel | Nơi mỗi kỳ nghỉ đều rực rỡ',
        'seo_default_description_vi' => 'Khách sạn sang trọng giữa lòng thành phố. Đặt phòng trực tiếp để nhận ưu đãi độc quyền.',
    ];

    echo "settings: VI SEO defaults\n";

    foreach ($pairs as $key => $value) {
        $stmt = $pdo->prepare('SELECT setting_value FROM settings WHERE setting_key = :key LIMIT 1');
        $stmt->execute(['key' => $key]);
        $current = $stmt->fetchColumn();

        if ($current !== false && !ViSeedTranslator::isCorrupted((string) $current) && trim((string) $current) !== '') {
            echo "  [SKIP] {$key}\n";
            $stats['skipped']++;
            continue;
        }

        echo '  [UPSERT] ' . $key . "\n";

        if ($dryRun) {
            $stats['updated']++;
            continue;
        }

        $exists = $current !== false;
        if ($exists) {
            $upd = $pdo->prepare('UPDATE settings SET setting_value = :val WHERE setting_key = :key');
            $upd->execute(['val' => $value, 'key' => $key]);
        } else {
            $ins = $pdo->prepare(
                'INSERT INTO settings (setting_key, setting_value, setting_type, group_name) VALUES (:key, :val, :type, :grp)'
            );
            $ins->execute(['key' => $key, 'val' => $value, 'type' => 'text', 'grp' => 'seo']);
        }
        $stats['updated']++;
    }
}
