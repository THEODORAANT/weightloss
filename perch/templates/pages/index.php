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
    <div id="hero" class="w-full flex flex-col items-center justify-center">
      <div class="bg-white flex flex-col-reverse lg:flex-row gap-[30px] lg:gap-0 items-center lg:items-start justify-start relative w-full py-[50px] lg:py-0">
        <div class="flex flex-col items-start px-[20px] md:px-[40px] lg:pl-[60px] xl:pl-[120px] lg:pr-[30px] xl:pr-[50px] lg:py-0 w-full lg:w-1/2 order-2 lg:order-1 lg:h-[750px] lg:justify-center xl:justify-between">
          <div class="flex flex-col gap-[24px] lg:gap-[32px] items-start w-full xl:mt-[100px]">
            <div class="flex flex-col gap-[16px] lg:gap-[20px] items-start w-full">
              <div class="flex flex-col justify-center w-full">
                <p class="font-semibold text-[#0d0d0d] text-[36px] md:text-[52px] lg:text-[72px] tracking-[-0.72px] md:tracking-[-1.04px] lg:tracking-[-1.44px] leading-[44px] md:leading-[60px] lg:leading-[90px]">Get expert weight-loss support today.</p>
              </div>
              <div class="flex flex-col justify-center w-full lg:w-[457px]">
                <p class="leading-[24px] text-[16px] text-[grey]">Start your journey with a professional consultation. Our UK clinicians will assess if prescription treatments are right for you. 
                  </p>
              </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-[10px] sm:h-[64px] items-stretch sm:items-center p-[8px] rounded-[8px] w-full sm:w-auto">
              <a href="#pricing" class="bg-[#3328bf] border border-[#3328bf] rounded-[8px] btn-glow w-full sm:w-auto">
                <div class="flex gap-[10px] items-center justify-center overflow-clip px-[32px] lg:px-[48px] py-[16px] rounded-[inherit]">
                  <p class="font-semibold leading-[28px] text-[18px] text-white whitespace-nowrap">Get started</p>
                </div>
              </a>
              <a href="#pricing" class="border border-[#3328bf] rounded-[8px] btn-glow w-full sm:w-auto">
                <div class="flex gap-[10px] items-center justify-center overflow-clip px-[22px] py-[16px] rounded-[inherit]">
                  <p class="font-semibold leading-[28px] text-[#324ea0] text-[18px] whitespace-nowrap">Reorder</p>
                </div>
              </a>
            </div>
          </div>
          <div class="flex gap-[15px] items-start mt-[40px] lg:mt-[40px] xl:mt-0 xl:mb-[60px]">
            <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" class="hover:opacity-70 transition-opacity">
              <img src="/new/images/Facebook.svg" alt="Facebook" class="w-[48px] h-auto" />
            </a>
            <a href="https://instagram.com" target="_blank" rel="noopener noreferrer" class="hover:opacity-70 transition-opacity">
              <img src="/new/images/Instagram.svg" alt="Instagram" class="w-[48px] h-auto" />
            </a>
            <a href="https://twitter.com" target="_blank" rel="noopener noreferrer" class="hover:opacity-70 transition-opacity">
              <img src="/new/images/Twitter.svg" alt="Twitter" class="w-[48px] h-auto" />
            </a>
            <a href="https://linkedin.com" target="_blank" rel="noopener noreferrer" class="hover:opacity-70 transition-opacity">
              <img src="/new/images/Linkedin.svg" alt="LinkedIn" class="w-[48px] h-auto" />
            </a>
          </div>
        </div>
        
        <div class="relative flex h-[400px] md:h-[600px] lg:h-[750px] items-start lg:items-center justify-center w-full lg:w-1/2 px-[20px] lg:px-0 order-1 lg:order-2">
          <div class="h-full w-full lg:h-[750px] rounded-lg lg:rounded-none overflow-hidden flex items-start lg:items-center justify-center">
            <img src="/new/images/Hero Image.png" alt="Hero" class="w-full h-full object-cover object-[center_top]" />
          </div>
          
          <div class="absolute bg-white flex flex-col gap-[20px] lg:gap-[25px] items-start bottom-[-40px] left-[20px] right-[20px] lg:left-[-80px] xl:left-[-120px] lg:right-auto lg:bottom-auto lg:top-[400px] xl:top-[480px] p-[24px] lg:p-[40px] rounded-[10px] shadow-[0px_100px_200px_0px_rgba(52,64,84,0.18)] max-w-[calc(100%-40px)] lg:max-w-none">
            <div class="flex flex-col justify-center leading-[0] text-[#0d0d0d]">
              <p class="leading-[32px] lg:leading-[44px] text-[24px] lg:text-[36px] mb-0">From</p>
              <p class="leading-[32px] lg:leading-[44px]"><span class="text-[24px] lg:text-[36px]">£95</span><span class="text-[16px] lg:text-[20px]"> / month</span></p>
            </div>
            <div class="flex gap-[4px] items-center justify-center px-0 py-[10px] rounded-[8px]">
              <p class="font-semibold leading-[24px] text-[16px] text-[grey] whitespace-nowrap">Get started</p>
              <svg class="w-[20px] h-[20px]" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M7.5 5L12.5 10L7.5 15" stroke="grey" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Injections Section -->
    <div id="injections" class="bg-gradient-to-b from-[#ffffff] to-[#d4dfff] w-full flex flex-col items-center justify-center">
      <div class="flex flex-col gap-[40px] lg:gap-[50px] items-center px-[20px] md:px-[40px] lg:px-[60px] py-[60px] md:py-[80px] lg:py-[100px] w-full">
        <div class="flex flex-col gap-[10px] items-start justify-center w-full lg:max-w-[1120px]">
          <div class="bg-[#afd136] flex gap-[10px] items-center justify-center overflow-clip px-[8px] py-[6px] rounded-[6px]">
            <p class="font-semibold leading-[24px] md:leading-[30px] text-[18px] md:text-[20px] text-white whitespace-nowrap">Weight Loss Injections</p>
          </div>
          <p class="font-medium text-[#0d0d0d] text-[32px] md:text-[48px] lg:text-[60px] tracking-[-0.64px] md:tracking-[-0.96px] lg:tracking-[-1.2px] leading-[40px] md:leading-[56px] lg:leading-[72px]">Self-administered injections to help your weight loss journey.</p>
          <p class="text-[16px] md:text-[18px] lg:text-[20px] text-[grey] leading-[24px] md:leading-[28px] lg:leading-[30px]">Here is what to expect from us.</p>
        </div>
        <div class="flex flex-col gap-[24px] md:gap-[32px] items-center justify-center w-full lg:max-w-[1120px]">
          <div class="flex flex-col md:flex-row gap-[24px] md:gap-[32px] items-stretch justify-center w-full">
            <div class="flex-1 border border-white flex flex-col gap-[20px] items-start justify-center p-[30px] md:p-[40px] lg:p-[50px] rounded-[20px]">
              <div class="w-[96px] h-[96px] md:w-[112px] md:h-[112px] lg:w-[128px] lg:h-[128px]">
                <img src="/new/images/icon-amplicator.svg" alt="Simple Applicator" class="w-full h-full" />
              </div>
              <div class="flex flex-col gap-[12px] items-start w-full">
                <p class="font-medium text-[#0d0d0d] text-[20px] md:text-[22px] lg:text-[24px] leading-[28px] md:leading-[30px] lg:leading-[32px]">Simple Applicator</p>
                <p class="text-[16px] text-[grey] leading-[24px]">You will be provided with one single applicator for 4 weeks worth of injections.</p>
              </div>
            </div>
            <div class="flex-1 border border-white flex flex-col gap-[20px] items-start justify-center p-[30px] md:p-[40px] lg:p-[50px] rounded-[20px]">
              <div class="w-[96px] h-[96px] md:w-[112px] md:h-[112px] lg:w-[128px] lg:h-[128px]">
                <img src="/new/images/icon-support.svg" alt="Online Support" class="w-full h-full" />
              </div>
              <div class="flex flex-col gap-[12px] items-start w-full">
                <p class="font-medium text-[#0d0d0d] text-[20px] md:text-[22px] lg:text-[24px] leading-[28px] md:leading-[30px] lg:leading-[32px]">Online Support</p>
                <p class="text-[16px] text-[grey] leading-[24px]">We will always be on hand via email, chat or scheduled call* to help you along the way.</p>
              </div>
            </div>
            <div class="flex-1 border border-white flex flex-col gap-[20px] items-start justify-center p-[30px] md:p-[40px] lg:p-[50px] rounded-[20px]">
              <div class="w-[96px] h-[96px] md:w-[112px] md:h-[112px] lg:w-[128px] lg:h-[128px]">
                <img src="/new/images/icon-blood-test.svg" alt="Optional Blood Tests" class="w-full h-full" />
              </div>
              <div class="flex flex-col gap-[12px] items-start w-full">
                <p class="font-medium text-[#0d0d0d] text-[20px] md:text-[22px] lg:text-[24px] leading-[28px] md:leading-[30px] lg:leading-[32px]">Optional Blood Tests</p>
                <p class="text-[16px] text-[grey] leading-[24px]">To help you better understand the changes you are going through we have partnered with a UKAS accredited Laboratory.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Getting Started Section -->
    <div id="process" class="bg-white w-full flex flex-col items-center justify-center">
      <div class="bg-white flex flex-col gap-[40px] lg:gap-[50px] items-center justify-center px-[20px] md:px-[40px] lg:px-[60px] py-[60px] md:py-[80px] lg:py-[100px] w-full">
        <div class="flex flex-col gap-[4px] items-start justify-center w-full lg:max-w-[1120px]">
          <div class="bg-[#afd136] flex gap-[10px] items-center justify-center overflow-clip px-[8px] py-[6px] rounded-[6px]">
            <p class="font-semibold leading-[24px] md:leading-[30px] text-[18px] md:text-[20px] text-white whitespace-nowrap">The Process</p>
          </div>
          <p class="font-medium leading-[40px] md:leading-[50px] lg:leading-[60px] text-[#0d0d0d] text-[32px] md:text-[40px] lg:text-[48px] tracking-[-0.64px] md:tracking-[-0.8px] lg:tracking-[-0.96px]">All you need to know about the coming months</p>
        </div>
        <div class="flex flex-col md:flex-row gap-[32px] md:gap-[40px] lg:gap-[74px] items-stretch w-full lg:max-w-[1120px]">
          <div class="flex flex-col gap-[24px] md:gap-[30px] items-start flex-1">
            <div class="border-2 border-[#3328bf] rounded-[16px] w-full md:w-auto">
              <div class="flex flex-col gap-[10px] items-center justify-center overflow-clip px-[20px] md:px-[29px] py-[14px] rounded-[inherit]">
                <p style="font-family: Catamaran, sans-serif;" class="font-normal leading-[1.18] text-[#3328bf] text-[20px] md:text-[24px] whitespace-nowrap">Getting Started</p>
              </div>
            </div>
            <div class="flex flex-col gap-[10px] items-start">
              <p class="font-medium leading-[28px] md:leading-[32px] text-[#0d0d0d] text-[20px] md:text-[24px]">Online Consultation</p>
              <p class="leading-[24px] text-[16px] text-[grey] w-full">You will need a few minutes to complete our online consultation and sign-up. If you are eligible you will proceed to complete your order.</p>
            </div>
          </div>
          <div class="flex flex-col gap-[24px] md:gap-[30px] items-start flex-1">
            <div class="border-2 border-[#3328bf] rounded-[16px] w-full md:w-auto">
              <div class="flex flex-col gap-[10px] items-center justify-center overflow-clip px-[20px] md:px-[26px] py-[14px] rounded-[inherit]">
                <p style="font-family: Catamaran, sans-serif;" class="font-normal leading-[1.18] text-[#3328bf] text-[20px] md:text-[24px] whitespace-nowrap">First 6 months</p>
              </div>
            </div>
            <div class="flex flex-col gap-[10px] items-start justify-center">
              <p class="font-medium leading-[28px] md:leading-[32px] text-[#0d0d0d] text-[20px] md:text-[24px]">Losing the first few pounds!</p>
              <p class="leading-[24px] text-[16px] text-[grey] w-full">Your initial weight loss will spur you onto a continued push. Not losing weight? We will be on hand to help and guide you.</p>
            </div>
          </div>
          <div class="flex flex-col gap-[24px] md:gap-[30px] items-start flex-1">
            <div class="border-2 border-[#3328bf] rounded-[16px] w-full md:w-auto">
              <div class="flex flex-col gap-[10px] items-center justify-center overflow-clip px-[20px] md:px-[27px] py-[14px] rounded-[inherit]">
                <p style="font-family: Catamaran, sans-serif;" class="font-normal leading-[1.18] text-[#3328bf] text-[20px] md:text-[24px] whitespace-nowrap">Beyond 6 months</p>
              </div>
            </div>
            <div class="flex flex-col gap-[10px] items-start">
              <p class="font-medium leading-[28px] md:leading-[32px] text-[#0d0d0d] text-[20px] md:text-[24px]">A lifestyle change.</p>
              <p class="leading-[24px] text-[16px] text-[grey] w-full">After the initial weight loss you will be encouraged to look more deeply at your nutrition and exercise to help you focus on continued weight loss and management.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Pricing Table -->
      <div id="pricing" class="bg-white w-full flex flex-col items-center justify-center">
        <div class="bg-white flex flex-col gap-[40px] lg:gap-[50px] items-center justify-center px-[20px] md:px-[40px] lg:px-[60px] py-[60px] md:py-[80px] lg:py-[100px] w-full">
          <div class="flex flex-col gap-[16px] lg:gap-[20px] items-center w-full lg:max-w-[1120px]">
            <div class="bg-[#afd136] flex gap-[10px] items-center justify-center overflow-clip px-[8px] py-[6px] rounded-[6px]">
              <p class="font-semibold leading-[24px] md:leading-[30px] text-[18px] md:text-[20px] text-white whitespace-nowrap">Weight Loss Injections</p>
            </div>
            <p class="font-medium text-[#0d0d0d] text-[28px] md:text-[36px] lg:text-[48px] text-center tracking-[-0.56px] md:tracking-[-0.72px] lg:tracking-[-0.96px] leading-[36px] md:leading-[48px] lg:leading-[60px] px-[10px]">GIP and GLP-1 Hormone Receptor Medications</p>
            <p class="text-[16px] text-[grey] text-center w-full max-w-[742px] leading-[24px] px-[10px]">Effective solutions to manage your weight loss and Type-2 diabetes.</p>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-[20px] lg:gap-[32px] items-stretch w-full lg:max-w-[1120px]">
               <?php
                                                         perch_collection('MedicationProgrammes', [

                                                             'count'      => 6,
                                                         ]);
                                                     ?>


            <div class="border border-[#d6d6d6] flex flex-col justify-between px-[24px] py-[30px] rounded-[20px]">
              <div class="flex flex-col gap-[24px] items-start w-full">
                <div class="flex flex-col gap-[12px] items-start w-full">
                  <p class="font-semibold leading-[24px] text-[18px] text-[#3328bf]">Wegovy Weight Loss Consultation</p>
                  <p class="leading-[24px] text-[16px] text-[grey] w-full">Clinically proven weight loss</p>
                </div>
                <div class="border-[#d6d6d6] border-b flex items-end pb-[24px] w-full">
                  <p class="font-medium leading-[48px] md:leading-[60px] text-[40px] md:text-[48px] tracking-[-0.8px] md:tracking-[-0.96px] text-[#0d0d0d]">$109</p>
                  <p class="leading-[24px] text-[16px] text-[#0d0d0d]"> / month</p>
                </div>
                <div class="flex flex-col gap-[16px] items-start w-full">
                  <p class="font-medium leading-[24px] text-[#0d0d0d] text-[16px]">Wegovy is a weekly injection that mimics GLP-1 hormone, suppresses appetite, and aids in significant, sustainable weight loss when used alongside a healthy lifestyle.</p>
                </div>
              </div>
              <a href="#pricing" class="bg-[#3328bf] flex gap-[10px] items-center justify-center overflow-clip px-[22px] py-[16px] rounded-[8px] shadow-[0px_1px_2px_0px_rgba(16,24,40,0.05)] w-full mt-[30px] btn-glow">
                <p class="font-semibold leading-[28px] text-[#fcfcfc] text-[18px] whitespace-nowrap">Learn more</p>
              </a>
            </div>
            <div class="border border-[#d6d6d6] flex flex-col justify-between px-[24px] py-[30px] rounded-[20px]">
              <div class="flex flex-col gap-[24px] items-start w-full">
                <div class="flex flex-col gap-[12px] items-start w-full">
                  <p class="font-semibold leading-[24px] text-[18px] text-[#3328bf]">Weight Loss Blood Test</p>
                  <p class="leading-[24px] text-[16px] text-[grey] w-full">Biochemistry profile for your weight loss journey.</p>
                </div>
                <div class="border-[#d6d6d6] border-b flex items-end pb-[24px] w-full">
                  <p class="font-medium leading-[48px] md:leading-[60px] text-[40px] md:text-[48px] tracking-[-0.8px] md:tracking-[-0.96px] text-[#0d0d0d]">$99</p>
                  <p class="leading-[24px] text-[16px] text-[#0d0d0d]"> / month</p>
                </div>
                <div class="flex flex-col gap-[16px] items-start w-full">
                  <p class="font-medium leading-[24px] text-[#0d0d0d] text-[16px]">Our weight loss blood test analyzes hormone levels, metabolism, thyroid function, and nutrient deficiencies to identify underlying issues that may affect weight loss and overall health.</p>
                </div>
              </div>
              <a href="#pricing" class="bg-[#3328bf] flex gap-[10px] items-center justify-center overflow-clip px-[22px] py-[16px] rounded-[8px] shadow-[0px_1px_2px_0px_rgba(16,24,40,0.05)] w-full mt-[30px] btn-glow">
                <p class="font-semibold leading-[28px] text-[#fcfcfc] text-[18px] whitespace-nowrap">Learn more</p>
              </a>
            </div>
            <div class="border border-[#d6d6d6] flex flex-col justify-between px-[24px] py-[30px] rounded-[20px]">
              <div class="flex flex-col gap-[24px] items-start w-full">
                <div class="flex flex-col gap-[12px] items-start w-full">
                  <p class="font-semibold leading-[24px] text-[18px] text-[#3328bf]">Mounjaro Weight Loss Consultation</p>
                  <p class="leading-[24px] text-[16px] text-[grey] w-full">Clinically-proven weight loss</p>
                </div>
                <div class="border-[#d6d6d6] border-b flex items-end pb-[24px] w-full">
                  <p class="font-medium leading-[48px] md:leading-[60px] text-[40px] md:text-[48px] tracking-[-0.8px] md:tracking-[-0.96px] text-[#0d0d0d]">$129</p>
                  <p class="leading-[24px] text-[16px] text-[#0d0d0d]"> / month</p>
                </div>
                <div class="flex flex-col gap-[16px] items-start w-full">
                  <p class="font-medium leading-[24px] text-[#0d0d0d] text-[16px]">Mounjaro helps with weight loss by mimicking gut hormones, reducing appetite, improving blood sugar, and supporting long-term weight management when combined with diet and exercise.</p>
                </div>
              </div>
              <a href="#pricing" class="bg-[#3328bf] flex gap-[10px] items-center justify-center overflow-clip px-[22px] py-[16px] rounded-[8px] shadow-[0px_1px_2px_0px_rgba(16,24,40,0.05)] w-full mt-[30px] btn-glow">
                <p class="font-semibold leading-[28px] text-[#fcfcfc] text-[18px] whitespace-nowrap">Learn more</p>
              </a>
            </div>
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

                                'count'      => 3,
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
            <a href="#pricing" class="bg-[#3328bf] border border-[#3328bf] rounded-[8px] btn-glow">
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
