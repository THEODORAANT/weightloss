<?php
$isLoggedIn = perch_member_logged_in();

perch_layout('client/header', [
    'page_title' => perch_page_title(true),
]);
?>

<section class="client-page">
  <div class="container all_content">
    <div class="client-hero">
      <?php if ($isLoggedIn) { ?>
        <h1>Manage your account</h1>
        <p>Update your profile details, keep your addresses current and review everything related to your treatment in one place.</p>
      <?php } else { ?>
        <h1>Welcome back to Get Weight Loss</h1>
        <p>Create an account to begin your consultation or log in to pick up where you left off. Your information is securely saved.</p>
      <?php } ?>
    </div>

    <div class="client-columns">
      <div>
        <div class="client-card">
          <?php if ($isLoggedIn) { ?>
            <div class="client-card__section">
              <h2 class="client-card__title">Profile details</h2>
              <p class="client-card__intro">Keep your personal details up to date so our clinicians can tailor their care and communications to you.</p>
              <?php perch_member_form('profile.html');
               $phoneRegistered = perch_twillio_is_customerphone_registered();
               $phoneVerified = perch_twillio_customer_verified();
               ?>
              <div class="d-flex align-items-center gap-3 mt-3 flex-wrap">
                <div class="badge bg-<?php echo $phoneVerified ? 'success' : 'warning text-dark'; ?> text-uppercase px-3 py-2">
                  <?php echo $phoneVerified ? 'Phone verified' : ($phoneRegistered ? 'Pending verification' : 'Phone not added'); ?>
                </div>
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#phoneModal">
                  <?php echo $phoneRegistered ? 'Verify phone' : 'Add phone'; ?>
                </button>
              </div>
            </div>
          <?php } else { ?>
            <div class="client-card__section">
              <h2 class="client-card__title">Create an account</h2>
              <p class="client-card__intro">Set up your profile in a couple of minutes so you can complete your consultation, upload documents and receive tailored treatment plans.</p>
              <?php

     $affiliate_referrer = '';

        if (!empty($_SESSION['affiliate_referrer'])) {
                $affiliate_referrer = preg_replace('/[^A-Za-z0-9]/', '', (string) $_SESSION['affiliate_referrer']);
        } elseif (!empty($_COOKIE['affiliate_referrer'])) {
                $affiliate_referrer = preg_replace('/[^A-Za-z0-9]/', '', (string) $_COOKIE['affiliate_referrer']);
        }
        PerchSystem::set_var('affiliate_referrer', $affiliate_referrer);
               perch_shop_registration_form(['template' => 'checkout/customer_create_wl.html']); ?>
            </div>
          <?php } ?>
        </div>
      </div>

      <div>
        <aside class="client-sidecard">
          <?php if ($isLoggedIn) { ?>
            <h2 class="client-sidecard__title">Delivery &amp; billing addresses</h2>
            <p class="client-sidecard__intro">Review the addresses saved on your account and keep them current before your next order ships.</p>
            <div class="client-panel__body">
              <?php perch_shop_customer_addresses(); ?>
            </div>
          <?php } else { ?>
            <h2 class="client-sidecard__title">Already registered?</h2>
            <p class="client-sidecard__intro">Sign in to continue your consultation, review your past orders and message our support team.</p>
            <?php perch_shop_login_form(); ?>
          <?php } ?>
        </aside>
      </div>
    </div>
  </div>
</section>
<?php if ($isLoggedIn) { ?>
  <div class="modal fade" id="phoneModal" tabindex="-1" aria-labelledby="phoneModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content p-2 p-md-4">
        <div class="modal-header border-0 pb-0">
          <div>
            <p class="text-uppercase text-muted small mb-1">Phone verification</p>
            <h2 class="modal-title h4" id="phoneModalLabel">
              <?php echo $phoneRegistered ? 'Verify your phone number' : 'Add your phone number'; ?>
            </h2>
            <p class="text-muted mb-0">
              <?php echo $phoneRegistered ? 'Enter the code we sent to confirm your phone number.' : 'Add your phone details so we can send you a verification code.'; ?>
            </p>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <?php
            if (!$phoneRegistered) {
              perch_twillio_registration_form();
            } elseif (!$phoneVerified) {
              perch_twillio_customer_confirmPhone_form([
                'return_url' => '/client/verify_phonecode',
              ]);
            } else {
              echo '<div class="alert alert-success mb-0" role="alert">Your phone number is already verified.</div>';
            }
          ?>
        </div>
      </div>
    </div>
  </div>
<?php } ?>

<?php /*
<script>
    const addressInput = document.getElementById('form1_shipping_postcode');
    const suggestionsBox = document.getElementById('suggestions');
    const resultDisplay = document.getElementById('selectedResult');

    let debounceTimer;

    addressInput.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        const query = addressInput.value.trim();
        if (query.length < 3) {
            suggestionsBox.innerHTML = '';
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch('client/lookup-postcode', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'query=' + encodeURIComponent(query)
            })
                .then(response => response.json())
                .then(data => {
                    suggestionsBox.innerHTML = '';
                    if (data.length === 0) return;

                    data.forEach(item => {
                        const div = document.createElement('div');
                        div.textContent = item;
                        div.classList.add('form-control');
                        div.onclick = () => {
                            addressInput.value = item;
                            suggestionsBox.innerHTML = '';
                            resultDisplay.textContent = "Selected: " + item;
                        };
                        suggestionsBox.appendChild(div);
                    });
                });
        }, 300);
    });

    document.addEventListener('click', (e) => {
        if (!suggestionsBox.contains(e.target) && e.target !== addressInput) {
            suggestionsBox.innerHTML = '';
        }
    });
</script>
*/?>
<?php perch_layout('getStarted/footer'); ?>
