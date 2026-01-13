<?php
  perch_layout('global/new/header', [
    'page_title' => perch_page_title(true),
  ]);
?>

<main class="w-full bg-slate-50">
  <section class="px-[20px] md:px-[40px] lg:px-[60px] py-[60px]">
    <div class="mx-auto w-full max-w-[1120px]">
      <div class="text-center mb-[50px]">
        <span class="inline-flex items-center justify-center rounded-full bg-[#3328bf]/10 px-[18px] py-[6px] text-[12px] font-semibold uppercase tracking-[0.2em] text-[#3328bf]">Shop products</span>
        <h1 class="mt-[16px] text-[32px] md:text-[40px] font-semibold text-[#0f172a]">Browse our shop</h1>
        <p class="mt-[12px] text-[16px] md:text-[18px] text-slate-600">Explore our latest products and add your favorites to the cart in one click.</p>
      </div>

      <?php
        $product_slug = perch_get('s');

        if ($product_slug) {
          perch_shop_product($product_slug, [
            'template' => 'products/shop-product.html',
          ]);

           perch_shop_product_variants($product_slug, [

                   'template' => 'products/shop-product.html',
                                            ]);

        } elseif (function_exists('perch_shop_products')) {
          perch_shop_products([
            'template' => 'products/shop-grid.html',
          ]);

        } else {
          echo '<p class="text-center text-slate-500">Shop products are unavailable right now.</p>';
        }
      ?>
    </div>
  </section>
</main>

<?php perch_layout('global/new/footer'); ?>
