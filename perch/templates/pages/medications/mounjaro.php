
<?php  // output the top of the page
    perch_layout('global/head');
   perch_layout('global/header', [
        'page_title' => perch_page_title(true),
    ]);

        /* main navigation
        perch_pages_navigation([
            'levels'   => 1,
            'template' => 'main_nav.html',
        ]);*/

    ?>


    <!-- Hero Section -->
    <div id="hero" class="w-full flex flex-col items-center justify-center bg-white">
      <div class="bg-white flex flex-col lg:flex-row items-center justify-between w-full">
        <div class="h-[400px] md:h-[550px] lg:h-[700px] w-full lg:w-[704px] order-2 lg:order-1">
          <img src="/new/images/mounjaro/mounjaro-hero-img.png" alt="Mounjaro Injection" class="w-full h-full object-cover" />
        </div>
        <div class="flex flex-col gap-[24px] lg:gap-[32px] items-start px-[20px] md:px-[40px] lg:pl-[15px] lg:pr-[160px] py-[40px] lg:py-0 w-full lg:w-[719px] order-1 lg:order-2">
          <div class="flex flex-col gap-[16px] lg:gap-[20px] items-start w-full">
            <h1 class="font-semibold text-[#0d0d0d] text-[36px] md:text-[56px] lg:text-[72px] tracking-[-0.72px] md:tracking-[-1.12px] lg:tracking-[-1.44px] leading-[44px] md:leading-[68px] lg:leading-[90px]">Mounjaro: Transforming Weight Loss Success</h1>
          </div>
          <div class="flex flex-col gap-[16px] lg:gap-[20px] items-start w-full">
            <p class="font-semibold text-[#0d0d0d] text-[24px] md:text-[28px] lg:text-[32px] tracking-[-0.48px] md:tracking-[-0.56px] lg:tracking-[-0.64px] leading-[32px] md:leading-[36px] lg:leading-[40px]">
              <span class="font-medium">From </span>
              <span class="text-[#324ea0] text-[48px] md:text-[56px] lg:text-[64px] leading-[60px] md:leading-[70px] lg:leading-[90px]">£129.00</span>
              <span class="font-medium"> / month</span>
            </p>
          </div>
          <div class="flex flex-col sm:flex-row gap-[15px] items-start w-full sm:w-auto">
            <a href="#pricing" class="bg-[#3328bf] border border-[#3328bf] rounded-[8px] btn-glow w-full sm:w-auto">
              <div class="flex gap-[6px] items-center justify-center overflow-clip px-[16px] py-[10px] rounded-[inherit]">
                <p class="font-semibold leading-[24px] text-[#fcfcfc] text-[16px] whitespace-nowrap">Get Started</p>
              </div>
            </a>
            <a href="#reorder" class="bg-[#afd136] border border-[#afd136] rounded-[8px] btn-glow w-full sm:w-auto">
              <div class="flex gap-[6px] items-center justify-center overflow-clip px-[16px] py-[10px] rounded-[inherit]">
                <p class="font-semibold leading-[24px] text-[#0d0d0d] text-[16px] whitespace-nowrap">Reorder</p>
              </div>
            </a>
          </div>
        </div>
      </div>
      <div class="flex flex-col md:flex-row gap-[16px] md:gap-[23px] items-start md:items-center justify-between px-[20px] md:px-[40px] lg:px-0 pb-[40px] lg:pb-[50px] pt-[30px] lg:pt-[50px] w-full max-w-[1120px]">
        <div class="flex gap-[16px] md:gap-[20px] items-center w-full md:w-auto">
          <img src="/new/images/common/gwl-bullet.svg" alt="bullet" class="w-[32px] h-[32px] md:w-[36px] md:h-[36px] flex-shrink-0" />
          <p class="font-normal leading-[24px] text-[16px] text-[grey]">Dual Action for Weight Loss</p>
        </div>
        <div class="flex gap-[16px] md:gap-[20px] items-center w-full md:w-auto">
          <img src="/new/images/common/gwl-bullet.svg" alt="bullet" class="w-[32px] h-[32px] md:w-[36px] md:h-[36px] flex-shrink-0" />
          <p class="font-normal leading-[24px] text-[16px] text-[grey]">Significant Results</p>
        </div>
        <div class="flex gap-[16px] md:gap-[20px] items-center w-full md:w-auto">
          <img src="/new/images/common/gwl-bullet.svg" alt="bullet" class="w-[32px] h-[32px] md:w-[36px] md:h-[36px] flex-shrink-0" />
          <p class="font-normal leading-[24px] text-[16px] text-[grey]">Beyond Diet & Exercise</p>
        </div>
      </div>
    </div>

    <!-- About Mounjaro Section -->
    <div id="about" class="bg-gradient-to-b from-[#ffffff] to-[#d4dfff] w-full flex flex-col items-center justify-center">
      <!-- What does it Do? -->
      <div class="w-full flex flex-col items-center justify-center">
        <div class="flex flex-col gap-[24px] lg:gap-[32px] items-center justify-center px-[20px] md:px-[40px] lg:px-[60px] py-[60px] md:py-[80px] lg:py-[100px] w-full lg:max-w-[1120px]">
          <div class="flex flex-col lg:flex-row gap-[40px] lg:gap-[60px] items-start w-full">
            <h2 class="font-medium text-[#0d0d0d] text-[32px] md:text-[40px] lg:text-[48px] tracking-[-0.64px] md:tracking-[-0.8px] lg:tracking-[-0.96px] leading-[40px] md:leading-[50px] lg:leading-[60px] w-full lg:w-auto whitespace-nowrap">What does it Do?</h2>
            <div class="flex flex-col gap-[16px] w-full">
              <p class="font-normal leading-[24px] text-[16px] text-[grey]">Mounjaro (Tirzepatide) is a groundbreaking medication that aids in weight loss by targeting two key hormone receptors, GIP and GLP-1, to regulate appetite and blood sugar.</p>
              <p class="font-normal leading-[24px] text-[16px] text-[grey]">Clinical studies show it can lead to up to 20% body weight reduction, surpassing other treatments. It's especially beneficial for individuals with obesity or type 2 diabetes who struggle with traditional methods like diet and exercise, offering a scientifically backed solution for sustainable weight management.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- What am I injecting? -->
      <div class="w-full flex flex-col items-center justify-center">
        <div class="flex flex-col lg:flex-row gap-[32px] items-center justify-center px-[20px] md:px-[40px] lg:px-[60px] py-[60px] md:py-[80px] lg:py-[100px] w-full lg:max-w-[1120px]">
          <div class="flex flex-col gap-[20px] lg:gap-[30px] items-start w-full lg:w-[544px]">
            <div class="flex flex-col gap-[16px] lg:gap-[20px] items-start w-full">
              <h2 class="font-medium text-[#0d0d0d] text-[32px] md:text-[40px] lg:text-[48px] tracking-[-0.64px] md:tracking-[-0.8px] lg:tracking-[-0.96px] leading-[40px] md:leading-[50px] lg:leading-[60px]">What am I injecting?</h2>
              <div class="flex flex-col gap-[16px] w-full">
                <p class="font-normal leading-[24px] text-[16px] text-[grey]">When using Mounjaro for weight loss, you are injecting a medication called Tirzepatide. Here's what it involves:</p>
                <p class="leading-[24px] text-[16px] text-[grey]"><span class="font-bold">Consult Your Doctor:</span> If side effects persist or worsen, seek medical advice.</p>
                <p class="leading-[24px] text-[16px] text-[grey]"><span class="font-bold">Active Ingredient:</span> Mounjaro contains Tirzepatide, a medication that mimics natural gut hormones to regulate appetite and blood sugar.</p>
                <p class="leading-[24px] text-[16px] text-[grey]"><span class="font-bold">Multi-Targeted Therapy:</span> It activates GIP and GLP-1 receptors, enhancing insulin production and reducing hunger.</p>
                <p class="leading-[24px] text-[16px] text-[grey]"><span class="font-bold">Once-Weekly Injection:</span> Delivered via a pre-filled, single-use pen, it's a simple and effective treatment for weight loss and diabetes management.</p>
              </div>
            </div>
          </div>
          <div class="h-[300px] md:h-[400px] lg:h-[450px] w-full lg:w-[544px] rounded-[20px] overflow-hidden">
            <img src="/new/images/mounjaro/mounjaro-img-1.png" alt="Mounjaro Injection" class="w-full h-full object-cover" />
          </div>
        </div>
      </div>

      <!-- What are the potential side effects? -->
      <div class="w-full flex flex-col items-center justify-center">
        <div class="flex flex-col lg:flex-row gap-[32px] items-center justify-center px-[20px] md:px-[40px] lg:px-[60px] py-[60px] md:py-[80px] lg:py-[100px] w-full lg:max-w-[1120px]">
          <div class="h-[300px] md:h-[400px] lg:h-[450px] w-full lg:w-[544px] rounded-[20px] overflow-hidden order-2 lg:order-1">
            <img src="/new/images/mounjaro/mounjaro-img-2.png" alt="Side Effects" class="w-full h-full object-cover" />
          </div>
          <div class="flex flex-col gap-[20px] lg:gap-[30px] items-start w-full lg:w-[544px] order-1 lg:order-2">
            <div class="flex flex-col gap-[16px] lg:gap-[20px] items-start w-full">
              <h2 class="font-medium text-[#0d0d0d] text-[32px] md:text-[40px] lg:text-[48px] tracking-[-0.64px] md:tracking-[-0.8px] lg:tracking-[-0.96px] leading-[40px] md:leading-[50px] lg:leading-[60px]">What are the potential side effects?</h2>
              <div class="flex flex-col gap-[16px] w-full">
                <p class="font-normal leading-[24px] text-[16px] text-[grey]">Mounjaro may cause some side effects, though they aren't experienced by everyone. Potential side effects include:</p>
                <p class="leading-[24px] text-[16px] text-[grey]"><span class="font-bold">Common Side Effects:</span> Nausea, vomiting, diarrhea, constipation, and decreased appetite.</p>
                <p class="leading-[24px] text-[16px] text-[grey]"><span class="font-bold">Gastrointestinal Discomfort:</span> Some may experience bloating, indigestion, or stomach pain.</p>
                <p class="leading-[24px] text-[16px] text-[grey]"><span class="font-bold">Serious but Rare:</span> Potential risks include pancreatitis, gallbladder issues, or severe allergic reactions.</p>
                <p class="leading-[24px] text-[16px] text-[grey]"><span class="font-bold">Consult Your Doctor:</span> If side effects persist or worsen, seek medical advice.</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Features Section -->
      <div id="features" class="w-full flex flex-col items-center justify-center px-[20px] md:px-[40px] lg:px-[60px] pb-[60px] md:pb-[80px] lg:pb-[100px]">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-[20px] w-full">
          <div class="bg-white/40 border border-white/50 flex flex-col gap-[12px] items-start justify-start p-[24px] rounded-[20px]">
            <img src="/new/images/common/gwl-bullet.svg" alt="bullet" class="w-[40px] h-[40px]" />
            <p class="font-semibold leading-[30px] text-[#0d0d0d] text-[20px]">Ongoing Support</p>
            <p class="font-normal leading-[30px] text-[#0d0d0d] text-[20px]">Always available via email/chat.</p>
          </div>
          <div class="bg-white/40 border border-white/50 flex flex-col gap-[12px] items-start justify-start p-[24px] rounded-[20px]">
            <img src="/new/images/common/gwl-bullet.svg" alt="bullet" class="w-[40px] h-[40px]" />
            <p class="font-semibold leading-[30px] text-[#0d0d0d] text-[20px]">You are in Control</p>
            <p class="font-normal leading-[30px] text-[#0d0d0d] text-[20px]">Each month you decide to continue or stop.</p>
          </div>
          <div class="bg-white/40 border border-white/50 flex flex-col gap-[12px] items-start justify-start p-[24px] rounded-[20px]">
            <img src="/new/images/common/gwl-bullet.svg" alt="bullet" class="w-[40px] h-[40px]" />
            <p class="font-semibold leading-[30px] text-[#0d0d0d] text-[20px]">Additional Testing</p>
            <p class="font-normal leading-[30px] text-[#0d0d0d] text-[20px]">We can arrange blood tests, through our partners</p>
          </div>
          <div class="bg-white/40 border border-white/50 flex flex-col gap-[12px] items-start justify-start p-[24px] rounded-[20px]">
            <img src="/new/images/common/gwl-bullet.svg" alt="bullet" class="w-[40px] h-[40px]" />
            <p class="font-semibold leading-[30px] text-[#0d0d0d] text-[20px]">Health Hub</p>
            <p class="font-normal leading-[30px] text-[#0d0d0d] text-[20px]">The health hub, an access point for news and tips.</p>
          </div>
          <div class="bg-white/40 border border-white/50 flex flex-col gap-[12px] items-start justify-start p-[24px] rounded-[20px]">
            <img src="/new/images/common/gwl-bullet.svg" alt="bullet" class="w-[40px] h-[40px]" />
            <p class="font-semibold leading-[30px] text-[#0d0d0d] text-[20px]">Discreet Delivery</p>
            <p class="font-normal leading-[30px] text-[#0d0d0d] text-[20px]">No names, no logos.</p>
          </div>
          <div class="bg-white/40 border border-white/50 flex flex-col gap-[12px] items-start justify-start p-[24px] rounded-[20px]">
            <img src="/new/images/common/gwl-bullet.svg" alt="bullet" class="w-[40px] h-[40px]" />
            <p class="font-semibold leading-[30px] text-[#0d0d0d] text-[20px]">Competitive Pricing</p>
            <p class="font-normal leading-[30px] text-[#0d0d0d] text-[20px]">We constantly monitor prices.</p>
          </div>
        </div>
      </div>
    </div>



      <!-- Pricing Table -->
      <div id="pricing" class="bg-white w-full flex flex-col items-center justify-center">
        <div class="bg-white flex flex-col gap-[40px] lg:gap-[50px] items-center justify-center px-[20px] md:px-[40px] lg:px-[60px] py-[60px] md:py-[80px] lg:py-[100px] w-full">
          <div class="flex flex-col gap-[16px] lg:gap-[20px] items-center w-full lg:max-w-[1120px]">
            <div class="bg-[#afd136] flex gap-[10px] items-center justify-center overflow-clip px-[8px] py-[6px] rounded-[6px]">
              <p class="font-semibold leading-[24px] md:leading-[30px] text-[18px] md:text-[20px] text-white whitespace-nowrap">Get Weight Loss Online Services</p>
            </div>
            <p class="font-medium text-[#0d0d0d] text-[28px] md:text-[36px] lg:text-[48px] text-center tracking-[-0.56px] md:tracking-[-0.72px] lg:tracking-[-0.96px] leading-[36px] md:leading-[48px] lg:leading-[60px] px-[10px]">GIP and GLP-1 Hormone Receptor Medications</p>
            <p class="text-[16px] text-[grey] text-center w-full max-w-[742px] leading-[24px] px-[10px]">We offer various services ranging from online consultations for weight, nutrition and blood tests*.

*Blood tests are provided by our UKAS accredited service partner.</p>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-[20px] lg:gap-[32px] items-stretch w-full lg:max-w-[1120px]">
               <?php
                                                         perch_collection('MedicationProgrammes', [

                                                             'count'      => 6,
                                                         ]);
                                                     ?>




          </div>
        </div>
      </div>
    </div>

    <!-- Testimonials Section -->
    <div id="testimonials" class="bg-white w-full flex flex-col items-center justify-center">
      <!-- Testimonials Header -->
      <div class="bg-white w-full flex flex-col items-center justify-center px-[20px] md:px-[40px] lg:px-[60px] pt-[60px] md:pt-[80px] lg:pt-[100px] pb-[40px] lg:pb-[50px]">
        <div class="flex flex-col gap-[16px] lg:gap-[20px] items-start justify-center w-full lg:max-w-[1120px]">
          <div class="bg-[#afd136] flex gap-[10px] items-center justify-center overflow-clip px-[8px] py-[6px] rounded-[6px]">
            <p class="font-semibold leading-[24px] md:leading-[30px] text-[18px] md:text-[20px] text-white whitespace-nowrap">More success stories</p>
          </div>
          <p class="font-medium text-[#0d0d0d] text-[32px] md:text-[40px] lg:text-[48px] tracking-[-0.64px] md:tracking-[-0.8px] lg:tracking-[-0.96px] leading-[40px] md:leading-[50px] lg:leading-[60px]">People who already love us</p>
          <p class="text-[16px] text-[grey] w-full max-w-[742px] leading-[24px]">With each client having different triggers and objectives for starting their weight loss journey, we share a few of the success stories here;</p>
        </div>
      </div>

      <!-- Testimonials Cards -->
      <div class="bg-white w-full flex flex-col items-center justify-center pb-[60px] md:pb-[80px] lg:pb-[100px]">
        <!-- Mobile/Tablet Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-[20px] md:gap-[24px] w-full lg:hidden px-[20px] md:px-[40px]">
           <?php
                          perch_collection('SuccessStories', [

                              'count'      => 3,
                          ]);
                      ?>



        </div>

        <!-- Desktop Carousel -->
        <div class="relative w-full overflow-hidden hidden lg:block">
          <div id="testimonialCarousel" class="testimonial-carousel flex gap-[32px]">
             <?php
                            perch_collection('SuccessStories', [

                                'count'      => 4,
                            ]);
                        ?>




          </div>
        </div>
      </div>
    </div>

    <!-- Features Section -->
    <div id="features" class="w-full flex flex-col items-center justify-center px-[20px] md:px-[40px] lg:px-[60px] pb-[60px] md:pb-[80px] lg:pb-[100px]">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-[20px] w-full">
        <div class="border border-[#d6d6d6] flex flex-col gap-[12px] items-start justify-start p-[24px] rounded-[20px]">
          <img src="/new/images/common/gwl-bullet.svg" alt="bullet" class="w-[40px] h-[40px]" />
          <p class="font-semibold leading-[26px] md:leading-[30px] text-[#0d0d0d] text-[18px] md:text-[20px]">Ongoing Support</p>
          <p class="leading-[26px] md:leading-[30px] text-[#0d0d0d] text-[16px] md:text-[20px]">Always available via email/chat.</p>
        </div>
        <div class="border border-[#d6d6d6] flex flex-col gap-[12px] items-start justify-start p-[24px] rounded-[20px]">
          <img src="/new/images/common/gwl-bullet.svg" alt="bullet" class="w-[40px] h-[40px]" />
          <p class="font-semibold leading-[26px] md:leading-[30px] text-[#0d0d0d] text-[18px] md:text-[20px]">You are in Control</p>
          <p class="leading-[26px] md:leading-[30px] text-[#0d0d0d] text-[16px] md:text-[20px]">Each month you decide to continue or stop.</p>
        </div>
        <div class="border border-[#d6d6d6] flex flex-col gap-[12px] items-start justify-start p-[24px] rounded-[20px]">
          <img src="/new/images/common/gwl-bullet.svg" alt="bullet" class="w-[40px] h-[40px]" />
          <p class="font-semibold leading-[26px] md:leading-[30px] text-[#0d0d0d] text-[18px] md:text-[20px]">Additional Testing</p>
          <p class="leading-[26px] md:leading-[30px] text-[#0d0d0d] text-[16px] md:text-[20px]">We can arrange blood tests, through our partners</p>
        </div>
        <div class="border border-[#d6d6d6] flex flex-col gap-[12px] items-start justify-start p-[24px] rounded-[20px]">
          <img src="/new/images/common/gwl-bullet.svg" alt="bullet" class="w-[40px] h-[40px]" />
          <p class="font-semibold leading-[26px] md:leading-[30px] text-[#0d0d0d] text-[18px] md:text-[20px]">Health Hub</p>
          <p class="leading-[26px] md:leading-[30px] text-[#0d0d0d] text-[16px] md:text-[20px]">The health hub, an access point for news and tips.</p>
        </div>
        <div class="border border-[#d6d6d6] flex flex-col gap-[12px] items-start justify-start p-[24px] rounded-[20px]">
          <img src="/new/images/common/gwl-bullet.svg" alt="bullet" class="w-[40px] h-[40px]" />
          <p class="font-semibold leading-[26px] md:leading-[30px] text-[#0d0d0d] text-[18px] md:text-[20px]">Discreet Delivery</p>
          <p class="leading-[26px] md:leading-[30px] text-[#0d0d0d] text-[16px] md:text-[20px]">No names, no logos.</p>
        </div>
        <div class="border border-[#d6d6d6] flex flex-col gap-[12px] items-start justify-start p-[24px] rounded-[20px]">
          <img src="/new/images/common/gwl-bullet.svg" alt="bullet" class="w-[40px] h-[40px]" />
          <p class="font-semibold leading-[26px] md:leading-[30px] text-[#0d0d0d] text-[18px] md:text-[20px]">Competitive Pricing</p>
          <p class="leading-[26px] md:leading-[30px] text-[#0d0d0d] text-[16px] md:text-[20px]">We constantly monitor prices.</p>
        </div>
      </div>
    </div>

    <!-- FAQ Section -->
    <div id="faq" class="bg-white w-full flex flex-col items-center justify-center px-[20px] md:px-[40px] lg:px-[60px] py-[60px] md:py-[80px] lg:py-[100px]">
      <div class="bg-white flex flex-col lg:flex-row gap-[40px] lg:gap-[32px] items-start justify-center w-full lg:max-w-[1120px]">
        <div class="flex flex-col gap-[24px] lg:gap-0 lg:h-[588px] items-start justify-between w-full lg:w-[418px]">
          <div class="flex flex-col gap-[10px] items-start justify-center">
            <div class="bg-[#afd136] flex gap-[10px] items-center justify-center overflow-clip px-[8px] py-[6px] rounded-[6px]">
              <p class="font-semibold leading-[24px] md:leading-[30px] text-[18px] md:text-[20px] text-white whitespace-nowrap">FAQs</p>
            </div>
            <p class="font-semibold leading-[40px] md:leading-[50px] lg:leading-[60px] text-[#0d0d0d] text-[32px] md:text-[40px] lg:text-[48px] tracking-[-0.64px] md:tracking-[-0.8px] lg:tracking-[-0.96px] w-full lg:w-[368px]">Your questions answered</p>

            <p class="font-medium leading-[26px] md:leading-[30px] text-[18px] md:text-[20px] text-[grey]">Couldn't not find what you were looking for? Write to us at help@getweightloss.co.uk.</p>
            <div class="flex flex-wrap gap-[6px] items-start">
            </div>
          </div>
        </div>

        <div class="bg-white border border-[#dbdbdb] flex flex-col items-start pb-[20px] pt-[30px] md:pt-[40px] px-0 rounded-[10px] w-full lg:w-[670px]">
           <?php
                        perch_collection('FAQS', [


                            'count'      => 7,
                        ]);
                    ?>


        </div>
      </div>
    </div>

    <!-- Blog Section -->
    <div id="blog" class="w-full flex flex-col items-center justify-center">
      <div class="flex flex-col gap-[40px] lg:gap-[50px] items-center overflow-clip px-[20px] md:px-[40px] lg:px-[60px] py-[60px] md:py-[80px] lg:py-[100px] w-full">
        <div class="flex flex-col gap-[10px] items-start w-full lg:max-w-[1120px]">
          <div class="bg-[#afd136] flex gap-[10px] items-center justify-center overflow-clip px-[8px] py-[6px] rounded-[6px]">
            <p class="font-semibold leading-[24px] md:leading-[30px] text-[18px] md:text-[20px] text-white whitespace-nowrap">Health Hub & News</p>
          </div>
          <p class="font-medium leading-[40px] md:leading-[50px] lg:leading-[60px] text-[#0d0d0d] text-[32px] md:text-[40px] lg:text-[48px] tracking-[-0.64px] md:tracking-[-0.8px] lg:tracking-[-0.96px]">Weight loss: what you need to know</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-[24px] md:gap-[28px] lg:gap-[32px] w-full lg:max-w-[1120px]">
          <?php
            perch_blog_custom(array(
              'filter'     => 'postDateTime',
              'template'   => 'weight_post_in_list.html',
              'sort'       => 'postDateTime',
              'sort-order' => 'DESC',
              'count'      => '3'
            ));
          ?>
        </div>
      </div>
    </div>

    <!-- CTA Section -->
    <div class="w-full flex flex-col items-center justify-center">
      <div class="bg-white flex flex-col items-center justify-center px-[20px] md:px-[40px] lg:px-[60px] py-0 w-full">
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-[32px] lg:gap-0 px-0 pt-[30px] md:pt-[40px] lg:pt-[50px] pb-[60px] md:pb-[80px] lg:pb-[100px] w-full lg:max-w-[1120px]">
          <p class="font-medium text-[#0d0d0d] text-[32px] md:text-[40px] lg:text-[48px] tracking-[-0.64px] md:tracking-[-0.8px] lg:tracking-[-0.96px] leading-[40px] md:leading-[50px] lg:leading-[60px] w-full lg:w-[544px]">Let's Find Your Perfect Plan Together</p>
          <div class="flex flex-col sm:flex-row gap-[10px] items-stretch sm:items-start w-full sm:w-auto">
            <a href="/get-started" class="bg-[#3328bf] border border-[#3328bf] rounded-[8px] btn-glow">
              <div class="flex gap-[6px] items-center justify-center overflow-clip px-[16px] py-[10px] rounded-[inherit]">
                <p class="font-semibold leading-[24px] text-[#fcfcfc] text-[16px] whitespace-nowrap">Get Started</p>
              </div>
            </a>
            <a href="#about" class="bg-[#fcfcfc] border border-[#d6d6d6] rounded-[8px] btn-glow">
              <div class="flex gap-[6px] items-center justify-center overflow-clip px-[16px] py-[10px] rounded-[inherit]">
                <p class="font-semibold leading-[24px] text-[#0d0d0d] text-[16px] whitespace-nowrap">Learn More</p>
                <svg class="w-[20px] h-[20px]" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M7.5 5L12.5 10L7.5 15" stroke="#0d0d0d" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>
    <?php //perch_content('Intro');
  perch_layout('global/footer');?>
