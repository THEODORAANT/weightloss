<?php if (!perch_member_logged_in() ){ header("Location: /client");}
else if (perch_member_logged_in() &&  !customer_has_paid_order()) {  header("Location: /get-started"); }

    // output the top of the page
    perch_layout('getStarted/header', [
        'page_title' => perch_page_title(true),
    ]);
    ?>
      <style>


            .subheader {
              background-color: #fff;
              border-bottom: 1px solid #ddd;
            }

            .welcome-msg {
              padding: 12px 20px;
              font-size: 16px;
              color: #333;
              border-bottom: 1px solid #eee;
            }

            .tabs {
              display: flex;
              padding: 0 20px;
              background-color: #f9f9f9;
            }

            .tab {
              padding: 12px 16px;
              margin-right: 10px;
              text-decoration: none;
              color: #555;
              border-bottom: 3px solid transparent;
              transition: all 0.2s ease;
              font-weight: 500;
            }

            .tab:hover {
              color: #000;
              border-color: #007bff;
            }

            .tab.active {
              color: #007bff;
              border-color: #007bff;
              background-color: #fff;
            }
            .last-pen-reminder {
              margin: 0 auto 1.5rem;
              text-align: center;
              color: #555;
              max-width: 480px;
            }
          </style>
             <?php if (perch_member_logged_in()) { ?>
  <div class="subheader">

     <div class="welcome-msg">
       Hello, <strong><?php echo perch_member_get('first_name'); ?></strong>
     </div>
    <?php $currentUrl =  $_SERVER['REQUEST_URI'];

     $parts = explode('/', $currentUrl);
     $lastPart = end($parts);
     // echo  $lastPart;
      $profile_tab="";
      $orders_tab="";
      $reorder_tab="";
       $documents_tab="";
        $affiliate_tab="";
  if($lastPart=="client"){
  $profile_tab="active";
  }else if( $lastPart=="orders" ){
   $orders_tab="active";
  }else if( $lastPart=="re-order"){
      $reorder_tab="active";
     }else if($lastPart=="success" ){
           $documents_tab="active";
           }else if($lastPart=="affiliate-dashboard" ){

           $affiliate_tab="active";
           }
      ?>
     <div class="tabs">
       <a href="/client" class="tab <?php echo $profile_tab; ?>">Profile</a>
                     <a href="/payment/success" class="tab <?php echo $documents_tab; ?>">Documents</a>

       <a href="/client/orders" class="tab <?php echo $orders_tab; ?>">Orders</a>
       <a href="/client/affiliate-dashboard" class="tab <?php echo $affiliate_tab; ?>">Affiliate</a>
       <a href="/order/re-order" class="tab <?php echo $reorder_tab; ?>">Order</a>
       <a href="/client/logout" class="tab ">Logout</a>
     </div>


   </div>
   <?php  } ?>

    <?php
        $last_pen_brand = null;
        $last_pen_dose = null;

        $last_pen_details = perch_shop_last_pen_details();

        if (is_array($last_pen_details)) {
            $last_pen_brand = $last_pen_details['brand'] ?? null;
            $last_pen_dose  = $last_pen_details['dose'] ?? null;
        }
    ?>

        <div class="main_product">
            <div id="product-selection">
               <h2 class="text-center fw-bolder">Order your next dose </h2>
               <?php if ($last_pen_brand || $last_pen_dose) { ?>
               <p class="last-pen-reminder">
                 <?php
                     $parts = [];
                     if ($last_pen_brand) {
                         $parts[] = PerchUtil::html($last_pen_brand);
                     }
                     if ($last_pen_dose) {
                         $parts[] = PerchUtil::html($last_pen_dose);
                     }
                     echo 'Your last pen was ' . implode(' – ', $parts) . '.';
                 ?>
               </p>
               <?php } ?>
    <?php


            perch_shop_products(['category' => 'products/weight-loss','template'=>'products/list_for_reorder']);

            ?>





            </div></div>

    <?php
//perch_shop_product('mounjaro-mounjaro');

           // perch_shop_products([    'template' => 'cart/cart-sum.html','category' => 'products/weight-loss']);
//perch_shop_product('wegovy-skuwegovy');

            ?>







        <?php
      perch_layout('getStarted/footer');?>
