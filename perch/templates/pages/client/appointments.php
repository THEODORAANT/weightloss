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

            <form id="pre-questions" class="form-grid intake-form" novalidate>
              <div class="intake-intro">
                <h3>Weight Loss with Injections: Client Intake Questionnaire</h3>
                <p>Thank you for taking the time to complete this form. Your answers will help us tailor a nutrition and lifestyle plan that complements your weight loss injections safely and effectively.</p>
              </div>

              <section class="intake-section intake-full">
                <h4>SECTION 1: Personal Information</h4>
                <div>
                  <label class="form-label" for="full_name">Full Name</label>
                  <input class="form-control" type="text" id="full_name" name="full_name" required>
                </div>
                <div>
                  <label class="form-label" for="date_of_birth">Date of Birth</label>
                  <input class="form-control" type="date" id="date_of_birth" name="date_of_birth" required>
                </div>
                <div>
                  <label class="form-label" for="email_address">Email Address</label>
                  <input class="form-control" type="email" id="email_address" name="email_address" required>
                </div>
                <div>
                  <label class="form-label" for="phone_number">Phone Number</label>
                  <input class="form-control" type="tel" id="phone_number" name="phone_number" required>
                </div>
                <fieldset class="option-group intake-full">
                  <legend class="form-label">Preferred method of contact</legend>
                  <label><input type="radio" name="preferred_contact" value="Phone" required> Phone</label>
                  <label><input type="radio" name="preferred_contact" value="Email"> Email</label>
                </fieldset>
              </section>

              <section class="intake-section intake-full">
                <h4>SECTION 2: Medical Background</h4>
                <fieldset class="option-group intake-full">
                  <legend class="form-label">Have you been prescribed a weight loss injection by a healthcare provider?</legend>
                  <label><input type="radio" name="injection_status" value="Yes" required> Yes</label>
                  <label><input type="radio" name="injection_status" value="No"> No</label>
                  <label><input type="radio" name="injection_status" value="I'm considering it"> I'm considering it</label>
                </fieldset>
                <fieldset class="option-group intake-full">
                  <legend class="form-label">Which injection are you using or considering?</legend>
                  <label><input type="radio" name="injection_type" value="Ozempic (Semaglutide)"> Ozempic (Semaglutide)</label>
                  <label><input type="radio" name="injection_type" value="Wegovy"> Wegovy</label>
                  <label><input type="radio" name="injection_type" value="Saxenda"> Saxenda</label>
                  <label><input type="radio" name="injection_type" value="Mounjaro (Tirzepatide)"> Mounjaro (Tirzepatide)</label>
                  <label><input type="radio" name="injection_type" value="Other"> Other</label>
                  <label><input type="radio" name="injection_type" value="Not sure yet"> Not sure yet</label>
                </fieldset>
                <div class="intake-full">
                  <label class="form-label" for="injection_type_other">Other injection (if selected)</label>
                  <input class="form-control" type="text" id="injection_type_other" name="injection_type_other">
                </div>
                <div>
                  <label class="form-label" for="start_date">Start date (if applicable)</label>
                  <input class="form-control" type="date" id="start_date" name="start_date">
                </div>
                <div>
                  <label class="form-label" for="current_dosage">Current dosage (if known)</label>
                  <input class="form-control" type="text" id="current_dosage" name="current_dosage">
                </div>
                <div class="intake-full">
                  <label class="form-label" for="side_effects">Have you experienced any side effects so far?</label>
                  <textarea id="side_effects" name="side_effects" class="form-control" rows="2"></textarea>
                </div>
                <fieldset class="option-group intake-full">
                  <legend class="form-label">Do you have any existing medical conditions?</legend>
                  <label><input type="checkbox" name="medical_conditions[]" value="Type 2 Diabetes"> Type 2 Diabetes</label>
                  <label><input type="checkbox" name="medical_conditions[]" value="PCOS"> PCOS</label>
                  <label><input type="checkbox" name="medical_conditions[]" value="High blood pressure"> High blood pressure</label>
                  <label><input type="checkbox" name="medical_conditions[]" value="High cholesterol"> High cholesterol</label>
                  <label><input type="checkbox" name="medical_conditions[]" value="Hypothyroidism/Hyperthyroidism"> Hypothyroidism/Hyperthyroidism</label>
                  <label><input type="checkbox" name="medical_conditions[]" value="Heart disease"> Heart disease</label>
                  <label><input type="checkbox" name="medical_conditions[]" value="None"> None</label>
                </fieldset>
                <div class="intake-full">
                  <label class="form-label" for="medical_conditions_other">Other medical conditions</label>
                  <input class="form-control" type="text" id="medical_conditions_other" name="medical_conditions_other">
                </div>
                <div class="intake-full">
                  <label class="form-label" for="medications_supplements">Are you taking any medications or supplements?</label>
                  <textarea id="medications_supplements" name="medications_supplements" class="form-control" rows="2"></textarea>
                </div>
                <div class="intake-full">
                  <label class="form-label" for="allergies_intolerances">Any allergies or intolerances?</label>
                  <textarea id="allergies_intolerances" name="allergies_intolerances" class="form-control" rows="2"></textarea>
                </div>
                <div>
                  <label class="form-label" for="menopause">Are you going through the menopause?</label>
                  <select class="form-control" id="menopause" name="menopause"><option value="">Select</option><option>Yes</option><option>No</option></select>
                </div>
                <div>
                  <label class="form-label" for="post_menopause">Are you post menopause?</label>
                  <select class="form-control" id="post_menopause" name="post_menopause"><option value="">Select</option><option>Yes</option><option>No</option></select>
                </div>
              </section>

              <section class="intake-section intake-full">
                <h4>SECTION 3: Weight History &amp; Goals</h4>
                <div>
                  <label class="form-label" for="current_weight">Current weight (kg/lbs)</label>
                  <input class="form-control" type="text" id="current_weight" name="current_weight" required>
                </div>
                <div>
                  <label class="form-label" for="goal_weight">Goal weight (kg/lbs)</label>
                  <input class="form-control" type="text" id="goal_weight" name="goal_weight" required>
                </div>
                <fieldset class="option-group intake-full">
                  <legend class="form-label">Have you tried to lose weight in the past?</legend>
                  <label><input type="radio" name="tried_before" value="Yes"> Yes</label>
                  <label><input type="radio" name="tried_before" value="No"> No</label>
                </fieldset>
                <fieldset class="option-group intake-full">
                  <legend class="form-label">What methods have you tried before?</legend>
                  <label><input type="checkbox" name="methods_tried[]" value="Calorie counting"> Calorie counting</label>
                  <label><input type="checkbox" name="methods_tried[]" value="Low-carb/Keto"> Low-carb/Keto</label>
                  <label><input type="checkbox" name="methods_tried[]" value="Intermittent fasting"> Intermittent fasting</label>
                  <label><input type="checkbox" name="methods_tried[]" value="Exercise programs"> Exercise programs</label>
                  <label><input type="checkbox" name="methods_tried[]" value="Commercial plans (e.g. Slimming World, WW)"> Commercial plans (e.g. Slimming World, WW)</label>
                </fieldset>
                <div class="intake-full">
                  <label class="form-label" for="methods_tried_other">Other method</label>
                  <input class="form-control" type="text" id="methods_tried_other" name="methods_tried_other">
                </div>
                <div class="intake-full">
                  <label class="form-label" for="worked_best">What has worked best for you in the past?</label>
                  <textarea id="worked_best" name="worked_best" class="form-control" rows="2"></textarea>
                </div>
                <div class="intake-full">
                  <label class="form-label" for="reasons_now">What are your main reasons for wanting to lose weight now?</label>
                  <textarea id="reasons_now" name="reasons_now" class="form-control" rows="2"></textarea>
                </div>
              </section>

              <section class="intake-section intake-full">
                <h4>SECTION 4: Nutrition &amp; Eating Habits</h4>
                <div class="intake-full">
                  <label class="form-label" for="typical_day_eating">What does a typical day of eating look like for you?</label>
                  <textarea id="typical_day_eating" name="typical_day_eating" class="form-control" rows="2"></textarea>
                </div>
                <div>
                  <label class="form-label" for="meals_per_day">How many meals do you eat per day (including snacks)?</label>
                  <input class="form-control" type="text" id="meals_per_day" name="meals_per_day">
                </div>
                <fieldset class="option-group">
                  <legend class="form-label">Do you eat out regularly?</legend>
                  <label><input type="radio" name="eat_out" value="Yes"> Yes</label>
                  <label><input type="radio" name="eat_out" value="No"> No</label>
                  <label><input type="radio" name="eat_out" value="Occasionally"> Occasionally</label>
                </fieldset>
                <fieldset class="option-group intake-full">
                  <legend class="form-label">Do you struggle with any of the following?</legend>
                  <label><input type="checkbox" name="struggles[]" value="Emotional eating"> Emotional eating</label>
                  <label><input type="checkbox" name="struggles[]" value="Late-night snacking"> Late-night snacking</label>
                  <label><input type="checkbox" name="struggles[]" value="Cravings for sugar or carbs"> Cravings for sugar or carbs</label>
                  <label><input type="checkbox" name="struggles[]" value="Portion control"> Portion control</label>
                  <label><input type="checkbox" name="struggles[]" value="Skipping meals"> Skipping meals</label>
                  <label><input type="checkbox" name="struggles[]" value="None of the above"> None of the above</label>
                </fieldset>
                <fieldset class="option-group intake-full">
                  <legend class="form-label">Any dietary preferences or restrictions?</legend>
                  <label><input type="checkbox" name="dietary_preferences[]" value="Vegetarian"> Vegetarian</label>
                  <label><input type="checkbox" name="dietary_preferences[]" value="Vegan"> Vegan</label>
                  <label><input type="checkbox" name="dietary_preferences[]" value="Gluten-free"> Gluten-free</label>
                  <label><input type="checkbox" name="dietary_preferences[]" value="Dairy-free"> Dairy-free</label>
                  <label><input type="checkbox" name="dietary_preferences[]" value="Halal"> Halal</label>
                  <label><input type="checkbox" name="dietary_preferences[]" value="Kosher"> Kosher</label>
                  <label><input type="checkbox" name="dietary_preferences[]" value="None"> None</label>
                </fieldset>
                <div class="intake-full">
                  <label class="form-label" for="dietary_preferences_other">Other dietary preference</label>
                  <input class="form-control" type="text" id="dietary_preferences_other" name="dietary_preferences_other">
                </div>
              </section>

              <section class="intake-section intake-full">
                <h4>SECTION 5: Lifestyle &amp; Wellness</h4>
                <fieldset class="option-group">
                  <legend class="form-label">How often do you exercise?</legend>
                  <label><input type="radio" name="exercise_frequency" value="Daily"> Daily</label>
                  <label><input type="radio" name="exercise_frequency" value="3–4 times/week"> 3–4 times/week</label>
                  <label><input type="radio" name="exercise_frequency" value="Occasionally"> Occasionally</label>
                  <label><input type="radio" name="exercise_frequency" value="Rarely"> Rarely</label>
                  <label><input type="radio" name="exercise_frequency" value="Never"> Never</label>
                </fieldset>
                <div>
                  <label class="form-label" for="activity_types">What types of activity do you enjoy?</label>
                  <textarea id="activity_types" name="activity_types" class="form-control" rows="2"></textarea>
                </div>
                <fieldset class="option-group">
                  <legend class="form-label">How would you describe your sleep pattern?</legend>
                  <label><input type="radio" name="sleep_pattern" value="7–9 hours, good quality"> 7–9 hours, good quality</label>
                  <label><input type="radio" name="sleep_pattern" value="5–7 hours, light or interrupted"> 5–7 hours, light or interrupted</label>
                  <label><input type="radio" name="sleep_pattern" value="Less than 5 hours"> Less than 5 hours</label>
                  <label><input type="radio" name="sleep_pattern" value="Struggle with insomnia"> Struggle with insomnia</label>
                </fieldset>
                <div>
                  <label class="form-label" for="stress_level">How would you rate your daily stress levels (1 = low, 10 = very high)?</label>
                  <input class="form-control" type="number" min="1" max="10" id="stress_level" name="stress_level">
                </div>
                <div>
                  <label class="form-label" for="stress_coping">How do you usually cope with stress?</label>
                  <textarea id="stress_coping" name="stress_coping" class="form-control" rows="2"></textarea>
                </div>
                <fieldset class="option-group intake-full">
                  <legend class="form-label">Do you smoke or drink alcohol?</legend>
                  <label><input type="radio" name="smoke_drink" value="Smoke"> Smoke</label>
                  <label><input type="radio" name="smoke_drink" value="Drink alcohol"> Drink alcohol</label>
                  <label><input type="radio" name="smoke_drink" value="Neither"> Neither</label>
                  <label><input type="radio" name="smoke_drink" value="Both"> Both</label>
                </fieldset>
              </section>

              <section class="intake-section intake-full">
                <h4>SECTION 6: Goals &amp; Support</h4>
                <div class="intake-full">
                  <label class="form-label" for="successful_weight_loss">What would successful weight loss look like for you?</label>
                  <textarea id="successful_weight_loss" name="successful_weight_loss" class="form-control" rows="2"></textarea>
                </div>
                <fieldset class="option-group">
                  <legend class="form-label">Are you open to making long-term changes to your eating habits?</legend>
                  <label><input type="radio" name="long_term_changes" value="Yes" required> Yes</label>
                  <label><input type="radio" name="long_term_changes" value="Somewhat"> Somewhat</label>
                  <label><input type="radio" name="long_term_changes" value="Not sure"> Not sure</label>
                </fieldset>
                <fieldset class="option-group">
                  <legend class="form-label">Do you currently have support from family or friends on this journey?</legend>
                  <label><input type="radio" name="support_network" value="Yes"> Yes</label>
                  <label><input type="radio" name="support_network" value="No"> No</label>
                  <label><input type="radio" name="support_network" value="Somewhat"> Somewhat</label>
                </fieldset>
                <div class="intake-full">
                  <label class="form-label" for="support_needed">What kind of support do you feel you need the most right now?</label>
                  <textarea id="support_needed" name="support_needed" class="form-control" rows="2"></textarea>
                </div>
                <div class="intake-full">
                  <label class="form-label" for="anything_else">Anything else you’d like to share with me?</label>
                  <textarea id="anything_else" name="anything_else" class="form-control" rows="3"></textarea>
                </div>
              </section>
            </form>

            <div class="step-actions">
              <div class="step-actions__group">
                <div id="submission-error" class="form-error" role="alert" hidden></div>
                <button class="btn btn-primary" id="go-to-cart" disabled>Submit and go to cart</button>
              </div>
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
  .step-actions__group { display:flex; flex-direction:column; align-items:flex-end; gap:10px; width:100%; }
  .step-actions__group .form-error { width:100%; }
  .form-grid { display:grid; gap:16px; grid-template-columns:1fr; }
  @media (min-width: 720px) { .form-grid { grid-template-columns:1fr 1fr; } }
  .form-grid textarea { min-height:88px; }
  .form-grid input, .form-grid select { width:100%; }
  .intake-form { gap:18px; }
  .intake-intro { grid-column:1 / -1; background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:14px; }
  .intake-intro h3 { margin:0 0 8px; }
  .intake-intro p { margin:0; color:#475569; }
  .intake-section { border:1px solid #e5e7eb; border-radius:12px; padding:14px; display:grid; gap:14px; grid-template-columns:1fr; }
  .intake-section h4 { margin:0; color:#1f2937; }
  .intake-full { grid-column:1 / -1; }
  @media (min-width: 960px) { .intake-section { grid-template-columns:1fr 1fr; } }
  .option-group { border:1px solid #e5e7eb; border-radius:10px; padding:10px 12px; display:grid; gap:8px; }
  .option-group legend { margin-bottom:4px; }

  .client-sidecard { position:sticky; top:110px; }
  .summary-list { display:grid; grid-template-columns:120px 1fr; gap:6px 12px; margin:0 0 14px; padding:0; }
  .summary-list dt { color:#6b7280; font-weight:600; }
  .summary-list dd { margin:0; font-weight:700; color:#111827; }
  .summary-total { display:flex; align-items:center; justify-content:space-between; font-size:1.05rem; padding-top:10px; border-top:1px solid #e5e7eb; }

  .form-error { background:#fef2f2; color:#b91c1c; border:1px solid #fecaca; border-radius:10px; padding:10px 12px; margin-top:6px; font-weight:600; }
</style>

<script>
  const state = {
    product: null,
    date: null,
    dateISO: null,
    slot: null,
    price: 0,
    questions: {
      goal: '',
      medical: '',
      notes: '',
      raw: {}
    }
  };

  const productRadios = Array.from(document.querySelectorAll('input[name="appointment_product"]'));
  const goToScheduleBtn = document.getElementById('go-to-schedule');
  const goToQuestionsBtn = document.getElementById('go-to-questions');
  const goToCartBtn = document.getElementById('go-to-cart');
  const dateInput = document.getElementById('appointment-date');
  const slotList = document.getElementById('slot-list');
  const slotMessage = document.getElementById('slot-message');
  const preQuestionsForm = document.getElementById('pre-questions');
  const submissionError = document.getElementById('submission-error');
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

  function setSubmissionError(message) {
    if (!submissionError) return;
    if (message) {
      submissionError.textContent = message;
      submissionError.hidden = false;
    } else {
      submissionError.textContent = '';
      submissionError.hidden = true;
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

  async function saveAppointment(payload) {
    const response = await fetch('/perch/addons/apps/perch_appointments/save_appointment.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      credentials: 'same-origin',
      body: JSON.stringify(payload),
    });

    if (!response.ok) {
      throw new Error('We could not save your appointment details. Please try again.');
    }

    const result = await response.json();
    if (!result?.result) {
      throw new Error(result?.message || 'We could not save your appointment details.');
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
    const requiredFields = ['full_name', 'date_of_birth', 'email_address', 'phone_number', 'current_weight', 'goal_weight'];
    const hasMissingRequired = requiredFields.some((id) => {
      const el = document.getElementById(id);
      return !el || !String(el.value || '').trim();
    });
    const hasPreferredContact = !!preQuestionsForm.querySelector('input[name="preferred_contact"]:checked');
    const hasInjectionStatus = !!preQuestionsForm.querySelector('input[name="injection_status"]:checked');
    const hasLongTermChanges = !!preQuestionsForm.querySelector('input[name="long_term_changes"]:checked');

    if (hasMissingRequired || !hasPreferredContact || !hasInjectionStatus || !hasLongTermChanges) {
      goToCartBtn.disabled = true;
      return false;
    }

    const formData = new FormData(preQuestionsForm);
    const answers = {};
    formData.forEach((value, key) => {
      if (key.endsWith('[]')) {
        const normalizedKey = key.replace('[]', '');
        if (!Array.isArray(answers[normalizedKey])) answers[normalizedKey] = [];
        answers[normalizedKey].push(String(value));
      } else if (Object.prototype.hasOwnProperty.call(answers, key)) {
        if (!Array.isArray(answers[key])) answers[key] = [answers[key]];
        answers[key].push(String(value));
      } else {
        answers[key] = String(value);
      }
    });

    state.questions.goal = answers.successful_weight_loss || answers.reasons_now || 'Weight loss support';
    state.questions.medical = [
      `Injection status: ${answers.injection_status || 'Not provided'}`,
      `Injection type: ${answers.injection_type || answers.injection_type_other || 'Not provided'}`,
      `Current dosage: ${answers.current_dosage || 'Not provided'}`,
      `Side effects: ${answers.side_effects || 'None provided'}`,
      `Medical conditions: ${Array.isArray(answers.medical_conditions) ? answers.medical_conditions.join(', ') : (answers.medical_conditions || 'Not provided')}`,
      `Medications/supplements: ${answers.medications_supplements || 'Not provided'}`,
      `Allergies/intolerances: ${answers.allergies_intolerances || 'Not provided'}`,
    ].join(' | ');
    state.questions.notes = JSON.stringify(answers);
    state.questions.raw = answers;

    goToCartBtn.disabled = false;
    setStepComplete('questions');
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
    state.dateISO = value || null;
    state.date = value ? new Date(value + 'T00:00:00').toLocaleDateString(undefined, { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) : null;
    state.slot = null;
    goToQuestionsBtn.disabled = true;
    goToCartBtn.disabled = true;
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
    document.getElementById('full_name').focus();
  });

  goToCartBtn.addEventListener('click', async () => {
    if (!validateQuestions()) return;
    if (!state.product?.slug) {
      setSubmissionError('Please choose an appointment product.');
      return;
    }
    if (!state.dateISO || !state.slot) {
      setSubmissionError('Please pick a date and time before continuing.');
      return;
    }

    setSubmissionError('');
    goToCartBtn.disabled = true;
    goToCartBtn.textContent = 'Processing...';

    try {
      await saveAppointment({
        product_slug: state.product.slug,
        product_name: state.product.name,
        product_price: state.price,
        appointment_date: state.dateISO,
        appointment_date_label: state.date,
        slot: state.slot,
        goal: state.questions.goal,
        medical: state.questions.medical,
        notes: state.questions.notes,
      });
      await addProductToCart(state.product.slug);
      window.location.href = 'https://getweightloss-dev-d2c5gpf7asdvh3a2.uksouth-01.azurewebsites.net/order/cart';
    } catch (error) {
      setSubmissionError(error.message || 'Unable to continue to checkout. Please try again.');
    } finally {
      goToCartBtn.disabled = false;
      goToCartBtn.textContent = 'Submit and go to cart';
    }
  });

  updateSummary();
  setStepEnabled('product', true);
  setStepEnabled('schedule', false);
  setStepEnabled('questions', false);
</script>

<?php perch_layout('getStarted/footer'); ?>
