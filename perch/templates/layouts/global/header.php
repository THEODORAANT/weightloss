<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GetWeightLoss - Weight Loss Success</title>
  <!-- Version: 2025-01-08-LATEST -->
  <link rel="stylesheet" href="/css/tailwind-subset.css?v=20250108">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Catamaran:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
    }
    
    /* FAQ Accordion smooth animations */
    .faq-answer {
      max-height: 0;
      overflow: hidden;
      opacity: 0;
      transition: all 0.3s linear;
    }
    
    .faq-answer.active {
      max-height: 500px;
      opacity: 1;
    }

    .faq-question:focus-visible {
      outline: 2px solid #3328bf;
      outline-offset: 4px;
    }

    .faq-question.active .faq-toggle-icon {
      background-color: #3328bf;
      border-color: #3328bf;
      color: #ffffff;
    }
    
    /* Mobile Sidebar Animations */
    .mobile-menu {
      transform: translateX(100%);
      transition: transform 0.3s ease;
    }
    
    .mobile-menu.active {
      transform: translateX(0);
    }
    
    .mobile-backdrop {
      opacity: 0;
      transition: opacity 0.3s ease;
    }
    
    .mobile-backdrop.active {
      opacity: 1;
    }
    
    /* Button Glow Effect */
    @keyframes buttonGlow {
      0% { background-position: -200% center; }
      100% { background-position: 200% center; }
    }
    
    .btn-glow:hover {
      position: relative;
      overflow: hidden;
    }
    
    .btn-glow:hover::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent 0%, rgba(255, 255, 255, 0.3) 50%, transparent 100%);
      background-size: 200% 100%;
      animation: buttonGlow 0.6s ease-out;
      pointer-events: none;
      z-index: 1;
    }
    
    .btn-glow > * {
      position: relative;
      z-index: 2;
    }
    
    /* Carousel */
    .testimonial-carousel {
      transition: transform 0.5s ease-in-out;
    }
  </style>
</head>
<body class="bg-white">
  <div class="bg-white flex flex-col items-center justify-center w-full">
    
    <!-- Navbar -->
    <nav id="mainNav" class="bg-white w-full h-[100px] flex items-center justify-center sticky top-0 z-[1000] transition-all duration-300">
      <div class="flex items-center justify-between px-[20px] lg:px-[60px] py-[28px] w-full">
        <a href="/" class="logo-container transition-all duration-300" style="width: 150px;">
          <img src="/asset/logo-final.png" alt="GetWeightLoss" class="h-auto w-full object-contain" />
        </a>
        
        <!-- Desktop Menu Links -->
        <div class="hidden lg:flex items-center justify-center">
          <a href="/" class="flex flex-col h-[36px] items-center justify-center px-[12px] py-0">
            <p class="font-semibold leading-[20px] text-[#3328bf] text-[14px] whitespace-nowrap">Home</p>
            <div class="w-[4px] h-[4px] bg-[#3328bf] rounded-full"></div>
          </a>
          <div class="relative group">
            <div class="flex gap-[10px] items-center justify-center px-[12px] py-[8px] cursor-pointer">
              <p class="font-semibold leading-[20px] text-[#616161] text-[14px] whitespace-nowrap">Weight Loss</p>
              <svg class="w-[20px] h-[20px]" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 7.5L10 12.5L15 7.5" stroke="#616161" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <div class="absolute top-full left-0 mt-2 w-[260px] bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-[9999]">
              <div class="py-2">
                <a href="/medications/mounjaro" class="block px-4 py-2 text-[14px] text-[#616161] hover:bg-gray-50 hover:text-[#3328bf] transition-colors">Mounjaro</a>
                <a href="/medications/ozempic" class="block px-4 py-2 text-[14px] text-[#616161] hover:bg-gray-50 hover:text-[#3328bf] transition-colors">Ozempic</a>
                <a href="/medications/wegovy" class="block px-4 py-2 text-[14px] text-[#616161] hover:bg-gray-50 hover:text-[#3328bf] transition-colors">Wegovy</a>
                <a href="/knowledge/review-answers" class="block px-4 py-2 text-[14px] text-[#616161] hover:bg-gray-50 hover:text-[#3328bf] transition-colors">Results</a>
              </div>
            </div>
          </div>
          <div class="relative group">
            <div class="flex gap-[10px] items-center justify-center px-[12px] py-[8px] cursor-pointer">
              <p class="font-semibold leading-[20px] text-[#616161] text-[14px] whitespace-nowrap">Knowledge</p>
              <svg class="w-[20px] h-[20px]" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 7.5L10 12.5L15 7.5" stroke="#616161" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <div class="absolute top-full left-0 mt-2 w-[240px] bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-[9999]">
              <div class="py-2">
                <a href="/knowledge/nutrition" class="block px-4 py-2 text-[14px] text-[#616161] hover:bg-gray-50 hover:text-[#3328bf] transition-colors">Nutrition</a>
                <a href="/knowledge/exercise" class="block px-4 py-2 text-[14px] text-[#616161] hover:bg-gray-50 hover:text-[#3328bf] transition-colors">Exercise</a>
                <a href="/knowledge/stress" class="block px-4 py-2 text-[14px] text-[#616161] hover:bg-gray-50 hover:text-[#3328bf] transition-colors">Stress</a>
                <a href="/knowledge/sleep" class="block px-4 py-2 text-[14px] text-[#616161] hover:bg-gray-50 hover:text-[#3328bf] transition-colors">Sleep</a>
              </div>
            </div>
          </div>
          <a href="/blog" class="flex gap-[10px] items-center justify-center px-[12px] py-[8px]">
            <p class="font-semibold leading-[20px] text-[#616161] text-[14px] whitespace-nowrap">Health Hub</p>
          </a>
          <a href="/about-us" class="flex gap-[10px] items-center justify-center px-[12px] py-[8px]">
            <p class="font-semibold leading-[20px] text-[#616161] text-[14px] whitespace-nowrap">About Us</p>
          </a>
        </div>
        
        <!-- Desktop Buttons -->
        <div class="hidden lg:flex gap-[10px] items-center justify-end">
          <a href="/client" class="flex gap-[4px] items-center justify-center overflow-clip px-[14px] py-[10px] rounded-[8px]">
            <p class="font-semibold leading-[24px] text-[#616161] text-[16px] whitespace-nowrap">Log in</p>
          </a>
          <a href="/contact-us" class="bg-[#3328bf] border border-[#3328bf] rounded-[8px] btn-glow">
            <div class="flex gap-[4px] items-center justify-center overflow-clip px-[14px] py-[10px] rounded-[inherit]">
              <p class="font-semibold leading-[24px] text-[#fcfcfc] text-[16px] whitespace-nowrap">Get Started</p>
            </div>
          </a>
        </div>

        <!-- Mobile/Tablet: Buttons and Hamburger -->
        <div class="flex lg:hidden gap-[10px] items-center justify-end">
          <a href="/client" class="flex gap-[4px] items-center justify-center overflow-clip px-[10px] py-[8px] rounded-[8px]">
            <p class="font-semibold leading-[20px] text-[#616161] text-[14px] whitespace-nowrap">Log in</p>
          </a>
          <a href="/contact-us" class="bg-[#3328bf] border border-[#3328bf] rounded-[8px] btn-glow">
            <div class="flex gap-[4px] items-center justify-center overflow-clip px-[10px] py-[8px] rounded-[inherit]">
              <p class="font-semibold leading-[20px] text-[#fcfcfc] text-[14px] whitespace-nowrap">Get Started</p>
            </div>
          </a>
          
          <!-- Hamburger Menu Button -->
          <button id="mobileMenuBtn" class="flex flex-col gap-[5px] items-center justify-center w-[40px] h-[40px] ml-[10px]">
            <span class="w-[24px] h-[2px] bg-[#0d0d0d] transition-all"></span>
            <span class="w-[24px] h-[2px] bg-[#0d0d0d] transition-all"></span>
            <span class="w-[24px] h-[2px] bg-[#0d0d0d] transition-all"></span>
          </button>
        </div>
      </div>
    </nav>

    <!-- Mobile Menu Sidebar -->
    <div id="mobileMenuOverlay" class="fixed inset-0 z-[2000] lg:hidden hidden">
      <div class="mobile-backdrop absolute inset-0 bg-black bg-opacity-50"></div>
      <div class="mobile-menu absolute right-0 top-0 h-full w-[300px] bg-white shadow-xl overflow-y-auto">
        <div class="flex justify-end p-[20px]">
          <button id="closeMobileMenuBtn" class="w-[40px] h-[40px] flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors">
            <svg class="w-[24px] h-[24px]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M18 6L6 18M6 6L18 18" stroke="#0d0d0d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
        </div>
        <nav class="flex flex-col px-[20px] pb-[20px]">
          <a href="/" class="mobile-menu-link py-[16px] border-b border-gray-200">
            <p class="font-semibold text-[16px] text-[#3328bf]">Home</p>
          </a>
          <div class="py-[16px] border-b border-gray-200">
            <p class="font-semibold text-[16px] text-[#0d0d0d] mb-[12px]">Weight Loss</p>
            <div class="flex flex-col gap-[8px] pl-[16px]">
              <a href="/medications/mounjaro" class="mobile-menu-link text-[14px] text-[#616161] py-[8px]">Mounjaro</a>
              <a href="/medications/ozempic" class="mobile-menu-link text-[14px] text-[#616161] py-[8px]">Ozempic</a>
              <a href="/medications/wegovy" class="mobile-menu-link text-[14px] text-[#616161] py-[8px]">Wegovy</a>
              <a href="/knowledge/review-answers" class="mobile-menu-link text-[14px] text-[#616161] py-[8px]">Results</a>
            </div>
          </div>
          <div class="py-[16px] border-b border-gray-200">
            <p class="font-semibold text-[16px] text-[#0d0d0d] mb-[12px]">Knowledge</p>
            <div class="flex flex-col gap-[8px] pl-[16px]">
              <a href="/knowledge/nutrition" class="mobile-menu-link text-[14px] text-[#616161] py-[8px]">Nutrition</a>
              <a href="/knowledge/exercise" class="mobile-menu-link text-[14px] text-[#616161] py-[8px]">Exercise</a>
              <a href="/knowledge/stress" class="mobile-menu-link text-[14px] text-[#616161] py-[8px]">Stress</a>
              <a href="/knowledge/sleep" class="mobile-menu-link text-[14px] text-[#616161] py-[8px]">Sleep</a>
            </div>
          </div>
          <a href="/blog" class="mobile-menu-link py-[16px] border-b border-gray-200">
            <p class="font-semibold text-[16px] text-[#616161]">Health Hub</p>
          </a>
          <a href="/about-us" class="mobile-menu-link py-[16px] border-b border-gray-200">
            <p class="font-semibold text-[16px] text-[#616161]">About Us</p>
          </a>
          <div class="flex flex-col gap-[12px] pt-[24px]">
            <a href="/client" class="mobile-menu-link text-[14px] text-[#3328bf] font-semibold">Log in</a>
            <a href="/contact-us" class="mobile-menu-link text-[14px] text-white font-semibold bg-[#3328bf] rounded-[8px] text-center py-[10px]">Get Started</a>
          </div>
        </nav>
      </div>
    </div>
  </div>

  <script>
    (() => {
      const mainNav = document.getElementById('mainNav');
      const mobileMenuBtn = document.getElementById('mobileMenuBtn');
      const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
      const mobileMenu = mobileMenuOverlay?.querySelector('.mobile-menu');
      const mobileBackdrop = mobileMenuOverlay?.querySelector('.mobile-backdrop');
      const closeMobileMenuBtn = document.getElementById('closeMobileMenuBtn');

      window.addEventListener('scroll', () => {
        if (!mainNav) return;
        if (window.scrollY > 10) {
          mainNav.classList.add('shadow-md');
          mainNav.classList.add('bg-white/90');
        } else {
          mainNav.classList.remove('shadow-md');
          mainNav.classList.remove('bg-white/90');
        }
      });

      const openMobileMenu = () => {
        mobileMenuOverlay?.classList.remove('hidden');
        requestAnimationFrame(() => {
          mobileMenu?.classList.add('active');
          mobileBackdrop?.classList.add('active');
        });
      };

      const closeMobileMenu = () => {
        mobileMenu?.classList.remove('active');
        mobileBackdrop?.classList.remove('active');
        setTimeout(() => mobileMenuOverlay?.classList.add('hidden'), 300);
      };

      mobileMenuBtn?.addEventListener('click', openMobileMenu);
      closeMobileMenuBtn?.addEventListener('click', closeMobileMenu);
      mobileBackdrop?.addEventListener('click', closeMobileMenu);

      document.querySelectorAll('.mobile-menu-link').forEach(link => {
        link.addEventListener('click', closeMobileMenu);
      });
    })();
  </script>

