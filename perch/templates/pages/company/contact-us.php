
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
    <section class="research-section py-5">
        <div class="container">
            <div class="row align-items-center gy-4">
                <div class="col-lg-6">
                    <div class="research pe-lg-5">
                        <span class="badge bg-dark">Contact Us</span>
                        <h1 class="mt-3">We&apos;re here to help every step of your journey</h1>
                        <p class="text-muted mb-4">
                            Our support team is on hand Monday to Friday, 9am – 5pm (UK time).
                            We aim to reply to all enquiries within one working day.
                        </p>
                        <div class="d-flex flex-column flex-sm-row gap-3">
                            <a class="btn btn-success px-4" href="mailto:support@getweightloss.co.uk">
                                Email support@getweightloss.co.uk
                            </a>
                            <a class="btn btn-outline-dark px-4" href="#help-options">Explore help options</a>
                        </div>
                        <p class="small text-muted mt-3 mb-0">
                            Need urgent medical help? Please call 111 or 999 in an emergency.
                        </p>
                    </div>
                </div>
                <div class="col-lg-6 research_image">
                    <div class="research_img mx-auto mx-lg-0">
                        <img src="/asset/contactus2.png" class="img-fluid" alt="Person checking messages on a phone">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5" id="help-options" style="background-color: #f4f6f8;">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-4">
                    <div class="h-100 p-4 border rounded-3 bg-white shadow-sm">
                        <h3 class="h5">General enquiries</h3>
                        <p class="text-muted mb-3">
                            Have a question about your account, orders, or programme access?
                            Send us a message and we&apos;ll point you in the right direction.
                        </p>
                        <a class="text-success fw-semibold" href="mailto:support@getweightloss.co.uk?subject=General%20enquiry">
                            Email the team &rarr;
                        </a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="h-100 p-4 border rounded-3 bg-white shadow-sm">
                        <h3 class="h5">Clinical support</h3>
                        <p class="text-muted mb-3">
                            If you have a clinical question about your medication or treatment plan,
                            our clinicians can assist via the same email address.
                        </p>
                        <a class="text-success fw-semibold" href="mailto:support@getweightloss.co.uk?subject=Clinical%20support">
                            Contact clinical support &rarr;
                        </a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="h-100 p-4 border rounded-3 bg-white shadow-sm">
                        <h3 class="h5">Feedback &amp; complaints</h3>
                        <p class="text-muted mb-3">
                            Your feedback helps us improve. Share your thoughts, compliments, or concerns
                            and we&apos;ll ensure the right team responds.
                        </p>
                        <a class="text-success fw-semibold" href="mailto:support@getweightloss.co.uk?subject=Feedback">
                            Share feedback &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5" style="background-color: #f9f9fa;">
        <div class="container">
            <div class="row gy-4 align-items-center">
                <div class="col-lg-5">
                    <div class="custom_text">
                        <b>Helpful information</b>
                        <h4 class="mt-2">Find answers even faster</h4>
                        <p class="text-muted">
                            Before you get in touch, these quick links might resolve your question straight away.
                        </p>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="row gy-3">
                        <div class="col-sm-6">
                            <div class="p-4 border rounded-3 h-100 bg-white">
                                <h5 class="h6">Order tracking</h5>
                                <p class="small text-muted mb-3">
                                    Check delivery status, tracking numbers, and recent order history.
                                </p>
                                <a class="text-success fw-semibold" href="/account/orders">View my orders &rarr;</a>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-4 border rounded-3 h-100 bg-white">
                                <h5 class="h6">Account &amp; billing</h5>
                                <p class="small text-muted mb-3">
                                    Update your personal details, payment information, or subscription settings.
                                </p>
                                <a class="text-success fw-semibold" href="/account">Manage my account &rarr;</a>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-4 border rounded-3 h-100 bg-white">
                                <h5 class="h6">Medication guidance</h5>
                                <p class="small text-muted mb-3">
                                    Review dosage information, side effects, and clinical advice from our experts.
                                </p>
                                <a class="text-success fw-semibold" href="/medications">Explore medications &rarr;</a>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-4 border rounded-3 h-100 bg-white">
                                <h5 class="h6">Programme resources</h5>
                                <p class="small text-muted mb-3">
                                    Access recipes, coaching tips, and tools to stay on track with your goals.
                                </p>
                                <a class="text-success fw-semibold" href="/resources">Browse resources &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="custom py-5" style="background-color: #ffffff;">
        <div class="container">
            <div class="custom_text text-center mb-5">
                <b>FAQs</b>
                <h4 class="mt-2">Your questions answered</h4>
                <p class="text-muted">Browse our most commonly asked questions or email us if you need something more specific.</p>
            </div>

            <ul class="accordion-list">
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
