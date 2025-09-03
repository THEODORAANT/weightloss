<?php  if (session_status() === PHP_SESSION_NONE) {
              session_start();
          } //include('../perch/runtime.php');
print_r($_SESSION);
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
        try {
            $package = perch_shop_update_package_status("confirmed");
            if ($package) {
            echo "checkout";print_r($package);
              //  $result= perch_shop_add_to_cart($_POST["dose"]);
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
