 <?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once dirname(__DIR__, 3) . '/addons/apps/perch_members/questionnaire_session_helpers.php';
    if(isset($_POST["dose"])){
                wl_restore_questionnaire_session('reorder');
                $_SESSION['questionnaire-reorder']["dose"] = $_POST["dose"];
                wl_save_questionnaire_session('reorder');
               $result= perch_shop_add_to_cart($_POST["dose"]);
               echo "<script>window.location.href='/client/questionnaire-re-order?step=weight';</script> ";
               exit;

                }

$activeQuestionnaireMode = 'first_time';
if (perch_member_logged_in() && customer_has_paid_order()) {
    $activeQuestionnaireMode = 'reorder';
}

wl_restore_questionnaire_session($activeQuestionnaireMode);

if (perch_member_logged_in() && perch_shop_addresses_set() && isset($_SESSION["package_billing_type"])) {
    $activeQuestionnaireSessionKey = wl_questionnaire_session_meta($activeQuestionnaireMode)['session_key'];

    if (empty($_SESSION[$activeQuestionnaireSessionKey])) {
        if ($activeQuestionnaireMode === 'reorder') {
            PerchUtil::redirect('/client/questionnaire-re-order?step=weight');
        }

        PerchUtil::redirect('/get-started');
    }
}

wl_save_questionnaire_session($activeQuestionnaireMode);
  //print_r($_SESSION);
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

$cart_popup_product_slug ="weight-loss-blood-test-nadl026"; //perch_get('s');
$show_cart_popup_product = false;

if ($cart_popup_product_slug) {
    $ShopRuntime = PerchShop_Runtime::fetch();
    $popup_product_id = (int) $ShopRuntime->get_product_id($cart_popup_product_slug);

    $popup_is_in_cart = false;

    if ($popup_product_id > 0) {
        $cart_data = perch_shop_cart([
            "skip-template" => true,
            "cache" => false,
        ], true);

        if (isset($cart_data['items']) && is_array($cart_data['items'])) {
            foreach ($cart_data['items'] as $cart_item) {
                if ((int) ($cart_item['productID'] ?? 0) === $popup_product_id) {
                    $popup_is_in_cart = true;
                    break;
                }
            }
        }
    }
    $show_cart_popup_product = !$popup_is_in_cart;
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
}/*else if(isset($_GET["success"])){
  // your 'success' and 'failure' URLs
  $return_url = '/payment/success/';
//  $cancel_url = 'https://getweightloss-dev-d2c5gpf7asdvh3a2.uksouth-01.azurewebsites.net/payment/went/wrong';
  $cancel_url = '/payment/went/wrong';
  perch_shop_checkout('stripe', [
    'return_url' => $return_url,
    'cancel_url' => $cancel_url,
    'confirm_klarna'=>true
  ]);
}*/
    ?>

<?php
$show_cart_popup_product = $show_cart_popup_product ?? false;
$cart_popup_product_slug = $cart_popup_product_slug ?? '';

if (!$show_cart_popup_product || !$cart_popup_product_slug) {
    return;
}
?>
<div id="cart-product-popup" class="cart-product-popup-overlay" aria-hidden="true">
    <div class="cart-product-popup-modal" role="dialog" aria-modal="true" aria-labelledby="cart-product-popup-title">
        <button type="button" id="close-cart-product-popup" class="cart-product-popup-close" aria-label="Close popup">&times;</button>
        <div class="cart-product-popup-shell">
            <div class="cart-product-popup-header">
                <p class="cart-product-popup-kicker">New in your plan</p>
                <h2 id="cart-product-popup-title">Add this product to your cart</h2>
                <p class="cart-product-popup-subtitle">Review the product details below and continue when you are ready.</p>
            </div>
            <div class="cart-product-popup-content">
                <?php
                perch_shop_product($cart_popup_product_slug, [
                    'template' => 'products/popup_product.html',
                ]);
                ?>
            </div>
        </div>
    </div>
</div>

<style>
  .cart-product-popup-overlay {
    position: fixed;
    inset: 0;
    background: radial-gradient(circle at top, rgba(51, 40, 191, 0.24), rgba(15, 23, 42, 0.78));
    backdrop-filter: blur(2px);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.25s ease;
  }

  .cart-product-popup-overlay.is-open {
    opacity: 1;
    pointer-events: auto;
  }

  .cart-product-popup-modal {
    width: min(980px, 100%);
    max-height: 92vh;
    overflow: auto;
    background: #fff;
    border: 1px solid #dbe3f3;
    border-radius: 20px;
    position: relative;
    padding: 20px;
    transform: translateY(16px) scale(0.98);
    opacity: 0;
    transition: transform 0.25s ease, opacity 0.25s ease;
    box-shadow: 0 28px 60px rgba(15, 23, 42, 0.25);
  }

  .cart-product-popup-overlay.is-open .cart-product-popup-modal {
    transform: translateY(0) scale(1);
    opacity: 1;
  }

  .cart-product-popup-close {
    position: absolute;
    top: 16px;
    right: 16px;
    border: 1px solid #dbe3f3;
    background: rgba(255, 255, 255, 0.98);
    width: 38px;
    height: 38px;
    border-radius: 999px;
    font-size: 26px;
    line-height: 1;
    cursor: pointer;
    color: #1e2a47;
    z-index: 2;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
  }

  .cart-product-popup-shell {
    overflow: hidden;
    border-radius: 16px;
    background: linear-gradient(180deg, #f8fbff 0%, #ffffff 26%);
  }

  .cart-product-popup-header {
    padding: 26px 28px 18px;
    border-bottom: 1px solid #ecf1fb;
  }

  .cart-product-popup-kicker {
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #425a9e;
    margin: 0 0 8px;
  }

  .cart-product-popup-header h2 {
    margin: 0;
    font-size: clamp(22px, 2vw, 30px);
    line-height: 1.2;
    color: #1f2f57;
  }

  .cart-product-popup-subtitle {
    margin: 10px 0 0;
    color: #5b6788;
    font-size: 15px;
    max-width: 60ch;
  }

  .cart-product-popup-content {
    padding: 22px 28px 26px;
  }

  @media (max-width: 768px) {
    .cart-product-popup-modal {
      padding: 14px;
      border-radius: 16px;
    }

    .cart-product-popup-header {
      padding: 20px 18px 14px;
    }

    .cart-product-popup-content {
      padding: 16px 18px 20px;
    }
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const popup = document.getElementById('cart-product-popup');
    const closeBtn = document.getElementById('close-cart-product-popup');
    if (!popup || !closeBtn) return;

    const openPopup = function () {
      popup.classList.add('is-open');
      popup.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
    };

    const closePopup = function () {
      popup.classList.remove('is-open');
      popup.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
    };

    window.requestAnimationFrame(openPopup);
    closeBtn.addEventListener('click', closePopup);

    popup.addEventListener('click', function (event) {
      if (event.target === popup) closePopup();
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') closePopup();
    });
  });
</script>

    <section class="main_order_summary">
        <div class="container mt-5">
            <div class="row">
                <!-- Left Section -->
                <div class="col-md-7">

                        <h2 class="fw-bold">Order summary</h2>
         <a href="/shop" class="continue-shopping-link text-decoration-none">
                                <span aria-hidden="true">&#8592;</span>
                                Continue shopping
                            </a>
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

   $affiliate_referrer = '';

        if (!empty($_SESSION['affiliate_referrer'])) {
                $affiliate_referrer = preg_replace('/[^A-Za-z0-9]/', '', (string) $_SESSION['affiliate_referrer']);
        } elseif (!empty($_COOKIE['affiliate_referrer'])) {
                $affiliate_referrer = preg_replace('/[^A-Za-z0-9]/', '', (string) $_COOKIE['affiliate_referrer']);
        }
        PerchSystem::set_var('affiliate_referrer', $affiliate_referrer);

                                                                // New customer sign up form
                                                                                            perch_shop_registration_form( ['template' => 'checkout/customer_create_wl.html']);


                                                                }else{

                                                                   ?>




          <div class="section-header">
      <?php


 if (!perch_shop_addresses_set()) {?>
                              <script>
                              if (typeof rdt === 'function') {
                                rdt('track', 'SignUp');
                              }
                              </script>
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
?>
<script>
if (typeof gtag === 'function') {
  gtag('event', 'begin_checkout', { currency: 'GBP' });
}
</script>
<?php
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
               </button>';
              /* echo ' <button id="klarna-button" onclick="window.location.href=\'/order/checkout?payment_method_types=klarna\';" class="klarna-button btn btn-light border d-flex align-items-center gap-2" style="background-color: #ffb3c7;">
                   <img src="/asset/payment-methods/klarnaicon.png" alt="Klarna Logo" style="height: 24px;" />
                   Pay with Klarna
               </button>';*/
             echo '</div><style>.stripe-button-new {
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
          <!-- <img width="92" height="64" src="/asset/payment-methods/klarna.png" alt="Mastercard" />-->

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
