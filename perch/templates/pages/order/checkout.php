<?php //include('../perch/runtime.php');
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['questionnaire']) && isset($_COOKIE['questionnaire'])) {
    $_SESSION['questionnaire'] = json_decode($_COOKIE['questionnaire'], true) ?: [];
}
if (empty($_SESSION['questionnaire-reorder']) && isset($_COOKIE['questionnaire_reorder'])) {
    $_SESSION['questionnaire-reorder'] = json_decode($_COOKIE['questionnaire_reorder'], true) ?: [];
}
/*
$orderIdForQuestionnaire = perch_shop_successful_order_id();
if (!$orderIdForQuestionnaire) {
    $ShopRuntime = PerchShop_Runtime::fetch();
    if ($ShopRuntime) {
        $ActiveOrder = $ShopRuntime->get_active_order();
        if ($ActiveOrder) {
            $orderIdForQuestionnaire = $ActiveOrder->id();
        }
    }
}
*/
 $return_url = '/payment/stripe';
  //$cancel_url = 'https://getweightloss-dev-d2c5gpf7asdvh3a2.uksouth-01.azurewebsites.net/payment/went/wrong';
  $cancel_url = '/payment/went/wrong';
  if(isset($_GET["payment_method_types"])){
   $payment_method_types="card";
  $payment_method_types=$_GET["payment_method_types"];
  }
 perch_shop_checkout('stripe', [
  'payment_method_types'=>$payment_method_types,
    'return_url' => $return_url,
    'cancel_url' => $cancel_url
  ]
?>
<?php
   /* $stripeform=false;

     perch_layout('product/header', [
          'page_title' => perch_page_title(true),
      ]);

 if (perch_member_logged_in() && perch_post('stripeToken')) {

  // your 'success' and 'failure' URLs
  $return_url = '/payment/stripe';
  $cancel_url = '/payment/went/wrong';
  perch_shop_checkout('stripe', [
    'return_url' => $return_url,
    'cancel_url' => $cancel_url,
    'token'      => perch_post('stripeToken')
  ]);
}else if(isset($_GET["success"])){
  // your 'success' and 'failure' URLs
  $return_url = '/payment/success/';
  $cancel_url = '/payment/went/wrong';
 // echo "here";
  perch_shop_checkout('stripe', [
    'return_url' => $return_url,
    'cancel_url' => $cancel_url,
    'confirm_klarna'=>true
  ]);
}else  if (perch_member_logged_in()) {
  ?>

  <main class="page__content page__gap container mt-5 checkout">

  <div class="page__group_title d-flex flex-column flex-lg-row justify-content-between align-items-lg-center">
    <h1 class="page__title urbanist-medium m-0 flex-grow-1 lh-sm">
      Payment Methods
    </h1>
    <p class="urbanist-regular m-0 flex-grow-1">
      We currently accept payments through credit cards only, ensuring a secure and seamless checkout experience.
    </p>
  </div>

  <div class="p-5 col-md-5 offset-md-4 grey-border">
      <div class="d-flex gap-3">
        <img width="92" height="64" src="/assets/img/payment-methods/visa.png" alt="Visa" />
        <img width="92" height="64" src="/assets/img/payment-methods/mastercard.png" alt="Mastercard" />
         <img width="92" height="64" src="/assets/img/payment-methods/klarna.png" alt="Mastercard" />

      </div>
      <h3 class="urbanist-bold mt-3">Payment with Card or klarna</h3>
      <p class="urbanist-regular m-0 flex-grow-1 mb-4">Securely complete your payment using your credit or debit card, and take the first step towards better health.</p>
      <?php
        // $stripeform=true;
        perch_shop_payment_form('stripe');
        echo "<br/>";
        perch_shop_payment_form('stripe-klarna');


      ?>
  </div>



  <?php
}
?>
</main>


    <?php
  perch_layout('getStarted/footer'); */?>
