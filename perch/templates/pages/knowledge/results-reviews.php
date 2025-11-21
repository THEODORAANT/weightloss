
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






    <!-- ===================================================================header section End================================================================================================  -->



    <!-- =======================================================WEIGHT LOSS  section  Start============================================================================================= -->

    <section class="weight-loss-section  mt-5">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="min_img">
                        <img src="/asset/ruls-1.jpg" alt="Happy Woman" class="img-fluid rounded">
                    </div>
                </div>
                <div class="col-md-6 title ">
                    <h6 class="text-uppercase" style="    color: #000; font-weight: 500;" >Weight Loss</h6>
                    <h2 class="fw-bold text-uppercase">Results drive Results</h2>
                    <p class="expert" >
                    Small weight loss successes build momentum—each pound lost boosts motivation, refines habits, and drives continued progress toward long-term weight and fitness goals.
</p>
                    <!-- <p class="price">From <span class="old-price">&pound;209.00</span> <span class="new-price" style="color: #288881 !important;" >&pound;125.00</span> / month</p>
     -->


                        <div class="min_img2 min_img3 ">
                            <img src="/asset/ruls-1.jpg" alt="Happy Woman" class="img-fluid rounded">
                            <div class="custom_border"></div>
                        </div>



                    <ul class="list-unstyled">
                        <!-- <li><i class="fa-solid fa-square-check"></i> Lose up to 50% more weight than dieting alone.¹</li> -->
                        <!-- <li><i class="fa-solid fa-square-check"></i> Continuous clinical care</li>
                        <li><i class="fa-solid fa-square-check"></i> Access experts in nutrition, exercise, and habit formation</li> -->
                    </ul>
                    <a href="/get-started" class="btn btn-primary  " style="width: 330px;
                    font-weight: 600;
                    padding: 15px 0px;
                    color: #000;
                    background-color: #B0D136;
                    border: none;
                    margin: 15px 0px; text-transform: uppercase; "  >Get Started →</a>
                    <a href="/order/re-order" class="btn btn-primary" style="width: 330px; font-weight: 600;padding: 15px 0px;color: #000;  background-color: rgb(255 160 77 / 88%);border: none;margin: 15px 0px;">Re Order →</a>

                </div>
            </div>
        </div>
    </section>




    <!-- =======================================================WEIGHT LOSS  section  End============================================================================================= -->







    <!-- =======================================================getweightloss Research  section  start============================================================================================= -->



    <section class="research-section ">

        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 research_image ">

                    <div class="research_img">
                        <img src="/asset/Landing-7.jpg" class="img-fluid" alt="Research Report">
                    </div>

                    <!-- <button class="btn btn-dark Read_full ">Read full report</button> -->

                </div>
                <div class="col-md-6">
                    <div class="research">
                        <h1 class="mt-3">Nadia, 30.</h1>
                        <p class="text-muted">“
                                               As a 30-year-old new mum, losing weight after pregnancy felt overwhelming. Between sleepless nights and caring for my baby, finding time for strict diets and intense workouts was tough. I chose weight loss injections as a supportive tool to boost my metabolism and manage cravings. Combined with healthy eating and movement, they’ve given me the extra help I need to regain confidence and energy for motherhood.
”</p>

                        <!-- <button class="btn btn-dark large_device ">Read full report</button> -->
                    </div>
                </div>
            </div>





            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="research">
                        <h1 class="mt-3">John, 37.</h1>
                        <p class="text-muted">“As a 37-year-old man, balancing work, family, and health felt like a challenge. Despite trying diets and exercise, losing weight remained difficult. I chose weight loss injections to help control cravings and boost metabolism, giving me the extra push I needed. Combined with healthier habits, they’ve made weight loss more manageable, helping me feel more energetic, confident, and ready to keep up with my busy lifestyle.”</p>
                        <!-- <button class="btn btn-dark large_device ">Read full report</button> -->
                    </div>
                </div>

                <div class="col-md-6 research_image ">

                    <div class="research_img">
                        <img src="/asset/Landing-8.jpg" class="img-fluid" alt="Research Report">
                    </div>
                    <!-- <button class="btn btn-dark Read_full ">Read full report</button> -->
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-md-6 research_image ">
                    <div class="research_img">
                        <img src="/asset/Landing-4.jpg" class="img-fluid" alt="Research Report">
                    </div>
                    <!-- <button class="btn btn-dark Read_full ">Read full report</button> -->
                </div>
                <div class="col-md-6">
                    <div class="research">
                        <h1 class="mt-3">Elaine, 49.</h1>
                        <p class="text-muted">“As a 49-year-old woman, losing weight had become increasingly challenging despite my efforts with diet and exercise. Hormonal changes and a slower metabolism made it harder to see results. I chose weight loss injections to help curb cravings and support my journey. Alongside healthier eating and movement, they’ve given me the boost I needed to feel more confident, energized, and in control of my health.”</p>

                        <!-- <button class="btn btn-dark large_device ">Read full report</button> -->
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Since I lost start -->

    <section class="More_success">
        <div class="containrt">

            <div class="effective_longTitle">
                <h1>More success stories</h1>
            </div>
    <!-- row 1 -->
            <div class="row">
   <?php
                  perch_collection('SuccessStories', [

                      'count'      => 3,
                  ]);
              ?>




            </div>
            <!-- row2 -->

            <div class="row">
   <?php
                  perch_collection('SuccessStories', [
'start'=> 4,
                      'count'      => 3,
                  ]);
              ?>









            </div>


             <!-- row2 end -->


        </div>



     </section>










    <!-- ======================================================= Research  section  End============================================================================================= -->


    <!-- ======================================================= backed by   section  Start ============================================================================================= -->




    <section class="weight-backed-section text-center">
        <div class="container">
            <div class="backed_content">
                <div class="backed_title">
                    <p class="badge-text" style="text-transform: uppercase;" >Weight Loss Injections</p>
                    <h2>Weight loss, backed by science</h2>
                    <p class="subheading">Here’s what to expect from our three-part programme.</p>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-4 col-sm-12 col-lg-4 ">
                        <div class="card">
                            <div class="card-icon">
                                <img src="/asset/Capsule.svg" alt="Pill Icon">
                            </div>
                            <h5>Clinically-proven <br> treatment</h5>
                            <p>Tailored treatment plans, designed by you and our clinicians to support your health journey.</p>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 col-lg-4">
                        <div class="card">
                            <div class="card-icon">
                                <img src="/asset/download uuuu.svg" alt="Healthcare Icon">
                            </div>
                            <h5>Ongoing clinical care</h5>
                            <p>Our clinicians will help you manage potential side effects, make dosage adjustments, and answer any questions you might have throughout the programme.</p>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 col-lg-4">
                        <div class="card">
                            <div class="card-icon">
                                <img src="/asset/download uuuu.svg" alt="Coaching Icon">
                            </div>
                            <h5>One-to-one coaching</h5>
                            <p>Unlimited access to nutritionists, exercise physiologists, and behaviour change experts. They’ll give you tailored advice to establish lasting habits.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<!-- Health coaching start -->
<!--
<section>

    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h1>Health coaching: your personalised support system</h1>
               <div content>
                 <p>Real results come from real support. With getweightloss’s health coaching, patients like Rebecca and Alex have not only achieved their weight loss goals but also learned how to maintain them long-term. Health coaching isn’t about restrictive diets or gruelling workout regimes. Instead, it’s tailored to you, designed to fit seamlessly into your life and make sustainable changes.

                    Rebecca’s story highlights the power of behavioural coaching. “Shahana has been enthusiastic, supportive, and genuinely invested in my journey. Our weekly check-ins and her guidance on managing side effects and optimising my nutrition have been crucial. She’s available for a phone call to clarify nutritional information whenever I need it,” she shares.

                    Patients who have never tried weight loss medication before and actively engage with coaching and the app see 50% more weight loss compared to those using medication alone.1</p>

               </div>
            </div>

            <div class="col-md-6">
                <div class="content_img">
                <img style="width: 60%;" src="asset/mobile1.webp"

                </div>
             </div>
        </div>
    </div>

</section> -->



 <!-- Health coaching end -->

<!-- Obesity start -->
<section class="Obesity_2" >

    <div class="container">
        <div class="row">
            <div class="col-md-6  ">

                <div class="not">
                    <h1>What are probable causes of weight gain outside the obvious.</h1>
               <div content>
<p>Weight is closely linked to stress, sleep, and diet, as these factors influence metabolism, appetite, and fat storage. Chronic stress triggers cortisol production, which can lead to increased cravings for high-calorie foods and abdominal fat accumulation. Poor sleep disrupts hunger hormones, increasing appetite and reducing metabolism, making weight management harder. Diet plays a central role—nutrient-rich foods support balanced hormones and energy levels, while processed foods contribute to weight gain. Managing stress through relaxation techniques, prioritizing quality sleep, and maintaining a balanced diet are key strategies for achieving a healthy weight and preventing obesity-related health complications.
</p>
               </div>
                </div>

            </div>

            <div class="col-md-6">
                <div class="content_img">
                <img style="width: 100%;" src="/asset/w11.webp">

                </div>
             </div>
        </div>
    </div>

</section>



<!-- Obesity end -->

 <!-- health challenge strat -->




 <section class="research-section" style="background-color: #F5F5F5;padding-top: 40px;" >

    <div class="container ">
        <div class="row align-items-center">
            <div class="col-md-6">


                <div class="research">

<!--
                    <span class="badge bg-dark">HEALTH CHALLENGE</span> -->
                    <h1 class="mt-3">What do weight loss injections do?</h1>
                    <p class="text-muted">Weight loss injections, such as GLP-1 receptor agonists, can aid long-term weight loss by regulating appetite, slowing digestion, and improving blood sugar control. These medications help individuals feel fuller for longer, reducing overeating and cravings. By addressing underlying metabolic factors, weight loss injections support sustainable changes rather than quick fixes. When combined with a healthy diet and regular exercise, they can lead to significant and maintained weight loss. Additionally, they may lower the risk of obesity-related conditions like diabetes and heart disease. However, long-term success depends on lifestyle modifications alongside medication use for lasting weight management.</p>

                    <button class="btn btn-dark large_device ">Learn more</button>


                </div>


            </div>
            <div class="col-md-6 research_image ">

                <div class="research_img" style="height: auto;" >
                    <img src="/asset/ex-4.jpg" class="img-fluid" alt="Research Report">
                </div>

                <button class="btn btn-dark Read_full ">Learn more</button>

            </div>
        </div>
    </div>

</section>



 <!-- end challange -->


        <!-- ======================================================= backed by   section  End============================================================================================= -->



            <!-- ======================================================= The progress    section  start============================================================================================= -->


            <!-- <section class="the_progress">


                <div class="container">



                    <div class="progress_content">


                        <div class="progress_title ">
                            <span class="badge bg-info text-dark loss ">YOUR WEIGHT LOSS JOURNEY</span>
                            <h2 class="mt-3">The progress you can expect</h2>
                        </div>

                        <div class="timeline mt-5">
                            <div class="row ">
                                <div class="col-md-4 online " style="position: relative;">
                                    <span class="badge bg-info text-dark">TODAY</span>
                                    <div class="image-container">
                                        <img src="asset/phon.webp" alt="Simple Assessment">
                                    </div>
                                    <h4 class="fw-bold">Simple assessment</h4>

                                    <p>Take our online consultation. If <br> eligible, you'll receive your <br> clinically-prescribed <br> medication swiftly. Access your <br> clinicians and coaches through <br> the app.</p>
                                </div>
                                <div class="col-md-4 online " style="position: relative;" >
                                    <span class="badge bg-info text-dark">1-6 MONTHS</span>
                                    <div class="image-container">
                                        <img src="asset/up.webp" alt="Simple Assessment">
                                    </div>
                                    <h4 class="fw-bold">Healthy weight <br> loss</h4>

                                    <p>Lose weight and learn how <br> to reframe your <br> relationship with food. <br> Expect increased fitness,<br> energy, and confidence.</p>
                                </div>
                                <div class="col-md-4 online " style="border: none;" >
                                    <span class="badge bg-info text-dark">6-12 MONTHS</span>
                                    <h4 class="fw-bold">Lasting change</h4>
                                    <div class="image-container">
                                        <img src="asset/dwun.webp" alt="Lasting Change">
                                    </div>
                                    <p>With continued support <br> from your coach, adopt <br> healthier lifestyle habits to <br> help maintain weight loss.</p>
                                </div>
                            </div>
                            <div class="progress-line">
                                <span class="dot"></span>
                                <span class="dot"></span>
                                <span class="dot"></span>
                                <span class="dot"></span>
                            </div>
                        </div>


                    </div>




                </div>



            </section> -->




            <!-- ======================================================= The progress   section  End============================================================================================= -->



                <!-- ======================================================= HEALTH CHALLENGE   section  start============================================================================================= -->










<!-- ======================================================= HEALTH CHALLENGE  section End============================================================================================= -->




 <!-- ======================================================= Effective solutionsE  section  start============================================================================================= -->

                    <!-- <section class="effective">
                        <div class="container">
                            <div class="effective_content">
                                <div class="effective_shortTitle">
                                    <span>WEIGHT LOSS TREATMENTS</span>
                                </div>
                                <div class="effective_longTitle">
                                    <h1>Effective solutions for weight loss</h1>
                                </div>
                                <div class="effective_longTitl">
                                    <span>A programme to change your weight, mindset, and behaviour.</span>
                                </div>
                                <div class="responsive" style="margin-top: 25px;" >
                                    <div class="wegovy">
                                        <div class="wegovy_title">
                                            <h5>Weight Loss Programme + Wegovy</h5>
                                        </div>
                                        <span class="proven" >Clinically-proven weight loss</span>
                                        <p class="price">From <span class="old-price">&pound;209.00</span> <span class="new-price" style="color: #288881 !important;" >&pound;125.00</span> / month</p>
                                        <div class="imges">
                                            <img src="asset/slider.webp" alt="">
                                        </div>
                                        <button>Learn more <i class="fa-solid fa-arrow-right"></i> </button>

                                    </div>
                                    <div class="wegovy">
                                        <div class="wegovy_title">
                                            <h5>Weight Loss Programme + Wegovy</h5>
                                        </div>
                                        <span class="proven" >Clinically-proven weight loss</span>
                                        <p class="price">From <span class="old-price">&pound;209.00</span> <span class="new-price" style="color: #288881 !important;" >&pound;125.00</span> / month</p>
                                        <div class="imges">
                                            <img src="asset/slider.webp" alt="">
                                        </div>
                                        <button>Learn more <i class="fa-solid fa-arrow-right"></i> </button>
                                    </div>
                                    <div class="wegovy">
                                        <div class="wegovy_title">
                                            <h5>Weight Loss Programme + Wegovy</h5>
                                        </div>
                                        <span class="proven" >Clinically-proven weight loss</span>
                                        <p class="price">From <span class="old-price">&pound;209.00</span> <span class="new-price" style="color: #288881 !important;" >&pound;125.00</span> / month</p>
                                        <div class="imges">
                                            <img src="asset/slider.webp" alt="">
                                        </div>
                                        <div class="slider_button">
                                            <button>Learn more <i class="fa-solid fa-arrow-right"></i></button>
                                            <button>Learn more</i></button>
                                        </div>
                                    </div>
                                    <div class="wegovy">
                                        <div class="wegovy_title">
                                            <h5>Weight Loss Programme + Wegovy</h5>
                                        </div>
                                        <span class="proven" >Clinically-proven weight loss</span>
                                        <p class="price">From <span class="old-price">&pound;209.00</span> <span class="new-price" style="color: #288881 !important;" >&pound;125.00</span> / month</p>
                                        <div class="imges">
                                            <img src="asset/slider.webp" alt="">
                                        </div>
                                        <div class="slider_button">
                                            <button>Learn more <i class="fa-solid fa-arrow-right"></i> </button>
                                            <button>Learn more </i> </button>
                                        </div>
                                    </div>
                                    <div class="wegovy">
                                        <div class="wegovy_title">
                                            <h5>Weight Loss Programme + Wegovy</h5>
                                        </div>
                                        <span class="proven" >Clinically-proven weight loss</span>
                                        <p class="price">From <span class="old-price">&pound;209.00</span> <span class="new-price" style="color: #288881 !important;" >&pound;125.00</span> / month</p>
                                        <div class="imges">
                                            <img src="asset/slider.webp" alt="">
                                        </div>
                                        <button>Learn more <i class="fa-solid fa-arrow-right"></i> </button>
                                    </div>
                                  </div>
                            </div>
                        </div>
                    </section> -->





 <!-- =======================================================Effective solutions  section  End============================================================================ -->



 <!-- =======================================================More success stories section  start =========================================================================== -->




 <!-- =======================================================More success stories  section  End  =========================================================================== -->


 <!-- ======================================================= UK licensed stories  section  start  =========================================================================== -->









 <!-- ======================================================= UK licensed  section  End  =========================================================================== -->


 <!-- ======================================================= Your expert team section  start  =========================================================================== -->


<!--
 <section class="Your_expert_team">



    <div class="expert_team">

        <div class="container">
            <div class="effective_content">
                <div class="effective_shortTitle">
                    <span>UK DOCTORS AND CLINICIANS</span>
                </div>
                <div class="effective_longTitle">
                    <h1>Your expert team</h1>
                </div>
                <div class="effective_longTitl">
                    <span>Specialists in weight loss, nutrition, and behavioural science.</span>
                </div>

            </div>
        </div>

    </div>


    <div class="responsive" style="margin-top: 25px;" >
        <div class="wegovy">
            <div class="wegovy_text">
                <div class="wegovy_title">
                    <h5>Dr Luke Pratsides</h5>
                </div>
                <span class="proven" >Head of Medical</span>
                <p>MBBS MSc MRCGP MFCI</p>
            </div>

            <div class="imges">
                <img src="asset/Dr Luke Pratsides.webp" alt="">
            </div>
        </div>

        <div class="wegovy">
            <div class="wegovy_text">
                <div class="wegovy_title">
                    <h5>Dr Bryony Henderson</h5>
                </div>
                <span class="proven" >Medical Director</span>
                <p>MBBS BSc MRCGP DFFP</p>
            </div>

            <div class="imges">
                <img src="asset/Dr Bryony Henderson.webp" alt="">
            </div>
        </div>
        <div class="wegovy">
            <div class="wegovy_text">
                <div class="wegovy_title">
                    <h5>Sophie Kanani</h5>
                </div>
                <span class="proven" >Health Coaching Operations Manager</span>
                <p>BSc (Hons) Dip</p>
            </div>

            <div class="imges">
                <img src="asset/Sophie.webp" alt="">
            </div>
        </div>
        <div class="wegovy">
            <div class="wegovy_text">
                <div class="wegovy_title">
                    <h5>Jess Uffindell</h5>
                </div>
                <span class="proven" >Registered Nutritionist</span>
                <p>BANT CNHC BSc (Hons)</p>
            </div>

            <div class="imges">
                <img src="asset/Jess Uffindell.webp" alt="">
            </div>
        </div>
        <div class="wegovy">
            <div class="wegovy_text">
                <div class="wegovy_title">
                    <h5>Mo Dekmak</h5>
                </div>
                <span class="proven" >Clinician</span>
                <p>MPHARM PGDIP IPRESC</p>
            </div>

            <div class="imges">
                <img src="asset/Mo Dekmak.webp" alt="">
            </div>
        </div>
        <div class="wegovy">
            <div class="wegovy_text">
                <div class="wegovy_title">
                    <h5>Shivani Sharma-Savani</h5>
                </div>
                <span class="proven" >Prescribing Lead</span>
                <p>MPharm PGCert PCert IP</p>
            </div>

            <div class="imges">
                <img src="asset/Shivani Sharma-Savani.webp" alt="">
            </div>
        </div>
        <div class="wegovy">
            <div class="wegovy_text">
                <div class="wegovy_title">
                    <h5>Faye Townsend</h5>
                </div>
                <span class="proven" >Senior Health Coach</span>
                <p>AfN BSc BDA PGDIP BPS</p>
            </div>

            <div class="imges">
                <img src="asset/Faye Townsend.webp" alt="">
            </div>
        </div>

      </div>


 </section> -->




 <!-- ======================================================= Your expert team  section  End  =========================================================================== -->


<!--
    <section class="custom" style="background-color: #f9f9fA;" >

    <div class="container">

        <div class="custom_text">
            <b>FAQs</b>
            <h4>Your questions answered</h4>
        </div>





    <ul class="accordion-list">
        <li>
          <h3>How much does a website cost?</h3>
          <div class="answer">
            <p>While we would love to be able to give a definitive, fixed price for a website, it really depends on the specific needs for each individual business. If one business needs a website comprised of five pages, while another has a substantially larger site of over 100 pages - obviously those projects are going to command different price points.</p>

            <p>With that being said - here are some general guidelines on what to expect from a pricing standpoint.</p>

            <p>If you can get by with a small website (between 3 - 10 pages) using a pre-designed template, you can expect to pay approximately $2,500.00. A mid-sized website that has anywhere from 11 - 25 pages, but still uses a pre-made template, will be between $3,000.00 - $5,000.00. If you have a lot of pages, are looking for something completely custom, or are looking for something that has special functionality such as eCommerce, custom calculators, or integrations with other services, you may be looking at anywhere from $10,000 - $20,000.</p>

            <p>Ultimately, the takeaway here is that we can accommodate projects of just about any budget - so long as expectations are set accordingly.</p>

          </div>
        </li>
        <li>
          <h3>Are there any monthly fees?</h3>
          <div class="answer"><p>This will vary depending on the type of project. For logo & branding projects, fees will be one-time costs. For website projects, we will typically charge a monthly fee, depending on the level of service you choose. To view a full list of our monthly packages, click here.</p></div>
        </li>
        <li>
          <h3>How much does a logo cost?</h3>
          <div class="answer">
            <p>Logo projects typically cost between $3,000 - $5000.</p>
            <p>"Why so much?" many people ask. To answer simply - because they require a lot of work. At least, to do them properly. And of all of the investments that you will make for your business, your logo & branding is one area where you don't want to skimp.</p>

            <p>In business, image is everything. And while your overall brand is based on far more than just your logo, a bad logo can create a strong first impression (for better or for worse).</p>

            <p>A properly designed logo requires hours of research, and hundreds of sketches and iterations. And with an increasing number of branded businesses in the world, doing research to verify that your mark is unique is a tedious yet necessary part of the process.</p>

            <p>With each logo project that we complete, we provide 3 - 5 unique logo concepts, a branding guide that details how to properly use the logo, several versions of the logo that are print-ready, and can be sent to any printer or publisher for easy use, detailed color schemes, typography & associated fonts, and design samples of your new logo.</p>

            <p>Click here to learn more about how we approach our logo projects, and why we believe that we are the top logo & branding agency in the area.</p>
          </div>
        </li>
        <li>
          <h3>What separates Right Creative from other top design agencies in the area?</h3>
          <div class="answer">
            <p>Experience, quality, and results.</p>
            <p><strong>Experience.</strong> We have been designing websites professional since the early days of the web - before CSS was mainstream, and people still used tables for their website layouts. In the 20+ years that we have been doing this, we have work with and learned from many of the top voices in several industries. To put it simply - there are very few people in the world, let alone the area that can top our years of experience.</p>

            <p><strong>Quality</strong> At Right Creative, we hold an almost unrealistic standard for quality. We demand excellence in every project we take on, and firmly believe that the quality of our work demonstrates our commitment to these standards.</p>

            <p><strong>Results</strong> More than anything else, our focus is, and will always be getting results for clients. We don't care how pretty something is - if it is not resulting in the growth or obtaining of goals for our clients, then it is not time or money well spent. Our mission is to turn visitors into paying customers - and every decision that we make is rooted in that mission.</p>

            <p><strong>Other distinguishing features</strong> Some of the other things that separate Right Creative from other top-rated agencies are: we hand-code all of our websites for maximum results. We don't rely on Wordpress, plugins, templates or themes to accomplish things, and are therefore not locked in to the limits they pose.</p>
          </div>
        </li>
        <li>
          <h3>We're just starting out. How can we afford your services?</h3>
          <div class="answer">
            <p>We started this business from scratch, and know full-well how tight money can be when you are first starting out. We empathize with the struggles of new business owners, and therefore work diligently so that the new businesses that we work with succeed.</p>

            <p>The primary criteria we look for when working with new businesses is the seriousness of the business owner. While it is natural for a new business owner to be cautious with the money they spend - we do look for a commitment to growth. If you do not have a specific plan for how you intend to grow your business, then the reality is - you will probably never be in a good position to afford our services.</p>

            <p>With that said - if you are growth-minded, then we are happy to help you achieve that growth, and are willing to work with you to come up with a plan that fits within your budget and comfort level. Whether this is a smaller site, financing options, or a payment plan - we are confident that we can figure out something that will be mutually beneficial.</p>

          </div>
        </li>
        <li>
          <h3>What is the difference between a pre-designed site and a custom site?</h3>
          <div class="answer">
            <p>The difference between pre-designed and custom sites is a lot like the difference between buying a home versus building a home.</p>

            <p>Pre-designed sites start with a website that has already been designed and coded. These sites will allow you to customize colors, fonts, photos, content, and your logo - but does not allow you to rearrange, reposition, or change the layout of the site. This option is great for newer businesses or businesses with a smaller budget. You not only get the benefit of a hand-coded, customized site, but you also benefit from a smaller price tag and faster turnaround time.</p>

            <p>Custom sites are built from scratch, exactly to your businesses needs. While custom sites often have a bigger price tag attached to them, they are also going to be more effective at converting your visitors into paying customers.</p>

          </div>
        </li>
        <li>
          <h3>Can I make edits to my own website once it launches?</h3>
          <div class="answer">
            <p>Yes and no. With each client that we work with, we designate time to figure out what content clients will need to edit on a regular basis, and create tools to allow them to do that easily. For things such as news, articles, blogs, and portfolios, clients will have access to the Content Management System, and add/edit/delete those things as needed.</p>

            <p>For other areas of the site, we request that our clients trust us to make those edits, so to maintain the integrity of their website.</p>

            <p>It is very common for clients who have full control over their site to see it deteriorate after a while due to non-designers & non-developers adding content to the site using markup & styling that is not cohesive with the rest of the site. What results is a website filled with pages containing broken-looking content, and improperly formatted text.</p>

            <p>Because we care about the quality of our sites, the reputations of our clients, and the results that they see - we prefer to let our clients be the experts at what they do - and us to be the experts at what we do.</p>

          </div>
        </li>
      </ul>

    </div>

</section> -->





 <!-- ======================================================= FAQs  section  start  =========================================================================== -->

<section class="KNOWLEDGe">


    <div class="containrt">
        <div class="knowledge_text">
            <div class="effective_shortTitle">
                <span style="color: #fff; text-transform: uppercase;">Health Hub & News</span>
            </div>
            <div class="effective_longTitle">
                 <h1>Weight loss: what you need to know</h1>
            </div>
        </div>
              <div class="responsive">
                <?php
                                      perch_blog_custom(array(
                                          'template' => 'weight_post_in_list.html',
                                          'count'=>8
                                      ));
                                      ?>



              </div>

    </div>



</section>







 <!-- ======================================================= FAQs  section  End  =========================================================================== -->




 <!--======================================================== footer section start================================================================= -->

 <section class="UK_licensed">
    <div class="responsiv" >
        <div class="licensed_content">
            <div class="card_slide">
                <div class="slide_imge">
                    <img src="/asset/download uuuu.svg" alt="">
                </div>
                <div class="slide_title">
                    <h5>Discreet Delivery</h5>
                </div>
                <div class="slide_content">
                    <p>No names, no logos.</p>
                </div>
            </div>
        </div>
        <div class="licensed_content">
            <div class="card_slide">
                <div class="slide_imge">
                    <img src="/asset/ecol.svg" alt="">
                </div>
                <div class="slide_title">
                    <h5>Ongoing Support</h5>
                </div>
                <div class="slide_content">
                    <p>Always available via email/chat.</p>
                </div>
            </div>
        </div>

        <div class="licensed_content">
            <div class="card_slide">
                <div class="slide_imge">
                    <img src="/asset/Capsule.svg" alt="">
                </div>
                <div class="slide_title">
                    <h5>You are in Control</h5>
                </div>
                <div class="slide_content">
                    <p>Each month you decide to continue or stop.</p>
                </div>
            </div>
        </div>
        <div class="licensed_content">
            <div class="card_slide">
                <div class="slide_imge">
                    <img src="/asset/star_1.svg" alt="">
                </div>
                <div class="slide_title">
                    <h5>Additional Testing</h5>
                </div>
                <div class="slide_content">
                    <p>We can arrange blood tests, through our partners</p>
                </div>
            </div>
        </div>

        <div class="licensed_content">
            <div class="card_slide">
                <div class="slide_imge">
                    <img src="/asset/shild.svg" alt="">
                </div>
                <div class="slide_title">
                    <h5>Health Hub
</h5>
                </div>
                <div class="slide_content">
                    <p>The health hub, an access point for news and tips.</p>
                </div>
            </div>
        </div>

        <div class="licensed_content">
            <div class="card_slide">
                <div class="slide_imge">
                    <img src="/asset/japan.svg" alt="">
                </div>
                <div class="slide_title">
                    <h5>Competitive Pricing
</h5>
                </div>
                <div class="slide_content">
                    <p>We constantly monitor prices.</p>
                </div>
            </div>
        </div>

       <!-- <div class="licensed_content">
            <div class="card_slide">
                <div class="slide_imge">
                    <img src="asset/sqar.svg" alt="">
                </div>
                <div class="slide_title">
                    <h5>Free, discreet delivery</h5>
                </div>
                <div class="slide_content">
                    <p>No names, no logos, no delivery fee.</p>
                </div>
            </div>
        </div>
        <div class="licensed_content">
            <div class="card_slide">
                <div class="slide_imge">
                    <img src="asset/sqar.svg" alt="">
                </div>
                <div class="slide_title">
                    <h5>Free, discreet delivery</h5>
                </div>
                <div class="slide_content">
                    <p>No names, no logos, no delivery fee.</p>
                </div>
            </div>
        </div>
        <div class="licensed_content">
            <div class="card_slide">
                <div class="slide_imge">
                    <img src="asset/sqar.svg" alt="">
                </div>
                <div class="slide_title">
                    <h5>Free, discreet delivery</h5>
                </div>
                <div class="slide_content">
                    <p>No names, no logos, no delivery fee.</p>
                </div>
            </div>
        </div>
        <div class="licensed_content">
            <div class="card_slide">
                <div class="slide_imge">
                    <img src="asset/sqar.svg" alt="">
                </div>
                <div class="slide_title">
                    <h5>Free, discreet delivery</h5>
                </div>
                <div class="slide_content">
                    <p>No names, no logos, no delivery fee.</p>
                </div>
            </div>
        </div>-->
    </div>
 </section>






  <?php

      perch_layout('global/new/footer');?>
