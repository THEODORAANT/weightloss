<?php
if (!perch_member_logged_in()) {
    PerchUtil::redirect('/client');
    exit;
}

perch_layout('client/header', [
    'page_title' => perch_page_title(true),
]);

$notifications = perch_member_notifications();
if ($notifications) {
    perch_member_mark_notifications_read();
}
?>

<section class="client-page">
  <div class="container all_content">
    <div class="client-hero">
      <h1>Your notifications</h1>
      <p>Stay up to date with updates from our clinicians and account alerts. We&apos;ll highlight anything new right here.</p>
    </div>

    <div class="row justify-content-center">
      <div class="col-xl-8 col-lg-9">
        <?php if ($notifications) { ?>
          <div class="client-card">
            <div class="client-card__section">
              <h2 class="client-card__title">Recent updates</h2>
              <p class="client-card__intro">Messages from the team and important account reminders appear below. We&apos;ve marked any unread alerts for you.</p>
              <ul class="client-list">
                <?php foreach ($notifications as $n) { ?>
                  <li class="client-list__item">
                    <div class="client-actions justify-content-between">
                      <div>
                        <div class="client-list__title">
                          <?php echo htmlspecialchars($n['title']); ?>
                          <?php if (!$n['read']) { ?><span class="client-pill"><span class="unread-dot"></span>New</span><?php } ?>
                        </div>
                        <p class="client-list__body"><?php echo htmlspecialchars($n['message']); ?></p>
                      </div>
                      <time class="text-muted small" datetime="<?php echo htmlspecialchars($n['date']); ?>">
                        <?php echo htmlspecialchars($n['date']); ?>
                      </time>
                    </div>
                  </li>
                <?php } ?>
              </ul>
            </div>
          </div>
        <?php } else { ?>
          <div class="client-empty">
            <h3>You&apos;re all caught up</h3>
            <p>There are no new notifications right now. We&apos;ll let you know here as soon as something needs your attention.</p>
          </div>
        <?php } ?>
      </div>
    </div>
  </div>
</section>

<?php perch_layout('getStarted/footer'); ?>
