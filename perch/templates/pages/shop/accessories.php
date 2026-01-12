<?php
  perch_layout('global/new/header', [
    'page_title' => perch_page_title(true),
  ]);
?>

<main class="w-full bg-slate-50">
  <section class="px-[20px] md:px-[40px] lg:px-[60px] py-[60px]">
    <div class="mx-auto w-full max-w-[1120px]">
      <div class="text-center mb-[50px]">
        <span class="inline-flex items-center justify-center rounded-full bg-[#3328bf]/10 px-[18px] py-[6px] text-[12px] font-semibold uppercase tracking-[0.2em] text-[#3328bf]">Shop accessories</span>
        <h1 class="mt-[16px] text-[32px] md:text-[40px] font-semibold text-[#0f172a]">Scales &amp; water bottles</h1>
        <p class="mt-[12px] text-[16px] md:text-[18px] text-slate-600">Stay on track with smart scales and reusable bottles in your favorite colors.</p>
      </div>

      <div class="space-y-[56px]">
        <section class="space-y-[24px]">
          <div>
            <h2 class="text-[24px] font-semibold text-[#0f172a]">Scales</h2>
            <p class="mt-[6px] text-[15px] text-slate-600">Track progress with accurate, easy-to-read digital scales.</p>
          </div>
          <div class="grid gap-[24px] sm:grid-cols-2 lg:grid-cols-3">
            <?php
              if (function_exists('perch_shop_products')) {
                perch_shop_products([
                  'category' => 'products/scales',
                  'template' => 'products/accessory-card.html',
                ]);
              } else {
                echo '<p class="text-slate-500">Shop products are unavailable right now.</p>';
              }
            ?>
          </div>
        </section>

        <section class="space-y-[24px]">
          <div>
            <h2 class="text-[24px] font-semibold text-[#0f172a]">Water bottles</h2>
            <p class="mt-[6px] text-[15px] text-slate-600">Pick your bottle and choose a color variant that suits your style.</p>
          </div>
          <div class="grid gap-[24px] sm:grid-cols-2 lg:grid-cols-3">
            <?php
              if (function_exists('perch_shop_products')) {
                perch_shop_products([
                  'category' => 'products/water-bottles',
                  'template' => 'products/accessory-card.html',
                ]);
              } else {
                echo '<p class="text-slate-500">Shop products are unavailable right now.</p>';
              }
            ?>
          </div>
        </section>
      </div>
    </div>
  </section>
</main>

<?php perch_layout('global/new/footer'); ?>
