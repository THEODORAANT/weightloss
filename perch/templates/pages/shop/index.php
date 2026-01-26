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
        <h1 class="mt-[16px] text-[32px] md:text-[40px] font-semibold text-[#0f172a]">GetWeightLoss Shop</h1>

             <p class="mt-[12px] text-[16px] md:text-[18px] text-slate-600"> Within the GWL Shop you will find products that have been curated to provide complimentary benefits for you during your weight loss programme.
</p>
        <p class="mt-[12px] text-[16px] md:text-[18px] text-slate-600">Stay on track with smart scales and devices; blood tests to help you monitor your body’s response to the weight loss and medication or stay hydrated with the a selection of water bottles.</p>
      </div>

      <div class="space-y-[56px]">
        <?php
          if (function_exists('perch_categories')) {
            perch_categories([
            'filter'=> 'catID',
            'match'=> 'in',
           'value'=> '3,5,6,9' ,
              'template' => 'shop-category-card.html',
            ]);
          } else {
            echo '<p class="text-center text-slate-500">Shop categories are unavailable right now.</p>';
          }
        ?>
      </div>
    </div>
  </section>
</main>

<?php perch_layout('global/new/footer'); ?>
