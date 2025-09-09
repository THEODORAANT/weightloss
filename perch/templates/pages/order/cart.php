 <?php   if (perch_member_logged_in() && perch_shop_addresses_set() && isset($_SESSION["package_billing_type"])) {
            $is_reorder = customer_has_paid_order();

                             if ($is_reorder) {
                                 if (empty($_SESSION['questionnaire-reorder'])) {
                                     PerchUtil::redirect('/client/questionnaire-re-order?step=weight');
                                 }
                             } else {
                                 if (empty($_SESSION['questionnaire'])) {
                                     PerchUtil::redirect('/get-started');
                                 }
                             }
           }

  print_r($_SESSION);
    // output the top of the page
     perch_layout('product/header', [
          'page_title' => perch_page_title(true),
      ]);

        /* main navigation
        perch_pages_navigation([
            'levels'   => 1,
            'template' => 'main_nav.html',
        ]);*/
        //echo "session";
      // print_r($_SESSION);


            if(isset($_POST["dose"])){
                $_SESSION['questionnaire-reorder']["dose"] = $_POST["dose"];
               $result= perch_shop_add_to_cart($_POST["dose"]);
               echo "<script>window.location.href='/client/questionnaire-re-order?step=weight';</script> ";
               exit;

                }


            //  echo "perch_member_logged_in".perch_member_logged_in() ;
             //  echo "stripeToken".perch_post('stripeToken');
 if (perch_member_logged_in() && perch_post('stripeToken')) {
//echo "stripeyoke";
  // your 'success' and 'failure' URLs
  $return_url = '/payment/stripe';
  //$cancel_url = 'https://getweightloss-dev-d2c5gpf7asdvh3a2.uksouth-01.azurewebsites.net/payment/went/wrong';
  $cancel_url = '/payment/went/wrong';
  perch_shop_checkout('stripe', [
    'return_url' => $return_url,
    'cancel_url' => $cancel_url,
    'token'      => perch_post('stripeToken')
  ]);
}else if(isset($_GET["success"])){
  // your 'success' and 'failure' URLs
  $return_url = '/payment/success/';
//  $cancel_url = 'https://getweightloss-dev-d2c5gpf7asdvh3a2.uksouth-01.azurewebsites.net/payment/went/wrong';
  $cancel_url = '/payment/went/wrong';
  perch_shop_checkout('stripe', [
    'return_url' => $return_url,
    'cancel_url' => $cancel_url,
    'confirm_klarna'=>true
  ]);
}
    ?>

    <section class="main_order_summary">
        <div class="container mt-5">
            <div class="row">
                <!-- Left Section -->
                <div class="col-md-7">

                        <h2 class="fw-bold">Order summary</h2>

                    <div class="main_page">
                        <!-- Create an Account Section -->
                          <?php     if (!perch_member_logged_in()) { ?>
                        <div class="section-header">
                            <span class="section-number">1</span>
                            <h4>Create an account</h4>
                        </div>
                        <div class="login_sec">
                            <p>Have an account?</p> <span><a href="">Log in</a></span>
                        </div>


                               <?php



                                                                // New customer sign up form
                                                                                            perch_shop_registration_form( ['template' => 'checkout/customer_create_wl.html']);


                                                                }else{

                                                                   ?>




          <div class="section-header">
      <?php


 if (!perch_shop_addresses_set()) {?>

                               </div>
                                     <div class="login_sec">
                                                                <p class="urbanist-regular m-0 flex-grow-1 mb-4"></p>

                                                       </div>
                                                       	<div class="mb-3">
                                                       		<div class="info_requirement">
   <div class="row">

                                     <div class="p-4">
  <?php  perch_shop_order_address_form();
//perch_shop_shipping_method_form();
  }else{ ?>
                                  <h4>Payment with Card or klarna</h4>
                              </div>
                                    <div class="login_sec">
                                                               <p class="urbanist-regular m-0 flex-grow-1 mb-4">Securely complete your payment using your credit or debit card, and take the first step towards better health.</p>

                                                      </div>
                                                      	<div class="mb-3">
                                                      		<div class="info_requirement">
  <div class="row">

                                    <div class="p-4">

      <?php
      if (perch_member_logged_in() && perch_shop_addresses_set() && !isset($_POST["dose"])) {
       /* $return_url = '/payment/stripe';
        //$cancel_url = 'https://getweightloss-dev-d2c5gpf7asdvh3a2.uksouth-01.azurewebsites.net/payment/went/wrong';
        $cancel_url = '/payment/went/wrong';
        perch_shop_checkout('stripe', [
          'return_url' => $return_url,
          'cancel_url' => $cancel_url
        ]);*/

         echo '<div class="d-flex flex-wrap gap-3">
               <button id="stripe-button" onclick="window.location.href=\'/order/checkout?payment_method_types=card\';" class="stripe-button-new">
                   Pay with Card
               </button>
               <button id="klarna-button" onclick="window.location.href=\'/order/checkout?payment_method_types=klarna\';" class="klarna-button btn btn-light border d-flex align-items-center gap-2" style="background-color: #ffb3c7;">
                   <img src="/asset/payment-methods/klarnaicon.png" alt="Klarna Logo" style="height: 24px;" />
                   Pay with Klarna
               </button>
           </div><style>.stripe-button-new {
                   background-color: #6772e5; /* Stripe brand blue */
                   color: #fff;
                   border: none;
                   padding: 12px 20px;
                   border-radius: 12px;
                   font-size: 16px;
                   font-weight: 600;
                   cursor: pointer;
                   transition: background-color 0.3s ease, transform 0.2s ease;
                   display: inline-flex;
                   align-items: center;
                   gap: 8px; /* space between logo and text */
                   text-decoration: none;
                 }

                 .stripe-button-new img {
                   height: 24px;
                 }

                 .stripe-button-new:hover {
                   background-color: #5469d4;
                   transform: translateY(-2px);
                 }

                 .stripe-button-new:active {
                   background-color: #4353aa;
                   transform: translateY(0);
                 }
                 </style>
';
        }
       // perch_shop_shipping_method_form();
        // $stripeform=true;


        /*  perch_shop_payment_form('stripe');
         echo "</div><br/> <div class='p-4'>";
          perch_shop_payment_form('stripe-klarna');*/
  }

      ?></div>
      </div>
      </div>
                                                            	</div>
      <br/>
     <div class="container">
                            <div class="row">

                                    <div class="p-4">
        <img width="92" height="64" src="/asset/payment-methods/visa.png" alt="Visa" />
        <img width="92" height="64" src="/asset/payment-methods/mastercard.png" alt="Mastercard" />
         <img width="92" height="64" src="/asset/payment-methods/klarna.png" alt="Mastercard" />

      </div> </div> </div>
  <?php }?>
                    </div>
                    <!-- Login Section (Initially Hidden) -->
                    <div id="login_section" class="d-none login_page full-width vh-100 d-flex align-items-start justify-content-start bg-light">
                        <div class="container">
                            <div class="row">

                                    <div class="p-4">
                                     <?php        if (!perch_member_logged_in()) { ?>
                                        <h3 class="text-left mb-4 fw-bolder">Log in to your account</h3>
                                        <p class="text-left sign_up_btn">
                                            Please log in to your account to complete your order. <br> Don't have an account? <a href="#"  id="back_to_signup">Sign up</a>
                                        </p>
                                       <?php

                                                                                                      // New customer sign up form

                                                                                                    perch_shop_login_form();


                                                                                                      }

                                                                                                         ?>


                                    </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Section -->
                <div class="col-md-5">
                    <div class="your_order">
                    <?php

                   perch_shop_cart( ['template' => 'cart/cart-sum.html']); ?>

                    </div>
                </div>

            </div>
        </div>

    </section>

    <?php
  perch_layout('getStarted/footer');?>
    <!-- script code for login form -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const loginLink = document.querySelector(".login_sec a");
            const backToSignup = document.getElementById("back_to_signup");
            const mainPage = document.querySelector(".main_page");
            const loginSection = document.getElementById("login_section");

            if (loginLink) {
                loginLink.addEventListener("click", function (event) {
                    event.preventDefault();
                    mainPage.style.display = "none";
                    loginSection.classList.remove("d-none");
                });
            }

            if (backToSignup) {
                backToSignup.addEventListener("click", function (event) {
                    event.preventDefault();
                    mainPage.style.display = "block";
                    loginSection.classList.add("d-none");
                });
            }
        });
    </script>
    <!-- script code for login form -->
