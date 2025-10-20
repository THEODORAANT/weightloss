<?php
    perch_layout('global/new/header', [
        'page_title' => perch_page_title(true),
    ]);
?>

<section class="relative overflow-hidden bg-gradient-to-br from-[#f5f7ff] via-white to-white">
    <div class="absolute inset-0 -z-10">
        <div class="absolute -top-32 -left-24 h-72 w-72 rounded-full bg-[#3328bf]/10 blur-3xl"></div>
        <div class="absolute bottom-0 right-0 h-80 w-80 rounded-full bg-emerald-200/30 blur-3xl"></div>
    </div>

    <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <div class="grid items-center gap-12 lg:grid-cols-2">
            <div class="space-y-6">
                <span class="inline-flex items-center rounded-full bg-[#3328bf]/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.24em] text-[#3328bf]">
                    <?php perch_content('contact_intro_badge'); ?>

                    <!-- Contact GetWeightLoss -->
                </span>
                <div class="space-y-4">
                      <h1 class="text-3xl font-semibold text-slate-900 sm:text-4xl lg:text-5xl">
                          <?php perch_content('contact_intro_heading'); ?>

                          <!-- Dedicated support for every step of your weight loss journey -->
                      </h1>
                      <p class="text-lg leading-relaxed text-slate-600">
                          <?php perch_content('contact_intro_description'); ?>

                          <!-- Our UK-based care team is available Monday to Friday, 9am&nbsp;&ndash;&nbsp;5pm. We aim to reply to every enquiry within one working day and make sure you feel supported from the moment you join us. -->
                      </p>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row">
                      <a class="inline-flex items-center justify-center rounded-full bg-emerald-500 px-6 py-3 text-base font-semibold text-white shadow-lg shadow-emerald-500/20 transition hover:bg-emerald-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-500" href="mailto:support@getweightloss.co.uk">
                          <?php perch_content('contact_primary_cta_text'); ?>

                          <!-- Email support@getweightloss.co.uk -->
                      </a>
                      <a class="inline-flex items-center justify-center rounded-full border border-slate-900/15 px-6 py-3 text-base font-semibold text-slate-900 transition hover:border-[#3328bf] hover:text-[#3328bf] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#3328bf]" href="#help-options">
                          <?php perch_content('contact_secondary_cta_text'); ?>

                          <!-- Explore help options -->
                      </a>
                  </div>
                  <p class="text-sm text-slate-500">
                      <?php perch_content('contact_urgent_notice'); ?>

                      <!-- For urgent medical assistance, call NHS 111 or dial 999 in an emergency. -->
                  </p>
            </div>

            <div class="">
                <div class="rounded-3xl border border-[#3328bf]/10 bg-white/80 p-8 shadow-2xl shadow-[#3328bf]/10 backdrop-blur-xl sm:p-10">
                    <div class="flex items-start gap-4">
                        <span class="flex h-14 w-14 items-center justify-center rounded-full bg-[#3328bf]/10 text-lg font-semibold text-[#3328bf]">
                            <?php perch_content('contact_card_initials'); ?>

                            <!-- EM -->
                        </span>
                        <div class="space-y-2">
                              <h2 class="text-2xl font-semibold text-slate-900"><?php perch_content('contact_card_heading'); ?>
<!-- How to reach us --></h2>
                              <p class="text-base leading-relaxed text-slate-600">
                                  <?php perch_content('contact_card_intro'); ?>

                                  <!-- Choose the option that suits you best and we&rsquo;ll connect you with the right team. -->
                              </p>
                        </div>
                    </div>
                    <div class="mt-6 space-y-3 text-sm text-slate-600">
                          <p><?php perch_content('contact_card_email'); ?>
<!-- <span class="font-semibold text-slate-900">Email:</span> <a class="text-[#3328bf] underline-offset-2 hover:underline" href="mailto:support@getweightloss.co.uk">support@getweightloss.co.uk</a> --></p>
                          <p><?php perch_content('contact_card_response_time'); ?>
<!-- <span class="font-semibold text-slate-900">Response time:</span> Within one working day --></p>
                          <p><?php perch_content('contact_card_availability'); ?>
<!-- <span class="font-semibold text-slate-900">Availability:</span> Monday&nbsp;&ndash;&nbsp;Friday, 9am&nbsp;&ndash;&nbsp;5pm --></p>
                    </div>
                    <div class="mt-6 rounded-2xl bg-slate-100/70 p-5 text-sm text-slate-500">
                          <?php perch_content('contact_card_note'); ?>

                          <!-- Please have your order number or account email to hand so we can quickly locate your details. -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-white py-20" id="help-options">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl text-center">
            <span class="inline-flex items-center justify-center rounded-full bg-slate-900/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.24em] text-slate-900">
                <?php perch_content('contact_help_badge'); ?>

                <!-- We can help with -->
            </span>
            <h2 class="mt-6 text-3xl font-semibold text-slate-900 sm:text-4xl">
                <?php perch_content('contact_help_heading'); ?>

                <!-- Specialist support designed around you -->
            </h2>
            <p class="mt-4 text-lg leading-relaxed text-slate-600">
                <?php perch_content('contact_help_description'); ?>

                <!-- Select the topic that matches your question so we can make sure the right person gets back to you. -->
            </p>
        </div>

        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <div class="group h-full rounded-2xl border border-slate-200 bg-white p-8 shadow-sm transition hover:-translate-y-1 hover:border-[#3328bf] hover:shadow-xl">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-[#3328bf]/10 text-sm font-semibold text-[#3328bf]">
                      <?php perch_content('contact_help_card_one_initials'); ?>

                      <!-- GQ -->
                </div>
                  <h3 class="text-xl font-semibold text-slate-900"><?php perch_content('contact_help_card_one_title'); ?>
<!-- General enquiries --></h3>
                  <p class="mt-4 text-base leading-relaxed text-slate-600">
                      <?php perch_content('contact_help_card_one_description'); ?>

                      <!-- Questions about your account, programme access, or order history? Drop us a message and we&rsquo;ll point you in the right direction. -->
                  </p>
                  <a class="mt-6 inline-flex items-center text-sm font-semibold text-[#3328bf] underline-offset-4 transition hover:underline" href="mailto:support@getweightloss.co.uk?subject=General%20enquiry">
                      <?php perch_content('contact_help_card_one_link_text'); ?>

                      <!-- Email the team -->
                      <span class="ml-2">&rarr;</span>
                  </a>
            </div>

            <div class="group h-full rounded-2xl border border-slate-200 bg-white p-8 shadow-sm transition hover:-translate-y-1 hover:border-[#3328bf] hover:shadow-xl">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-[#3328bf]/10 text-sm font-semibold text-[#3328bf]">
                      <?php perch_content('contact_help_card_two_initials'); ?>

                      <!-- CS -->
                </div>
                  <h3 class="text-xl font-semibold text-slate-900"><?php perch_content('contact_help_card_two_title'); ?>
<!-- Clinical support --></h3>
                  <p class="mt-4 text-base leading-relaxed text-slate-600">
                      <?php perch_content('contact_help_card_two_description'); ?>

                      <!-- Our clinicians can advise on medication usage, side effects, or treatment plans. The more detail you can share, the faster we can help. -->
                  </p>
                  <a class="mt-6 inline-flex items-center text-sm font-semibold text-[#3328bf] underline-offset-4 transition hover:underline" href="mailto:support@getweightloss.co.uk?subject=Clinical%20support">
                      <?php perch_content('contact_help_card_two_link_text'); ?>

                      <!-- Contact clinical support -->
                      <span class="ml-2">&rarr;</span>
                  </a>
            </div>

            <div class="group h-full rounded-2xl border border-slate-200 bg-white p-8 shadow-sm transition hover:-translate-y-1 hover:border-[#3328bf] hover:shadow-xl">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-[#3328bf]/10 text-sm font-semibold text-[#3328bf]">
                      <?php perch_content('contact_help_card_three_initials'); ?>

                      <!-- FB -->
                </div>
                  <h3 class="text-xl font-semibold text-slate-900"><?php perch_content('contact_help_card_three_title'); ?>
<!-- Feedback &amp; complaints --></h3>
                  <p class="mt-4 text-base leading-relaxed text-slate-600">
                      <?php perch_content('contact_help_card_three_description'); ?>

                      <!-- Your experience matters. Share compliments, concerns, or suggestions and we&rsquo;ll ensure the right person follows up. -->
                  </p>
                  <a class="mt-6 inline-flex items-center text-sm font-semibold text-[#3328bf] underline-offset-4 transition hover:underline" href="mailto:support@getweightloss.co.uk?subject=Feedback">
                      <?php perch_content('contact_help_card_three_link_text'); ?>

                      <!-- Share your feedback -->
                      <span class="ml-2">&rarr;</span>
                  </a>
            </div>
        </div>
    </div>
</section>

<section class="bg-slate-50 py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid items-center gap-12 lg:grid-cols-2">
            <div class="space-y-6">
                <span class="inline-flex items-center rounded-full bg-[#3328bf]/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.24em] text-[#3328bf]">
                    <?php perch_content('contact_resources_badge'); ?>

                    <!-- Helpful information -->
                </span>
                <h2 class="text-3xl font-semibold text-slate-900 sm:text-4xl">
                    <?php perch_content('contact_resources_heading'); ?>

                    <!-- Find quick answers online -->
                </h2>
                <p class="text-lg leading-relaxed text-slate-600">
                    <?php perch_content('contact_resources_description'); ?>

                    <!-- These resources cover the most common topics customers contact us about. Explore them anytime &mdash; they&rsquo;re updated regularly with new advice from our team. -->
                </p>
                <ul class="space-y-3 text-base text-slate-600">
                    <li class="flex items-start gap-3">
                        <span class="mt-1 text-lg font-bold text-[#3328bf]">&middot;</span>
                          <?php perch_content('contact_resources_bullet_one'); ?>

                          <!-- Step-by-step guidance on orders, billing, and programme access -->
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-1 text-lg font-bold text-[#3328bf]">&middot;</span>
                          <?php perch_content('contact_resources_bullet_two'); ?>

                          <!-- Expert articles covering medication safety and lifestyle tips -->
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-1 text-lg font-bold text-[#3328bf]">&middot;</span>
                          <?php perch_content('contact_resources_bullet_three'); ?>

                          <!-- Downloadable tools and resources to support your progress -->
                    </li>
                </ul>
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-[#3328bf] hover:shadow-lg">
                      <h3 class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500"><?php perch_content('contact_resources_card_one_title'); ?>
<!-- Order tracking --></h3>
                      <p class="mt-4 text-sm leading-relaxed text-slate-600">
                          <?php perch_content('contact_resources_card_one_description'); ?>

                          <!-- Check delivery updates, track parcels, and review your recent order history. -->
                      </p>
                      <a class="mt-6 inline-flex items-center text-sm font-semibold text-[#3328bf] underline-offset-4 transition hover:underline" href="/client/orders">
                          <?php perch_content('contact_resources_card_one_link_text'); ?>

                          <!-- View my orders -->
                          <span class="ml-2">&rarr;</span>
                      </a>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-[#3328bf] hover:shadow-lg">
                      <h3 class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500"><?php perch_content('contact_resources_card_two_title'); ?>
<!-- Account &amp; billing --></h3>
                      <p class="mt-4 text-sm leading-relaxed text-slate-600">
                          <?php perch_content('contact_resources_card_two_description'); ?>

                          <!-- Update personal details, change payment methods, or manage subscriptions. -->
                      </p>
                      <a class="mt-6 inline-flex items-center text-sm font-semibold text-[#3328bf] underline-offset-4 transition hover:underline" href="/client">
                          <?php perch_content('contact_resources_card_two_link_text'); ?>

                          <!-- Manage my account -->
                          <span class="ml-2">&rarr;</span>
                      </a>
                </div>



            </div>
        </div>
    </div>
</section>

<section class="py-20">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-[28px] bg-gradient-to-r from-[#3328bf] to-[#22308c] px-8 py-12 text-white shadow-2xl sm:px-12">
            <div class="grid gap-8 lg:grid-cols-[2fr,1fr] lg:items-center">
                <div class="space-y-4">
                    <h2 class="text-3xl font-semibold sm:text-4xl"><?php perch_content('contact_cta_heading'); ?>
<!-- Prefer to talk things through? --></h2>
                    <p class="text-lg leading-relaxed text-white/80">
                        <?php perch_content('contact_cta_description'); ?>

                        <!-- Email us and request a call back. A member of our team will arrange a convenient time to walk through your questions and next steps. -->
                    </p>
                </div>
                <div class="flex lg:justify-end">
                    <a class="inline-flex items-center justify-center rounded-full bg-white px-6 py-3 text-base font-semibold text-[#3328bf] shadow-lg shadow-black/10 transition hover:bg-slate-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white" href="mailto:support@getweightloss.co.uk?subject=Call%20back%20request">
                        <?php perch_content('contact_cta_button_text'); ?>

                        <!-- Request a call back -->
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-white py-20">
    <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <span class="inline-flex items-center rounded-full bg-[#3328bf]/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.24em] text-[#3328bf]">
                <?php perch_content('contact_faq_badge'); ?>

                <!-- FAQs -->
            </span>
            <h2 class="mt-6 text-3xl font-semibold text-slate-900 sm:text-4xl">
                <?php perch_content('contact_faq_heading'); ?>

                <!-- Your questions answered -->
            </h2>
            <p class="mt-4 text-lg leading-relaxed text-slate-600">
                <?php perch_content('contact_faq_description'); ?>

                <!-- Browse our most commonly asked questions. If you need anything else, drop us a message and we&rsquo;ll be happy to help. -->
            </p>
        </div>

        <ul class="mt-12 space-y-4">
            <?php
                perch_collection('FAQS', [
                    'count' => 7,
                ]);
            ?>
        </ul>
    </div>
</section>

<?php
    perch_layout('global/new/footer');
?>
