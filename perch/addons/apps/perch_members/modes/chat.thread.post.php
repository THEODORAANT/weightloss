<?php
if (!$tables_ready) {
    echo $HTML->warning_message($Lang->get('Chat tables have not been created yet. Run the SQL script in %s.', '<code>sql/create_chat_tables.sql</code>'));
    return;
}

if (!$thread) {
    echo $HTML->failure_message($Lang->get('Conversation not found.'));
    echo '<p><a href="' . $API->app_path() . '/chat/">' . $Lang->get('Back to chat list') . '</a></p>';
    return;
}

$memberName = $Lang->get('Member #%s', $thread['memberID']);
$memberEmail = '';
if ($Member) {
    $memberArray = $Member->to_array();
    $first = isset($memberArray['first_name']) ? trim($memberArray['first_name']) : '';
    $last = isset($memberArray['last_name']) ? trim($memberArray['last_name']) : '';
    $email = isset($memberArray['memberEmail']) ? trim($memberArray['memberEmail']) : '';
    $combined = trim($first . ' ' . $last);
    if ($combined !== '') {
        $memberName = $combined;
    }
    $memberEmail = $email;
}

echo $HTML->title_panel([
    'heading' => $Lang->get('Conversation with %s', $memberName),
    'button' => [
        'text' => $Lang->get('Back to conversations'),
        'link' => $API->app_path() . '/chat/',
        'icon' => 'core/undo',
    ],
]);

if ($status_change === 'closed') {
    echo $HTML->success_message($Lang->get('Conversation closed.'));
} elseif ($status_change === 'open') {
    echo $HTML->success_message($Lang->get('Conversation reopened.'));
}

if ($sent) {
    echo $HTML->success_message($Lang->get('Reply sent.'));
}

if ($message_error) {
    echo $HTML->failure_message($HTML->encode($message_error));
}

$details = [];
$details[] = $Lang->get('Status: %s', $thread['status'] === 'closed' ? $Lang->get('Closed') : $Lang->get('Open'));
if ($memberEmail) {
    $details[] = $Lang->get('Email: %s', $HTML->encode($memberEmail));
}
if ($thread['last_message_at']) {
    $details[] = $Lang->get('Last message: %s', date('d M Y H:i', strtotime($thread['last_message_at'])));
}

echo '<div class="chat-thread-summary"><p>' . implode('<br>', $details) . '</p></div>';

if (!PerchUtil::count($messages)) {
    echo '<p class="hint">' . $Lang->get('No messages yet. Send a reply to start the conversation.') . '</p>';
} else {
    echo '<div class="chat-thread-log">';
    foreach ($messages as $message) {
        $is_member = $message['sender_type'] === 'member';
        $author = $is_member ? $Lang->get('Member') : $Lang->get('Staff');
        $timestamp = date('d M Y H:i', strtotime($message['created_at']));
        echo '<div class="chat-thread-message ' . ($is_member ? 'chat-thread-member' : 'chat-thread-staff') . '">';
        echo '<div class="chat-thread-meta">';
        echo '<strong>' . $author . '</strong> <span>' . $timestamp . '</span>';
        echo '</div>';
        echo '<div class="chat-thread-body">' . nl2br($HTML->encode($message['body'])) . '</div>';
        echo '</div>';
    }
    echo '</div>';
}

echo '<div class="chat-thread-actions">';
if ($thread['status'] === 'open') {
    echo '<form method="post" class="inline-form" action="" style="margin-bottom:1rem">';
    echo '<input type="hidden" name="action" value="close">';
    echo '<button class="button button-small" type="submit">' . $Lang->get('Close conversation') . '</button>';
    echo '</form>';
} else {
    echo '<form method="post" class="inline-form" action="" style="margin-bottom:1rem">';
    echo '<input type="hidden" name="action" value="open">';
    echo '<button class="button button-small" type="submit">' . $Lang->get('Reopen conversation') . '</button>';
    echo '</form>';
}

echo '<form method="post" action="" class="reply-form">';
if ($thread['status'] === 'closed') {
    echo '<p class="hint">' . $Lang->get('This conversation is closed. Reopen it to send a reply.') . '</p>';
}
$disabled = $thread['status'] === 'closed' ? ' disabled' : '';
echo '<input type="hidden" name="action" value="message">';
echo '<div class="field">';
echo '<label class="description" for="message">' . $Lang->get('Reply') . '</label>';
echo '<textarea class="input-simple" id="message" name="message" rows="4"' . $disabled . '></textarea>';
echo '</div>';
echo '<div class="buttons">';
echo '<button type="submit" class="button button-primary"' . $disabled . '>' . $Lang->get('Send reply') . '</button>';
echo '</div>';
echo '</form>';

echo '</div>';
?>

<style>
.chat-thread-summary {
    margin-bottom: 20px;
}
.chat-thread-log {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 16px;
    max-height: 60vh;
    overflow-y: auto;
    margin-bottom: 20px;
}
.chat-thread-message {
    background: #fff;
    border-radius: 6px;
    padding: 12px 14px;
    margin-bottom: 12px;
    border-left: 4px solid #64748b;
}
.chat-thread-message.chat-thread-member {
    border-left-color: #3328bf;
}
.chat-thread-message.chat-thread-staff {
    border-left-color: #16a34a;
}
.chat-thread-meta {
    display: flex;
    justify-content: space-between;
    font-size: 0.85rem;
    margin-bottom: 6px;
    color: #475569;
}
.chat-thread-body {
    font-size: 0.95rem;
    line-height: 1.5;
    white-space: pre-wrap;
}
.reply-form textarea {
    width: 100%;
}
.inline-form {
    display: inline-block;
    margin-right: 10px;
}
</style>
