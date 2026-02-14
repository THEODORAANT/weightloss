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

    if ($database === '' || $user === '' || $password === null) {
        return ['ok' => false, 'error' => 'Missing required credentials.'];
    }

    if (!preg_match('/^[0-9]+$/', $port)) {
        return ['ok' => false, 'error' => 'Invalid port value: ' . $port];
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
        ];
    }

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

    $result = [
        'ok' => true,
        'dry_run' => false,
        'sql_path' => $sqlPath,
        'gz_path' => null,
        'command' => $command,
    ];

    if ($gzip) {
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

        $result['gz_path'] = $gzPath;
    }

    return $result;
}
