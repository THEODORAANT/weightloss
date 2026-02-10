<?php if (!perch_member_logged_in() ){ header("Location: /client");}
else if (perch_member_logged_in() &&  !customer_has_paid_order()) {  header("Location: /get-started"); }
        unset($_SESSION['questionnaire_saved']);
 unset($_SESSION['questionnaire']);
  unset($_SESSION['questionnaire-reorder']);
    perch_layout('client/header', [
        'page_title' => perch_page_title(true),
    ]);
?>
<?php
    $last_pen_brand = null;
    $last_pen_dose = null;

    $last_pen_details = perch_shop_last_pen_details();

    if (is_array($last_pen_details)) {
        $last_pen_brand = $last_pen_details['brand'] ?? null;
        $last_pen_dose  = $last_pen_details['dose'] ?? null;
    }
?>
<main class="client-documents-main client-orders-main">
  <section class="client-documents py-5">
    <div class="container client-documents__container">
      <div class="client-documents__intro text-center mb-5">
        <span class="client-documents__eyebrow">Order</span>
        <h1 class="client-documents__heading fw-bolder mb-3">Order your next dose</h1>
        <p class="client-documents__lead mb-0">Keep your treatment on track by selecting the medication you need to replenish. Choose your dosage and add it straight to your basket.</p>
      </div>

      <div class="client-documents__content client-orders__content">
        <?php if ($last_pen_brand || $last_pen_dose) { ?>
          <div class="order-reminder card-shadow">
            <div class="order-reminder__eyebrow">Previous prescription</div>
            <p class="order-reminder__text mb-0">
              <?php
                  $parts = [];
                  if ($last_pen_brand) {
                      $parts[] = PerchUtil::html($last_pen_brand);
                  }
                  if ($last_pen_dose) {
                      $parts[] = PerchUtil::html($last_pen_dose);
                  }
                  echo 'Your last pen was ' . implode(' – ', $parts) . '.';
              ?>
            </p>
          </div>
        <?php } ?>

        <div class="order-grid">
          <?php
            perch_shop_empty_cart();
            perch_shop_products(['category' => 'products/weight-loss','template'=>'products/list_for_reorder']);
          ?>
        </div>
      </div>
    </div>
  </section>
</main>

<style>
    .client-orders-main {
        background-color: #f4f7fb;
        min-height: 100vh;
    }

    .client-orders__content {
        display: flex;
        flex-direction: column;
        gap: 32px;
    }

    .card-shadow {
        box-shadow: 0 20px 50px rgba(8, 28, 59, 0.08);
        border-radius: 20px;
        background-color: #ffffff;
        padding: 32px;
        border: 1px solid rgba(9, 44, 95, 0.08);
    }

    .order-reminder {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .order-reminder__eyebrow {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: fit-content;
        padding: 6px 14px;
        font-size: 0.75rem;
        letter-spacing: 0.12em;
        font-weight: 600;
        text-transform: uppercase;
        color: #4d5b75;
        background-color: rgba(77, 91, 117, 0.12);
        border-radius: 999px;
    }

    .order-reminder__text {
        color: #1f2a44;
        font-size: 1rem;
    }

    .order-grid {
        display: grid;
        gap: 24px;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    }

    .client-documents__eyebrow {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 18px;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        color: #4d5b75;
        background: rgba(77, 91, 117, 0.12);
        border-radius: 999px;
    }

    .client-documents__heading {
        color: #081c3b;
        font-size: clamp(2rem, 2.8vw, 2.75rem);
        line-height: 1.2;
    }

    .client-documents__lead {
        max-width: 680px;
        margin: 0 auto;
        color: #51627d;
        font-size: 1.05rem;
    }

    @media (max-width: 767px) {
        .card-shadow {
            padding: 24px;
        }

        .order-grid {
            gap: 18px;
        }
    }
</style>
<?php
  perch_layout('getStarted/footer');?>
