<?php

declare(strict_types=1);

require_once __DIR__ . '/db_backup_lib.php';

$options = getopt('', [
    'host::',
    'port::',
    'database::',
    'user::',
    'password::',
    'output-dir::',
    'filename::',
    'gzip',
    'engine::',
    'dry-run',
    'help',
]);

if (isset($options['help'])) {
    echo "Database backup script\n\n";
    echo "Usage:\n";
    echo "  php scripts/backup_db.php [options]\n\n";
    echo "Options:\n";
    echo "  --host=<host>          Database host (default from env, fallback: 127.0.0.1)\n";
    echo "  --port=<port>          Database port (default from env, fallback: 3306)\n";
    echo "  --database=<name>      Database name (required if not in env)\n";
    echo "  --user=<user>          Database user (required if not in env)\n";
    echo "  --password=<password>  Database password (required if not in env)\n";
    echo "  --output-dir=<dir>     Output directory (default: backups/db)\n";
    echo "  --filename=<name>      Override output filename\n";
    echo "  --gzip                 Also create a gzip-compressed copy (.gz)\n";
    echo "  --engine=<engine>      Backup engine: auto|mysqldump|php (default: auto)\n";
    echo "  --dry-run              Show command and output path without running\n";
    echo "  --help                 Show this help message\n";
    exit(0);
}

function env_first(array $keys, ?string $default = null): ?string
{
    foreach ($keys as $key) {
        $value = getenv($key);
        if ($value !== false && $value !== '') {
            return $value;
        }
    }

    return $default;
}

$result = weightloss_db_backup([
    'host' => isset($options['host']) ? (string)$options['host'] : env_first(['DB_HOST', 'MYSQL_HOST', 'PERCH_DB_SERVER'], '127.0.0.1'),
    'port' => isset($options['port']) ? (string)$options['port'] : env_first(['DB_PORT', 'MYSQL_PORT', 'PERCH_DB_PORT'], '3306'),
    'database' => isset($options['database']) ? (string)$options['database'] : env_first(['DB_NAME', 'MYSQL_DATABASE', 'PERCH_DB_DATABASE']),
    'user' => isset($options['user']) ? (string)$options['user'] : env_first(['DB_USER', 'MYSQL_USER', 'PERCH_DB_USERNAME']),
    'password' => isset($options['password']) ? (string)$options['password'] : env_first(['DB_PASSWORD', 'MYSQL_PASSWORD', 'PERCH_DB_PASSWORD']),
    'output_dir' => isset($options['output-dir']) ? (string)$options['output-dir'] : 'backups/db',
    'filename' => isset($options['filename']) ? (string)$options['filename'] : null,
    'gzip' => isset($options['gzip']),
    'engine' => isset($options['engine']) ? (string)$options['engine'] : 'auto',
    'dry_run' => isset($options['dry-run']),
]);

if (empty($result['ok'])) {
    fwrite(STDERR, ($result['error'] ?? 'Backup failed.') . "\n");
    exit(1);
}

if (!empty($result['dry_run'])) {
    echo "Dry run only.\n";
    echo "Would run:\n" . $result['command'] . "\n";
    echo "Output file:\n" . $result['sql_path'] . "\n";
    if (!empty($result['gz_path'])) {
        echo "Compressed output:\n" . $result['gz_path'] . "\n";
    }
    exit(0);
}

echo "Backup created: " . $result['sql_path'] . "\n";
if (!empty($result['engine'])) {
    echo "Engine used: " . $result['engine'] . "\n";
}
if (!empty($result['gz_path'])) {
    echo "Compressed backup created: " . $result['gz_path'] . "\n";
}
