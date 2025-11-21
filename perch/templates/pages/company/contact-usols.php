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
<section class="research-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="research">
                    <span class="badge bg-dark">Contact Us</span>
                    <h1 class="mt-3">Please email us for any questions you may have</h1>
                    <p class="text-muted"><a href="mailto:support@getweightloss.co.uk"> support@getweightloss.co.uk</a></p>
                </div>
            </div>
            <div class="col-md-6 research_image ">

                <div class="research_img">
                    <img src="/asset/contactus2.png" class="img-fluid" alt="Research Report">
                </div>



            </div>
        </div>
    </div>
</section>

<section class="custom" style="background-color: #f9f9fA;">

    <div class="container">

        <div class="custom_text">
            <b>FAQs</b>
            <h4>Your questions answered</h4>
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
<?php //perch_content('Intro');
perch_layout('global/new/footer'); ?>