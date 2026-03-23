<?php
if (!perch_member_logged_in()) {
    PerchUtil::redirect('/client');
}

perch_layout('client/header', [
    'page_title' => perch_page_title(true),
]);
?>

<section class="client-page">
  <div class="container all_content">
    <div class="client-hero">
      <h1>Change your password</h1>
      <p>Keep your account secure by updating your password regularly. Use a strong password that you do not reuse anywhere else.</p>
    </div>

    <div class="row justify-content-center">
      <div class="col-xl-7 col-lg-8">
        <div class="client-card">
          <div class="client-card__section">
            <h2 class="client-card__title">Update password</h2>
            <p class="client-card__intro">Enter your current password, then choose a new one. Your changes apply immediately after you save.</p>
            <?php perch_member_form('password_client.html'); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php perch_layout('getStarted/footer'); ?>
