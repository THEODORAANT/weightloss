    <?php  // output the top of the page
    perch_layout('global/header', [
        'page_title' => perch_page_title(true),
    ]);

        /* main navigation
        perch_pages_navigation([
            'levels'   => 1,
            'template' => 'main_nav.html',
        ]);*/

    ?>

    <style>
        .contact-hero {
            background: radial-gradient(120% 120% at 0% 0%, rgba(51, 40, 191, 0.16) 0%, rgba(51, 40, 191, 0) 65%),
                        linear-gradient(135deg, #f5f7ff 0%, #ffffff 50%, #f8fbff 100%);
        }

        .badge-soft-primary,
        .badge-soft-dark {
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 999px;
            padding: 0.5rem 0.9rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: auto;
        }

        .badge-soft-primary {
            background: rgba(51, 40, 191, 0.12);
            color: #3328bf;
        }

        .badge-soft-dark {
            background: rgba(13, 13, 13, 0.08);
            color: #0d0d0d;
        }

        .contact-highlight-card {
            backdrop-filter: blur(6px);
            border-radius: 24px;
            border: 1px solid rgba(51, 40, 191, 0.08);
            box-shadow: 0 20px 45px -25px rgba(51, 40, 191, 0.6);
        }

        .contact-highlight-card .list-unstyled li + li {
            margin-top: 0.75rem;
        }

        .support-card,
        .quick-link-card {
            transition: all 0.2s ease-in-out;
            border-radius: 20px;
            border: 1px solid #e5e7eb;
        }

        .support-card:hover,
        .quick-link-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 18px 35px -22px rgba(0, 0, 0, 0.25);
            border-color: rgba(51, 40, 191, 0.25);
        }

        .support-card .icon-circle {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: rgba(51, 40, 191, 0.08);
            color: #3328bf;
            font-weight: 600;
            font-size: 1.25rem;
        }

        .contact-cta {
            background: linear-gradient(135deg, #3328bf 0%, #22308c 100%);
            border-radius: 28px;
        }

        .contact-cta .btn-light {
            color: #3328bf;
        }

        @media (max-width: 575.98px) {
            .contact-highlight-card {
                border-radius: 18px;
            }
        }
    </style>

    <section class="contact-hero py-5 py-lg-5">
        <div class="container">
            <div class="row align-items-center g-4 g-xl-5">
                <div class="col-lg-7">
                    <span class="badge-soft-primary">Contact GetWeightLoss</span>
                    <h1 class="display-5 fw-semibold mt-3 mb-3 text-dark">
                        Dedicated support for every step of your weight loss journey
                    </h1>
                    <p class="lead text-secondary mb-4">
                        Our UK-based care team are on hand Monday to Friday, 9am – 5pm. We aim to respond to every enquiry within one working day and make sure you feel supported from the moment you join us.
                    </p>
                    <div class="d-flex flex-column flex-sm-row gap-3">
                        <a class="btn btn-success btn-lg px-4" href="mailto:support@getweightloss.co.uk">
                            Email support@getweightloss.co.uk
                        </a>
                        <a class="btn btn-outline-dark btn-lg px-4" href="#help-options">Explore help options</a>
                    </div>
                    <p class="small text-muted mt-4 mb-0">
                        For urgent medical assistance, call NHS 111 or dial 999 in an emergency.
                    </p>
                </div>
                <div class="col-lg-5">
                    <div class="contact-highlight-card bg-white p-4 p-md-5 shadow-sm">
                        <div class="d-flex align-items-start gap-3 mb-4">
                            <span class="icon-circle">EM</span>
                            <div>
                                <h2 class="h4 mb-1 text-dark">How to reach us</h2>
                                <p class="text-secondary mb-0">Choose the option that suits you best and we&rsquo;ll connect you with the right team.</p>
                            </div>
                        </div>
                        <ul class="list-unstyled mb-4 text-secondary">
                            <li><strong>Email:</strong> <a class="text-decoration-none text-primary" href="mailto:support@getweightloss.co.uk">support@getweightloss.co.uk</a></li>
                            <li><strong>Response time:</strong> Within one working day</li>
                            <li><strong>Availability:</strong> Monday – Friday, 9am – 5pm</li>
                        </ul>
                        <div class="p-3 rounded-4 bg-light">
                            <p class="mb-0 small text-muted">
                                Please have your order number or account email to hand so we can quickly locate your details.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5" id="help-options">
        <div class="container">
            <div class="text-center mb-5">
                <span class="badge-soft-dark">We can help with</span>
                <h2 class="h1 fw-semibold mt-3 mb-2 text-dark">Specialist support designed around you</h2>
                <p class="text-secondary mb-0">Select the topic that matches your question so we can make sure the right person gets back to you.</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="support-card h-100 bg-white p-4 shadow-sm">
                        <div class="icon-circle mb-3">GQ</div>
                        <h3 class="h5 text-dark">General enquiries</h3>
                        <p class="text-secondary mb-4">
                            Questions about your account, programme access, or order history? Drop us a message and we&rsquo;ll point you in the right direction.
                        </p>
                        <a class="fw-semibold text-decoration-none" href="mailto:support@getweightloss.co.uk?subject=General%20enquiry">Email the team &rarr;</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="support-card h-100 bg-white p-4 shadow-sm">
                        <div class="icon-circle mb-3">CS</div>
                        <h3 class="h5 text-dark">Clinical support</h3>
                        <p class="text-secondary mb-4">
                            Our clinicians can advise on medication usage, side effects, or treatment plans. The more detail you can share, the faster we can help.
                        </p>
                        <a class="fw-semibold text-decoration-none" href="mailto:support@getweightloss.co.uk?subject=Clinical%20support">Contact clinical support &rarr;</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="support-card h-100 bg-white p-4 shadow-sm">
                        <div class="icon-circle mb-3">FB</div>
                        <h3 class="h5 text-dark">Feedback &amp; complaints</h3>
                        <p class="text-secondary mb-4">
                            Your experience matters. Share compliments, concerns, or suggestions and we&rsquo;ll ensure the right person follows up.
                        </p>
                        <a class="fw-semibold text-decoration-none" href="mailto:support@getweightloss.co.uk?subject=Feedback">Share your feedback &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-light">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-lg-5">
                    <div class="pe-lg-4">
                        <span class="badge-soft-primary">Helpful information</span>
                        <h2 class="h1 fw-semibold mt-3 mb-3 text-dark">Find quick answers online</h2>
                        <p class="text-secondary mb-4">
                            These resources cover the most common topics customers contact us about. Explore them anytime &mdash; they&rsquo;re updated regularly with new advice from our team.
                        </p>
                        <ul class="list-unstyled text-secondary mb-0">
                            <li class="d-flex align-items-start gap-2 mb-2"><span class="text-primary fw-bold">&middot;</span> Step-by-step guidance on orders, billing and programme access</li>
                            <li class="d-flex align-items-start gap-2 mb-2"><span class="text-primary fw-bold">&middot;</span> Expert articles covering medication safety and lifestyle tips</li>
                            <li class="d-flex align-items-start gap-2"><span class="text-primary fw-bold">&middot;</span> Downloadable tools and resources to support your progress</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <div class="quick-link-card bg-white h-100 p-4 shadow-sm">
                                <h3 class="h6 text-uppercase text-secondary">Order tracking</h3>
                                <p class="small text-muted mb-4">Check delivery updates, track parcels, and review your recent order history.</p>
                                <a class="fw-semibold text-decoration-none" href="/account/orders">View my orders &rarr;</a>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="quick-link-card bg-white h-100 p-4 shadow-sm">
                                <h3 class="h6 text-uppercase text-secondary">Account &amp; billing</h3>
                                <p class="small text-muted mb-4">Update personal details, change payment methods, or manage subscriptions.</p>
                                <a class="fw-semibold text-decoration-none" href="/account">Manage my account &rarr;</a>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="quick-link-card bg-white h-100 p-4 shadow-sm">
                                <h3 class="h6 text-uppercase text-secondary">Medication guidance</h3>
                                <p class="small text-muted mb-4">Review dosages, side effects, and clinical advice from our prescribing team.</p>
                                <a class="fw-semibold text-decoration-none" href="/medications">Explore medications &rarr;</a>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="quick-link-card bg-white h-100 p-4 shadow-sm">
                                <h3 class="h6 text-uppercase text-secondary">Programme resources</h3>
                                <p class="small text-muted mb-4">Recipes, coaching tips, and digital tools to keep your goals on track.</p>
                                <a class="fw-semibold text-decoration-none" href="/resources">Browse resources &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="contact-cta text-white p-5 p-lg-5 shadow-sm">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <h2 class="h2 fw-semibold mb-3">Prefer to talk things through?</h2>
                        <p class="mb-0">Email us and request a call back. A member of our team will arrange a convenient time to walk through your questions and next steps.</p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a class="btn btn-light btn-lg px-4" href="mailto:support@getweightloss.co.uk?subject=Call%20back%20request">Request a call back</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-white">
        <div class="container">
            <div class="text-center mb-5">
                <span class="badge-soft-primary">FAQs</span>
                <h2 class="h1 fw-semibold mt-3 mb-3 text-dark">Your questions answered</h2>
                <p class="text-secondary mb-0">Browse our most commonly asked questions. If you need anything else, drop us a message and we&rsquo;ll be happy to help.</p>
            </div>
            <ul class="accordion-list list-unstyled mx-auto" style="max-width: 820px;">
            <?php
                perch_collection('FAQS', [
                    'count'      => 7,
                ]);
            ?>
            </ul>
        </div>
    </section>

      <?php
           // perch_content('Intro');
          perch_layout('global/footer');?>
