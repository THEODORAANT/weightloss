<?php  // output the top of the page
    perch_layout('global/new/header', [
        'page_title' => perch_page_title(true),
    ]);
?>

<section class="bg-gradient-to-br from-indigo-50 via-white to-sky-50">
    <div class="max-w-7xl mx-auto px-6 py-16 lg:py-24">
        <div class="grid gap-12 lg:grid-cols-2 lg:items-center">
            <div class="space-y-6">
                <span class="inline-flex items-center gap-2 rounded-full bg-indigo-100 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-indigo-700">
                    <?php perch_content('about_intro_badge'); ?>

                    <!-- About GetWeightLoss -->
                </span>
                <h1 class="text-4xl font-semibold tracking-tight text-slate-900 sm:text-5xl">
                    <?php perch_content('about_intro_heading'); ?>

                    <!-- Clinician-led weight care designed around real people -->
                </h1>
                <p class="text-lg leading-relaxed text-slate-600">
                    <?php perch_content('about_intro_description'); ?>

                    <!-- We are a pharmacist-founded service that blends proven medication with clear guidance, coaching, and ongoing support. Every consultation is reviewed by UK-registered clinicians who keep your safety and long-term success at the centre of every decision. -->
                </p>
                <div class="grid gap-4 sm:grid-cols-3">
                   <!-- <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                        <p class="text-3xl font-semibold text-indigo-600">15k+</p>
                        <p class="mt-1 text-sm font-medium uppercase tracking-wider text-slate-500">Patient consultations</p>
                    </div>-->
                    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                        <p class="text-3xl font-semibold text-indigo-600"><?php perch_content('about_stat_one_value'); ?>
<!-- 4.4★ --></p>
                        <p class="mt-1 text-sm font-medium uppercase tracking-wider text-slate-500"><?php perch_content('about_stat_one_label'); ?>
<!-- Trust Pilot Review --></p>
                    </div>
                    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-100">
                        <p class="text-3xl font-semibold text-indigo-600"><?php perch_content('about_stat_two_value'); ?>
<!-- 24/7 --></p>
                        <p class="mt-1 text-sm font-medium uppercase tracking-wider text-slate-500"><?php perch_content('about_stat_two_label'); ?>
<!-- Clinical oversight --></p>
                    </div>
                </div>
            </div>
            <div class="relative isolate overflow-hidden rounded-3xl bg-white p-8 shadow-xl ring-1 ring-indigo-100">
                <div class="absolute -right-12 -top-12 h-48 w-48 rounded-full bg-indigo-100 blur-3xl"></div>
                <div class="absolute -bottom-16 -left-10 h-52 w-52 rounded-full bg-sky-100 blur-3xl"></div>
                <div class="relative space-y-4">
                    <h2 class="text-2xl font-semibold text-slate-900"><?php perch_content('about_column_heading'); ?>
<!-- Why we exist --></h2>
                    <p class="text-base leading-relaxed text-slate-600">
                        <?php perch_content('about_column_paragraph_one'); ?>

                        <!-- Access to trusted, evidence-based weight care can feel confusing. We created GetWeightLoss to remove that friction—pairing expert clinical review with plain-English advice so you can feel confident about every step you take. -->
                    </p>
                    <p class="text-base leading-relaxed text-slate-600">
                        <?php perch_content('about_column_paragraph_two'); ?>

                        <!-- From the moment you complete your online consultation you have direct access to pharmacists, prescribers, and a support team who genuinely understand what sustainable change looks like in the real world. -->
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-white">
    <div class="max-w-7xl mx-auto px-6 py-16 lg:py-20">
        <div class="max-w-3xl space-y-4 text-center mx-auto">
            <span class="inline-flex items-center justify-center rounded-full bg-slate-100 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-700">
                <?php perch_content('about_promise_badge'); ?>

                <!-- Our promise -->
            </span>
            <h2 class="text-3xl font-semibold text-slate-900 sm:text-4xl"><?php perch_content('about_promise_heading'); ?>
<!-- Care that combines science with empathy --></h2>
            <p class="text-lg leading-relaxed text-slate-600">
                <?php perch_content('about_promise_description'); ?>

                <!-- Medication is only one part of the picture. Our multidisciplinary team support you with practical coaching, progress monitoring, and lifestyle guidance so healthy habits become second nature. -->
            </p>
        </div>
        <div class="mt-12 grid gap-6 lg:grid-cols-3">
            <article class="flex h-full flex-col gap-4 rounded-3xl bg-slate-50 p-8 text-left shadow-sm ring-1 ring-slate-100">
                <h3 class="text-xl font-semibold text-slate-900"><?php perch_content('about_promise_card_one_title'); ?>
<!-- Clinical excellence --></h3>
                <p class="text-base leading-relaxed text-slate-600">
                    <?php perch_content('about_promise_card_one_description'); ?>

                    <!-- Every prescription is reviewed by GPhC-registered pharmacists and independent prescribers using the latest clinical guidance. We only recommend treatments that are right for your health history. -->
                </p>
            </article>
            <article class="flex h-full flex-col gap-4 rounded-3xl bg-slate-50 p-8 text-left shadow-sm ring-1 ring-slate-100">
                <h3 class="text-xl font-semibold text-slate-900"><?php perch_content('about_promise_card_two_title'); ?>
<!-- Personal guidance --></h3>
                <p class="text-base leading-relaxed text-slate-600"><?php perch_content('about_promise_card_two_description'); ?>
<!-- We’re here to help you every step of the way. Our friendly team provides personal support by phone and email whenever you need assistance. -->
                </p>
            </article>
            <article class="flex h-full flex-col gap-4 rounded-3xl bg-slate-50 p-8 text-left shadow-sm ring-1 ring-slate-100">
                <h3 class="text-xl font-semibold text-slate-900"><?php perch_content('about_promise_card_three_title'); ?>
<!-- Responsible access --></h3>
                <p class="text-base leading-relaxed text-slate-600">
                    <?php perch_content('about_promise_card_three_description'); ?>

                    <!-- We champion safe, sustainable progress. That means educating you on side effects, pairing treatment with nutrition and movement strategies, and celebrating every milestone along the way. -->
                </p>
            </article>
        </div>
    </div>
</section>

<section class="bg-slate-900">
    <div class="max-w-7xl mx-auto px-6 py-16 lg:py-20 text-white">
        <div class="grid gap-12 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
            <div class="space-y-6">
                <span class="inline-flex items-center justify-center rounded-full bg-indigo-500/20 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-indigo-100">
                    <?php perch_content('about_journey_badge'); ?>

                    <!-- How we support you -->
                </span>
                <h2 class="text-3xl font-semibold sm:text-4xl"><?php perch_content('about_journey_heading'); ?>
<!-- A transparent journey from consultation to maintenance --></h2>

                <ol class="space-y-4 text-left">
                    <li class="flex gap-4 rounded-2xl bg-white/5 p-4">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-500 text-base font-semibold">1</span>
                        <div>
                            <p class="text-lg font-semibold"><?php perch_content('about_journey_step_one_title'); ?>
<!-- Clinical assessment --></p>
                            <p class="text-sm text-indigo-100/80">
<?php perch_content('about_journey_step_one_description'); ?>

<!-- Share your health history and our prescribing pharmacists will ensure you receive the best advice given your personal history. --></p>
                        </div>
                    </li>
                    <li class="flex gap-4 rounded-2xl bg-white/5 p-4">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-500 text-base font-semibold">2</span>
                        <div>
                            <p class="text-lg font-semibold"><?php perch_content('about_journey_step_two_title'); ?>
<!-- Your treatment plan --></p>
                            <p class="text-sm text-indigo-100/80"><?php perch_content('about_journey_step_two_description'); ?>
<!-- we will provide you with the best plan for you. Not everyone is eligible and neither is it a one size fits all. --></p>
                        </div>
                    </li>
                    <li class="flex gap-4 rounded-2xl bg-white/5 p-4">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-500 text-base font-semibold">3</span>
                        <div>
                            <p class="text-lg font-semibold"><?php perch_content('about_journey_step_three_title'); ?>
<!-- Ongoing partnership --></p>
                            <p class="text-sm text-indigo-100/80"><?php perch_content('about_journey_step_three_description'); ?>
<!-- Regular check-ins, open messaging with our clinicians, and proactive adjustments help keep your progress steady and safe. --></p>
                        </div>
                    </li>
                </ol>
            </div>
            <div class="relative isolate overflow-hidden rounded-3xl bg-indigo-500/10 p-8 shadow-lg ring-1 ring-indigo-400/30">
                <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.3),_transparent_60%)]"></div>
                <h3 class="text-2xl font-semibold"><?php perch_content('about_appreciation_heading'); ?>
<!-- What patients appreciate most --></h3>
                <ul class="mt-6 space-y-4 text-left text-base text-indigo-100/90">
                    <li class="flex items-start gap-3">
                        <span class="mt-1 inline-flex h-6 w-6 items-center justify-center rounded-full bg-white/10 text-sm font-semibold">&bull;</span>
                        <span><?php perch_content('about_appreciation_point_one'); ?>
<!-- Simple explanations that make complex medications feel approachable. --></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-1 inline-flex h-6 w-6 items-center justify-center rounded-full bg-white/10 text-sm font-semibold">&bull;</span>
                        <span><?php perch_content('about_appreciation_point_two'); ?>
<!-- Fast responses from a friendly team who recognise individual challenges. --></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="mt-1 inline-flex h-6 w-6 items-center justify-center rounded-full bg-white/10 text-sm font-semibold">&bull;</span>
                        <span><?php perch_content('about_appreciation_point_three'); ?>
<!-- Guidance that keeps motivation high long after the first prescription. --></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="bg-white">
    <div class="max-w-7xl mx-auto px-6 py-16 lg:py-20">
        <div class="grid gap-12 lg:grid-cols-2 lg:items-start">
            <div class="space-y-4">
                <span class="inline-flex items-center justify-center rounded-full bg-slate-100 px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-700">
                    <?php perch_content('about_story_badge'); ?>

                    <!-- Our story -->
                </span>
                <h2 class="text-3xl font-semibold text-slate-900 sm:text-4xl"><?php perch_content('about_story_heading'); ?>
<!-- From local pharmacy to nationwide supplier --></h2>
                <p class="text-lg leading-relaxed text-slate-600">
                    <?php perch_content('about_story_description'); ?>

                    <!-- GetWeightLoss began with a single pharmacy determined to demystify weight loss medications. We now support patients all over the UK with digital consultations, responsive delivery, and the reassurance of a real clinical team behind every message. -->
                </p>
            </div>
            <div class="space-y-6">
                <div class="flex gap-4 rounded-3xl bg-slate-50 p-6 shadow-sm ring-1 ring-slate-100">
                    <div class="mt-1 h-10 w-10 rounded-full bg-indigo-100 text-center text-lg font-semibold text-indigo-600 leading-10"><?php perch_content('about_story_year_one'); ?>
<!-- 2020 --></div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900"><?php perch_content('about_story_year_one_title'); ?>
<!-- Idea to impact --></h3>
                        <p class="text-base text-slate-600"><?php perch_content('about_story_year_one_description'); ?>
<!-- Our pharmacists launch structured consultations after seeing patients struggle to access consistent, evidence-led support. --></p>
                    </div>
                </div>
                <div class="flex gap-4 rounded-3xl bg-slate-50 p-6 shadow-sm ring-1 ring-slate-100">
                    <div class="mt-1 h-10 w-10 rounded-full bg-indigo-100 text-center text-lg font-semibold text-indigo-600 leading-10"><?php perch_content('about_story_year_two'); ?>
<!-- 2021 --></div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900"><?php perch_content('about_story_year_two_title'); ?>
<!-- Digital-first service --></h3>
                        <p class="text-base text-slate-600"><?php perch_content('about_story_year_two_description'); ?>
<!-- We roll out online assessments, enabling faster, more convenient clinical reviews without compromising safety. --></p>
                    </div>
                </div>
                <div class="flex gap-4 rounded-3xl bg-slate-50 p-6 shadow-sm ring-1 ring-slate-100">
                    <div class="mt-1 h-10 w-10 rounded-full bg-indigo-100 text-center text-lg font-semibold text-indigo-600 leading-10"><?php perch_content('about_story_year_three'); ?>
<!-- 2023 --></div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900"><?php perch_content('about_story_year_three_title'); ?>
<!-- Expanded treatments --></h3>
                        <p class="text-base text-slate-600"><?php perch_content('about_story_year_three_description'); ?>
<!-- We introduce GLP-1 options such as Wegovy, Ozempic, and Mounjaro, each supported by comprehensive education and monitoring. --></p>
                    </div>
                </div>
                <div class="flex gap-4 rounded-3xl bg-slate-50 p-6 shadow-sm ring-1 ring-slate-100">
                    <div class="mt-1 h-10 w-10 rounded-full bg-indigo-100 text-center text-lg font-semibold text-indigo-600 leading-10"><?php perch_content('about_story_year_four'); ?>
<!-- Today --></div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900"><?php perch_content('about_story_year_four_title'); ?>
<!-- Partners in progress --></h3>
                        <p class="text-base text-slate-600"><?php perch_content('about_story_year_four_description'); ?>
<!-- We continue investing in coaching, clinical governance, and technology that empower people to feel healthy, confident, and supported. --></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-slate-50">
    <div class="max-w-7xl mx-auto px-6 py-16 lg:py-20">
        <div class="max-w-3xl mx-auto text-center space-y-4">
            <span class="inline-flex items-center justify-center rounded-full bg-white px-4 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-slate-700">
                <?php perch_content('about_leadership_badge'); ?>

                <!-- Leadership -->
            </span>
            <h2 class="text-3xl font-semibold text-slate-900 sm:text-4xl"><?php perch_content('about_leadership_heading'); ?>
<!-- Led by clinicians who care deeply --></h2>
            <p class="text-lg leading-relaxed text-slate-600">
                <?php perch_content('about_leadership_description'); ?>

                <!-- Our leadership team oversees every treatment pathway, making sure our patients receive safe, compassionate, and transparent support. -->
            </p>
        </div>
        <div class="mt-12 grid gap-6 lg:grid-cols-2">
            <article class="flex h-full flex-col justify-between gap-6 rounded-3xl bg-white p-8 text-left shadow-sm ring-1 ring-slate-100">
                <div class="space-y-3">
                    <h3 class="text-xl font-semibold text-slate-900"><?php perch_content('about_leader_one_name'); ?>
<!-- Imran Tailor --></h3>
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-indigo-600"><?php perch_content('about_leader_one_role'); ?>
<!-- Superintendent Pharmacist & Co-founder --></p>
                    <p class="text-base leading-relaxed text-slate-600">
                        <?php perch_content('about_leader_one_bio'); ?>

                        <!-- Imran leads our clinical governance and ensures every protocol meets stringent pharmacy standards. With over 15 years of experience, he is passionate about patient education and responsible access to modern treatments. -->
                    </p>
                </div>
            </article>
            <article class="flex h-full flex-col justify-between gap-6 rounded-3xl bg-white p-8 text-left shadow-sm ring-1 ring-slate-100">
                <div class="space-y-3">
                    <h3 class="text-xl font-semibold text-slate-900"><?php perch_content('about_leader_two_name'); ?>
<!-- Sadia Memon --></h3>
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-indigo-600"><?php perch_content('about_leader_two_role'); ?>
<!-- Lead Clinical Pharmacist --></p>
                    <p class="text-base leading-relaxed text-slate-600">
                        <?php perch_content('about_leader_two_bio'); ?>

                        <!-- Sadia works directly with patients to tailor medication plans, monitor outcomes, and coordinate with prescribers. Her focus is on clear communication and giving each person the tools to maintain lasting change. -->
                    </p>
                </div>
            </article>
        </div>
    </div>
</section>

<section class="bg-white">
    <div class="max-w-7xl mx-auto px-6 py-16 lg:py-20">
        <div class="grid gap-10 rounded-3xl bg-gradient-to-br from-indigo-500 via-indigo-600 to-blue-600 px-8 py-10 text-white shadow-xl lg:grid-cols-[1.3fr_0.7fr] lg:items-center lg:px-12 lg:py-14">
            <div class="space-y-4">
                <h2 class="text-3xl font-semibold sm:text-4xl"><?php perch_content('about_cta_heading'); ?>
<!-- Ready to start your journey? --></h2>
                <p class="text-lg leading-relaxed text-indigo-100">
                    <?php perch_content('about_cta_description'); ?>

                    <!-- Complete our online consultation and receive tailored advice from our pharmacist-led team within one working day. We will guide you step-by-step so you always feel supported. -->
                </p>
            </div>
            <div class="flex flex-col items-start gap-4 sm:flex-row sm:items-center sm:justify-end">
                <a href="/get-started" class="inline-flex items-center justify-center rounded-full bg-white px-6 py-3 text-base font-semibold text-indigo-600 shadow-sm transition hover:bg-indigo-50">
                    <?php perch_content('about_cta_primary_button'); ?>

                    <!-- Start your consultation -->
                </a>
                <a href="mailto:support@getweightloss.co.uk" class="inline-flex items-center justify-center rounded-full border border-white/60 px-6 py-3 text-base font-semibold text-white transition hover:bg-white/10">
                    <?php perch_content('about_cta_secondary_button'); ?>

                    <!-- Speak to our team -->
                </a>
            </div>
        </div>
    </div>
</section>

<?php
    perch_layout('global/new/footer');
?>
