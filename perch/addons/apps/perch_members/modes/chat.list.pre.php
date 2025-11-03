<?php
$ChatRepo = new PerchMembers_ChatRepository($API);
$status = PerchUtil::get('status', 'open');
$threads = [];
$tables_ready = $ChatRepo->tables_ready();

if ($tables_ready) {
    $threads = $ChatRepo->list_threads([
        'status' => $status === 'all' ? null : $status,
    ]);
}
