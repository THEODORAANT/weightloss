<?php
$heading = $Lang->get('Member chat conversations');

echo $HTML->title_panel([
    'heading' => $heading,
]);


$smartbar = new PerchSmartbar($CurrentUser, $HTML, $Lang);
$smartbar->add_item([
    'title' => $Lang->get('Open'),
    'link'  =>'/perch/addons/apps/perch_members/chat/?status=open',
    'active' => ($status === 'open'),
]);
$smartbar->add_item([
    'title' => $Lang->get('Closed'),
    'link'  =>  '/perch/addons/apps/perch_members/chat/?status=closed',
    'active' => ($status === 'closed'),
]);
$smartbar->add_item([
    'title' => $Lang->get('All'),
    'link'  =>  '/perch/addons/apps/perch_members/chat/?status=all',
    'active' => ($status === 'all'),
]);

echo $smartbar->render();

if (!$tables_ready) {
    echo $HTML->warning_message($Lang->get('Chat tables have not been created yet. Run the SQL script in %s.', '<code>sql/create_chat_tables.sql</code>'));
    return;
}

if (!PerchUtil::count($threads)) {
    echo $HTML->warning_message($Lang->get('No conversations found. Conversations will appear once members send their first message.'));
    return;
}

$rows = [];
foreach ($threads as $item) {
    $thread = $item['thread'];
    $memberData = $item['member'];
    $lastMessage = $item['last_message'];

    $memberName = $Lang->get('Member #%s', $thread['memberID']);
    if (is_array($memberData)) {
        $first = isset($memberData['first_name']) ? trim($memberData['first_name']) : '';
        $last = isset($memberData['last_name']) ? trim($memberData['last_name']) : '';
        $email = isset($memberData['memberEmail']) ? trim($memberData['memberEmail']) : '';
        $combined = trim($first . ' ' . $last);
        if ($combined !== '') {
            $memberName = $combined;
        } elseif ($email !== '') {
            $memberName = $email;
        }
    }

    $preview = '';
    if ($lastMessage) {
        $body = (string)$lastMessage['body'];
        $preview = $HTML->encode(PerchUtil::excerpt_char($body, 140, false, false, '…'));
    }

    $statusLabel = $thread['status'] === 'closed' ? $Lang->get('Closed') : $Lang->get('Open');
    $statusClass = $thread['status'] === 'closed' ? 'tag-warning' : 'tag-success';

    $rows[] = [
        'member' => '<a href="' . $API->app_path() . '/chat/thread.php?id=' . (int)$thread['id'] . '">' . $HTML->encode($memberName) . '</a>' . ($item['staff_has_unread'] ? ' <span class="tag tag-alert">' . $Lang->get('Unread') . '</span>' : ''),
        'last_message' => $preview ?: '<span class="hint">' . $Lang->get('No messages yet') . '</span>',
        'updated' => $thread['last_message_at'] ? date('d M Y H:i', strtotime($thread['last_message_at'])) : $Lang->get('Never'),
        'status' => '<span class="tag ' . $statusClass . '">' . $statusLabel . '</span>',
    ];
}

echo '<table class="d">';
echo '<thead>';
echo '<tr>';
echo '<th>' . $HTML->encode($Lang->get('Member')) . '</th>';
echo '<th>' . $HTML->encode($Lang->get('Last message')) . '</th>';
echo '<th>' . $HTML->encode($Lang->get('Updated')) . '</th>';
echo '<th>' . $HTML->encode($Lang->get('Status')) . '</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

foreach ($rows as $row) {
    echo '<tr>';
    echo '<td>' . $row['member'] . '</td>';
    echo '<td>' . $row['last_message'] . '</td>';
    echo '<td>' . $HTML->encode($row['updated']) . '</td>';
    echo '<td>' . $row['status'] . '</td>';
    echo '</tr>';
}

echo '</tbody>';
echo '</table>';
