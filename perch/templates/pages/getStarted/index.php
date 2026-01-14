<?php if (perch_member_logged_in() &&  customer_has_paid_order()) {     header("Location: /order/re-order"); } ?>
    <?php  // output the top of the page
    perch_layout('getStarted/header', [
        'page_title' => perch_page_title(true),
    ]);

        /* main navigation
        perch_pages_navigation([
            'levels'   => 1,
            'template' => 'main_nav.html',
        ]);*/

    ?>
  <!--  <div class="modal fade" id="holidayScheduleModal" tabindex="-1" aria-labelledby="holidayScheduleTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
             <div class="modal-content" style="color:white;background-color: rgb(51 40 191 / var(--tw-bg-opacity, 1));">
                <div class="modal-header">
                    <h5 class="modal-title" id="holidayScheduleTitle">Holiday delivery schedule</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>As the Christmas and New Year holidays fast approach, we want to make sure you have your medication when you need it.</p>
                    <p>Please take a moment to review our holiday delivery schedule below when planning your orders:</p>
                    <ul>
                        <li><strong>Standard Service:</strong> All orders placed before December 21st will be delivered as normal.</li>
                        <li><strong>Late December Orders:</strong> Orders placed between December 21st and December 28th will be dispatched on December 29th.</li>
                        <li><strong>New Year Orders:</strong> Orders placed between December 29th and January 1st will be dispatched on January 2nd.</li>
                        <li><strong>Back to Normal:</strong> Our regular service and delivery schedule will fully resume on January 2nd, 2026!</li>
                    </ul>
                    <p>We truly appreciate your support this past year. We hope you have a wonderful holiday season and a very Happy New Year!</p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
-->
       <section class="treatment_journey">

            <div class="container">
               <div class="title_content">
                <div class="treatment_title">
                    <div class="title_content">
                        <h1><?php perch_content('Heading'); ?>
                        </h1>
                    </div>
                </div>
                <div class="treatment_content">
                    <div class="content_text">
                        <p>
                            <?php perch_content('Intro'); ?></p>
                    </div>
                </div>
               </div>
               <div class="treatment_menu" >
                <div class="menu_about">
                    <div class="about_content">
                        <div class="about_logo">
                            <div class="logo_box">
                                <div class="logo_icon">
                                    <i class="fa-regular fa-user" style="font-size: 25px;" ></i>
                                </div>
                            </div>
                        </div>
                        <div class="about_you">
                            <div class="about_titlr">
                                <b>About you</b>
                            </div>
                            <div class="about_text">
                                <span>It is essential we know as much about you in order to understand your medical history.</span>
                            </div>
                        </div>
                    </div>
                </div>
               </div>


               <div class="treatment_menu" style="border: 1px solid #dadee6;" >
                <div class="menu_about">
                    <div class="about_content">
                        <div class="about_logo">
                            <div class="logo_box">
                                <div class="logo_icon">

                                    <i class="fa-solid fa-clipboard-list" style="font-size: 25px;" ></i>
                                </div>
                            </div>
                        </div>
                        <div class="about_you">
                            <div class="about_titlr" >
                                <b style="color: #545454 !important;" >Choose your treatment</b>
                            </div>
                            <div class="about_text">
                                <span style="color: #545454 !important;" >
                                You will be presented with the weight loss injections.</span>
                            </div>
                        </div>
                    </div>
                </div>
               </div>


               <div class="treatment_menu" style="border: 1px solid #dadee6;" >
                <div class="menu_about">
                    <div class="about_content">
                        <div class="about_logo">
                            <div class="logo_box">
                                <div class="logo_icon">
                                    <i class="fa-solid fa-mobile" style="font-size: 25px;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="about_you">
                            <div class="about_titlr">
                                <b style="color: #545454 !important;" >Complete your order</b>
                            </div>
                            <div class="about_text">
                                <span style="color: #545454 !important;" >Once you have chosen you will be taken to the Order Summary for payment.
</span>
                            </div>
                        </div>
                    </div>
                </div>
               </div>


               <div class="treatment_menu" style="border: 1px solid #dadee6;" >
                <div class="menu_about">
                    <div class="about_content">
                        <div class="about_logo">
                            <div class="logo_box">
                                <div class="logo_icon">
                                    <i class="fa-regular fa-image" style="font-size: 25px;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="about_you">
                            <div class="about_titlr">
                                <b style="color: #545454 !important;" >Access the portal to complete verification</b>
                            </div>
                            <div class="about_text">
                                <span style="color: #545454 !important;" >
Once you have placed your order you will need to access our portal to complete the process & upload your ID and video.</span>
                            </div>
                        </div>
                    </div>
                </div>
               </div>

               <div class="get_started">
                <div class="started_button">
                    <div class="get_btn">
                       <a href="/get-started/consultation"> <button>Get started</button></a>
                    </div>
                </div>
               </div>

               <div class="excellent_content">






               </div>
            </div>

        </section>

  <!--  <script>
      document.addEventListener('DOMContentLoaded', function () {
        var modalElement = document.getElementById('holidayScheduleModal');
        if (modalElement && window.bootstrap) {
          var holidayModal = bootstrap.Modal.getOrCreateInstance(modalElement);
          var closeTriggers = modalElement.querySelectorAll('[data-bs-dismiss="modal"]');

          closeTriggers.forEach(function (trigger) {
            trigger.addEventListener('click', function () {
              holidayModal.hide();
            });

            trigger.addEventListener('touchend', function (event) {
              event.preventDefault();
              holidayModal.hide();
            }, { passive: false });
          });

          holidayModal.show();
        }
      });
    </script>-->

    <?php
  perch_layout('getStarted/footer');?>
