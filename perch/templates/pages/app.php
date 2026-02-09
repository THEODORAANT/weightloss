<?php
// Output the top of the page
perch_layout('global/head');
perch_layout('global/new/header', [
  'page_title' => perch_page_title(true),
]);
?>

<!-- Hero Section -->
<div id="app-hero" class="w-full flex flex-col items-center justify-center bg-gradient-to-b from-white to-[#f5f7ff]">
  <div class="flex flex-col-reverse lg:flex-row gap-[40px] lg:gap-[60px] items-center justify-center w-full px-[20px] md:px-[40px] lg:px-[60px] xl:px-[120px] py-[60px] md:py-[80px] lg:py-[100px] max-w-[1400px]">

    <!-- Left Content -->
    <div class="flex flex-col gap-[32px] items-start w-full lg:w-1/2">
      <div class="flex flex-col gap-[20px] items-start">
        <div class="bg-[#afd136] flex gap-[10px] items-center justify-center px-[12px] py-[8px] rounded-[8px]">
          <p class="font-semibold leading-[24px] text-[18px] text-white whitespace-nowrap">Get Weight Loss App</p>
        </div>
        <h1 class="font-semibold text-[#0d0d0d] text-[42px] md:text-[56px] lg:text-[64px] tracking-[-0.84px] md:tracking-[-1.12px] lg:tracking-[-1.28px] leading-[48px] md:leading-[64px] lg:leading-[76px]">
          Your Personal Weight Management Companion
        </h1>
        <p class="text-[18px] md:text-[20px] text-[grey] leading-[28px] md:leading-[32px]">
          Take control of your health journey with our comprehensive mobile app. Manage your weight loss treatment, track your progress, and stay connected with your healthcare team - all in one place.
        </p>
      </div>

      <!-- App Store Buttons -->
      <div class="flex flex-col sm:flex-row gap-[16px] items-stretch sm:items-center w-full sm:w-auto">
        <a href="https://apps.apple.com/gb/app/get-weight-loss/id6753794417" target="_blank" rel="noopener noreferrer" class="inline-block">
          <img src="/new/images/app-store-badge.svg" alt="Download on the App Store" class="h-[56px] w-auto hover:opacity-80 transition-opacity" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />
          <div style="display:none;" class="bg-black text-white rounded-[12px] px-[24px] py-[12px] h-[56px] items-center justify-center gap-[8px] hover:opacity-80 transition-opacity">
            <svg class="w-[32px] h-[32px]" viewBox="0 0 24 24" fill="white">
              <path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09l.01-.01zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/>
            </svg>
            <div class="flex flex-col">
              <span class="text-[10px]">Download on the</span>
              <span class="text-[18px] font-semibold">App Store</span>
            </div>
          </div>
        </a>
        <a href="https://play.google.com/store/apps/details?id=com.knosee.getweightloss" target="_blank" rel="noopener noreferrer" class="inline-block">
          <img src="/new/images/google-play-badge.svg" alt="Get it on Google Play" class="h-[56px] w-auto hover:opacity-80 transition-opacity" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />
          <div style="display:none;" class="bg-black text-white rounded-[12px] px-[24px] py-[12px] h-[56px] items-center justify-center gap-[8px] hover:opacity-80 transition-opacity">
            <svg class="w-[28px] h-[28px]" viewBox="0 0 24 24" fill="white">
              <path d="M3,20.5V3.5C3,2.91 3.34,2.39 3.84,2.15L13.69,12L3.84,21.85C3.34,21.6 3,21.09 3,20.5M16.81,15.12L6.05,21.34L14.54,12.85L16.81,15.12M20.16,10.81C20.5,11.08 20.75,11.5 20.75,12C20.75,12.5 20.53,12.9 20.18,13.18L17.89,14.5L15.39,12L17.89,9.5L20.16,10.81M6.05,2.66L16.81,8.88L14.54,11.15L6.05,2.66Z"/>
            </svg>
            <div class="flex flex-col">
              <span class="text-[10px]">GET IT ON</span>
              <span class="text-[18px] font-semibold">Google Play</span>
            </div>
          </div>
        </a>
      </div>

      <!-- App Stats -->
      <div class="flex flex-wrap gap-[24px] md:gap-[40px] items-start pt-[16px]">
        <div class="flex flex-col gap-[4px]">
          <p class="font-semibold text-[#0d0d0d] text-[32px] leading-[40px]">5.0</p>
          <p class="text-[14px] text-[grey]">App Store Rating</p>
        </div>
        <div class="flex flex-col gap-[4px]">
          <p class="font-semibold text-[#0d0d0d] text-[32px] leading-[40px]">Free</p>
          <p class="text-[14px] text-[grey]">Download</p>
        </div>
        <div class="flex flex-col gap-[4px]">
          <p class="font-semibold text-[#0d0d0d] text-[32px] leading-[40px]">iOS & Android</p>
          <p class="text-[14px] text-[grey]">Available On</p>
        </div>
      </div>
    </div>

    <!-- Right Image -->
    <div class="relative flex items-center justify-center w-full lg:w-1/2">
      <div class="relative w-full max-w-[500px]">
        <img src="/asset/app-promo/app-woman-weight.png" alt="Get Weight Loss App - Weight Tracking" class="w-full h-auto rounded-[20px] shadow-[0px_40px_80px_0px_rgba(52,64,84,0.15)]" />
      </div>
    </div>
  </div>
</div>

<!-- Features Section -->
<div id="app-features" class="bg-white w-full flex flex-col items-center justify-center">
  <div class="flex flex-col gap-[50px] lg:gap-[60px] items-center px-[20px] md:px-[40px] lg:px-[60px] py-[80px] md:py-[100px] lg:py-[120px] w-full">

    <!-- Section Header -->
    <div class="flex flex-col gap-[16px] items-center text-center w-full max-w-[800px]">
      <div class="bg-[#afd136] flex gap-[10px] items-center justify-center px-[12px] py-[8px] rounded-[8px]">
        <p class="font-semibold leading-[24px] text-[18px] text-white whitespace-nowrap">Everything You Need</p>
      </div>
      <h2 class="font-medium text-[#0d0d0d] text-[36px] md:text-[48px] lg:text-[56px] tracking-[-0.72px] md:tracking-[-0.96px] lg:tracking-[-1.12px] leading-[44px] md:leading-[56px] lg:leading-[68px]">
        Manage your weight loss journey simple and stress-free
      </h2>
      <p class="text-[18px] md:text-[20px] text-[grey] leading-[28px] md:leading-[32px]">
        Comprehensive support from initial consultation through ongoing assistance in a single platform.
      </p>
    </div>

    <!-- Features Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-[24px] lg:gap-[32px] w-full max-w-[1200px]">

      <!-- Feature 1: Free Consultation -->
      <div class="bg-gradient-to-b from-[#f5f7ff] to-white border border-[#e0e6ff] flex flex-col gap-[20px] items-start p-[32px] lg:p-[40px] rounded-[20px] hover:shadow-lg transition-shadow">
        <div class="bg-[#3328bf] rounded-[16px] p-[16px] w-[64px] h-[64px] flex items-center justify-center">
          <svg class="w-[32px] h-[32px]" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="9" cy="7" r="4"></circle>
            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
          </svg>
        </div>
        <div class="flex flex-col gap-[12px]">
          <h3 class="font-semibold text-[#0d0d0d] text-[22px] lg:text-[24px] leading-[30px] lg:leading-[32px]">Free Consultation</h3>
          <p class="text-[16px] text-[grey] leading-[24px]">Initial complimentary consultation to understand your options and create a personalized plan.</p>
        </div>
      </div>

      <!-- Feature 2: Easy Ordering -->
      <div class="bg-gradient-to-b from-[#f5f7ff] to-white border border-[#e0e6ff] flex flex-col gap-[20px] items-start p-[32px] lg:p-[40px] rounded-[20px] hover:shadow-lg transition-shadow">
        <div class="bg-[#3328bf] rounded-[16px] p-[16px] w-[64px] h-[64px] flex items-center justify-center">
          <svg class="w-[32px] h-[32px]" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
            <circle cx="9" cy="21" r="1"></circle>
            <circle cx="20" cy="21" r="1"></circle>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
          </svg>
        </div>
        <div class="flex flex-col gap-[12px]">
          <h3 class="font-semibold text-[#0d0d0d] text-[22px] lg:text-[24px] leading-[30px] lg:leading-[32px]">Easy Ordering</h3>
          <p class="text-[16px] text-[grey] leading-[24px]">Place and manage orders directly within the application with just a few taps.</p>
        </div>
      </div>

      <!-- Feature 3: Quick Reorders -->
      <div class="bg-gradient-to-b from-[#f5f7ff] to-white border border-[#e0e6ff] flex flex-col gap-[20px] items-start p-[32px] lg:p-[40px] rounded-[20px] hover:shadow-lg transition-shadow">
        <div class="bg-[#3328bf] rounded-[16px] p-[16px] w-[64px] h-[64px] flex items-center justify-center">
          <svg class="w-[32px] h-[32px]" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
            <polyline points="23 4 23 10 17 10"></polyline>
            <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
          </svg>
        </div>
        <div class="flex flex-col gap-[12px]">
          <h3 class="font-semibold text-[#0d0d0d] text-[22px] lg:text-[24px] leading-[30px] lg:leading-[32px]">Quick Reorders</h3>
          <p class="text-[16px] text-[grey] leading-[24px]">Simplified reordering process for regular restocking ensures you never run out.</p>
        </div>
      </div>

      <!-- Feature 4: Weight Tracking -->
      <div class="bg-gradient-to-b from-[#f5f7ff] to-white border border-[#e0e6ff] flex flex-col gap-[20px] items-start p-[32px] lg:p-[40px] rounded-[20px] hover:shadow-lg transition-shadow">
        <div class="bg-[#afd136] rounded-[16px] p-[16px] w-[64px] h-[64px] flex items-center justify-center">
          <svg class="w-[32px] h-[32px]" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
          </svg>
        </div>
        <div class="flex flex-col gap-[12px]">
          <h3 class="font-semibold text-[#0d0d0d] text-[22px] lg:text-[24px] leading-[30px] lg:leading-[32px]">Weight Tracking</h3>
          <p class="text-[16px] text-[grey] leading-[24px]">Manual progress logging or synchronization with smart scales for automatic tracking.</p>
        </div>
      </div>

      <!-- Feature 5: Treatment Reminders -->
      <div class="bg-gradient-to-b from-[#f5f7ff] to-white border border-[#e0e6ff] flex flex-col gap-[20px] items-start p-[32px] lg:p-[40px] rounded-[20px] hover:shadow-lg transition-shadow">
        <div class="bg-[#afd136] rounded-[16px] p-[16px] w-[64px] h-[64px] flex items-center justify-center">
          <svg class="w-[32px] h-[32px]" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
          </svg>
        </div>
        <div class="flex flex-col gap-[12px]">
          <h3 class="font-semibold text-[#0d0d0d] text-[22px] lg:text-[24px] leading-[30px] lg:leading-[32px]">Treatment Reminders</h3>
          <p class="text-[16px] text-[grey] leading-[24px]">Customizable notifications to ensure consistent adherence to your treatment regimen.</p>
        </div>
      </div>

      <!-- Feature 6: Reorder Alerts -->
      <div class="bg-gradient-to-b from-[#f5f7ff] to-white border border-[#e0e6ff] flex flex-col gap-[20px] items-start p-[32px] lg:p-[40px] rounded-[20px] hover:shadow-lg transition-shadow">
        <div class="bg-[#afd136] rounded-[16px] p-[16px] w-[64px] h-[64px] flex items-center justify-center">
          <svg class="w-[32px] h-[32px]" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
          </svg>
        </div>
        <div class="flex flex-col gap-[12px]">
          <h3 class="font-semibold text-[#0d0d0d] text-[22px] lg:text-[24px] leading-[30px] lg:leading-[32px]">Reorder Alerts</h3>
          <p class="text-[16px] text-[grey] leading-[24px]">Smart notifications when your supplies need replenishing so you're always prepared.</p>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Treatment Tracking Showcase -->
<div id="app-showcase" class="bg-gradient-to-b from-white to-[#f5f7ff] w-full flex flex-col items-center justify-center">
  <div class="flex flex-col lg:flex-row gap-[50px] lg:gap-[80px] items-center px-[20px] md:px-[40px] lg:px-[60px] xl:px-[120px] py-[80px] md:py-[100px] lg:py-[120px] w-full max-w-[1400px]">

    <!-- Image -->
    <div class="relative flex items-center justify-center w-full lg:w-1/2 order-2 lg:order-1">
      <div class="relative w-full max-w-[500px]">
        <img src="/asset/app-promo/app-man-injects-2000x2000.png" alt="Get Weight Loss App - Treatment Tracking" class="w-full h-auto rounded-[20px] shadow-[0px_40px_80px_0px_rgba(52,64,84,0.15)]" />
      </div>
    </div>

    <!-- Content -->
    <div class="flex flex-col gap-[24px] items-start w-full lg:w-1/2 order-1 lg:order-2">
      <div class="bg-[#3328bf] flex gap-[10px] items-center justify-center px-[12px] py-[8px] rounded-[8px]">
        <p class="font-semibold leading-[24px] text-[18px] text-white whitespace-nowrap">Track Your Progress</p>
      </div>
      <h2 class="font-medium text-[#0d0d0d] text-[36px] md:text-[44px] lg:text-[52px] tracking-[-0.72px] md:tracking-[-0.88px] lg:tracking-[-1.04px] leading-[44px] md:leading-[52px] lg:leading-[62px]">
        Stay on track with injection monitoring
      </h2>
      <p class="text-[18px] md:text-[20px] text-[grey] leading-[28px] md:leading-[32px]">
        Keep a detailed record of your injection timeline and dose progression. Monitor your adherence rate and view your complete injection history at a glance.
      </p>

      <!-- Benefits List -->
      <div class="flex flex-col gap-[16px] pt-[8px]">
        <div class="flex gap-[12px] items-start">
          <div class="bg-[#afd136] rounded-full p-[6px] mt-[2px] flex-shrink-0">
            <svg class="w-[16px] h-[16px]" viewBox="0 0 16 16" fill="none">
              <path d="M13.3334 4L6.00008 11.3333L2.66675 8" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <p class="text-[16px] md:text-[18px] text-[#0d0d0d] leading-[24px] md:leading-[28px]">Track 100% adherence with visual progress indicators</p>
        </div>
        <div class="flex gap-[12px] items-start">
          <div class="bg-[#afd136] rounded-full p-[6px] mt-[2px] flex-shrink-0">
            <svg class="w-[16px] h-[16px]" viewBox="0 0 16 16" fill="none">
              <path d="M13.3334 4L6.00008 11.3333L2.66675 8" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <p class="text-[16px] md:text-[18px] text-[#0d0d0d] leading-[24px] md:leading-[28px]">View complete injection history with timeline charts</p>
        </div>
        <div class="flex gap-[12px] items-start">
          <div class="bg-[#afd136] rounded-full p-[6px] mt-[2px] flex-shrink-0">
            <svg class="w-[16px] h-[16px]" viewBox="0 0 16 16" fill="none">
              <path d="M13.3334 4L6.00008 11.3333L2.66675 8" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </div>
          <p class="text-[16px] md:text-[18px] text-[#0d0d0d] leading-[24px] md:leading-[28px]">Never miss a dose with smart reminder notifications</p>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- Download CTA Section -->
<div id="download-cta" class="bg-white w-full flex flex-col items-center justify-center">
  <div class="flex flex-col items-center justify-center gap-[40px] px-[20px] md:px-[40px] lg:px-[60px] py-[80px] md:py-[100px] lg:py-[120px] w-full">
    <div class="flex flex-col gap-[24px] items-center text-center max-w-[800px]">
      <h2 class="font-medium text-[#0d0d0d] text-[36px] md:text-[48px] lg:text-[56px] tracking-[-0.72px] md:tracking-[-0.96px] lg:tracking-[-1.12px] leading-[44px] md:leading-[56px] lg:leading-[68px]">
        Ready to take control of your weight loss journey?
      </h2>
      <p class="text-[18px] md:text-[20px] text-[grey] leading-[28px] md:leading-[32px] max-w-[600px]">
        Download the Get Weight Loss app today and start managing your health with ease.
      </p>
    </div>

    <!-- Download Buttons -->
    <div class="flex flex-col sm:flex-row gap-[16px] items-stretch sm:items-center">
      <a href="https://apps.apple.com/gb/app/get-weight-loss/id6753794417" target="_blank" rel="noopener noreferrer" class="inline-block">
        <img src="/new/images/app-store-badge.svg" alt="Download on the App Store" class="h-[56px] w-auto hover:opacity-80 transition-opacity" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />
        <div style="display:none;" class="bg-black text-white rounded-[12px] px-[24px] py-[12px] h-[56px] items-center justify-center gap-[8px] hover:opacity-80 transition-opacity">
          <svg class="w-[32px] h-[32px]" viewBox="0 0 24 24" fill="white">
            <path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09l.01-.01zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/>
          </svg>
          <div class="flex flex-col">
            <span class="text-[10px]">Download on the</span>
            <span class="text-[18px] font-semibold">App Store</span>
          </div>
        </div>
      </a>
      <a href="https://play.google.com/store/apps/details?id=com.knosee.getweightloss" target="_blank" rel="noopener noreferrer" class="inline-block">
        <img src="/new/images/google-play-badge.svg" alt="Get it on Google Play" class="h-[56px] w-auto hover:opacity-80 transition-opacity" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />
        <div style="display:none;" class="bg-black text-white rounded-[12px] px-[24px] py-[12px] h-[56px] items-center justify-center gap-[8px] hover:opacity-80 transition-opacity">
          <svg class="w-[28px] h-[28px]" viewBox="0 0 24 24" fill="white">
            <path d="M3,20.5V3.5C3,2.91 3.34,2.39 3.84,2.15L13.69,12L3.84,21.85C3.34,21.6 3,21.09 3,20.5M16.81,15.12L6.05,21.34L14.54,12.85L16.81,15.12M20.16,10.81C20.5,11.08 20.75,11.5 20.75,12C20.75,12.5 20.53,12.9 20.18,13.18L17.89,14.5L15.39,12L17.89,9.5L20.16,10.81M6.05,2.66L16.81,8.88L14.54,11.15L6.05,2.66Z"/>
          </svg>
          <div class="flex flex-col">
            <span class="text-[10px]">GET IT ON</span>
            <span class="text-[18px] font-semibold">Google Play</span>
          </div>
        </div>
      </a>
    </div>

    <!-- Additional Info -->
    <div class="flex flex-col gap-[12px] items-center text-center pt-[24px]">
      <div class="flex items-center gap-[8px]">
        <svg class="w-[20px] h-[20px]" viewBox="0 0 24 24" fill="none" stroke="#afd136" stroke-width="2">
          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
          <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
        <p class="text-[16px] text-[grey]">Free to download • Available on iOS and Android</p>
      </div>
      <p class="text-[14px] text-[grey]">Compatible with iOS 15.6+ and Android devices</p>
    </div>

  </div>
</div>

<?php perch_layout('global/new/footer'); ?>
