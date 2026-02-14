<?php

declare(strict_types=1);

function weightloss_db_backup(array $config): array
{
    $host = (string)($config['host'] ?? '127.0.0.1');
    $port = (string)($config['port'] ?? '3306');
    $database = isset($config['database']) ? (string)$config['database'] : '';
    $user = isset($config['user']) ? (string)$config['user'] : '';
    $password = array_key_exists('password', $config) ? (string)$config['password'] : null;
    $outputDir = (string)($config['output_dir'] ?? 'backups/db');
    $gzip = !empty($config['gzip']);
    $dryRun = !empty($config['dry_run']);
    $engine = isset($config['engine']) ? (string)$config['engine'] : 'auto'; // auto|mysqldump|php

    if ($database === '' || $user === '' || $password === null) {
        return ['ok' => false, 'error' => 'Missing required credentials.'];
    }

    if (!preg_match('/^[0-9]+$/', $port)) {
        return ['ok' => false, 'error' => 'Invalid port value: ' . $port];
    }

    if (!in_array($engine, ['auto', 'mysqldump', 'php'], true)) {
        return ['ok' => false, 'error' => 'Invalid engine. Use one of: auto, mysqldump, php'];
    }

    if (!is_dir($outputDir) && !$dryRun) {
        if (!mkdir($outputDir, 0775, true) && !is_dir($outputDir)) {
            return ['ok' => false, 'error' => 'Failed to create output directory: ' . $outputDir];
        }
    }

    $filename = isset($config['filename']) && (string)$config['filename'] !== ''
        ? (string)$config['filename']
        : $database . '_' . date('Ymd_His') . '.sql';

    $sqlPath = rtrim($outputDir, '/\\') . DIRECTORY_SEPARATOR . $filename;

    $cmd = [
        'mysqldump',
        '--single-transaction',
        '--quick',
        '--lock-tables=false',
        '--set-gtid-purged=OFF',
        '-h',
        $host,
        '-P',
        $port,
        '-u',
        $user,
        $database,
    ];

    $escaped = array_map('escapeshellarg', $cmd);
    $command = implode(' ', $escaped) . ' > ' . escapeshellarg($sqlPath);

    if ($dryRun) {
        return [
            'ok' => true,
            'dry_run' => true,
            'command' => $command,
            'sql_path' => $sqlPath,
            'gz_path' => $gzip ? $sqlPath . '.gz' : null,
            'engine' => $engine,
        ];
    }

    $result = null;

    if ($engine === 'mysqldump' || $engine === 'auto') {
        $result = weightloss_backup_with_mysqldump($command, $sqlPath, $password);

        if (!empty($result['ok'])) {
            $result['engine'] = 'mysqldump';
        } elseif ($engine === 'auto' && weightloss_mysqldump_missing($result['error'] ?? '')) {
            $result = weightloss_backup_with_php($host, (int)$port, $database, $user, $password, $sqlPath);
            if (!empty($result['ok'])) {
                $result['engine'] = 'php';
            }
        }
    } else {
        $result = weightloss_backup_with_php($host, (int)$port, $database, $user, $password, $sqlPath);
        if (!empty($result['ok'])) {
            $result['engine'] = 'php';
        }
    }

    if (empty($result['ok'])) {
        return $result;
    }

    if ($gzip) {
        $gzipResult = weightloss_backup_gzip($sqlPath);
        if (empty($gzipResult['ok'])) {
            return $gzipResult;
        }
        $result['gz_path'] = $gzipResult['gz_path'];
    } else {
        $result['gz_path'] = null;
    }

    return $result;
}

function weightloss_backup_with_mysqldump(string $command, string $sqlPath, string $password): array
{
    $env = $_ENV;
    $env['MYSQL_PWD'] = $password;

    $descriptors = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];

    $process = proc_open($command, $descriptors, $pipes, null, $env);
    if (!is_resource($process)) {
        return ['ok' => false, 'error' => 'Failed to start mysqldump process.'];
    }

    fclose($pipes[0]);
    $stdout = stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[2]);
    $exitCode = proc_close($process);

    if ($exitCode !== 0) {
        if (is_file($sqlPath)) {
            @unlink($sqlPath);
        }

        $error = 'mysqldump failed with exit code ' . $exitCode . '.';
        if ($stderr) {
            $error .= ' ' . trim($stderr);
        } elseif ($stdout) {
            $error .= ' ' . trim($stdout);
        }

        return ['ok' => false, 'error' => $error];
    }

    if (!is_file($sqlPath) || filesize($sqlPath) === 0) {
        return ['ok' => false, 'error' => 'Backup file was not created or is empty: ' . $sqlPath];
    }

    return [
        'ok' => true,
        'dry_run' => false,
        'sql_path' => $sqlPath,
        'command' => $command,
    ];
}

function weightloss_backup_with_php(string $host, int $port, string $database, string $user, string $password, string $sqlPath): array
{
    if (!class_exists('mysqli')) {
        return ['ok' => false, 'error' => 'mysqldump is unavailable and mysqli extension is not installed for PHP fallback.'];
    }

    mysqli_report(MYSQLI_REPORT_OFF);
    $mysqli = @new mysqli($host, $user, $password, $database, $port);
    if ($mysqli->connect_errno) {
        return ['ok' => false, 'error' => 'PHP fallback connection failed: ' . $mysqli->connect_error];
    }

    $mysqli->set_charset('utf8mb4');

    $fh = @fopen($sqlPath, 'wb');
    if (!$fh) {
        $mysqli->close();
        return ['ok' => false, 'error' => 'Failed to create backup file: ' . $sqlPath];
    }

    fwrite($fh, "-- SQL backup generated by PHP fallback\n");
    fwrite($fh, "-- Database: `" . str_replace('`', '``', $database) . "`\n");
    fwrite($fh, "-- Generated at: " . date('Y-m-d H:i:s') . "\n\n");
    fwrite($fh, "SET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS=0;\n\n");

    $tables = $mysqli->query('SHOW FULL TABLES');
    if (!$tables) {
        fclose($fh);
        $mysqli->close();
        @unlink($sqlPath);
        return ['ok' => false, 'error' => 'PHP fallback failed to list tables: ' . $mysqli->error];
    }

    while ($row = $tables->fetch_array(MYSQLI_NUM)) {
        $table = (string)$row[0];
        $type = isset($row[1]) ? strtoupper((string)$row[1]) : 'BASE TABLE';

        $tableEsc = '`' . str_replace('`', '``', $table) . '`';

        if ($type === 'VIEW') {
            $create = $mysqli->query('SHOW CREATE VIEW ' . $tableEsc);
            if ($create && ($createRow = $create->fetch_assoc()) && isset($createRow['Create View'])) {
                fwrite($fh, "DROP VIEW IF EXISTS {$tableEsc};\n");
                fwrite($fh, $createRow['Create View'] . ";\n\n");
            }
            if ($create) {
                $create->free();
            }
            continue;
        }

        $create = $mysqli->query('SHOW CREATE TABLE ' . $tableEsc);
        if (!$create) {
            fclose($fh);
            $mysqli->close();
            @unlink($sqlPath);
            return ['ok' => false, 'error' => 'PHP fallback failed on SHOW CREATE TABLE for ' . $table . ': ' . $mysqli->error];
        }

        $createRow = $create->fetch_assoc();
        $createSQL = $createRow['Create Table'] ?? '';
        $create->free();

        fwrite($fh, "DROP TABLE IF EXISTS {$tableEsc};\n");
        fwrite($fh, $createSQL . ";\n\n");

        $data = $mysqli->query('SELECT * FROM ' . $tableEsc, MYSQLI_USE_RESULT);
        if (!$data) {
            fclose($fh);
            $mysqli->close();
            @unlink($sqlPath);
            return ['ok' => false, 'error' => 'PHP fallback failed selecting data from ' . $table . ': ' . $mysqli->error];
        }

        while ($dataRow = $data->fetch_assoc()) {
            $cols = [];
            $vals = [];

            foreach ($dataRow as $col => $val) {
                $cols[] = '`' . str_replace('`', '``', (string)$col) . '`';
                if ($val === null) {
                    $vals[] = 'NULL';
                } else {
                    $vals[] = "'" . $mysqli->real_escape_string((string)$val) . "'";
                }
            }

            fwrite(
                $fh,
                'INSERT INTO ' . $tableEsc . ' (' . implode(', ', $cols) . ') VALUES (' . implode(', ', $vals) . ");\n"
            );
        }

        $data->free();
        fwrite($fh, "\n");
    }

    $tables->free();
    fwrite($fh, "SET FOREIGN_KEY_CHECKS=1;\n");

    fclose($fh);
    $mysqli->close();

    if (!is_file($sqlPath) || filesize($sqlPath) === 0) {
        return ['ok' => false, 'error' => 'Backup file was not created or is empty: ' . $sqlPath];
    }

    return [
        'ok' => true,
        'dry_run' => false,
        'sql_path' => $sqlPath,
        'command' => 'php-fallback',
    ];
}

function weightloss_backup_gzip(string $sqlPath): array
{
    $gzPath = $sqlPath . '.gz';
    $in = fopen($sqlPath, 'rb');
    $out = gzopen($gzPath, 'wb9');

    if (!$in || !$out) {
        if (is_resource($in)) {
            fclose($in);
        }
        if (is_resource($out)) {
            gzclose($out);
        }
        return ['ok' => false, 'error' => 'Failed to gzip backup file.'];
    }

    while (!feof($in)) {
        $chunk = fread($in, 1024 * 1024);
        if ($chunk === false) {
            fclose($in);
            gzclose($out);
            return ['ok' => false, 'error' => 'Failed while reading SQL backup for compression.'];
        }
        gzwrite($out, $chunk);
    }

    fclose($in);
    gzclose($out);

    if (!is_file($gzPath) || filesize($gzPath) === 0) {
        return ['ok' => false, 'error' => 'Gzip file was not created or is empty: ' . $gzPath];
    }

    return ['ok' => true, 'gz_path' => $gzPath];
}

function weightloss_mysqldump_missing(string $error): bool
{
    $errorLower = strtolower($error);
    return str_contains($errorLower, 'exit code 127') || str_contains($errorLower, 'not found');
}
