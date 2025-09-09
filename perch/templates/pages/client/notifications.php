<?php
    perch_layout('client/header', [
        'page_title' => perch_page_title(true),
    ]);

$notifications = perch_member_notifications();
if ($notifications) {
    perch_member_mark_notifications_read();
}
?>

<section class="main_order_summary">
    <div class="container mt-5">
        <h2>Your Notifications</h2>
        <?php if ($notifications) { ?>
            <ul class="list-group">
                <?php foreach ($notifications as $n) { ?>
                    <li class="list-group-item">
                        <?php if (!$n['read']) { ?><span class="unread-dot"></span><?php } ?>
                        <strong><?php echo htmlspecialchars($n['title']); ?></strong>
                        <p><?php echo htmlspecialchars($n['message']); ?></p>
                        <small class="text-muted"><?php echo htmlspecialchars($n['date']); ?></small>
                    </li>
                <?php } ?>
            </ul>
        <?php } else { ?>
            <p>No notifications.</p>
        <?php } ?>
    </div>
</section>

<?php perch_layout('getStarted/footer'); ?>
