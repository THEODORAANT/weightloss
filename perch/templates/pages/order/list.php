
<?php
    perch_layout('client/header', [
        'page_title' => perch_page_title(true),
    ]);


if (perch_member_logged_in()) {
?>
  <section class="shippin_section">
    <div class="container all_content mt-4">
        <h2 class="text-center fw-bolder">Your Orders</h2>

        <div class="plans mt-4">

          <?php
          perch_shop_orders(["sort"=>"orderCreated","sort-order"=>"DESC"]);
            ?>

        </div>
    </div>
</section>




  <?php
}
?>


    <?php
  perch_layout('getStarted/footer');?>

