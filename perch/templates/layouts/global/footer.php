


 <!--======================================================== footer section start================================================================= -->



  <!--====================== Footer Section Start =============================-->
  <section class="footer_section container-fluid">



  <div class="as_seen">

  </div>


    <div class="container">
      <div class="main_footer">
        <div class="row">
          <div class="col-md-3">
              <div class="footer_list">
                <h5 class="footer_title">Treatments</h5>
                <ul>
                  <li><a href="/medications/wegovy">Wegovy

</a></li>
                  <li><a href="/medications/mounjaro">Mounjaro</a></li>
                  <li><a href="/medications/ozempic">Ozempic</a></li>
                  <li><a href="/tests">Blood Tests</a></li>

                </ul>
              </div>
          </div>
          <div class="col-md-3">
              <div class="footer_list">
                <h5 class="footer_title">Contact</h5>
                <ul>
                 <li><a href="/contact-us">Contact Us</a></li>
                  <li><a href="/faqs">FAQS</a></li>
                  <li><a href="mailto:support@getweightloss.co.uk"> support@getweightloss.co.uk</a></li>

                </ul>
              </div>
          </div>
          <div class="col-md-3">
              <div class="footer_list">
                <h5 class="footer_title">Get weight loss</h5>

   <ul>
        <li><a style="text-decoration: none;" href="/get-started">Get Started</a></li>
                  <li><a style="text-decoration: none;" href="/client">Log in</a></li>
                  <li><a style="text-decoration: none;"  href="/sitemap">Sitemap</a></li>
                      <li><a style="text-decoration: none;" href="/terms-and-conditions">Terms & conditions</a></li>
                                      <li><a style="text-decoration: none;" href="/refer-a-friend">Refer a friend</a></li>
                                      <li><a style="text-decoration: none;" href="/privacy-notice">Privacy notice</a></li>
                                      <li><a style="text-decoration: none;" href="/cookies-policy">Cookies policy</a></li>
                                      <li><a style="text-decoration: none;"  href="/make-a-complaint">Make a complaint</a></li>
                </ul>
              </div>
            </div>
          <div class="col-md-3">
              <div class="footer_list">
                <h5 class="footer_title">Follow</h5>
                <ul>
                  <li><a href="/blog">Blog</a></li>
                 <!-- <li><a href="https://www.facebook.com/">Facebook</a></li>
                  <li><a href="https://x.com/">Twitter</a></li>
                  <li><a href="https://www.instagram.com/">Instagram</a></li>-->
                </ul>
              </div>
          </div>


          <div class="logo_section">
            <div class="row">
          <!--================= Logo section Start=====================    <div class="col-md-6">

              </div>
              <div class="col-md-6">
                <div class="right_side">
                  <div class="icon_links">

                  </div>
                </div>
                  <ul>
                    <li><a href="/terms-and-conditions">Terms & conditions</a></li>
                    <li><a href="/refer-a-friend">Refer a friend</a></li>
                    <li><a href="/privacy-notice">Privacy notice</a></li>
                    <li><a href="/cookies-policy">Cookies policy</a></li>
                    <li><a href="/complaints">Make a complaint</a></li>
                  </ul>
              </div> --================= Logo section End=====================-->
              <br/>
              <br/>
            </div>
          </div>
        </div>
        <!--=============== copyright Section Start ===============-->
        <div style="margin-top: 17px;" class="copyright">

          <p>Copyright © . All rights reserved. Getweightloss is a trading name of MI Health Limited. Registered in England and Wales, 13008704.</p>
        </div>
<button id="scrollToTop">↑</button>
        <!--=============== copyright Section End =================-->
      </div>
    </div>
  </section>
  <!--====================== Footer Section End =============================-->





 <!--======================================================== footer section end================================================================= -->=





    <!-- ==================================================================coding End======================================================================================================== -->





    <?php
        $gaConfig = function_exists('perch_content_custom')
            ? perch_content_custom('Google Analytics', [
                'template' => 'google_analytics_ga4.html',
                'skip-template' => true,
                'count' => 1,
            ])
            : [];

        $gaMeasurementId = '';

        if (is_array($gaConfig) && !empty($gaConfig)) {
            $firstConfig = $gaConfig[0];
            if (is_array($firstConfig) && !empty($firstConfig['measurement_id'])) {
                $gaMeasurementId = trim($firstConfig['measurement_id']);
            }
        }

        if ($gaMeasurementId === '') {
            $gaMeasurementId = getenv('GA_MEASUREMENT_ID')
                ?: getenv('GOOGLE_ANALYTICS_ID')
                ?: getenv('GOOGLE_ANALYTICS_MEASUREMENT_ID')
                ?: '';
        }

        if ($gaMeasurementId !== ''):
    ?>
        <!-- Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars($gaMeasurementId, ENT_QUOTES, 'UTF-8'); ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', <?= json_encode($gaMeasurementId); ?>, { 'anonymize_ip': true });
        </script>
    <?php endif; ?>

    <script type="text/javascript" src="/js/jquery-3.7.1.min.js"></script>
    <script type="text/javascript" src="/js/popper.min.js"></script>
    <script type="text/javascript" src="/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="/js/slick.min.js"></script>
    <script type="text/javascript" src="/js/lazyload.min.js"></script>
    <script type="text/javascript" src="/js/theme.js"></script>
    <script type="text/javascript" src="/js/my-2.js"></script>
    <script src="/js/my.js"></script>
    <script src="/js/due.js"></script>


    <script>
        // next button active when any checkbox selected
document.addEventListener("DOMContentLoaded", function () {
    let checkboxes = document.querySelectorAll(".check1");
    let nextButton = document.getElementById("nextButton");
    let nextLink = nextButton.querySelector("a");

    function toggleNextButton() {
        let isAnyChecked = Array.from(checkboxes).some(chk => chk.checked);

        if (isAnyChecked) {
            nextButton.classList.remove("disabled");
            nextButton.style.backgroundColor = "#000000";
            nextLink.style.color = "black";
            nextButton.style.cursor = "pointer";
            nextLink.style.pointerEvents = "auto";
        } else {
            nextButton.classList.add("disabled");
            nextButton.style.backgroundColor = "#d3d3d3";
            nextLink.style.color = "#a0a0a0";
            nextButton.style.cursor = "default";
            nextLink.style.pointerEvents = "none";
        }
    }

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener("change", toggleNextButton);
    });

    toggleNextButton();
});
// next button active when any checkbox selected

</script>
<!--  -->

  <style>


        #scrollToTop {
            position: fixed;
            bottom: 40px;
            right: 40px;
            background-color: #333;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 50%;
            font-size: 20px;
            cursor: pointer;
            display: none;
        }
    </style>




<script>
    const scrollButton = document.getElementById("scrollToTop");

    window.addEventListener("scroll", () => {
        const scrollTop = window.scrollY;
        const scrollHeight = document.documentElement.scrollHeight;
        const clientHeight = window.innerHeight;

        // Show button only when at the bottom
        if (scrollTop + clientHeight >= scrollHeight) {
            scrollButton.style.display = "block";
        } else {
            scrollButton.style.display = "none";
        }
    });

    scrollButton.addEventListener("click", () => {
        window.scrollTo({ top: 0, behavior: "smooth" });
    });
</script>

</body>
</html>
