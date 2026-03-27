<?php
$show_cart_popup_product = $show_cart_popup_product ?? false;
$cart_popup_product_slug = $cart_popup_product_slug ?? '';

if (!$show_cart_popup_product || !$cart_popup_product_slug) {
    return;
}
?>
<div id="cart-product-popup" class="cart-product-popup-overlay" aria-hidden="true">
    <div class="cart-product-popup-modal" role="dialog" aria-modal="true" aria-label="Product details">
        <button type="button" id="close-cart-product-popup" class="cart-product-popup-close" aria-label="Close popup">&times;</button>
        <div class="cart-product-popup-content">
            <?php
            perch_shop_product($cart_popup_product_slug, [
                'template' => 'products/popup_product.html',
            ]);
            ?>
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
    width: min(1040px, 100%);
    max-height: 92vh;
    overflow: auto;
    background: transparent;
    border-radius: 20px;
    position: relative;
    padding: 8px;
    transform: translateY(16px) scale(0.98);
    opacity: 0;
    transition: transform 0.25s ease, opacity 0.25s ease;
  }

  .cart-product-popup-overlay.is-open .cart-product-popup-modal {
    transform: translateY(0) scale(1);
    opacity: 1;
  }

  .cart-product-popup-close {
    position: sticky;
    top: 4px;
    margin-left: auto;
    border: 1px solid #dbe3f3;
    background: rgba(255, 255, 255, 0.95);
    width: 40px;
    height: 40px;
    border-radius: 999px;
    font-size: 28px;
    line-height: 1;
    cursor: pointer;
    color: #1e2a47;
    z-index: 2;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
  }

  .cart-product-popup-content {
    margin-top: -8px;
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
