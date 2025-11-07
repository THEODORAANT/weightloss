<?php
perch_layout('client/header', [
    'page_title' => perch_page_title(true),
]);
?>

<section class="client-page">
  <div class="container all_content">
    <div class="client-hero">
      <h1>Reset your password</h1>
      <p>Enter the email associated with your account and we&apos;ll send you a secure link to create a new password.</p>
    </div>

    <div class="row justify-content-center">
      <div class="col-xl-6 col-lg-7">
        <div class="client-card">
          <div class="client-card__section">
            <h2 class="client-card__title">Send reset email</h2>
            <p class="client-card__intro">We&apos;ll email you instructions immediately. If you don&apos;t see the email within a few minutes, check your spam folder.</p>
            <?php perch_member_form('reset_password.html'); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php perch_layout('getStarted/footer'); ?>
