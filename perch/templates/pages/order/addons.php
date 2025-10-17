

    <?php  // output the top of the page
    perch_layout('product/header', [
        'page_title' => perch_page_title(true),
    ]);

        /* main navigation
        perch_pages_navigation([
            'levels'   => 1,
            'template' => 'main_nav.html',
        ]);*/
       // echo "post";print_r($_POST);

          $body = file_get_contents('php://input');
         // echo "post";print_r($body);
         // $data = json_decode($body);

            parse_str($body, $data);
             //  echo "data";print_r($data);
          //  print_r($data);


        //echo $raw;
        if(isset($data["product"])){
        perch_shop_add_to_cart($data["product"]);

        }

    ?>
  <section class="purchage_section">
    <div class="product-container">
        <!-- Popular add-ons section Start -->
     <div class="popular_addons">
        <h1>Popular add-ons</h1>
        <p>Power-up your health with our additional diagnostics and supplements.</p>
     </div>


     <?php    perch_shop_products([
                                                                                         'template' => 'products/medical-list.html',
                                                                                         'category' => 'products/medical-tests',


                                                                                     ]); ?>

        <div class="bottom_text">
            <p>*Discount may not be applied to subsequent purchases</p>
        </div>
        <a href="/order/cart"><button class="continue-btn">Continue<i class="fa-solid fa-arrow-right"></i></button></a>
    </div>

</section>

    <?php
  perch_layout('global/footer');?>
