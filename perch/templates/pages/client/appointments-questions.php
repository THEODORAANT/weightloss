<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!perch_member_logged_in()) {
    header('Location: /client');
    exit;
}

perch_layout('client/header', [
    'page_title' => 'Appointment questionnaire',
]);
?>

<section class="client-page">
  <div class="container all_content">
    <div class="questionnaire-shell">
      <div class="questionnaire-header">
        <p class="questionnaire-step">Step 3 of 3</p>
        <h1>Appointment questionnaire</h1>
        <p>Complete this page to finish your booking and continue to cart.</p>
      </div>

      <div class="questionnaire-summary" id="booking-summary">
        <div><strong>Appointment:</strong> <span id="summary-product">—</span></div>
        <div><strong>Date:</strong> <span id="summary-date">—</span></div>
        <div><strong>Slot:</strong> <span id="summary-slot">—</span></div>
      </div>

      <form id="pre-questions" class="form-grid intake-form" novalidate>
        <div class="intake-intro">
          <h3>Weight Loss with Injections: Client Intake Questionnaire</h3>
          <p>Thank you for taking the time to complete this form. Your answers help our team prepare before your appointment.</p>
        </div>

        <section class="intake-section intake-full">
          <h4>SECTION 1: Personal Information</h4>
          <div><label class="form-label" for="full_name">Full Name</label><input class="form-control" type="text" id="full_name" name="full_name" required></div>
          <div><label class="form-label" for="date_of_birth">Date of Birth</label><input class="form-control" type="date" id="date_of_birth" name="date_of_birth" required></div>
          <div><label class="form-label" for="email_address">Email Address</label><input class="form-control" type="email" id="email_address" name="email_address" required></div>
          <div><label class="form-label" for="phone_number">Phone Number</label><input class="form-control" type="tel" id="phone_number" name="phone_number" required></div>
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
          <div class="intake-full"><label class="form-label" for="side_effects">Have you experienced any side effects so far?</label><textarea id="side_effects" name="side_effects" class="form-control" rows="2"></textarea></div>
          <div class="intake-full"><label class="form-label" for="medications_supplements">Are you taking any medications or supplements?</label><textarea id="medications_supplements" name="medications_supplements" class="form-control" rows="2"></textarea></div>
          <div class="intake-full"><label class="form-label" for="allergies_intolerances">Any allergies or intolerances?</label><textarea id="allergies_intolerances" name="allergies_intolerances" class="form-control" rows="2"></textarea></div>
        </section>

        <section class="intake-section intake-full">
          <h4>SECTION 3: Goals</h4>
          <div><label class="form-label" for="current_weight">Current weight (kg/lbs)</label><input class="form-control" type="text" id="current_weight" name="current_weight" required></div>
          <div><label class="form-label" for="goal_weight">Goal weight (kg/lbs)</label><input class="form-control" type="text" id="goal_weight" name="goal_weight" required></div>
          <div class="intake-full"><label class="form-label" for="reasons_now">What are your main reasons for wanting to lose weight now?</label><textarea id="reasons_now" name="reasons_now" class="form-control" rows="2"></textarea></div>
          <div class="intake-full"><label class="form-label" for="successful_weight_loss">What would successful weight loss look like for you?</label><textarea id="successful_weight_loss" name="successful_weight_loss" class="form-control" rows="2"></textarea></div>
          <fieldset class="option-group intake-full">
            <legend class="form-label">Are you open to making long-term changes to your eating habits?</legend>
            <label><input type="radio" name="long_term_changes" value="Yes" required> Yes</label>
            <label><input type="radio" name="long_term_changes" value="Somewhat"> Somewhat</label>
            <label><input type="radio" name="long_term_changes" value="Not sure"> Not sure</label>
          </fieldset>
        </section>
      </form>

      <div class="step-actions step-actions--split">
        <a href="/client/appointments" class="btn btn-secondary">Back to scheduling</a>
        <div class="step-actions__group">
          <div id="submission-error" class="form-error" role="alert" hidden></div>
          <button class="btn btn-primary" id="go-to-cart" type="button" disabled>Submit and go to cart</button>
        </div>
      </div>
    </div>
  </div>
</section>

<style>
  .questionnaire-shell { max-width:980px; margin:0 auto; }
  .questionnaire-header { margin-bottom:16px; }
  .questionnaire-step { text-transform:uppercase; letter-spacing:.06em; font-size:.78rem; color:#4f46e5; font-weight:700; margin:0 0 6px; }
  .questionnaire-header h1 { margin:0 0 8px; }
  .questionnaire-summary { background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:12px; display:grid; gap:6px; margin-bottom:16px; }
  .form-grid { display:grid; gap:16px; grid-template-columns:1fr; }
  @media (min-width: 720px) { .form-grid { grid-template-columns:1fr 1fr; } }
  .intake-form { gap:18px; }
  .intake-intro { grid-column:1 / -1; background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:14px; }
  .intake-section { border:1px solid #e5e7eb; border-radius:12px; padding:14px; display:grid; gap:14px; grid-template-columns:1fr; }
  .intake-section h4 { margin:0; }
  .intake-full { grid-column:1 / -1; }
  @media (min-width: 960px) { .intake-section { grid-template-columns:1fr 1fr; } }
  .option-group { border:1px solid #e5e7eb; border-radius:10px; padding:10px 12px; display:grid; gap:8px; }
  .step-actions { margin-top:18px; display:flex; justify-content:flex-end; }
  .step-actions--split { justify-content:space-between; align-items:flex-start; gap:12px; }
  .step-actions__group { display:flex; flex-direction:column; align-items:flex-end; gap:10px; width:100%; max-width:420px; }
  .step-actions__group .form-error { width:100%; }
  .form-error { background:#fef2f2; color:#b91c1c; border:1px solid #fecaca; border-radius:10px; padding:10px 12px; margin-top:6px; font-weight:600; }
</style>

<script>
  const stateRaw = sessionStorage.getItem('appointmentBooking');
  const state = stateRaw ? JSON.parse(stateRaw) : null;

  if (!state || !state.product?.slug || !state.dateISO || !state.slot) {
    window.location.href = '/client/appointments';
  }

  document.getElementById('summary-product').textContent = state?.product?.name || '—';
  document.getElementById('summary-date').textContent = state?.date || '—';
  document.getElementById('summary-slot').textContent = state?.slot || '—';

  const preQuestionsForm = document.getElementById('pre-questions');
  const goToCartBtn = document.getElementById('go-to-cart');
  const submissionError = document.getElementById('submission-error');

  function setSubmissionError(message) {
    if (message) {
      submissionError.textContent = message;
      submissionError.hidden = false;
    } else {
      submissionError.textContent = '';
      submissionError.hidden = true;
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

    goToCartBtn.disabled = hasMissingRequired || !hasPreferredContact || !hasInjectionStatus || !hasLongTermChanges;
    return !goToCartBtn.disabled;
  }

  async function saveAppointment(payload) {
    const response = await fetch('/perch/addons/apps/perch_appointments/save_appointment.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'same-origin',
      body: JSON.stringify(payload),
    });

    if (!response.ok) throw new Error('We could not save your appointment details. Please try again.');

    const result = await response.json();
    if (!result?.result) throw new Error(result?.message || 'We could not save your appointment details.');
  }

  async function addProductToCart(id) {

    const response = await fetch('/order/add-to-cart', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json; charset=UTF-8' },
      body: JSON.stringify({ m: id }),
    });
console.log(response);
    //if (!response.ok) throw new Error('Unable to reach checkout. Please try again.');

    const result = await response.json();
    //if (!result?.result) throw new Error('We could not add this appointment to your basket.');
  }

  preQuestionsForm.addEventListener('input', validateQuestions);

  goToCartBtn.addEventListener('click', async () => {
    if (!validateQuestions()) return;

    const formData = new FormData(preQuestionsForm);
    const answers = {};
    formData.forEach((value, key) => {
      if (key.endsWith('[]')) {
        const normalizedKey = key.replace('[]', '');
        if (!Array.isArray(answers[normalizedKey])) answers[normalizedKey] = [];
        answers[normalizedKey].push(String(value));
      } else {
        answers[key] = String(value);
      }
    });

    const goal = answers.successful_weight_loss || answers.reasons_now || 'Weight loss support';
    const medical = [
      `Injection status: ${answers.injection_status || 'Not provided'}`,
      `Injection type: ${answers.injection_type || 'Not provided'}`,
      `Side effects: ${answers.side_effects || 'None provided'}`,
      `Medications/supplements: ${answers.medications_supplements || 'Not provided'}`,
      `Allergies/intolerances: ${answers.allergies_intolerances || 'Not provided'}`,
    ].join(' | ');

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
        goal,
        medical,
        notes: JSON.stringify(answers),
      });
console.log(state.product);
      await addProductToCart(state.product.id);
      sessionStorage.removeItem('appointmentBooking');
      window.location.href = '/order/cart';
    } catch (error) {
      setSubmissionError(error.message || 'Unable to continue to checkout. Please try again.');
      goToCartBtn.disabled = false;
      goToCartBtn.textContent = 'Submit and go to cart';
    }
  });

  validateQuestions();
</script>

<?php perch_layout('getStarted/footer'); ?>
