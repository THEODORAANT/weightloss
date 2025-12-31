<?php
$isLoggedIn = perch_member_logged_in();

perch_layout('client/header', [
    'page_title' => perch_page_title(true),
]);
?>

<section class="client-page">
  <div class="container">
    <div class="modal fade" id="verifyCodeModal" tabindex="-1" aria-labelledby="verifyCodeModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content p-2 p-md-4">
          <div class="modal-header border-0 pb-0">
            <div>
              <p class="text-uppercase text-muted small mb-1">Phone verification</p>
              <h2 class="modal-title h4" id="verifyCodeModalLabel">Enter your verification code</h2>
              <p class="text-muted mb-0">We sent a code to the number you provided. Enter it below to confirm your phone.</p>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <?php if (!perch_twillio_customer_verified()) { ?>
              <?php verify_customer_from_form(); ?>
            <?php } else { ?>
              <div class="alert alert-success mb-0" role="alert">Your phone number is already verified.</div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var verifyModalEl = document.getElementById('verifyCodeModal');
    if (verifyModalEl) {
      var verifyModal = new bootstrap.Modal(verifyModalEl);
      verifyModal.show();
    }
  });
</script>

<?php perch_layout('getStarted/footer'); ?>
