<?php  if (session_status() === PHP_SESSION_NONE) {
              session_start();
          }

$draft = isset($_COOKIE['draft_package_item']) ? json_decode($_COOKIE['draft_package_item'], true) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantities = perch_post('qty');
    $removals = perch_post('remove');

    if (perch_post('action') === 'checkout') {
        try {
            $Item =perch_shop_package_contents(['itemID' =>$_GET['renew'],'skip-template'=>true], true);
            if ($Item) {
             $productID = $Item->variantID() ? $Item->variantID() : $Item->productID();
                                    if ($productID) {
                                        perch_shop_add_to_cart($productID, $Item->qty());
                                    }

        setcookie('draft_package_item', $Item->itemID(), time()+3600,'/');

                PerchUtil::redirect('/order/cart');
            }
        } catch (Exception $e) {
            // fall through to error redirect
            print_r($e);
        }
        //PerchUtil::redirect('/package-builder?error=package_exists');
    }
}
    perch_layout('client/header', [
        'page_title' => perch_page_title(true),
    ]);

?>
  <section class="shippin_section">
    <div class="container all_content mt-4">
        <h2 class="text-center fw-bolder">Package Renew</h2>

        <div class="plans mt-4">
<form method="post">
    <?php
     perch_shop_package_contents(['itemID' =>$_GET['renew'],
        'template' => 'products/package-summary/summary.html',
    ]);
    ?>
     <button type="submit" name="action" value="checkout">Proceed to checkout</button>


</form>

        </div>
    </div>
</section>

    <?php
  perch_layout('getStarted/footer');?>
