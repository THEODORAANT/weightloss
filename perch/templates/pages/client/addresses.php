
    <?php  // output the top of the page
    perch_layout('client/header', [
        'page_title' => perch_page_title(true),
    ]);

        /* main navigation
        perch_pages_navigation([
            'levels'   => 1,
            'template' => 'main_nav.html',
        ]);*/

    ?>

        <section class="main_order_summary">
            <div class="container mt-5">
                <div class="row">
                    <!-- Left Section -->
                    <div class="col-md-7">


                        <div class="main_page">
                            <!-- Create an Account Section -->



                            <div class="login_sec">

                            </div>
       <?php      perch_shop_edit_address_form(perch_get('addressID'));  ?>

                        </div>

                    </div>
  </div>
    </div>
      </section>

<script>
    const addressInput = document.getElementById('form1_address_1');
    //console.log("addressInput");console.log(addressInput);
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
            fetch('lookup-postcode', {
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
                          div.classList.add('form-control');

                        div.textContent = item;
                        div.onclick = () => {
                            addressInput.value = item;
                            suggestionsBox.innerHTML = '';
                            resultDisplay.textContent = "Selected: " + item;
                        };
                        suggestionsBox.appendChild(div);
                    });
                });
        }, 300); // debounce to avoid excessive requests
    });

    document.addEventListener('click', (e) => {
        if (!suggestionsBox.contains(e.target) && e.target !== addressInput) {
            suggestionsBox.innerHTML = '';
        }
    });
</script>
          <?php
        perch_layout('getStarted/footer');?>
