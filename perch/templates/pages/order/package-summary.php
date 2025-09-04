<?php  if (session_status() === PHP_SESSION_NONE) {
              session_start();
          }
if (!isset($_SESSION['perch_shop_package_id']) && isset($_GET['package'])) {
    $_SESSION['perch_shop_package_id'] = $_GET['package'];
} //include('../perch/runtime.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantities = perch_post('qty');
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
    }
    if (perch_post('action') === 'checkout') {
        $is_reorder = customer_has_paid_order();
        if ($is_reorder) {
            if (empty($_SESSION['questionnaire-reorder'])) {
                PerchUtil::redirect('/order/re-order');
            }
        } else {
            if (empty($_SESSION['questionnaire'])) {
                PerchUtil::redirect('/getStarted');
            }
        }
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
                PerchUtil::redirect('checkout.php');
            }
        } catch (Exception $e) {
            // fall through to error redirect
        }
        PerchUtil::redirect('package-builder.php?error=package_exists');
    }
}
    perch_layout('client/header', [
        'page_title' => perch_page_title(true),
    ]);

?>
  <section class="shippin_section">
    <div class="container all_content mt-4">
        <h2 class="text-center fw-bolder">Package Summary</h2>

        <div class="plans mt-4">
<form method="post">
    <?php perch_shop_package_contents([
        'template' => 'products/package-summary/summary.html',
    ]); ?>

    <button type="submit" name="action" value="update">Update package</button>
    <button type="submit" name="action" value="checkout">Proceed to checkout</button>
</form>

        </div>
    </div>
</section>

    <?php
  perch_layout('getStarted/footer');?>
