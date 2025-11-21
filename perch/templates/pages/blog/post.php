
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


<?php perch_blog_post(perch_get('s')); ?>

      <?php

      perch_layout('global/new/footer');?>
