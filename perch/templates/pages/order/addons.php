<?php
    // output the top of the page
    perch_layout('global/new/header', [
        'page_title' => perch_page_title(true),
    ]);

    $body = file_get_contents('php://input');
    parse_str($body, $data);

    if (isset($data['product'])) {
        perch_shop_add_to_cart($data['product']);
    }
?>

<main class="client-orders-main addons-main">
  <section class="client-documents py-5">
    <div class="container client-documents__container">
      <div class="client-documents__intro text-center mb-5">
        <span class="client-documents__eyebrow">Order</span>
        <h1 class="client-documents__heading fw-bolder mb-3">Choose your add-ons</h1>
        <p class="client-documents__lead mb-0">Power up your health plan with additional diagnostics and supplements tailored to your journey.</p>
      </div>

      <div class="client-documents__content client-orders__content">
        <div class="order-grid order-grid--addons">
          <?php
            perch_shop_products([
              'template' => 'products/medical-list.html',
              'category' => 'products/medical-tests',
            ]);
          ?>
        </div>

        <div class="order-note">*Discount may not be applied to subsequent purchases</div>

        <div class="order-actions text-center">
          <a class="continue-link" href="/order/cart">Continue</a>
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

  .order-grid {
      display: grid;
      gap: 24px;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  }

  .main_product {
      position: relative;
      display: flex;
      flex-direction: column;
      gap: 16px;
      padding: 24px;
      border-radius: 18px;
      background: #fff;
      box-shadow: 0 20px 50px rgba(8, 28, 59, 0.08);
      border: 1px solid rgba(9, 44, 95, 0.08);
      height: 100%;
  }

  .main_product img {
      width: 100%;
      height: 200px;
      object-fit: cover;
      border-radius: 12px;
      background: #f0f4fb;
  }

  .main_product .most-popular {
      position: absolute;
      top: 16px;
      left: 16px;
      background: #0f5dff;
      color: #fff;
      padding: 6px 12px;
      border-radius: 999px;
      font-size: 0.75rem;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
  }

  .main_product .details_product {
      display: flex;
      flex-direction: column;
      gap: 10px;
      flex: 1;
  }

  .main_product h3 {
      color: #081c3b;
      font-size: 1.25rem;
      margin: 0;
  }

  .main_product p {
      color: #51627d;
      font-size: 0.98rem;
      margin: 0;
  }

  .main_product .price {
      color: #0f5dff;
      font-weight: 700;
      font-size: 1.1rem;
  }

  .main_product .old_price {
      color: #97a5bb;
      font-size: 0.95rem;
      text-decoration: line-through;
      margin-left: 8px;
  }

  .main_product .add-btn {
      align-self: flex-start;
      background: linear-gradient(135deg, #0f5dff, #3a8dff);
      color: #fff;
      border: none;
      padding: 10px 18px;
      border-radius: 12px;
      font-weight: 600;
      cursor: pointer;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
  }

  .main_product .add-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 12px 24px rgba(15, 93, 255, 0.25);
  }

  .order-note {
      color: #4d5b75;
      font-size: 0.95rem;
      text-align: center;
  }

  .order-actions .continue-link {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      background: #081c3b;
      color: #fff;
      padding: 12px 24px;
      border-radius: 14px;
      text-decoration: none;
      font-weight: 700;
      box-shadow: 0 20px 40px rgba(8, 28, 59, 0.18);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
  }

  .order-actions .continue-link::after {
      content: '\2192';
      font-size: 1.1rem;
  }

  .order-actions .continue-link:hover {
      transform: translateY(-2px);
      box-shadow: 0 24px 48px rgba(8, 28, 59, 0.2);
  }

  @media (max-width: 767px) {
      .client-documents__intro {
          padding: 0 12px;
      }

      .order-grid {
          gap: 18px;
      }

      .main_product {
          padding: 20px;
      }

      .main_product img {
          height: 180px;
      }
  }
</style>
<?php
  perch_layout('global/new/footer');
?>
