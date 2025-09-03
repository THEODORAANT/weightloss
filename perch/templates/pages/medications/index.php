<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Weight Loss</title>
        <link rel="shortcut icon" href="./asset/logo-final.png" type="image/x-icon">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin /><link
          href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Poppins:wght@700;800&display=swap"rel="stylesheet"/>
        <link rel="stylesheet" href="css/bootstrap.min.css" />
        <link rel="stylesheet" href="css/custom-font.css" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/style-2.css">
      </head>
<body>

    <!-- ==================================================================coding Start====================================================================================================== -->

    <!-- ===================================================================header section Start=============================================================================================  -->




     <!-- ==================================================================coding Start====================================================================================================== -->
     <header class="my_header" style="z-index: 999999;" >
        <!-- Mobile Menu Button -->
        <!-- <button class="menu_btn" onclick="mobileshow()">
         ☰
       </button> -->
         <input type="checkbox" id="check" onclick="mobileshow()"/>
         <label for="check">
           <i class="fa-solid fa-bars" id="btn"></i>
           <i class="fa-solid fa-xmark" id="cancel"></i>
         </label>
       <!-- Mobile Menu Button -->
       <div id="treatment-toggle" class="what_treat">
             Menu <i class="fas fa-chevron-down"></i>
       </div>
       <div class="logo">
        <a href=""><img src="asset/logo-final.png" style="height: 200px; width: 200px;" alt=""></a>
       </div>
       <div id="user-icon" class="user_account">
           <i class="fas fa-user"></i>
       </div>
   </header>

   <!--/////////////=============What we treat main section start//////////////-->
   <div id="dropdown-section" class="dropdown-content treatments_content bg-black text-white p-3" style="display: none;">
       <div class="row">
           <div class="col-md-4 treatments_left" id="treatment-options">
               <h6 class="treatment_title">Categories</h6>
               <ul>
                   <li class="page_links" data-target="weight-loss"><a href="#">Weight Loss</a></li>
                   <!-- <li class="page_links" data-target="low-testosterone"><a href="#">Low Testosterone</a></li>
                   <li class="page_links" data-target="sexual-performance"><a href="#">Sexual Performance</a></li>
                   <li class="page_links" data-target="diagnostics"><a href="#">Diagnostics</a></li>
                   <li class="page_links" data-target="hair-health"><a href="#">Hair Health</a></li>
                   <li class="page_links" data-target="supplements"><a href="#">Supplements</a></li>
                   <li class="page_links" data-target="consultations"><a href="#">Consultations</a></li>
                   <li class="page_links" data-target="health-advice"><a href="#">Health and Advice</a></li>
                   <li class="page_links" data-target="health-advice"><a href="/all-treatments">All Treatments</a></li> -->
               </ul>

               <!--////////////////========== Meet getweightloss section Start=======////////////-->
               <div class="meet_getweightloss">
                 <div class="meet_title mt-md-4 mb-md-3">
                     <h6 class="treatment_title">Resources & Information</h6>
                 </div>
                 <div class="meet_content">
                     <ul>
                         <li><a href="#">Health Hub</a></li>
                         <li><a href="#">Help & support</a></li>
                         <li><a href="#">About Us</a></li>
                         <!-- <li><a href="#">Careers</a></li>
                         <li><a href="#">Reviews</a></li>
                         <li><a href="#">Media enquiries</a></li>
                         <li><a href="#">Clinical research</a></li>
                         <li><a href="#">Experts</a></li> -->
                     </ul>
                 </div>
                  <!--////////////////========== Social Icon section Start=======////////////-->
                  <div class="social_icons_section">
                     <a href="#"><i class="fa-brands fa-square-facebook"></i></a>
                     <a href="#"><i class="fa-brands fa-instagram"></i></a>
                     <a href="#"><i class="fa-brands fa-square-twitter"></i></a>
                  </div>
                  <!--////////////////========== Social Icon section End=======////////////-->
             </div>
             <!--////////////////========== Meet getweightloss section End=======////////////-->
           </div>
           <!--////////////////========== Meet getweightloss section End=======////////////-->
           <!-- Additional sections for other treatments can be added similarly start-->




           <div class="col-md-4" id="dynamic-content">
             <!-- Weight Loss button elemets start -->
               <section id="weight-loss" class="content-section my_content">
                   <div class="multiple_links">
                     <h5 class="heading_link"><a href="landing_page.html">Weight Loss</a></h5>
                     <!-- <h6 class="sub_link"><a href="treatments.html">Weight Loss Injections</a></h6> -->
                     <ul>
                       <li class="list_link"><a href="page_mounjaro.html">Mounjaro</a></li>
                       <li class="list_link"><a href="page_ozempic.html">Ozempic </a></li>
                       <li class="list_link"><a href="page_wegovy.html">Wegovy</a></li>
                       <!-- <li class="list_link"><a href="page_alli.html">Alli</a></li>
                       <li class="list_link"><a href="weight_loss_support_supplement.html">Weight Loss Support supplement</a></li> -->
                     </ul>
                   </div>
                   <div class="multiple_links">
                     <h6 class="sub_link"><a href="results-reviews.html">Results</a></h6>
                     <ul>
                       <!-- <li class="list_link"><a href="page_alli.html">Alli</a></li> -->
                     </ul>
                   </div>

                   <div class="multiple_links">
                    <!-- <h6 class="sub_link"><a href="knowledge.html">Knowledge</a></h6> -->
                    <ul>
                      <!-- <li class="list_link"><a href="tips.html">Tips</a></li> -->
                      <!-- <li class="list_link"><a href="nutrition.html">Nutrition</a></li> -->
                      <li class="list_link"><a href="exercise.html">Exercise</a></li>
                      <li class="list_link"><a href="stress.html">Stress</a></li>
                      <!-- <li class="list_link"><a href="sleep.html">Sleep</a></li> -->
                    </ul>
                  </div>


                   <!-- <div class="multiple_links">
                     <h5 class="heading_link"><a href="">Health Hub</a></h5>
                      <ul>
                        <li class="list_link"><a href="health-coaching.html">Weight v Nutrition</a></li>
                       <li class="list_link"><a href="medication.html">Weight v Sleep</a></li>
                       <li class="list_link"><a href="obesity_asa_disease.html">Weight v Stress</a></li>
                     </ul>
                   </div> -->
                   <div class="multiple_links">
                     <!-- <h6 class="sub_link"><a href="#">Ending obesity</a></h6> -->
                     <ul>
                       <!-- <li class="list_link"><a href="#">Time for change</a></li> -->

                       <!-- <li class="list_link"><a href="total_health.html">Total Health Challenge</a></li> -->
                     </ul>
                   </div>

               </section>






               <!-- Weight Loss button elemets start -->
               <!-- low-testosterone button elemets start -->
               <section id="low-testosterone" class="content-section my_content">
                   <div class="multiple_links">
                     <h6 class="sub_link mb-3"><a href="#">Testosterone Blood Test</a></h6>
                     <h6 class="sub_link"><a href="#">Knowledge</a></h6>
                     <ul>
                       <li class="list_link"><a href="#">Condition and diagnosis</a></li>
                     </ul>
                   </div>
               </section>
               <!-- low-testosterone button elemets End -->
               <!-- Sexual Performance button elements start -->
               <section id="sexual-performance" class="content-section my_content">
                   <div class="multiple_links">
                     <h6 class="sub_link"><a href="#">Erectile Dysfunction</a></h6>
                     <ul>
                       <li class="list_link"><a href="#">Tadalafil Daily</a></li>
                       <li class="list_link"><a href="#">Sildenafil</a></li>
                       <li class="list_link"><a href="#">Tadalafil</a></li>
                       <li class="list_link"><a href="#">Viagra Connect</a></li>
                     </ul>
                   </div>
                   <div class="multiple_links">
                     <h6 class="sub_link"><a href="#">Premature Ejaculation</a></h6>
                     <ul>
                       <li class="list_link"><a href="#">Delay Wipes</a></li>
                       <li class="list_link"><a href="#">Priligy</a></li>
                     </ul>
                   </div>
               </section>
               <!-- Sexual Performance button elements end -->

               <!-- Diagnostics button start -->
               <section id="diagnostics" class="content-section my_content">
                   <div class="multiple_links">
                     <h6 class="sub_link"><a href="#">Diagnostics</a></h6>
                     <h6 class="sub_link mt-5"><a href="#">Full Check-Up</a></h6>
                     <ul>
                       <li class="list_link"><a href="#">Complete Blood Test</a></li>
                       <li class="list_link"><a href="#">Core Blood Test</a></li>
                     </ul>
                     <h6 class="sub_link mt-5"><a href="#">Sexual Performance</a></h6>
                     <ul>
                       <li class="list_link"><a href="#">Sexual Performance Blood Test</a></li>
                       <li class="list_link"><a href="#">Complete Sexual Performance Blood Test</a></li>
                     </ul>
                     <h6 class="sub_link mt-5"><a href="#">Hormone Health</a></h6>
                     <ul>
                       <li class="list_link"><a href="#">Testosterone Blood Test</a></li>
                       <li class="list_link"><a href="#">Male Hormone Blood Test</a></li>
                       <li class="list_link"><a href="#">Complete Hormone Blood Test</a></li>
                     </ul>
                   </div>
                   <div class="multiple_links">
                     <h6 class="sub_link"><a href="#">Metabolic Health</a></h6>
                     <ul>
                       <li class="list_link"><a href="#">Weight Loss Blood Test</a></li>
                       <li class="list_link"><a href="#">Metabolic Blood Test</a></li>
                       <li class="list_link"><a href="#">Complete Metabolic Health Blood Test</a></li>
                     </ul>
                   </div>
                   <div class="multiple_links">
                     <h6 class="sub_link"><a href="#">Heart Health</a></h6>
                     <ul>
                       <li class="list_link"><a href="#">Cholesterol Blood Test</a></li>
                       <li class="list_link"><a href="#">Cardiovascular Blood Test</a></li>
                       <li class="list_link"><a href="#">Complete Cardiovascular Blood Test</a></li>
                     </ul>
                   </div>
                   <div class="multiple_links">
                     <h6 class="sub_link"><a href="#">Nutritional Health</a></h6>
                     <ul>
                       <li class="list_link"><a href="#">Nutritional Health Blood Test</a></li>
                     </ul>
                   </div>
               </section>
               <!-- Diagnostics button end -->

               <!-- Diagnostics button end -->
               <section id="hair-health" class="content-section my_content">
                 <div class="multiple_links">
                   <h6 class="sub_link"><a href="#">Hair Loss</a></h6>
                   <ul>
                     <li class="list_link"><a href="#">Finasteride</a></li>
                     <li class="list_link"><a href="#">Regaine</a></li>
                     <li class="list_link"><a href="#">Rosemary Oil</a></li>
                   </ul>
                 </div>
               </section>
               <!-- Diagnostics button end -->

               <!-- Supplements button elements Start -->
               <section id="supplements" class="content-section my_content">
                 <div class="multiple_links">
                   <h6 class="sub_link"><a href="#">Supplements</a></h6>
                   <ul>
                     <li class="list_link"><a href="#">High Strength Testosterone Support</a></li>
                     <li class="list_link"><a href="#">Testosterone Support</a></li>
                     <li class="list_link"><a href="#">Cellular Protection</a></li>
                     <li class="list_link"><a href="#">Maca Root</a></li>
                     <li class="list_link"><a href="#">Glucomannan for Weight Loss</a></li>
                     <li class="list_link"><a href="#">Sleep Deep</a></li>
                     <li class="list_link"><a href="#">Meal Replacement Shake - Vanilla</a></li>
                     <li class="list_link"><a href="#">Meal Replacement Shake - Chocolate</a></li>
                   </ul>
                 </div>
               </section>
                <!-- Supplements button elements End -->

               <!-- Consultations button elements Start -->
               <section id="consultations" class="content-section my_content">
                 <div class="multiple_links">
                   <h6 class="sub_link"><a href="#">Doctor Consultations</a></h6>
                   <ul>
                     <li class="list_link"><a href="#">One-Off Doctor Consultations</a></li>
                   </ul>
                 </div>
               </section>
                <!-- Consultations button elements End -->
               <!-- Health and Advice button elements Start -->
               <section id="health-advice" class="content-section my_content">
                 <div class="multiple_links">
                   <h6 class="sub_link"><a href="#">Women's health</a></h6>
                   <h6 class="sub_link mt-5 mb-5"><a href="#">Men's health</a></h6>
                 </div>
               </section>
                <!-- Health and Advice button elements End -->


               <!-- All Treatments button elements Start -->

               <!-- No date showing here -->

                <!-- All Treatments button elements End -->


               <!-- Additional sections for other treatments can be added similarly End-->
           </div>
           <div class="col-md-4 recomended_content" id="recommended">
               <h6 class="recomended_title fw-bolder">Take Control of Your Health</h6>
               <div class="p-2 bg-secondary rounded recomended_box">
                   <!-- <p>Take a free online consultation</p> -->
                   <button class="get_started_btn "><a href="/get-started">Get started</a></button>
               </div>
           </div>
       </div>
   </div>
   <!--/////////////==========What we treat main section END==========//////////////-->









    <!-- ===================================================================header section End================================================================================================  -->



    <!-- =======================================================WEIGHT LOSS  section  Start============================================================================================= -->

    <section class="weight-loss-section" style="background-color: #000;" >
        <div class="container-fluid">
           <div class="mounjaro_content" style="color: #fff;" >
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="min_img" style="background-color: #94ACB3;" >
                        <img src="asset/123.webp" alt="Happy Woman" class="img-fluid rounded">
                    </div>
                </div>
                <div class="col-md-6 title ">
                  <div class="works">
                    <h6 class="text-uppercase" style=" color: #fff; font-weight: 500;" >Weight Loss</h6>
                  <h2 class="fw-bold" style="text-transform: uppercase;" >Mounjaro</h2>
                  <p class="expert" >How it works: GIP and GLP-1 receptor agonists suppress appetite, reduce cravings, and improve blood sugar levels</p>
                  <p class="expert" >Effectiveness: Drives an average of 21% body weight reduction¹</p>
                  <p class="price">From <span class="old-price">&pound;209.00</span> <span class="new-price" style="color: #288881 !important;" >&pound;125.00</span> / month</p>
                  </div>
                      <div class="min_img2 min_img3 " style="background-color: #94ACB3;" >
                          <img src="asset/123.webp" alt="Happy Woman" class="img-fluid rounded">
                          <div class="custom_border"></div>
                      </div>
                  <div class="excellent_content">
                      <div class="excellent_title">
                              <div class="box">
                                  <div class="excellent_text">
                                      <h5>Excellent</h5>
                                  </div>
                                  <div class="star">
                                      <img src="asset/stars-4.5.svg" alt="">
                                  </div>
                              </div>
                              <div class="box box2">
                                  <div class="excellent_reviews">
                                      <span> <strong>21,953</strong> reviews on</span>
                                  </div>
                                  <div class="reviews_img">
                                      <img src="asset/star.png" alt="">
                                      <h6>Trustpilot</h6>
                                  </div>
                              </div>
                      </div>
                 </div>
                </div>
              </div>
           </div>
        </div>
    </section>




    <!-- =======================================================Effective weight  section  End============================================================================================= -->


    <!-- =======================================================Effective weight  section  start============================================================================================= -->


    <section class="effective_weight">
      <div class="container">
        <div class="row">
          <div class="col-sm-12 col-md-6 col-lg-6">
            <div class="weight_title">
              <h1>Effective weight <br> loss</h1>
            </div>
          </div>
          <div class="col-sm-12 col-md-6 col-lg-6">
            <div class="effective_content">
              <p>Mounjaro enhances your body's natural insulin response and slows down gastric emptying by imitating GLP-1 and GIP hormones. This not only helps in reducing hunger but also minimises food cravings.1</p>
              <p>Implementing behavioural change strategies alongside the treatment is known to be an effective method for weight loss.1</p>
            </div>
          </div>
        </div>
      </div>
    </section>


    <!-- =======================================================WEIGHT LOSS  section  End============================================================================================= -->

    <!-- =======================================================Clinically  section  start============================================================================================= -->


    <section class="clinically">
      <div class="container">
        <div class="row">
          <div class="col-sm-12 col-md-6 col-lg-6">
            <div class="clinically_images">
              <img src="asset/clinically.webp" alt="" style="width: 100%;" >
            </div>
          </div>
          <div class="col-sm-12 col-md-6 col-lg-6">
              <div class="clinically_content">
                <div class="clinically_title">
                  <h3>Clinically proven</h3>
                </div>
                <div class="clinically_text">
                  <p>
                    Tirzepatide, the active ingredient in Mounjaro, has <br> gone through several trials that highlight its effects <br> on people who are overweight and obese.1,2
                  </p>
                </div>
                <div class="clinically_text_content">
                  <p>
                    Alongside healthy habits, the treatment has demonstrated significant efficacy and safety in comprehensive clinical trials.1,2 It’s been approved by NICE and MHRA for weight loss since late 2023.3,4
                  </p>
                </div>
                <ul>
                  <li>Keeps you full between meals</li>
                  <li>Reduce cravings for dairy, savoury, sweet, and high-fat foods</li>
                  <li>Non-addictive</li>
                  <li>Easy to administer</li>
                </ul>
              </div>
          </div>
        </div>
      </div>
    </section>


    <!-- =======================================================Clinically  section  End============================================================================================= -->


    <!-- =======================================================Meet the experts  start============================================================================================= -->


 <section class="Your_expert_team">
  <div class="expert_team">
      <div class="container">
          <div class="effective_content">
              <div class="effective_longTitle">
                  <h1>Meet the experts</h1>
              </div>
              <div class="effective_longTitl">
                  <p>At Getweightloss, our Weight Loss Programme is crafted by a team of dedicated experts in the fields of obesity management, <br> behavioural change, and metabolic health.</p><br>
                  <p>
                      With treatments like Mounjaro, which combines GLP-1 and GIP receptor agonists to suppress appetite and improve <br> blood sugar regulation, our experts have designed a holistic programme that pairs advanced medications with <br> personalised health guidance for maximum impact.
                  </p><br>
                  <p>
                      From nutritionists to behavioural psychologists, each expert brings a unique perspective to enhance your journey and <br> help you lose weight effectively and sustainably.
                  </p><br>
              </div>
              <button>Learn more about our experts <i class="fa-solid fa-arrow-right"></i> </button>
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
</section>





    <!-- =======================================================Meet the experts  End============================================================================================= -->




        <!-- ======================================================= UK licensed experts  start============================================================================================= -->



 <section class="UK_licensed">





  <div class="responsiv" >
      <div class="licensed_content">
          <div class="card_slide">
              <div class="slide_imge">
                  <img src="asset/download uuuu.svg" alt="">
              </div>
              <div class="slide_title">
                  <h5>Ongoing clinical support</h5>
              </div>
              <div class="slide_content">
                  <p>Access expert clinicians and medical advice.</p>
              </div>
          </div>
      </div>




      <div class="licensed_content">
          <div class="card_slide">
              <div class="slide_imge">
                  <img src="asset/ecol.svg" alt="">
              </div>
              <div class="slide_title">
                  <h5>Pause or cancel any time</h5>
              </div>
              <div class="slide_content">
                  <p>You're always in control of your treatment.</p>
              </div>
          </div>
      </div>



      <div class="licensed_content">
          <div class="card_slide">
              <div class="slide_imge">
                  <img src="asset/Capsule.svg" alt="">
              </div>
              <div class="slide_title">
                  <h5>Innovative treatments</h5>
              </div>
              <div class="slide_content">
                  <p>Advanced, clinically-proven medications.</p>
              </div>
          </div>
      </div>



      <div class="licensed_content">
          <div class="card_slide">
              <div class="slide_imge">
                  <img src="asset/star_1.svg" alt="">
              </div>
              <div class="slide_title">
                  <h5>Rated excellent on Trustpilot</h5>
              </div>
              <div class="slide_content">
                  <p>4.6* from over 20,000 people like you.</p>
              </div>
          </div>
      </div>



      <div class="licensed_content">
          <div class="card_slide">
              <div class="slide_imge">
                  <img src="asset/shild.svg" alt="">
              </div>
              <div class="slide_title">
                  <h5>Trusted</h5>
              </div>
              <div class="slide_content">
                  <p>Regulated by the Care Quality Commission.</p>
              </div>
          </div>
      </div>

      <div class="licensed_content">
          <div class="card_slide">
              <div class="slide_imge">
                  <img src="asset/japan.svg" alt="">
              </div>
              <div class="slide_title">
                  <h5>UK licensed</h5>
              </div>
              <div class="slide_content">
                  <p>Medications and clinicians.</p>
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
  </div>
</section>

        <!-- =======================================================  UK licensed experts  End============================================================================================= -->



        <!-- =======================================================  OTHER SOLUTIONS experts  start ============================================================================================= -->

        <section class="effective">
          <div class="container">
              <div class="effective_content">
                  <div class="effective_shortTitle">
                      <span style="text-transform: uppercase;" >Weight Loss Injections</span>
                  </div>
                   <div class="effective_longTitle">
                                                     <h1>GIP and GLP-1 Hormone Receptor Medications</h1>
                                                 </div>
                                                 <div class="effective_longTitl">
                                                     <span>Effective solutions to manage your weight loss and Type-2 diabetes.</span>
                                                 </div>
                  <div class="responsiv" style="margin-top: 25px;" >

                      <div class="box_slide">
                          <div class="wegovy">
                              <div class="wegovy_title">
                                  <h5>Weight Loss Programme + Wegovy</h5>
                              </div>
                              <span class="proven" >Clinically-proven weight loss</span>
                              <p class="price">From <span class="old-price">&pound;209.00</span> <span class="new-price" style="color: #288881 !important;" >&pound;125.00</span> / month</p>
                              <div class="imges">
                                  <img src="asset/slider.webp" alt="">
                              </div>
                              <div class="slide_butto" >

                                  <button><a href="page_wegovy.html">Learn more <i class="fa-solid fa-arrow-right"></i> </a></button>


                              </div>
                          </div>
                      </div>

                      <div class="box_slide">
                          <div class="wegovy">
                              <div class="wegovy_title">
                                  <h5>Weight Loss Programme + Mounjaro</h5>
                              </div>
                              <span class="proven" >Clinically-proven weight loss</span>
                              <p class="price">From <span class="old-price">&pound;209.00</span> <span class="new-price" style="color: #288881 !important;" >&pound;125.00</span> / month</p>
                              <div class="imges">
                                  <img src="asset/slider.webp" alt="">
                              </div>
                              <div class="slide_butto">
                                  <button><a href="page_mounjaro.html">Learn more <i class="fa-solid fa-arrow-right"></i></a> </button>
                              </div>
                          </div>
                      </div>

                      <div class="box_slide">

                      <div class="wegovy">
                          <div class="wegovy_title">
                              <h5>Glucomannan for Weight Loss</h5>
                          </div>
                          <span class="proven" >Helps keep you full</span>
                          <p class="price">From <span class="old-price">&pound;209.00</span> <span class="new-price" style="color: #288881 !important;" >&pound;12.66</span> / bottle</p>
                          <div class="imges">
                              <img src="asset/1.webp" alt="">
                          </div>
                          <div class="slide_butto">
                              <div class="slider_button">
                                  <button ><a href="shipping.html">Buy now <i class="fa-solid fa-arrow-right"></i></a></button>
                                  <button><a href="weight_loss_support_supplement.html">Learn more</a></i></button>
                              </div>
                          </div>

                      </div>
                      </div>



                      <div class="box_slide">
                      <div class="wegovy">
                          <div class="wegovy_title">
                              <h5>Weight Loss Blood Test</h5>
                          </div>
                          <span class="proven" >Discover the impact of weight loss on your <br> body and health</span>
                          <p class="price">From <span class="old-price">&pound;209.00</span> <span class="new-price" style="color: #288881 !important;" >&pound;78.00</span> / kit</p>
                          <div class="imges">
                              <img src="asset/2.webp" alt="" style="width: 178px;" >
                          </div>
                          <div class="slide_butto">
                              <div class="slider_button" >
                                  <button ><a href="shipping.html">Buy now <i class="fa-solid fa-arrow-right"></i></a></button>
                                  <button style="margin: 30px 10px 0px 0px;">Learn more </i> </button>
                              </div>
                          </div>
                      </div>

                  </div>
                  <div class="box_slide">
                      <div class="wegovy">
                          <div class="wegovy_title">
                              <h5>Weight Loss Programme + Alli</h5>
                          </div>
                          <span class="proven" >Reduces fat absorption</span>
                          <p class="price">From <span class="old-price">&pound;209.00</span> <span class="new-price" style="color: #288881 !important;" >&pound;125.00</span> / month</p>
                          <div class="imges">
                              <img src="asset/3.webp" alt="">
                          </div>
                          <div class="slide_butto">
                              <button><a href="page_alli.html">Learn more <i class="fa-solid fa-arrow-right"></i></a> </button>
                          </div>
                      </div>

                  </div>

                    </div>
              </div>
          </div>
      </section>



 <!-- =======================================================  OTHER SOLUTIONS experts  End============================================================================================= -->

 <!-- =======================================================  Personalised experts  sart ============================================================================================= -->


 <section class="research-section  py-5">
  <div class="container">
      <div class="row align-items-center">
          <div class="col-md-6">
              <div class="research">
                  <h1 class="mt-3">Personalised health coaching</h1>
                  <p class="text-muted">

                    Health coaching is the backbone of lasting change in our programme. Alongside Mounjaro, our health coaches are here to support you in building sustainable habits in <a href="">nutrition, exercise, sleep,</a> <a href="">and mindset.</a>

                  </p>


                  <p class="text-muted" >
                    While Mounjaro suppresses hunger and reduces food <br> cravings, our coaches help you optimise these effects by <br> guiding you toward a balanced lifestyle. From tips on <br> satisfying, nutritious meals that to creative ways to <br> integrate movement into your day, our coaches are <br> committed to helping you unlock the full potential of <br> your treatment.

                  </p>
                  <button class="btn btn-dark large_device ">Learn more about our experts <i class="fa-solid fa-arrow-right"></i> </button>
              </div>
          </div>
          <div class="col-md-6 research_image ">

              <div class="research_img">
                  <img src="asset/mobaiaaa.webp" class="img-fluid" alt="Research Report">
              </div>

              <button class="btn btn-dark Read_full ">Learn more about our experts <i class="fa-solid fa-arrow-right"></i> </button>

          </div>
      </div>
  </div>
</section>




 <!-- =======================================================  Personalised experts  End============================================================================================= -->

 <!-- =======================================================  Obesity  section start============================================================================================= -->





 <section class="research-section  py-5">
  <div class="container">
      <div class="row align-items-center">
          <div class="col-md-6">
              <div class="research">
                  <!-- <span class="badge bg-dark">getweightloss RESEARCH</span> -->
                  <h1 class="mt-3">Obesity: fighting the disease with effective treatment</h1>
                  <p class="text-muted">

                    At , we treat obesity not simply as a symptom but as a complex, chronic disease influenced by factors like genetics, hormones, and environment.5 Our approach recognises that effective treatment requires more than lifestyle changes alone - it demands a targeted medical response.

                  </p>
                  <p class="text-muted" >
                    At , we treat obesity not simply as a symptom but as a complex, chronic disease influenced by factors like genetics, hormones, and environment.5 Our approach recognises that effective treatment requires more than lifestyle changes alone - it demands a targeted medical response.
                  </p>
                  <button class="btn btn-dark large_device ">Read more about our approach <i class="fa-solid fa-arrow-right"></i></button>
              </div>
          </div>
          <div class="col-md-6 research_image ">

              <div class="research_img" style="width: 340px;margin-left: 15px;" >
                  <img src="asset/ex-4.jpg" class="img-fluid" alt="Research Report">
              </div>

              <button class="btn btn-dark Read_full ">Read more about our approach<i class="fa-solid fa-arrow-right"></i></button>

          </div>
      </div>
  </div>
</section>








 <!-- =======================================================  Obesity   section End============================================================================================= -->

 <!-- =======================================================  REAL PEOPLE, REAL IMPACT   section start============================================================================================= -->


 <section class="More_success" style="margin-top: 50px;" >
  <div class="containrt">
      <div class="effective_shortTitle">
          <span>REAL PEOPLE, REAL IMPACT</span>
      </div>
      <div class="effective_longTitle">
          <h1>Over 500,000 people have trusted us to improve their health</h1>
      </div>

      <button class="btn btn-dark Read_full " style="background-color: transparent;color: #000; margin: 30px 0px; " >Read more about our approach<i class="fa-solid fa-arrow-right"></i></button>

      <div class="row">
          <div class="col_sm-12 col-md-4 col-lg-4 ">
              <div class="success_card">
                  <div class="i_feel">
                      <div class="card_images">
                          <img src="asset/coma.svg" alt="">
                      </div>
                      <div class="card_content">
                          <p>I feel I can live a longer, happier life with <br> less risk of cardiovascular issues. I've <br> struggled with being overweight for many <br> years, so the impact has been life- <br> changing.</p>
                      </div>
                      <div class="card_title">
                          <b>Rebecca, 33</b>
                      </div>
                  </div>
                  <div class="card_mega_img">
                      <img src="asset/card.webp" alt="">
                  </div>
              </div>
          </div>
          <div class="col_sm-12 col-md-4 col-lg-4 ">
              <div class="success_card">
                  <div class="i_feel">
                      <div class="card_images">
                          <img src="asset/coma.svg" alt="">
                      </div>
                      <div class="card_content">
                          <p>I discovered an old bag of clothes and <br>couldn’t believe how many of them fit me <br> where I would have had no chance <br>before. I feel amazing. I think I look much <br> better than I ever did.</p>
                      </div>
                      <div class="card_title">
                          <b>Andy, 55</b>
                      </div>
                  </div>
                  <div class="card_mega_img">
                      <img src="asset/card-2.webp" alt="">
                  </div>
              </div>
          </div>
          <div class="col_sm-12 col-md-4 col-lg-4 ">
              <div class="success_card">
                  <div class="i_feel">
                      <div class="card_images">
                          <img src="asset/coma.svg" alt="">
                      </div>
                      <div class="card_content">
                          <p>I want to be in good shape to enjoy life. <br> But be happy as I’m doing it, rather than <br> miserable. And I think I’ve managed that <br> with the programme.</p>
                      </div>
                      <div class="card_title">
                          <b>Mark, 59</b>
                      </div>
                  </div>
                  <div class="card_mega_img" style="margin-top: 24px;" >
                      <img src="asset/card-3.webp" alt="">
                  </div>
              </div>
          </div>
      </div>
      <div class="excellent_content">
          <div class="excellent_title" style="color: #000;">
                  <div class="box">
                      <div class="excellent_text">
                          <h5>Excellent</h5>
                      </div>
                      <div class="star">
                          <img src="asset/stars-4.5.svg" alt="">
                      </div>
                  </div>
                  <div class="box box2">
                      <div class="excellent_reviews">
                          <span> <strong>21,953</strong> reviews on</span>
                      </div>
                      <div class="reviews_img">
                          <img src="asset/star.png" alt="">
                          <h6>Trustpilot</h6>
                      </div>
                  </div>
          </div>
     </div>
  </div>
</section>



 <!-- =======================================================  REAL PEOPLE, REAL IMPACT   section End============================================================================================= -->


 <!-- =======================================================  Your questions answered   section start  ============================================================================================= -->




 <section class="custom" style="background-color: #f9f9fA;" >

  <div class="container">

      <div class="custom_text">
          <b>FAQs</b>
          <h4>Your questions answered</h4>
      </div>





  <ul class="accordion-list">


    </ul>
  </div>
</section>






 <!-- =======================================================  Your questions answered   section End ============================================================================================= -->

 <!-- =======================================================  Knowledge   section start  ============================================================================================= -->



 <section class="KNOWLEDGe">
  <div class="containrt">
      <div class="knowledge_text">
          <div class="effective_shortTitle">
              <span style="color: #fff;" >KNOWLEDGE</span>
          </div>
          <div class="effective_longTitle">
              <h1>Weight loss: what you need to know</h1>
          </div>
      </div>
            <div class="responsive">
              <div class="success_card" style="margin-top: 25px;" >
                  <div class="i_feel">
                      <div class="card_button">
                          <a href=""><span>WEIGHT LOSS</span><span>3 minute read</span></a>
                      </div>
                      <div class="card_heading">
                          <h5>Tirzepatide and <br>  semaglutide: what’s <br> the difference?</h5>
                      </div>
                      <div class="card_content">
                          <p>Semaglutide is a tried and tested medication, prescribed for weight loss under the brand name Wegovy. A new treatment, which has also been through rigorous clinical trials, tirzepatide, has demonstrated even greater weight loss  <br>effectiveness when combined with a healthy lifestyle.</p>
                      </div>
                  </div>
                  <div class="card_mega_img" style="margin-top: 45px;" >
                      <div class="card_title">
                          <a href="" style="text-decoration: none;color: #000;" ><b>Read more <i class="fa-solid fa-arrow-right"></i></b></a>
                      </div>
                      <img src="asset/weight_loss.webp" alt="">
                  </div>
              </div>


              <div class="success_card" style="margin-top: 25px;" >
                  <div class="i_feel">
                      <div class="card_button">
                          <a href=""><span>WEIGHT LOSS</span><span>4 minute read</span></a>
                      </div>
                      <div class="card_heading">
                          <h5>Mounjaro weight loss <br> medication: how it works</h5>
                      </div>
                      <div class="card_content">
                          <p>Wegovy, Ozempic, Saxenda, Rybelsus…in recent years, a wave of new weight <br> loss medications have been approved to treat obesity in the UK. Mounjaro is the most recent of those, having been approved by the MHRA in November 2023.</p>
                      </div>
                  </div>
                  <div class="card_mega_img" style="margin-top: 75px;" >
                      <div class="card_title">
                          <a href="" style="text-decoration: none;color: #000;" ><b>Read more <i class="fa-solid fa-arrow-right"></i></b></a>
                      </div>
                      <img src="asset/Mounjaro.webp" alt="">
                  </div>
              </div>



              <div class="success_card" style="margin-top: 25px;" >
                  <div class="i_feel">
                      <div class="card_button">
                          <a href=""><span>WEIGHT LOSS</span><span>3 minute read</span></a>
                      </div>
                      <div class="card_heading">
                          <h5>What are the most <br> common side <br> effects of Mounjaro?</h5>
                      </div>
                      <div class="card_content">
                          <p>As with any medication, there are a few side effects to be aware of. Let’s run <br> through some of the most common ones, as well as some tips on what to do if you’re experiencing any of them.</p>
                      </div>

                  </div>
                  <div class="card_mega_img" style="margin-top: 75px;" >
                      <div class="card_title">
                          <a href="" style="text-decoration: none;color: #000;" ><b>Read more <i class="fa-solid fa-arrow-right"></i></b></a>
                      </div>
                      <img src="asset/What.webp" alt="">
                  </div>
              </div>
          </div>
     </div>
</section>

 <!-- =======================================================  Knowledge   section End ============================================================================================= -->

    <!--============================================================ References  section start ============================================== -->



    <!-- <section class="references">
      <div class="references_content">
        <div class="container">
          <div class="row">
            <div class="col-sm-12 col-md-6 col-lg-12 ">
              <div class="references_title">
                <h1>References</h1>
              </div>
              <p>
                1 Based on tirzepatide 15mg over 72 weeks. Jastreboff, A. M., Aronne, L. J., Ahmad, N. N., Wharton, S., Connery, L., Alves, B., Kiyosue, A., Zhang, S., Liu, B., <br> Bunck, M. C., Stefanski, A., & the SURMOUNT-1 Investigators. Tirzepatide once weekly for the treatment of obesity. The New England Journal of Medicine (2022)
              </p>
              <p>
                2 Aronne, L. J., Sattar, N., Horn, D. B., Bays, H. E., Wharton, S., Lin, W. Y., Ahmad, N. N., Zhang, S., Liao, R., Bunck, M. C., Jouravskaya, I., Murphy, M. A., & <br> SURMOUNT-4 Investigators (2024). Continued Treatment With Tirzepatide for Maintenance of Weight Reduction in Adults With Obesity: The SURMOUNT-4 <br> Randomized Clinical Trial. JAMA, 331(1), 38–48. <a href="">https://doi.org/10.1001/jama.2023.24945</a>
              </p>
              <p>
                3 National Institute for Health and Care Excellence (NICE). (2024). Tirzepatide for managing overweight and obesity [ID6179]. Retrieved December 6, 2024, from <br> <a href="">https://www.nice.org.uk/guidance/ta6179</a>
              </p>
              <p>
                4 Medicines and Healthcare products Regulatory Agency (MHRA). (2023, November 8). MHRA authorises diabetes drug Mounjaro (tirzepatide) for weight <br> management and weight loss. Retrieved December 6, 2024, from <a href="">https://www.gov.uk/government/news/mhra-authorises-diabetes-drug-mounjaro-tirzepatide-for-weight-management-and-weight-loss</a>
              </p>
              <p>
                5 Lee A, Cardel M, Donahoo WT. Social and Environmental Factors Influencing Obesity. [Updated 2019 Oct 12]. In: Feingold KR, Anawalt B, Blackman MR, et al., editors. Endotext [Internet]. South Dartmouth (MA): MDText.com, Inc.; 2000-. Available from: https://www.ncbi.nlm.nih.gov/books/NBK278977/
              </p>
            </div>
          </div>
        </div>
      </div>
  </section> -->




  <!--============================================================ References  section end ============================================== -->






 <!--======================================================== footer section start================================================================= -->



  <!--====================== Footer Section Start =============================-->
  <section class="footer_section container-fluid">
    <!-- footer reviews section -->
    <div class="excellent_content footer_reviews">
      <div class="excellent_title">
        <div class="box">
          <div class="excellent_text checkout">
              <h5>Check out our 22,081 reviews</h5>
          </div>
          <div class="star">
              <img src="asset/stars-4.5.svg" alt="">
          </div>
        </div>
        <div class="box box2">
          <div class="excellent_reviews">
            <span> <strong>21,953</strong> reviews on</span>
          </div>
          <div class="reviews_img">
            <img src="asset/star.png" alt="">
              <h6>Trustpilot</h6>
          </div>
        </div>
      </div>

  </div>

  <div class="hover_item">
    <p>Helping each other make better choice.<span>read and write reviews</span></p>
  </div>
  <div class="as_seen">
    <h5>AS SEEN IN</h5>
  </div>


    <div class="container">
      <div class="main_footer">
        <div class="row">
          <div class="col-md-3">
              <div class="footer_list">
                <h5 class="footer_title">Treatments</h5>
                <ul>
                  <li><a href="#">Erectile Dysfunction</a></li>
                  <li><a href="#">Weight Loss</a></li>
                  <li><a href="#">Low Testosterone</a></li>
                  <li><a href="#">Diagnostics</a></li>
                  <li><a href="#">Hair Loss</a></li>
                  <li><a href="#">Supplements</a></li>
                  <li><a href="#">Premature Ejaculation</a></li>
                  <li><a href="#">Beard Growth</a></li>
                  <li><a href="#">Consultations</a></li>
                  <li><a href="#">See all</a></li>
                </ul>
              </div>
          </div>
          <div class="col-md-3">
              <div class="footer_list">
                <h5 class="footer_title">Contact</h5>
                <ul>
                  <li><a href="#">Help Centre</a></li>
                  <li><a href="#">Customer Care</a></li>
                  <li><a href="#">Clinical Team</a></li>
                  <li><a href="#">Press Enquiries</a></li>
                </ul>
              </div>
          </div>
          <div class="col-md-3">
              <div class="footer_list">
                <h5 class="footer_title">Weight loss</h5>
                <ul>
                  <li><a href="#">Log in</a></li>
                  <li><a href="#">Careers</a> <span class="hiring">We're Hiring</span></li>
                  <li><a href="#">Sitemap</a></li>
                </ul>
              </div>
            </div>
          <div class="col-md-3">
              <div class="footer_list">
                <h5 class="footer_title">Follow</h5>
                <ul>
                  <li><a href="">Blog</a></li>
                  <li><a href="https://www.facebook.com/">Facebook</a></li>
                  <li><a href="https://x.com/">Twitter</a></li>
                  <li><a href="https://www.instagram.com/">Instagram</a></li>
                </ul>
              </div>
          </div>

          <!--================= Logo section Start=====================-->
          <div class="logo_section">
            <div class="row">
              <div class="col-md-6">
                <div class="getweightloss_logo">
                  <img src="/asset/logo-final.png" alt=" Logo" style="width: 250px; padding-top: -20px!important;">
                </div>
              </div>
              <div class="col-md-6">
                <div class="right_side">
                  <div class="icon_links">
                    <img src="./asset/Register logo.png" alt="register logo">
                    <img src="./asset/CER logo.png" alt="CER logo">
                    <img src="./asset/Care quality logo.png" alt="CareQuality Logo">
                  </div>
                </div>
                <!--=============== page lonking start =================-->
                  <ul>
                    <li><a href="./terms.html">Terms & conditions</a></li>
                    <li><a href="./terms.html">Terms of sale</a></li>
                    <li><a href="./terms.html">Privacy notice</a></li>
                    <li><a href="./terms.html">Cookies policy</a></li>
                    <li><a href="./terms.html">Make a complaint</a></li>
                  </ul>
                <!--=============== page lonking End =================-->
              </div>
            </div>
          </div>
          <!--================= Logo section End=====================-->
        </div>
        <!--=============== copyright Section Start ===============-->
        <div class="copyright">
          <p>Copyright © Vir Health Limited. All rights reserved. weight loss is a trading name of Vir Health Limited. Registered office Floor 4, Farringdon Point, 33 Farringdon Road, London, England, EC1M 3JF. Registered in England and Wales, company number 11449267. Registered VAT number 310206374.</p>
        </div>

        <!--=============== copyright Section End =================-->
      </div>
    </div>
  </section>
  <!--====================== Footer Section End =============================-->





 <!--======================================================== footer section end================================================================= -->=





    <!-- ==================================================================coding End======================================================================================================== -->





    <script type="text/javascript" src="js/jquery-3.7.1.min.js"></script>
    <script type="text/javascript" src="js/popper.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/slick.min.js"></script>
    <script type="text/javascript" src="js/lazyload.min.js"></script>
    <script type="text/javascript" src="js/theme.js"></script>
    <script type="text/javascript" src="js/my-2.js"></script>
    <script src="js/my.js"></script>





</body>
</html>
