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
              <?php perch_member_form('profile.html'); ?>
            </div>
          <?php } else { ?>
            <div class="client-card__section">
              <h2 class="client-card__title">Create an account</h2>
              <p class="client-card__intro">Set up your profile in a couple of minutes so you can complete your consultation, upload documents and receive tailored treatment plans.</p>
              <?php perch_shop_registration_form(['template' => 'checkout/customer_create_wl.html']); ?>
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
