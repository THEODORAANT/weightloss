<?php
if (!perch_member_logged_in()) {
    PerchUtil::redirect('/client');
    exit;
}

perch_layout('client/header', [
    'page_title' => perch_page_title(true),
]);
?>

<section class="client-page">
  <div class="container all_content">
    <div class="client-hero">
      <h1>Edit your address</h1>
      <p>Keep your delivery details accurate so your treatments arrive without delay. Update the fields below and save your changes.</p>
    </div>

    <div class="row justify-content-center">
      <div class="col-xl-7 col-lg-9">
        <div class="client-card">
          <div class="client-card__section">
            <h2 class="client-card__title">Address details</h2>
            <p class="client-card__intro">Adjust your billing or shipping address information. We use these details for deliveries and important account updates.</p>
            <?php perch_shop_edit_address_form(perch_get('addressID')); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
    const addressInput = document.getElementById('form1_address_1');
    const suggestionsBox = document.getElementById('suggestions');
    const resultDisplay = document.getElementById('selectedResult');

    if (addressInput && suggestionsBox) {
        let debounceTimer;

        addressInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            const query = addressInput.value.trim();
            if (query.length < 3) {
                suggestionsBox.innerHTML = '';
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch('lookup-postcode', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'query=' + encodeURIComponent(query)
                })
                    .then(response => response.json())
                    .then(data => {
                        suggestionsBox.innerHTML = '';
                        if (!Array.isArray(data) || data.length === 0) return;

                        data.forEach(item => {
                            const div = document.createElement('div');
                            div.classList.add('form-control');
                            div.textContent = item;
                            div.onclick = () => {
                                addressInput.value = item;
                                suggestionsBox.innerHTML = '';
                                if (resultDisplay) {
                                    resultDisplay.textContent = 'Selected: ' + item;
                                }
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
    }
</script>
<?php perch_layout('getStarted/footer'); ?>
