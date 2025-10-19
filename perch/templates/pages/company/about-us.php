<?php  // output the top of the page
    perch_layout('global/header', [
        'page_title' => perch_page_title(true),
    ]);
?>

<style>
    .about-hero {
        background: radial-gradient(120% 120% at 0% 0%, rgba(51, 40, 191, 0.14) 0%, rgba(51, 40, 191, 0) 65%),
                    linear-gradient(135deg, #f6f8ff 0%, #ffffff 45%, #f6fbff 100%);
    }

    .about-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        padding: 0.5rem 1rem;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        background: rgba(51, 40, 191, 0.12);
        color: #3328bf;
    }

    .about-values-card,
    .about-story-card,
    .about-leadership-card {
        border-radius: 22px;
        border: 1px solid #e5e7eb;
        transition: all 0.2s ease-in-out;
        background: #ffffff;
    }

    .about-values-card:hover,
    .about-story-card:hover,
    .about-leadership-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 18px 35px -22px rgba(0, 0, 0, 0.25);
        border-color: rgba(51, 40, 191, 0.22);
    }

    .about-stat {
        border-radius: 18px;
        background: rgba(51, 40, 191, 0.08);
        color: #3328bf;
        padding: 1.5rem 1rem;
        text-align: center;
    }

    .about-stat h3 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .about-stat p {
        margin: 0;
        font-size: 0.85rem;
        color: #332e75;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .about-timeline::before {
        content: "";
        position: absolute;
        top: 0;
        bottom: 0;
        left: 32px;
        width: 2px;
        background: rgba(51, 40, 191, 0.15);
    }

    .about-timeline-item {
        position: relative;
        padding-left: 70px;
    }

    .about-timeline-item::before {
        content: "";
        position: absolute;
        left: 26px;
        top: 12px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #3328bf;
        box-shadow: 0 0 0 6px rgba(51, 40, 191, 0.18);
    }

    @media (max-width: 575.98px) {
        .about-timeline::before {
            left: 20px;
        }

        .about-timeline-item {
            padding-left: 60px;
        }

        .about-timeline-item::before {
            left: 14px;
        }
    }
</style>

<section class="about-hero py-5 py-lg-5">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <span class="about-pill">About GetWeightLoss</span>
                <h1 class="display-5 fw-semibold mt-3 mb-3 text-dark">
                    Pharmacist-led care that puts your health first
                </h1>
                <p class="lead text-secondary mb-4">
                    We combine evidence-based medication, compassionate coaching, and practical lifestyle support to help you lose weight safely and confidently. Every plan is overseen by UK-registered clinicians who understand the realities of everyday life.
                </p>
                <div class="row g-3">
                    <div class="col-sm-4">
                        <div class="about-stat h-100">
                            <h3>15k+</h3>
                            <p>Consultations</p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="about-stat h-100">
                            <h3>4.8★</h3>
                            <p>Service rating</p>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="about-stat h-100">
                            <h3>24/7</h3>
                            <p>Clinical oversight</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="about-story-card p-4 p-md-5 shadow-sm">
                    <h2 class="h4 text-dark mb-3">Why we exist</h2>
                    <p class="text-secondary mb-0">
                        GetWeightLoss was founded by pharmacists who saw patients struggling to access trustworthy weight loss support. We bridge that gap with medically supervised treatment, clear guidance, and a human connection that keeps you motivated for the long term.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <span class="about-pill">Our approach</span>
            <h2 class="h1 fw-semibold mt-3 mb-3 text-dark">Care that combines science with empathy</h2>
            <p class="text-secondary mb-0">
                Every programme is personalised to your medical history and goals. We monitor your progress, adjust treatment when needed, and provide the tools that make healthy changes stick.
            </p>
        </div>
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="about-values-card h-100 p-4 p-lg-5 shadow-sm">
                    <h3 class="h5 text-dark">Clinical excellence</h3>
                    <p class="text-secondary mb-0">
                        Prescriptions are managed by GPhC-registered pharmacists and independent prescribers who ensure every medication is safe, appropriate, and supported by up-to-date evidence.
                    </p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="about-values-card h-100 p-4 p-lg-5 shadow-sm">
                    <h3 class="h5 text-dark">Personal guidance</h3>
                    <p class="text-secondary mb-0">
                        Your dedicated support team checks in regularly, offering practical advice on nutrition, movement, and mindset so you always know what to do next.
                    </p>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="about-values-card h-100 p-4 p-lg-5 shadow-sm">
                    <h3 class="h5 text-dark">Responsible access</h3>
                    <p class="text-secondary mb-0">
                        Medication is only one part of the solution. We help you build sustainable habits and understand how to use injections safely alongside lifestyle adjustments.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-lg-6">
                <div class="about-story-card p-4 p-lg-5 shadow-sm h-100">
                    <span class="about-pill bg-white text-primary">What makes us different</span>
                    <h2 class="h2 fw-semibold mt-3 mb-3 text-dark">Medication with meaning</h2>
                    <p class="text-secondary mb-3">
                        We only recommend clinically proven treatments such as Wegovy, Ozempic, and Mounjaro when they align with your medical profile. Every prescription includes education on dosage, side effects, and how to pair medication with behaviour change.
                    </p>
                    <p class="text-secondary mb-0">
                        Our goal is not a quick fix. It is to help you transform your relationship with food, movement, and wellbeing so results last long after the injections stop.
                    </p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="bg-white rounded-4 border shadow-sm p-4 p-lg-5 position-relative">
                    <h3 class="h5 text-dark">How we support you</h3>
                    <ul class="list-unstyled text-secondary mb-0 mt-3">
                        <li class="d-flex align-items-start gap-2 mb-2"><span class="text-primary fw-bold">&middot;</span> Online assessments with pharmacist review within one working day.</li>
                        <li class="d-flex align-items-start gap-2 mb-2"><span class="text-primary fw-bold">&middot;</span> Easy access to your treatment plan, dosage schedule, and educational resources.</li>
                        <li class="d-flex align-items-start gap-2 mb-2"><span class="text-primary fw-bold">&middot;</span> Direct messaging with our care team for advice on medication usage and lifestyle adjustments.</li>
                        <li class="d-flex align-items-start gap-2"><span class="text-primary fw-bold">&middot;</span> Follow-up reviews to celebrate progress and adapt your plan whenever life changes.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-5 align-items-start">
            <div class="col-lg-5">
                <span class="about-pill">Our story</span>
                <h2 class="h1 fw-semibold mt-3 mb-3 text-dark">From community pharmacy to nationwide support</h2>
                <p class="text-secondary mb-0">
                    GetWeightLoss grew out of a community pharmacy that saw first-hand how confusing and inconsistent weight management care could be. Today we support patients across the UK with digital consultations, reliable delivery, and the reassurance that a real clinical team is in your corner.
                </p>
            </div>
            <div class="col-lg-7">
                <div class="position-relative about-timeline ps-3 ps-sm-4">
                    <div class="about-timeline-item mb-4">
                        <h3 class="h5 text-dark mb-1">2020 &mdash; The idea</h3>
                        <p class="text-secondary mb-0">Our pharmacists begin offering structured weight management consultations after recognising a growing need for evidence-based support.</p>
                    </div>
                    <div class="about-timeline-item mb-4">
                        <h3 class="h5 text-dark mb-1">2021 &mdash; Building the platform</h3>
                        <p class="text-secondary mb-0">We launch our digital assessment service to make access easier while maintaining strict clinical governance and oversight.</p>
                    </div>
                    <div class="about-timeline-item mb-4">
                        <h3 class="h5 text-dark mb-1">2023 &mdash; Expanding treatments</h3>
                        <p class="text-secondary mb-0">New GLP-1 medications such as Wegovy and Mounjaro are introduced with full educational pathways to help patients use them safely.</p>
                    </div>
                    <div class="about-timeline-item">
                        <h3 class="h5 text-dark mb-1">Today &mdash; Your partner in progress</h3>
                        <p class="text-secondary mb-0">We continue to invest in coaching, clinical talent, and technology that give every patient a personalised route to long-term success.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <span class="about-pill">Leadership</span>
            <h2 class="h1 fw-semibold mt-3 mb-3 text-dark">Led by clinicians who care deeply</h2>
            <p class="text-secondary mb-0">Our leadership team ensures every patient experience is safe, empathetic, and backed by the latest clinical guidance.</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="about-leadership-card p-4 p-lg-5 shadow-sm h-100">
                    <h3 class="h5 text-dark mb-1">Imran Tailor</h3>
                    <p class="text-primary fw-semibold mb-3">Superintendent Pharmacist &amp; Co-founder</p>
                    <p class="text-secondary mb-0">
                        Imran oversees our clinical governance and ensures every treatment pathway follows best practice. With over 15 years in pharmacy, he champions patient education and compassionate care.
                    </p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-leadership-card p-4 p-lg-5 shadow-sm h-100">
                    <h3 class="h5 text-dark mb-1">Sadia Memon</h3>
                    <p class="text-primary fw-semibold mb-3">Lead Clinical Pharmacist</p>
                    <p class="text-secondary mb-0">
                        Sadia works directly with patients to tailor medication plans, monitor outcomes, and coordinate with prescribers. Her focus is on empowering every person to understand their treatment and feel supported.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="about-cta text-white p-5 p-lg-5 rounded-4 shadow-sm" style="background: linear-gradient(135deg, #3328bf 0%, #22308c 100%);">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <h2 class="h2 fw-semibold mb-3">Ready to start your journey?</h2>
                    <p class="mb-0">Complete our online consultation and receive tailored advice from our pharmacist-led team within one working day.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a class="btn btn-light btn-lg px-4" href="/get-started">Start your consultation</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
    perch_layout('global/footer');
?>
