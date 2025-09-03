

    <?php  // output the top of the page
    perch_layout('client/header', [
        'page_title' => perch_page_title(true),
    ]);
?>
     <section class="main_order_summary">
            <div class="container mt-5">
                <div class="row">
                    <!-- Left Section -->
                    <div class="col-md-7">


                        <div class="main_page">
                            <!-- Create an Account Section -->



                            <div class="login_sec">

                            </div>
                            <?php
perch_member_form('reset_password.html');

    ?>
   </div>

                    </div>
  </div>
    </div>
      </section>

        <?php
      perch_layout('getStarted/footer');?>
