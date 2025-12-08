<?php
$ChatRepo = new PerchMembers_ChatRepository($API);
$tables_ready = $ChatRepo->tables_ready();
$threadID = (int)PerchUtil::get('id');
$memberID = (int)PerchUtil::get('member_id');
$thread = null;
$messages = [];
$Member = null;
$message_error = null;
$creation_error = null;
$sent = PerchUtil::get('sent') === '1';
$created = PerchUtil::get('created') === '1';
$status_change = PerchUtil::get('status_change');

if (!$tables_ready) {
    return;
}

$Members = new PerchMembers_Members($API);

if ($threadID > 0) {
    $thread = $ChatRepo->get_thread($threadID);
}

if (!$thread && $memberID > 0) {
    $TargetMember = $Members->find($memberID);
    if (!$TargetMember) {
        $creation_error = $Lang->get('Member not found.');
    } else {
        $thread = $ChatRepo->get_or_create_thread_for_member($memberID);
        if ($thread) {
            PerchUtil::redirect($API->app_path() . '/chat/thread.php?id=' . (int)$thread['id'] . '&created=1');
        } else {
            $creation_error = $Lang->get('Unable to start a conversation for this member.');
        }
    }
}

if (!$thread) {
    if ($creation_error) {
        return;
    }

    PerchUtil::redirect($API->app_path() . '/chat/');
}

$Member = $Members->find($thread['memberID']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = PerchUtil::post('action');
    if ($action === 'message') {
        $body = trim((string)PerchUtil::post('message'));
        if ($body === '') {
            $message_error = $Lang->get('Please enter a message.');
        } else {
            $ChatRepo->add_staff_message($threadID, $CurrentUser->id(), $body);
            $ChatRepo->mark_thread_read_by_staff($threadID);
            PerchUtil::redirect($API->app_path() . '/chat/thread.php?id=' . $threadID . '&sent=1');
        }
    } elseif ($action === 'close') {
        $ChatRepo->set_thread_status($threadID, 'closed');
        PerchUtil::redirect($API->app_path() . '/chat/thread.php?id=' . $threadID . '&status_change=closed');
    } elseif ($action === 'open') {
        $ChatRepo->set_thread_status($threadID, 'open');
        PerchUtil::redirect($API->app_path() . '/chat/thread.php?id=' . $threadID . '&status_change=open');
    }
}

$messages = $ChatRepo->get_messages($threadID);
$ChatRepo->mark_thread_read_by_staff($threadID);
