<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!perch_member_logged_in()) {
    header('Location: /client');
    exit;
}

$memberID = perch_member_get('id');
if (!$memberID) {
    $memberID = perch_member_get('memberID');
}

$appointments = [];

$API = new PerchAPI(1.0, 'perch_appointments');
$DB = $API->get('DB');
$table = PERCH_DB_PREFIX . 'appointments';

if ($memberID) {
    $sql = 'SELECT appointmentID, productName, productPrice, appointmentDate, appointmentDateLabel, slotLabel, goal, medical, notes, createdAt '
        . 'FROM ' . $table . ' '
        . 'WHERE memberID=' . $DB->pdb((int) $memberID) . ' '
        . 'ORDER BY appointmentDate DESC, appointmentID DESC';

    $rows = $DB->get_rows($sql);
    if (is_array($rows)) {
        $appointments = $rows;
    }
}

perch_layout('client/header', [
    'page_title' => perch_page_title(true),
]);
?>

<section class="client-page">
  <div class="container all_content">
    <div class="client-hero">
      <h1>My appointments</h1>
      <p>View your booked nutrition and wellbeing appointments in one place.</p>
    </div>

    <?php if (!empty($appointments)): ?>
      <div class="appointments-list">
        <?php foreach ($appointments as $appointment): ?>
          <article class="appointment-item">
            <header class="appointment-item__header">
              <h2><?php echo htmlspecialchars($appointment['productName'] ?? 'Appointment', ENT_QUOTES, 'UTF-8'); ?></h2>
              <span class="appointment-item__price">£<?php echo number_format((float) ($appointment['productPrice'] ?? 0), 2); ?></span>
            </header>
            <dl class="appointment-item__meta">
              <div>
                <dt>Date</dt>
                <dd><?php echo htmlspecialchars($appointment['appointmentDateLabel'] ?? ($appointment['appointmentDate'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?></dd>
              </div>
              <div>
                <dt>Time slot</dt>
                <dd><?php echo htmlspecialchars($appointment['slotLabel'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></dd>
              </div>
              <div>
                <dt>Booked on</dt>
                <dd><?php echo htmlspecialchars(date('d M Y, H:i', strtotime((string) ($appointment['createdAt'] ?? 'now'))), ENT_QUOTES, 'UTF-8'); ?></dd>
              </div>
            </dl>

            <?php if (!empty($appointment['goal'])): ?>
              <div class="appointment-item__section">
                <h3>Session goal</h3>
                <p><?php echo nl2br(htmlspecialchars($appointment['goal'], ENT_QUOTES, 'UTF-8')); ?></p>
              </div>
            <?php endif; ?>

            <?php if (!empty($appointment['medical'])): ?>
              <div class="appointment-item__section">
                <h3>Medical notes</h3>
                <p><?php echo nl2br(htmlspecialchars($appointment['medical'], ENT_QUOTES, 'UTF-8')); ?></p>
              </div>
            <?php endif; ?>
          </article>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="empty-state">
        <h2>No appointments yet</h2>
        <p>You haven’t booked any appointments yet. Once you book one, it will appear here.</p>
        <a href="/client/appointments" class="btn btn-primary">Book an appointment</a>
      </div>
    <?php endif; ?>
  </div>
</section>

<style>
  .appointments-list { display:grid; gap:16px; }
  .appointment-item { background:#fff; border:1px solid #e5e7eb; border-radius:14px; padding:16px; box-shadow:0 10px 22px rgba(15,23,42,.05); }
  .appointment-item__header { display:flex; justify-content:space-between; gap:12px; align-items:flex-start; margin-bottom:12px; }
  .appointment-item__header h2 { margin:0; font-size:1.15rem; }
  .appointment-item__price { background:#eef2ff; color:#312e81; border-radius:999px; padding:6px 12px; font-weight:700; }
  .appointment-item__meta { display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:10px; margin:0 0 10px; }
  .appointment-item__meta dt { color:#64748b; font-size:.82rem; text-transform:uppercase; letter-spacing:.05em; }
  .appointment-item__meta dd { margin:2px 0 0; font-weight:600; }
  .appointment-item__section { border-top:1px solid #e5e7eb; padding-top:10px; margin-top:10px; }
  .appointment-item__section h3 { margin:0 0 6px; font-size:.95rem; }
  .appointment-item__section p { margin:0; color:#334155; }
  .empty-state { text-align:center; border:1px dashed #cbd5e1; border-radius:14px; padding:28px 20px; background:#fff; }
  .empty-state h2 { margin-bottom:8px; }
  .empty-state p { margin:0 0 14px; color:#64748b; }
</style>

<?php perch_layout('getStarted/footer'); ?>
