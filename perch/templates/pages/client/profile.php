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
      <h1>Your profile</h1>
      <p>Review and update the personal information linked to your account. Accurate details help our clinicians personalise your treatment.</p>
    </div>

    <div class="row justify-content-center">
      <div class="col-xl-7 col-lg-8">
        <div class="client-card">
          <div class="client-card__section">
            <h2 class="client-card__title">Account information</h2>
            <p class="client-card__intro">Make changes to your name, contact details and any other personal information here.</p>
            <?php perch_member_form('profile.html'); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php perch_layout('getStarted/footer'); ?>
