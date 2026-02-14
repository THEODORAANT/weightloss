<?php

require_once __DIR__ . '/../../../../../scripts/db_backup_lib.php';

$Form = $API->get('Form');

$message = false;
$message_type = 'success';

$template_string = '<perch:shop id="output_dir" type="text" label="Output folder" default="backups/db" required="true" />\n'
    . '<perch:shop id="filename" type="text" label="Filename (optional)" />\n'
    . '<perch:shop id="gzip" type="checkbox" label="Also create .gz file" value="1" />';

$Template = $API->get('Template');
$Template->set_from_string($template_string, 'shop');

$Form->handle_empty_block_generation($Template);
$Form->set_required_fields_from_template($Template, []);

if ($Form->submitted()) {
    $output_dir = isset($_POST['output_dir']) ? trim((string) $_POST['output_dir']) : 'backups/db';
    if ($output_dir === '') {
        $output_dir = 'backups/db';
    }

    $filename = isset($_POST['filename']) ? trim((string) $_POST['filename']) : null;
    if ($filename === '') {
        $filename = null;
    }

    $gzip = isset($_POST['gzip']) && (string) $_POST['gzip'] !== '' && (string) $_POST['gzip'] !== '0';

    $result = weightloss_db_backup([
        'host' => defined('PERCH_DB_SERVER') ? PERCH_DB_SERVER : '127.0.0.1',
        'port' => defined('PERCH_DB_PORT') ? (string) PERCH_DB_PORT : '3306',
        'database' => defined('PERCH_DB_DATABASE') ? PERCH_DB_DATABASE : null,
        'user' => defined('PERCH_DB_USERNAME') ? PERCH_DB_USERNAME : null,
        'password' => defined('PERCH_DB_PASSWORD') ? PERCH_DB_PASSWORD : null,
        'output_dir' => $output_dir,
        'filename' => $filename,
        'gzip' => $gzip,
        'dry_run' => false,
    ]);

    if (!empty($result['ok'])) {
        $message = 'Backup created: ' . $result['sql_path'];
        if (!empty($result['gz_path'])) {
            $message .= ' | Compressed: ' . $result['gz_path'];
        }
    } else {
        $message = $result['error'] ?? 'Backup failed.';
        $message_type = 'error';
    }
}

$recent_backups = [];
$backup_dir = realpath(__DIR__ . '/../../../../../backups/db');

if ($backup_dir && is_dir($backup_dir)) {
    $files = glob($backup_dir . '/*.sql*');
    if ($files) {
        rsort($files);
        foreach (array_slice($files, 0, 20) as $file) {
            $recent_backups[] = [
                'name' => basename($file),
                'path' => $file,
                'size' => filesize($file),
                'modified' => date('Y-m-d H:i:s', filemtime($file)),
            ];
        }
    }
}
