
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
        <section class="custom" style="background-color: #f9f9fA;" >

        <div class="container">

            <div class="custom_text">
                <b>FAQs</b>
                <h4>Your questions answered</h4>
            </div>





        <ul class="accordion-list">
    <?php
        perch_collection('FAQS', [
        ]);
    ?>
          </ul>

        </div>

    </section>
  <?php

      perch_layout('global/footer');?>
