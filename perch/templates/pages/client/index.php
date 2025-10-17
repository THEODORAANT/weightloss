 <?php    /* if (!perch_member_logged_in()) {
  header("Location: /order"); // Redirect to the selected URL
                        exit();
 }*/
 ?>

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

                             <?php     if (!perch_member_logged_in()) { ?> <h2 class="fw-bold">Register</h2><?php } ?>

                        <div class="main_page">
                            <!-- Create an Account Section -->

                               <?php     if (!perch_member_logged_in()) { ?>
                            <div class="section-header">
                             <span class="section-number">1</span>
                                <h4>Create an account</h4>
                            </div>
                            <div class="login_sec">

                            </div>


                               <?php



                                                                // New customer sign up form
                                                                                            perch_shop_registration_form( ['template' => 'checkout/customer_create_wl.html']);



                                                                }else{
                                                                echo '       <div class="section-header">
                                                                                                      <h4>Profile</h4>
                                                                                                  </div>
                                                                                                  <div class="login_sec">

                                                                                                  </div>';
                                                                perch_member_form('profile.html');
                                                                }

                                                                   ?>

                        </div>

                    </div>

                    <!-- Right Section -->
                    <div class="col-md-5">
                        <div class="your_order">
                        <?php        if (!perch_member_logged_in()) { ?>
                            <h4>Login</h4>
                            <div class="order-summary p-3 border rounded">
                             <h3 class="text-left mb-4 fw-bolder">Log in to your account</h3>
                                            <p class="text-left sign_up_btn">
                                                Please log in to your account to complete your order.
                                            </p>




     <?php

                                                                // New customer sign up form

                                                              perch_shop_login_form();


                                                                }else{?>

                                                                                            <div class="order-summary p-3 border rounded">
                                                                                             <h3 class="text-left mb-4 fw-bolder">Shipping and Billing adresess</h3>
                                                                                             <?php

                                                                                               perch_shop_customer_addresses();
                                                                                              /* if (!perch_shop_addresses_set()) {
                                                                                                   perch_shop_order_address_form();
                                                                                                 }*/



                                                                }

                                                                   ?>


                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </section>
<?php /*

<script>
    const addressInput = document.getElementById('form1_shipping_postcode');
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
        }, 300); // debounce to avoid excessive requests
    });

    document.addEventListener('click', (e) => {
        if (!suggestionsBox.contains(e.target) && e.target !== addressInput) {
            suggestionsBox.innerHTML = '';
        }
    });
</script>
*/?>
    <?php
  perch_layout('getStarted/footer');?>
