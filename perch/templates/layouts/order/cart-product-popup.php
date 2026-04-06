<?php
$show_cart_popup_product = $show_cart_popup_product ?? false;
$cart_popup_product_slug = $cart_popup_product_slug ?? '';

if (!$show_cart_popup_product || !$cart_popup_product_slug) {
    return;
}
?>
<div id="cart-product-popup" class="cart-product-popup-overlay" aria-hidden="true">
    <div class="cart-product-popup-modal" role="dialog" aria-modal="true" aria-labelledby="cart-product-popup-title">
        <button type="button" id="close-cart-product-popup" class="cart-product-popup-close" aria-label="Close popup">&times;</button>
        <div class="cart-product-popup-shell">
            <div class="cart-product-popup-header">
                <p class="cart-product-popup-kicker">New in your plan</p>
                <h2 id="cart-product-popup-title">Add this product to your cart</h2>
                <p class="cart-product-popup-subtitle">Review the product details below and continue when you are ready.</p>
            </div>
            <div class="cart-product-popup-content">
                <?php
                perch_shop_product($cart_popup_product_slug, [
                    'template' => 'products/popup_product.html',
                ]);
                ?>
            </div>
        </div>
    </div>
</div>

<style>
  .cart-product-popup-overlay {
    position: fixed;
    inset: 0;
    background: radial-gradient(circle at top, rgba(51, 40, 191, 0.24), rgba(15, 23, 42, 0.78));
    backdrop-filter: blur(2px);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.25s ease;
  }

  .cart-product-popup-overlay.is-open {
    opacity: 1;
    pointer-events: auto;
  }

  .cart-product-popup-modal {
    width: min(980px, 100%);
    max-height: 92vh;
    overflow: auto;
    background: #fff;
    border: 1px solid #dbe3f3;
    border-radius: 20px;
    position: relative;
    padding: 20px;
    transform: translateY(16px) scale(0.98);
    opacity: 0;
    transition: transform 0.25s ease, opacity 0.25s ease;
    box-shadow: 0 28px 60px rgba(15, 23, 42, 0.25);
  }

  .cart-product-popup-overlay.is-open .cart-product-popup-modal {
    transform: translateY(0) scale(1);
    opacity: 1;
  }

  .cart-product-popup-close {
    position: absolute;
    top: 16px;
    right: 16px;
    border: 1px solid #dbe3f3;
    background: rgba(255, 255, 255, 0.98);
    width: 38px;
    height: 38px;
    border-radius: 999px;
    font-size: 26px;
    line-height: 1;
    cursor: pointer;
    color: #1e2a47;
    z-index: 2;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
  }

  .cart-product-popup-shell {
    overflow: hidden;
    border-radius: 16px;
    background: linear-gradient(180deg, #f8fbff 0%, #ffffff 26%);
  }

  .cart-product-popup-header {
    padding: 26px 28px 18px;
    border-bottom: 1px solid #ecf1fb;
  }

  .cart-product-popup-kicker {
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #425a9e;
    margin: 0 0 8px;
  }

  .cart-product-popup-header h2 {
    margin: 0;
    font-size: clamp(22px, 2vw, 30px);
    line-height: 1.2;
    color: #1f2f57;
  }

  .cart-product-popup-subtitle {
    margin: 10px 0 0;
    color: #5b6788;
    font-size: 15px;
    max-width: 60ch;
  }

  .cart-product-popup-content {
    padding: 22px 28px 26px;
  }

  @media (max-width: 768px) {
    .cart-product-popup-modal {
      padding: 14px;
      border-radius: 16px;
    }

    .cart-product-popup-header {
      padding: 20px 18px 14px;
    }

    .cart-product-popup-content {
      padding: 16px 18px 20px;
    }
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const popup = document.getElementById('cart-product-popup');
    const closeBtn = document.getElementById('close-cart-product-popup');
    if (!popup || !closeBtn) return;

    const openPopup = function () {
      popup.classList.add('is-open');
      popup.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
    };

    const closePopup = function () {
      popup.classList.remove('is-open');
      popup.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
    };

    window.requestAnimationFrame(openPopup);
    closeBtn.addEventListener('click', closePopup);

    popup.addEventListener('click', function (event) {
      if (event.target === popup) closePopup();
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') closePopup();
    });
  });
</script>
