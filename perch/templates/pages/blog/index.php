
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
<section class="your_health_content">


    <div class="health_content">
        <div class="containr">
            <div class="knowledge_text">
                <div class="effective_shortTitle">
                    <span style="color: #fff;text-transform: uppercase;" >Health Hub & News</span>
                </div>
                <div class="effective_longTitle">
                    <h1>Invaluable insights into your health</h1><br>
                    <span style="color: #8e8e8e;">Welcome to the ultimate knowledge hub for living happier, healthier, and longer.</span>
                </div>
            </div>
        </div>
    </div>


    <div class="your_health">
        <div class="container">
                <div class="row">
<?php
perch_blog_custom(array(

    'template' => 'weight_post_in_list.html',
));
?>
</div>

            </div>
        </div>
</section>



      <?php

      perch_layout('global/footer');?>
