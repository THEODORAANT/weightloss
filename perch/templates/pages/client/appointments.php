<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!perch_member_logged_in()) {
    header('Location: /client');
    exit;
}

if (!function_exists('wl_appointment_extract_currency_amount')) {
    function wl_appointment_extract_currency_amount($value)
    {
        if (is_array($value)) {
            foreach ($value as $amount) {
                if (is_numeric($amount)) {
                    return $amount + 0;
                }
            }
            return null;
        }

        if (is_numeric($value)) {
            return $value + 0;
        }

        return null;
    }
}

$appointmentProducts = [];

if (function_exists('perch_shop_products')) {
    $rawProducts = perch_shop_products([
        'template' => 'products/medical-list.html',
        'category' => 'products/medical-tests',
        'skip-template' => true,
    ], true);

    if (is_array($rawProducts)) {
        foreach ($rawProducts as $product) {
            $price = wl_appointment_extract_currency_amount($product['sale_price'] ?? $product['price'] ?? null);

            $appointmentProducts[] = [
                'id' => $product['slug'] ?? ($product['productSlug'] ?? ($product['productID'] ?? uniqid('appointment_', true))),
                'slug' => $product['slug'] ?? ($product['productSlug'] ?? ''),
                'name' => $product['title'] ?? 'Appointment',
                'price' => $price ?? 0,
                'duration' => '25 minutes + 5 minutes prep',
                'description' => trim(strip_tags($product['description'] ?? '')),
            ];
        }
    }
}

perch_layout('client/header', [
    'page_title' => perch_page_title(true),
]);
?>

<section class="client-page">
  <div class="container all_content">
    <div class="client-hero">
      <h1>Book a nutritionist or wellbeing appointment</h1>
      <p>Select your service, pick a weekday slot and answer a few quick questions so our team can prepare before you check out.</p>
    </div>

    <div class="client-columns">
      <div class="client-columns__primary">
        <div class="client-card" data-step="product">
          <div class="client-card__section">
            <div class="client-card__heading">
              <div>
                <p class="step-label">Step 1</p>
                <h2 class="client-card__title">Choose your appointment</h2>
                <p class="client-card__intro">Pick between a nutritionist consultation or a wellbeing check-in. Prices are shown per 25 minute slot.</p>
              </div>
              <span class="step-status" aria-live="polite"></span>
            </div>

            <div class="appointment-grid" role="radiogroup" aria-label="Appointment products">
              <?php if (!empty($appointmentProducts)): ?>
                <?php foreach ($appointmentProducts as $product): ?>
                  <label class="appointment-card">
                    <input type="radio" name="appointment_product" value="<?php echo htmlspecialchars($product['id'], ENT_QUOTES, 'UTF-8'); ?>" data-name="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>" data-price="<?php echo htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8'); ?>" data-duration="<?php echo htmlspecialchars($product['duration'], ENT_QUOTES, 'UTF-8'); ?>" data-slug="<?php echo htmlspecialchars($product['slug'], ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="appointment-card__body">
                      <div class="appointment-card__title-row">
                        <h3><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <span class="appointment-card__price">£<?php echo number_format((float) $product['price'], 2); ?></span>
                      </div>
                      <p class="appointment-card__description"><?php echo htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                      <p class="appointment-card__meta"><strong>Duration:</strong> <?php echo htmlspecialchars($product['duration'], ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                  </label>
                <?php endforeach; ?>
              <?php else: ?>
                <p class="muted">No appointment products are available right now. Please check back soon.</p>
              <?php endif; ?>
            </div>

            <div class="step-actions">
              <button class="btn btn-primary" id="go-to-schedule" <?php echo empty($appointmentProducts) ? 'disabled' : ''; ?>>Continue to scheduling</button>
            </div>
          </div>
        </div>

        <div class="client-card" data-step="schedule" aria-disabled="true">
          <div class="client-card__section">
            <div class="client-card__heading">
              <div>
                <p class="step-label">Step 2</p>
                <h2 class="client-card__title">Pick a weekday slot</h2>
                <p class="client-card__intro">Appointments run Monday to Friday with 25 minute consultations and 5 minutes of prep between bookings.</p>
              </div>
              <span class="step-status" aria-live="polite"></span>
            </div>

            <div class="schedule-grid">
              <div class="schedule-inputs">
                <label class="form-label" for="appointment-date">Choose a date</label>
                <input type="date" id="appointment-date" class="form-control" min="<?php echo date('Y-m-d'); ?>" aria-describedby="date-help" disabled>
                <p class="muted" id="date-help">Slots are available Monday to Friday at 09:00-12:35 and 19:00-20:35.</p>
              </div>

              <div class="slots-panel" aria-live="polite">
                <div id="slot-message" class="muted">Select a date to see available times.</div>
                <div id="slot-list" class="slot-list" role="radiogroup" aria-label="Available appointment slots"></div>
              </div>
            </div>

            <div class="step-actions">
              <button class="btn btn-primary" id="go-to-questions" disabled>Continue to questions</button>
            </div>
          </div>
        </div>

        <div class="client-card" data-step="questions" aria-disabled="true">
          <div class="client-card__section">
            <div class="client-card__heading">
              <div>
                <p class="step-label">Step 3</p>
                <h2 class="client-card__title">Pre-appointment questions</h2>
                <p class="client-card__intro">Share the focus for your session so your clinician can prepare tailored guidance.</p>
              </div>
              <span class="step-status" aria-live="polite"></span>
            </div>

            <form id="pre-questions" class="form-grid" novalidate>
              <div>
                <label class="form-label" for="goal">What would you like to focus on?</label>
                <textarea id="goal" name="goal" class="form-control" rows="3" required placeholder="e.g. balancing meals, improving energy, staying consistent"></textarea>
              </div>
              <div>
                <label class="form-label" for="medical">Anything medical we should know about?</label>
                <textarea id="medical" name="medical" class="form-control" rows="3" required placeholder="Current medications, allergies or recent changes"></textarea>
              </div>
              <div>
                <label class="form-label" for="notes">Additional notes (optional)</label>
                <textarea id="notes" name="notes" class="form-control" rows="2" placeholder="Meal preferences, goals for this year, or questions"></textarea>
              </div>
            </form>

            <div class="step-actions">
              <button class="btn btn-primary" id="go-to-checkout" disabled>Go to checkout</button>
            </div>
          </div>
        </div>

        <div class="client-card" data-step="checkout" aria-disabled="true">
          <div class="client-card__section">
            <div class="client-card__heading">
              <div>
                <p class="step-label">Step 4</p>
                <h2 class="client-card__title">Checkout</h2>
                <p class="client-card__intro">Confirm your details and place the booking. We’ll send a confirmation and calendar invite.</p>
              </div>
              <span class="step-status" aria-live="polite"></span>
            </div>

            <form id="checkout-form" class="form-grid" novalidate>
              <div>
                <label class="form-label" for="full-name">Full name</label>
                <input id="full-name" name="full-name" class="form-control" type="text" required placeholder="Your name" />
              </div>
              <div>
                <label class="form-label" for="email">Email</label>
                <input id="email" name="email" class="form-control" type="email" required placeholder="you@example.com" />
              </div>
              <div>
                <label class="form-label" for="phone">Mobile number (optional)</label>
                <input id="phone" name="phone" class="form-control" type="tel" placeholder="For reminders" />
              </div>
              <div>
                <label class="form-label" for="checkout-notes">Anything else to share?</label>
                <textarea id="checkout-notes" name="checkout-notes" class="form-control" rows="2" placeholder="Access needs, preferred clinician or other details"></textarea>
              </div>
              <div id="checkout-error" class="form-error" role="alert" hidden></div>
              <div class="step-actions">
                <button class="btn btn-primary" id="confirm-booking">Confirm booking</button>
              </div>
            </form>

            <div id="confirmation" class="confirmation" hidden>
              <div class="confirmation__icon" aria-hidden="true">✓</div>
              <h3>Booking confirmed</h3>
              <p>We’ve reserved your <span data-summary="product-name"></span> on <span data-summary="date"></span> at <span data-summary="slot"></span>. A confirmation email will follow shortly.</p>
              <p class="muted">You can revisit this page any time to book another slot.</p>
            </div>
          </div>
        </div>
      </div>

      <aside class="client-columns__secondary">
        <div class="client-sidecard">
          <h2 class="client-sidecard__title">Appointment summary</h2>
          <p class="client-sidecard__intro">Selections update automatically as you move through the steps.</p>
          <dl class="summary-list">
            <dt>Product</dt>
            <dd id="summary-product">—</dd>
            <dt>Date</dt>
            <dd id="summary-date">—</dd>
            <dt>Slot</dt>
            <dd id="summary-slot">—</dd>
            <dt>Price</dt>
            <dd id="summary-price">—</dd>
          </dl>
          <div class="summary-total">
            <span>Total</span>
            <strong id="summary-total">£0.00</strong>
          </div>
          <p class="muted">Slots: Monday–Friday, 09:00–12:35 & 19:00–20:35 (25 minutes with 5 minutes prep).</p>
        </div>
      </aside>
    </div>
  </div>
</section>

<style>
  .client-card__heading { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; }
  .step-label { text-transform:uppercase; letter-spacing:0.05em; font-weight:700; font-size:0.8rem; color:#6366f1; margin:0 0 6px; }
  .step-status { color:#16a34a; font-weight:600; font-size:0.95rem; }
  .appointment-grid { display:grid; gap:12px; grid-template-columns:repeat(auto-fit, minmax(260px, 1fr)); margin-top:18px; }
  .appointment-card { border:1px solid #e5e7eb; border-radius:14px; background:#fff; cursor:pointer; transition:border-color 0.2s ease, box-shadow 0.2s ease; display:block; }
  .appointment-card:hover { border-color:#4338ca; box-shadow:0 12px 30px rgba(67,56,202,0.12); }
  .appointment-card input { position:absolute; opacity:0; pointer-events:none; }
  .appointment-card__body { padding:16px; display:flex; flex-direction:column; gap:8px; }
  .appointment-card__title-row { display:flex; align-items:center; justify-content:space-between; gap:10px; }
  .appointment-card__title-row h3 { margin:0; font-size:1.05rem; }
  .appointment-card__price { background:#eef2ff; color:#312e81; padding:6px 10px; border-radius:10px; font-weight:700; }
  .appointment-card__description { margin:0; color:#4b5563; }
  .appointment-card__meta { margin:0; color:#1f2937; font-weight:600; }
  .appointment-card.is-selected { border-color:#4338ca; box-shadow:0 18px 36px rgba(67,56,202,0.16); }

  .schedule-grid { display:grid; gap:20px; grid-template-columns:1fr; }
  @media (min-width: 960px) { .schedule-grid { grid-template-columns:320px 1fr; align-items:start; } }
  .slots-panel { border:1px solid #e5e7eb; border-radius:12px; padding:16px; background:#fff; min-height:180px; }
  .slot-list { display:grid; gap:10px; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); margin-top:10px; }
  .slot { border:1px solid #e5e7eb; border-radius:10px; padding:10px 12px; background:#f9fafb; display:flex; align-items:center; justify-content:space-between; gap:12px; cursor:pointer; transition:all 0.2s ease; }
  .slot:hover { border-color:#4338ca; background:#eef2ff; }
  .slot input { position:absolute; opacity:0; pointer-events:none; }
  .slot__time { font-weight:700; color:#111827; }
  .slot__label { color:#4b5563; font-size:0.9rem; }
  .slot.is-selected { border-color:#4338ca; background:#eef2ff; box-shadow:0 12px 24px rgba(67,56,202,0.16); }

  .step-actions { margin-top:18px; display:flex; justify-content:flex-end; }
  .form-grid { display:grid; gap:16px; grid-template-columns:1fr; }
  @media (min-width: 720px) { .form-grid { grid-template-columns:1fr 1fr; } }
  .form-grid textarea { min-height:88px; }

  .client-sidecard { position:sticky; top:110px; }
  .summary-list { display:grid; grid-template-columns:120px 1fr; gap:6px 12px; margin:0 0 14px; padding:0; }
  .summary-list dt { color:#6b7280; font-weight:600; }
  .summary-list dd { margin:0; font-weight:700; color:#111827; }
  .summary-total { display:flex; align-items:center; justify-content:space-between; font-size:1.05rem; padding-top:10px; border-top:1px solid #e5e7eb; }

  .confirmation { margin-top:20px; padding:18px; border-radius:12px; background:#ecfdf3; border:1px solid #bbf7d0; }
  .confirmation__icon { width:42px; height:42px; border-radius:50%; background:#16a34a; color:#fff; display:inline-flex; align-items:center; justify-content:center; font-weight:800; margin-bottom:10px; }
  .form-error { background:#fef2f2; color:#b91c1c; border:1px solid #fecaca; border-radius:10px; padding:10px 12px; margin-top:6px; font-weight:600; }
</style>

<script>
  const state = {
    product: null,
    date: null,
    slot: null,
    price: 0,
    questions: {
      goal: '',
      medical: '',
      notes: ''
    },
    checkout: {
      name: '',
      email: '',
      phone: '',
      notes: ''
    }
  };

  const productRadios = Array.from(document.querySelectorAll('input[name="appointment_product"]'));
  const goToScheduleBtn = document.getElementById('go-to-schedule');
  const goToQuestionsBtn = document.getElementById('go-to-questions');
  const goToCheckoutBtn = document.getElementById('go-to-checkout');
  const confirmBookingBtn = document.getElementById('confirm-booking');
  const dateInput = document.getElementById('appointment-date');
  const slotList = document.getElementById('slot-list');
  const slotMessage = document.getElementById('slot-message');
  const preQuestionsForm = document.getElementById('pre-questions');
  const checkoutForm = document.getElementById('checkout-form');
  const confirmation = document.getElementById('confirmation');
  const checkoutError = document.getElementById('checkout-error');
  const summaryElements = {
    product: document.getElementById('summary-product'),
    date: document.getElementById('summary-date'),
    slot: document.getElementById('summary-slot'),
    price: document.getElementById('summary-price'),
    total: document.getElementById('summary-total'),
    productNameToken: document.querySelector('[data-summary="product-name"]'),
    dateToken: document.querySelector('[data-summary="date"]'),
    slotToken: document.querySelector('[data-summary="slot"]'),
  };

  const SLOT_WINDOWS = [
    { label: 'Morning', start: '09:00', lastStart: '12:35' },
    { label: 'Evening', start: '19:00', lastStart: '20:35' },
  ];
  const SLOT_LENGTH = 25; // minutes
  const SLOT_PADDING = 5; // minutes prep

  function setCheckoutError(message) {
    if (!checkoutError) return;
    if (message) {
      checkoutError.textContent = message;
      checkoutError.hidden = false;
    } else {
      checkoutError.textContent = '';
      checkoutError.hidden = true;
    }
  }

  async function addProductToCart(slug) {
    if (!slug) {
      throw new Error('Please select an appointment product before checking out.');
    }

    const response = await fetch('/order/addtocart.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `m=${encodeURIComponent(slug)}`,
    });

    if (!response.ok) {
      throw new Error('Unable to reach checkout. Please try again.');
    }

    const result = await response.json();
    if (!result?.result) {
      throw new Error('We could not add this appointment to your basket.');
    }

    return true;
  }

  function setStepEnabled(stepName, enabled) {
    const card = document.querySelector(`[data-step="${stepName}"]`);
    if (!card) return;
    card.setAttribute('aria-disabled', enabled ? 'false' : 'true');
    const inputs = card.querySelectorAll('input, textarea, button');
    inputs.forEach((input) => {
      if (enabled) {
        input.removeAttribute('disabled');
      } else {
        input.setAttribute('disabled', 'disabled');
      }
    });
    const statusEl = card.querySelector('.step-status');
    if (statusEl) {
      statusEl.textContent = enabled ? '' : 'Complete the previous step';
    }
  }

  function formatTime(totalMinutes) {
    const hours = Math.floor(totalMinutes / 60);
    const minutes = totalMinutes % 60;
    return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
  }

  function buildSlotsForDate(date) {
    const day = date.getDay();
    if (day === 0 || day === 6) {
      slotMessage.textContent = 'Appointments run Monday to Friday. Please pick a weekday.';
      slotList.innerHTML = '';
      return;
    }

    slotMessage.textContent = 'Select a time that suits you:';
    slotList.innerHTML = '';

    SLOT_WINDOWS.forEach((window) => {
      const baseParts = window.start.split(':').map(Number);
      const startMinutes = baseParts[0] * 60 + baseParts[1] + SLOT_PADDING;
      const lastStartParts = window.lastStart.split(':').map(Number);
      const lastStartMinutes = lastStartParts[0] * 60 + lastStartParts[1];

      let slotStart = startMinutes;
      while (slotStart <= lastStartMinutes) {
        const slotEnd = slotStart + SLOT_LENGTH;
        const slotId = `${window.label}-${slotStart}`;
        const slotNode = document.createElement('label');
        slotNode.className = 'slot';
        slotNode.innerHTML = `
          <input type="radio" name="slot" value="${slotId}" aria-label="${window.label} ${formatTime(slotStart)} to ${formatTime(slotEnd)}">
          <div>
            <div class="slot__time">${formatTime(slotStart)} – ${formatTime(slotEnd)}</div>
            <div class="slot__label">${window.label} slot</div>
          </div>
          <span class="slot__label">25 mins + 5 mins prep</span>
        `;

        slotNode.addEventListener('click', () => {
          document.querySelectorAll('.slot').forEach((s) => s.classList.remove('is-selected'));
          slotNode.classList.add('is-selected');
          const input = slotNode.querySelector('input');
          if (input) {
            input.checked = true;
            state.slot = `${formatTime(slotStart)} - ${formatTime(slotEnd)}`;
            updateSummary();
            goToQuestionsBtn.disabled = false;
            setStepComplete('schedule');
          }
        });

        slotList.appendChild(slotNode);
        slotStart += SLOT_LENGTH + SLOT_PADDING;
      }
    });
  }

  function updateSummary() {
    summaryElements.product.textContent = state.product ? `${state.product.name}` : '—';
    summaryElements.date.textContent = state.date || '—';
    summaryElements.slot.textContent = state.slot || '—';
    const priceText = state.product ? `£${state.price.toFixed(2)}` : '—';
    summaryElements.price.textContent = priceText;
    summaryElements.total.textContent = state.product ? `£${state.price.toFixed(2)}` : '£0.00';

    if (summaryElements.productNameToken) summaryElements.productNameToken.textContent = state.product?.name || 'your appointment';
    if (summaryElements.dateToken) summaryElements.dateToken.textContent = state.date || 'your chosen date';
    if (summaryElements.slotToken) summaryElements.slotToken.textContent = state.slot || 'your chosen time';
  }

  function setStepComplete(stepName) {
    const statusEl = document.querySelector(`[data-step="${stepName}"] .step-status`);
    if (statusEl) {
      statusEl.textContent = 'Ready';
    }
  }

  function validateQuestions() {
    const goal = document.getElementById('goal');
    const medical = document.getElementById('medical');
    if (!goal.value.trim() || !medical.value.trim()) {
      goToCheckoutBtn.disabled = true;
      return false;
    }
    state.questions.goal = goal.value.trim();
    state.questions.medical = medical.value.trim();
    state.questions.notes = document.getElementById('notes').value.trim();
    goToCheckoutBtn.disabled = false;
    setStepComplete('questions');
    return true;
  }

  function validateCheckout() {
    if (!checkoutForm.reportValidity()) {
      return false;
    }
    const nameField = document.getElementById('full-name');
    const emailField = document.getElementById('email');
    const phoneField = document.getElementById('phone');
    state.checkout.name = nameField.value.trim();
    state.checkout.email = emailField.value.trim();
    state.checkout.phone = phoneField.value.trim();
    state.checkout.notes = document.getElementById('checkout-notes').value.trim();
    setStepComplete('checkout');
    return true;
  }

  productRadios.forEach((input) => {
    input.addEventListener('change', () => {
      productRadios.forEach((radio) => radio.closest('.appointment-card')?.classList.remove('is-selected'));
      input.closest('.appointment-card')?.classList.add('is-selected');
      state.product = {
        id: input.value,
        name: input.dataset.name,
        slug: input.dataset.slug,
        duration: input.dataset.duration,
      };
      state.price = Number(input.dataset.price) || 0;
      updateSummary();
      setStepComplete('product');
      goToScheduleBtn.disabled = false;
    });
  });

  goToScheduleBtn.addEventListener('click', () => {
    setStepEnabled('schedule', true);
    goToQuestionsBtn.disabled = true;
    dateInput.disabled = false;
    dateInput.focus();
  });

  dateInput.addEventListener('change', () => {
    const value = dateInput.value;
    state.date = value ? new Date(value + 'T00:00:00').toLocaleDateString(undefined, { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) : null;
    state.slot = null;
    goToQuestionsBtn.disabled = true;
    if (!value) {
      slotList.innerHTML = '';
      slotMessage.textContent = 'Select a date to see available times.';
      updateSummary();
      return;
    }

    const selectedDate = new Date(value);
    buildSlotsForDate(selectedDate);
    updateSummary();
  });

  preQuestionsForm.addEventListener('input', validateQuestions);

  goToQuestionsBtn.addEventListener('click', () => {
    setStepEnabled('questions', true);
    validateQuestions();
    document.getElementById('goal').focus();
  });

  goToCheckoutBtn.addEventListener('click', () => {
    if (!validateQuestions()) return;
    setStepEnabled('checkout', true);
    document.getElementById('full-name').focus();
  });

  confirmBookingBtn.addEventListener('click', async (event) => {
    event.preventDefault();
    if (!validateCheckout()) return;

    setCheckoutError('');
    const slug = state.product?.slug;
    if (!slug) {
      setCheckoutError('Please choose an appointment before confirming.');
      return;
    }

    confirmBookingBtn.disabled = true;
    confirmBookingBtn.textContent = 'Processing...';

    try {
      await addProductToCart(slug);
      confirmation.hidden = false;
      confirmation.scrollIntoView({ behavior: 'smooth', block: 'center' });
    } catch (error) {
      setCheckoutError(error.message || 'Unable to complete booking. Please try again.');
    } finally {
      confirmBookingBtn.disabled = false;
      confirmBookingBtn.textContent = 'Confirm booking';
    }
  });

  updateSummary();
  setStepEnabled('product', true);
  setStepEnabled('schedule', false);
  setStepEnabled('questions', false);
  setStepEnabled('checkout', false);
</script>

<?php perch_layout('getStarted/footer'); ?>
