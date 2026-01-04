<?php

declare(strict_types=1);

require_once __DIR__ . '/../perch/runtime.php';

$options = getopt('', [
    'days::',
    'limit::',
    'dry-run',
    'help',
]);

if (isset($options['help'])) {
    echo 'Usage: php scripts/archive_closed_chats.php [--days=N] [--limit=N] [--dry-run]' . PHP_EOL;
    echo '       --days     Archive chats closed on or before N days ago (default: 30).' . PHP_EOL;
    echo '       --limit    Maximum number of threads to process in this run.' . PHP_EOL;
    echo '       --dry-run  Show what would be archived without writing files or deleting rows.' . PHP_EOL;
    exit(0);
}

$days = isset($options['days']) ? (int) $options['days'] : 30;
if ($days < 0) {
    fwrite(STDERR, 'The --days option must be zero or a positive integer.' . PHP_EOL);
    exit(1);
}

$limit = isset($options['limit']) ? (int) $options['limit'] : null;
if ($limit !== null && $limit < 1) {
    fwrite(STDERR, 'The --limit option must be greater than zero when supplied.' . PHP_EOL);
    exit(1);
}

$dryRun = array_key_exists('dry-run', $options);
$cutoff = (new DateTimeImmutable(sprintf('-%d days', $days)))->format('Y-m-d H:i:s');

$DB = PerchDB::fetch();
$threadsTable = PERCH_DB_PREFIX . 'chat_threads';
$messagesTable = PERCH_DB_PREFIX . 'chat_messages';
$closuresTable = PERCH_DB_PREFIX . 'chat_thread_closures';

$threadsTableExists = $DB->get_value('SHOW TABLES LIKE ' . $DB->pdb($threadsTable));
$messagesTableExists = $DB->get_value('SHOW TABLES LIKE ' . $DB->pdb($messagesTable));

if ($threadsTableExists === false || $threadsTableExists === null || $messagesTableExists === false || $messagesTableExists === null) {
    fwrite(STDERR, 'Chat tables are missing. Please run sql/create_chat_tables.sql first.' . PHP_EOL);
    exit(1);
}

$closuresTableExists = $DB->get_value('SHOW TABLES LIKE ' . $DB->pdb($closuresTable));
if ($closuresTableExists === false || $closuresTableExists === null) {
    $sql = 'CREATE TABLE IF NOT EXISTS `' . $closuresTable . '` (
        `id` int unsigned NOT NULL AUTO_INCREMENT,
        `threadID` int unsigned NOT NULL,
        `last_message_id` int unsigned DEFAULT NULL,
        `closed_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `thread_closed_at` (`threadID`, `closed_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';

    $DB->execute($sql);
}

$cutoffQuoted = $DB->pdb($cutoff);

$sql = 'SELECT t.*, lc.last_closed_at, COALESCE(msg.message_count, 0) AS message_count'
    . ' FROM ' . $threadsTable . ' t'
    . ' LEFT JOIN ('
    . '   SELECT threadID, MAX(closed_at) AS last_closed_at'
    . '   FROM ' . $closuresTable
    . '   GROUP BY threadID'
    . ' ) lc ON lc.threadID = t.id'
    . ' LEFT JOIN ('
    . '   SELECT threadID, COUNT(*) AS message_count'
    . '   FROM ' . $messagesTable
    . '   GROUP BY threadID'
    . ' ) msg ON msg.threadID = t.id'
    . ' WHERE t.status = ' . $DB->pdb('closed')
    . ' AND lc.last_closed_at IS NOT NULL'
    . ' AND lc.last_closed_at <= ' . $cutoffQuoted
    . ' ORDER BY lc.last_closed_at ASC, t.id ASC';

if ($limit !== null) {
    $sql .= ' LIMIT ' . $limit;
}

$threads = $DB->get_rows($sql) ?: [];

if (count($threads) === 0) {
    echo 'No closed chat threads found on or before ' . $cutoff . '.' . PHP_EOL;
    exit(0);
}

$archiveRoot = __DIR__ . '/../logs/chat_archives/' . date('Y-m-d');
if (!$dryRun) {
    if (!is_dir($archiveRoot)) {
        mkdir($archiveRoot, 0777, true);
    }

    if (!is_writable($archiveRoot)) {
        chmod($archiveRoot, 0777);
    }
}

$archivedCount = 0;

foreach ($threads as $thread) {
    $threadID = (int) $thread['id'];
    $lastClosedAt = $thread['last_closed_at'] ?? null;
    $messages = $DB->get_rows(
        'SELECT * FROM ' . $messagesTable
        . ' WHERE threadID = ' . $DB->pdb($threadID)
        . ' ORDER BY id ASC'
    ) ?: [];

    $closures = $DB->get_rows(
        'SELECT * FROM ' . $closuresTable
        . ' WHERE threadID = ' . $DB->pdb($threadID)
        . ' ORDER BY id ASC'
    ) ?: [];

    $archiveData = [
        'archived_at' => date('c'),
        'thread' => $thread,
        'closures' => $closures,
        'messages' => $messages,
    ];

    if ($dryRun) {
        echo 'Would archive thread #' . $threadID . ' (closed at ' . $lastClosedAt . ') with ' . count($messages) . ' messages.' . PHP_EOL;
        continue;
    }

    $filename = sprintf(
        '%s/thread-%d-%s.json',
        $archiveRoot,
        $threadID,
        date('Ymd_His')
    );

    $written = file_put_contents($filename, json_encode($archiveData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    if ($written === false) {
        fwrite(STDERR, 'Failed to write archive file for thread #' . $threadID . '.' . PHP_EOL);
        continue;
    }

    $DB->execute('DELETE FROM ' . $closuresTable . ' WHERE threadID = ' . $DB->pdb($threadID));
    $DB->execute('DELETE FROM ' . $threadsTable . ' WHERE id = ' . $DB->pdb($threadID));

    echo 'Archived thread #' . $threadID . ' to ' . $filename . ' and deleted from database.' . PHP_EOL;
    $archivedCount++;
}

if ($dryRun) {
    echo 'Dry run complete. ' . count($threads) . ' thread(s) matched the criteria.' . PHP_EOL;
} else {
    echo 'Done. Archived ' . $archivedCount . ' thread(s) closed on or before ' . $cutoff . '.' . PHP_EOL;
}

