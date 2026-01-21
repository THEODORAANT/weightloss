<?php
  perch_layout('global/new/header', [
    'page_title' => perch_page_title(true),
  ]);
?>

<main class="w-full bg-slate-50">
  <section class="px-[20px] md:px-[40px] lg:px-[60px] py-[60px]">
    <div class="mx-auto w-full max-w-[1120px]">
      <?php
        $product_slug = perch_get('s');
        $category_slug = perch_get('category');
        $category_title = $category_slug ? ucwords(str_replace('-', ' ', $category_slug)) : null;

        PerchSystem::set_var('category_slug', $category_slug);
      ?>

      <div class="mb-[32px] flex flex-col items-center gap-[16px] text-center">
        <div class="flex flex-wrap items-center justify-center gap-[10px] text-[13px] font-semibold text-slate-500">
          <a href="/shop" class="inline-flex items-center gap-[6px] text-[#3328bf] hover:text-[#2a21a3]">
            ← Back to shop
          </a>
          <span class="text-slate-300">|</span>
          <a href="/shop/products" class="inline-flex items-center gap-[6px] text-[#3328bf] hover:text-[#2a21a3]">
            All categories
          </a>
          <?php if ($category_title) { ?>
            <span class="text-slate-300">|</span>
            <span class="inline-flex items-center gap-[6px] text-slate-600">
              Browsing: <?php echo PerchUtil::html($category_title); ?>
            </span>
          <?php } ?>
        </div>
        <?php if (function_exists('perch_categories')) { ?>
          <?php perch_categories([
            'set' => 'shop',
            'template' => 'categories/shop-category-nav.html',
          ]); ?>
        <?php } ?>
      </div>

      <div class="text-center mb-[50px]">
        <span class="inline-flex items-center justify-center rounded-full bg-[#3328bf]/10 px-[18px] py-[6px] text-[12px] font-semibold uppercase tracking-[0.2em] text-[#3328bf]">Shop products</span>
        <h1 class="mt-[16px] text-[32px] md:text-[40px] font-semibold text-[#0f172a]">
          <?php echo $category_title ? PerchUtil::html($category_title) . ' accessories' : 'Browse our shop'; ?>
        </h1>
        <p class="mt-[12px] text-[16px] md:text-[18px] text-slate-600">
          <?php echo $category_title ? 'Explore everything in this category and compare the options.' : 'Explore our latest products and add your favorites to the cart in one click.'; ?>
        </p>
      </div>

      <?php
        if ($product_slug) {
          perch_shop_product($product_slug, [
            'template' => 'products/shop-product.html',
          ]);

          /* perch_shop_product_variants($product_slug, [

                   'template' => 'products/shop-product.html',
                                            ]);*/

        } elseif (function_exists('perch_shop_products')) {
          $product_options = [
            'template' => 'products/shop-grid.html',
          ];

          if ($category_slug) {
            $product_options['category'] = $category_slug;
          }

          perch_shop_products($product_options);
        } else {
          echo '<p class="text-center text-slate-500">Shop products are unavailable right now.</p>';
        }
      ?>
    </div>
  </section>
</main>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const activeCategory = params.get('category');
    if (!activeCategory) return;
    document.querySelectorAll('.category-pill').forEach((pill) => {
      if (pill.dataset.category === activeCategory) {
        pill.classList.add('border-[#3328bf]', 'bg-[#3328bf]', 'text-white');
      }
    });
  });
</script>

<?php perch_layout('global/new/footer'); ?>
