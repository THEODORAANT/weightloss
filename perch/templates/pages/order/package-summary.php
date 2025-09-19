<?php  if (session_status() === PHP_SESSION_NONE) {
              session_start();
          }
          $_SESSION['perch_shop_package_monthly_checkout'] = false;
          if (!isset($_SESSION['perch_shop_package_id']) && isset($_COOKIE['perch_shop_package_id'])) {
            $_SESSION['perch_shop_package_id'] = $_COOKIE['perch_shop_package_id'];
          }
          if (!isset($_SESSION['package_billing_type']) && isset($_COOKIE['package_billing_type'])) {
            $_SESSION['package_billing_type'] = $_COOKIE['package_billing_type'];
          }
          if(isset($_GET['package'])){
            $_SESSION['perch_shop_package_monthly_checkout'] = true;
          }

if (!isset($_SESSION['perch_shop_package_id']) && isset($_GET['package'])) {
    $_SESSION['perch_shop_package_id'] = $_GET['package'];
    setcookie('perch_shop_package_id', $_GET['package'], time()+3600, '/');
} //include('../perch/runtime.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 if (perch_post('action') === 'update') {
 perch_shop_package_remove( $_SESSION['perch_shop_package_id']);
      PerchUtil::redirect('/order/package-builder');
 }
   /* $quantities = perch_post('qty');
    $removals = perch_post('remove');
    if (is_array($quantities)) {
        foreach ($quantities as $itemID => $qty) {
            perch_shop_package_update_item($itemID, (int)$qty);
        }
    }
    if (is_array($removals)) {
        foreach ($removals as $itemID => $doRemove) {
            if ($doRemove) {
                perch_shop_package_remove_item($itemID);
            }
        }
    }*/
    if (perch_post('action') === 'checkout') {
        try {
            $package = perch_shop_update_package_status("confirmed");
            if ($package) {
                $billing = $_SESSION['package_billing_type'] ?? 'prepaid';
                $items   = $package->get_items();
                if (is_array($items)) {
                    foreach ($items as $Item) {
                        if ($billing === 'monthly' && (int)$Item->month() > 1) {
                            continue;
                        }
                        $productID = $Item->variantID() ? $Item->variantID() : $Item->productID();
                        if ($productID) {
                            perch_shop_add_to_cart($productID, $Item->qty());
                        }
                    }
                }

                PerchUtil::redirect('/order/cart');
            }
        } catch (Exception $e) {
            // fall through to error redirect
        }
        PerchUtil::redirect('/package-builder?error=package_exists');
    }
}
    perch_layout('client/header', [
        'page_title' => perch_page_title(true),
    ]);

?>
    <section class="main_order_summary">
        <div class="container mt-5">
            <div class="row">
                <!-- Left Section -->
                <div class="col-md-7">

                        <h2 class="fw-bold">Order summary</h2>

                    <div class="main_page">
                       <div class="your_order">
<form method="post">
    <?php
       PerchSystem::set_var('monthly_checkout',$_SESSION['perch_shop_package_monthly_checkout']);
     perch_shop_package_contents([
        'template' => 'products/package-summary/summary.html',
    ]);
    if (isset( $_SESSION['perch_shop_package_monthly_checkout']) &&  $_SESSION['perch_shop_package_monthly_checkout']){
    ?>
     <button type="submit" class="add-btn" name="action" value="checkout">Proceed to checkout</button>

 <?}else{?>

    <button class="add-btn" type="submit" name="action" value="update">Reset package</button>
    <button  class="add-btn" type="submit" name="action" value="checkout">Proceed to checkout</button>
    <?php  }?>
</form>
 </div>
    </div>
   </div>
        </div>
    </div>
</section>

    <?php
  perch_layout('getStarted/footer');?>
