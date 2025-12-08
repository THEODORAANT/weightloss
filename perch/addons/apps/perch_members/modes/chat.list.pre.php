<?php
$ChatRepo = new PerchMembers_ChatRepository($API);
$status = PerchUtil::get('status', 'open');
$threads = [];
$tables_ready = $ChatRepo->tables_ready();
$create_error = null;

if ($tables_ready) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $member_id = (int)PerchUtil::post('member_id');
        if ($member_id < 1) {
            $create_error = $Lang->get('Please enter a valid member ID.');
        } else {
            $thread = $ChatRepo->get_or_create_thread_for_member($member_id);
            if ($thread) {
                PerchUtil::redirect($API->app_path() . '/chat/thread.php?id=' . (int)$thread['id'] . '&created=1');
            }

            $create_error = $Lang->get('Unable to start a conversation for this member.');
        }
    }

    $threads = $ChatRepo->list_threads([
        'status' => $status === 'all' ? null : $status,
    ]);
}
