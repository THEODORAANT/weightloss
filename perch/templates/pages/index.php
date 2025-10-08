<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GetWeightLoss - Weight Loss Success</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
    <nav class="bg-white w-full h-[100px] flex items-center justify-center sticky top-0 z-[1000]">
        <div class="flex items-center justify-between px-[15px] py-[28px] w-full lg:w-[1150px]">
            <a href="#hero" class="h-[50px] w-[111.307px]">
                <img src="/new/images/Logo.svg" alt="GetWeightLoss" class="h-full w-full object-contain" />
            </a>

            <!-- Desktop Menu Links -->
            <div class="hidden lg:flex items-center justify-center">
                <a href="#hero" class="flex flex-col h-[36px] items-center justify-center px-[12px] py-0">
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
                    <div class="absolute top-full left-0 mt-2 w-[220px] bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-[9999]">
                        <div class="py-2">
                            <a href="#injections" class="block px-4 py-2 text-[14px] text-[#616161] hover:bg-gray-50 hover:text-[#3328bf] transition-colors">Weight Loss Injections</a>
                            <a href="#pricing" class="block px-4 py-2 text-[14px] text-[#616161] hover:bg-gray-50 hover:text-[#3328bf] transition-colors">Pricing Plans</a>
                            <a href="#process" class="block px-4 py-2 text-[14px] text-[#616161] hover:bg-gray-50 hover:text-[#3328bf] transition-colors">The Process</a>
                            <a href="#testimonials" class="block px-4 py-2 text-[14px] text-[#616161] hover:bg-gray-50 hover:text-[#3328bf] transition-colors">Success Stories</a>
                        </div>
                    </div>
                </div>
                <div class="relative group">
                    <div class="flex gap-[10px] items-center justify-center px-[12px] py-[8px] cursor-pointer">
                        <p class="font-semibold leading-[20px] text-[#616161] text-[14px] whitespace-nowrap">Resources</p>
                        <svg class="w-[20px] h-[20px]" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 7.5L10 12.5L15 7.5" stroke="#616161" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="absolute top-full left-0 mt-2 w-[200px] bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-[9999]">
                        <div class="py-2">
                            <a href="#blog" class="block px-4 py-2 text-[14px] text-[#616161] hover:bg-gray-50 hover:text-[#3328bf] transition-colors">Health Hub & News</a>
                            <a href="#faq" class="block px-4 py-2 text-[14px] text-[#616161] hover:bg-gray-50 hover:text-[#3328bf] transition-colors">FAQs</a>
                            <a href="#features" class="block px-4 py-2 text-[14px] text-[#616161] hover:bg-gray-50 hover:text-[#3328bf] transition-colors">Features</a>
                        </div>
                    </div>
                </div>
                <a href="#about" class="flex gap-[10px] items-center justify-center px-[12px] py-[8px]">
                    <p class="font-semibold leading-[20px] text-[#616161] text-[14px] whitespace-nowrap">About Us</p>
                </a>
            </div>

            <!-- Desktop Buttons -->
            <div class="hidden lg:flex gap-[10px] items-center justify-end">
                <a href="#login" class="flex gap-[4px] items-center justify-center overflow-clip px-[14px] py-[10px] rounded-[8px]">
                    <p class="font-semibold leading-[24px] text-[#616161] text-[16px] whitespace-nowrap">Log in</p>
                </a>
                <a href="#register" class="bg-[#3328bf] border border-[#3328bf] rounded-[8px] btn-glow">
                    <div class="flex gap-[4px] items-center justify-center overflow-clip px-[14px] py-[10px] rounded-[inherit]">
                        <p class="font-semibold leading-[24px] text-[#fcfcfc] text-[16px] whitespace-nowrap">Register</p>
                    </div>
                </a>
            </div>

            <!-- Mobile/Tablet: Buttons and Hamburger -->
            <div class="flex lg:hidden gap-[10px] items-center justify-end">
                <a href="#login" class="flex gap-[4px] items-center justify-center overflow-clip px-[10px] py-[8px] rounded-[8px]">
                    <p class="font-semibold leading-[20px] text-[#616161] text-[14px] whitespace-nowrap">Log in</p>
                </a>
                <a href="#register" class="bg-[#3328bf] border border-[#3328bf] rounded-[8px] btn-glow">
                    <div class="flex gap-[4px] items-center justify-center overflow-clip px-[10px] py-[8px] rounded-[inherit]">
                        <p class="font-semibold leading-[20px] text-[#fcfcfc] text-[14px] whitespace-nowrap">Register</p>
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
                <a href="#hero" class="mobile-menu-link py-[16px] border-b border-gray-200">
                    <p class="font-semibold text-[16px] text-[#3328bf]">Home</p>
                </a>
                <div class="py-[16px] border-b border-gray-200">
                    <p class="font-semibold text-[16px] text-[#0d0d0d] mb-[12px]">Weight Loss</p>
                    <div class="flex flex-col gap-[8px] pl-[16px]">
                        <a href="#injections" class="mobile-menu-link text-[14px] text-[#616161] py-[8px]">Weight Loss Injections</a>
                        <a href="#pricing" class="mobile-menu-link text-[14px] text-[#616161] py-[8px]">Pricing Plans</a>
                        <a href="#process" class="mobile-menu-link text-[14px] text-[#616161] py-[8px]">The Process</a>
                        <a href="#testimonials" class="mobile-menu-link text-[14px] text-[#616161] py-[8px]">Success Stories</a>
                    </div>
                </div>
                <div class="py-[16px] border-b border-gray-200">
                    <p class="font-semibold text-[16px] text-[#0d0d0d] mb-[12px]">Resources</p>
                    <div class="flex flex-col gap-[8px] pl-[16px]">
                        <a href="#blog" class="mobile-menu-link text-[14px] text-[#616161] py-[8px]">Health Hub & News</a>
                        <a href="#faq" class="mobile-menu-link text-[14px] text-[#616161] py-[8px]">FAQs</a>
                        <a href="#features" class="mobile-menu-link text-[14px] text-[#616161] py-[8px]">Features</a>
                    </div>
                </div>
                <a href="#about" class="mobile-menu-link py-[16px] border-b border-gray-200">
                    <p class="font-semibold text-[16px] text-[#616161]">About Us</p>
                </a>
            </nav>
        </div>
    </div>

    <!-- Hero Section -->
    <div id="hero" class="w-full flex flex-col items-center justify-center">
        <div class="bg-white flex flex-col-reverse lg:flex-row gap-[30px] lg:gap-[50px] items-center lg:items-start justify-center relative w-full py-[50px] lg:py-0">
            <div class="flex flex-col items-start px-[20px] md:px-[40px] lg:pl-[160px] lg:pr-0 lg:py-0 w-full lg:w-[704px] order-2 lg:order-1 lg:h-[1024px] lg:justify-between">
                <div class="flex flex-col gap-[24px] lg:gap-[32px] items-start w-full lg:mt-[100px]">
                    <div class="flex flex-col gap-[16px] lg:gap-[20px] items-start w-full">
                        <div class="flex flex-col justify-center w-full">
                            <p class="font-semibold text-[#0d0d0d] text-[36px] md:text-[52px] lg:text-[72px] tracking-[-0.72px] md:tracking-[-1.04px] lg:tracking-[-1.44px] leading-[44px] md:leading-[60px] lg:leading-[90px]">Weight Loss Success</p>
                        </div>
                        <div class="flex flex-col justify-center w-full lg:w-[457px]">
                            <p class="leading-[24px] text-[16px] text-[grey]">We will help you work towards your weight loss success and provide you with support when you need it*.</p>
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
                <div class="flex gap-[15px] items-start mt-[40px] lg:mt-0 lg:mb-[60px]">
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

            <div class="relative flex h-[400px] md:h-[600px] lg:h-[1024px] items-start lg:items-center justify-center w-full lg:w-[686px] px-[20px] lg:px-0 order-1 lg:order-2">
                <div class="h-full w-full lg:h-[1024px] lg:w-[686px] rounded-lg overflow-hidden flex items-start lg:items-center justify-center">
                    <img src="/new/images/Hero Image.png" alt="Hero" class="w-full h-full object-cover object-top lg:object-center" />
                </div>

                <div class="absolute bg-white flex flex-col gap-[20px] lg:gap-[25px] items-start bottom-[-40px] left-[20px] right-[20px] lg:left-[-120px] lg:right-auto lg:bottom-auto lg:top-[653.5px] p-[24px] lg:p-[40px] rounded-[10px] shadow-[0px_100px_200px_0px_rgba(52,64,84,0.18)] max-w-[calc(100%-40px)] lg:max-w-none">
                    <div class="flex flex-col justify-center leading-[0] text-[#0d0d0d]">
                        <p class="leading-[32px] lg:leading-[44px] text-[24px] lg:text-[36px] mb-0">From</p>
                        <p class="leading-[32px] lg:leading-[44px]"><span class="text-[24px] lg:text-[36px]">£109.00</span><span class="text-[16px] lg:text-[20px]"> / month</span></p>
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

    <!-- About Us Section -->
    <div id="about" class="bg-white w-full flex flex-col items-center justify-center mt-[60px] lg:mt-0">
        <div class="flex flex-col lg:flex-row gap-[32px] items-center justify-center px-[20px] md:px-[40px] lg:px-[15px] py-[60px] md:py-[80px] lg:py-[100px] w-full max-w-[1150px]">
            <div class="flex flex-col gap-[24px] lg:gap-[30px] items-start w-full lg:w-[544px]">
                <div class="flex flex-col gap-[16px] lg:gap-[20px] items-start w-full">
                    <div class="flex flex-col justify-center w-full">
                        <p class="font-medium text-[#0d0d0d] text-[32px] md:text-[40px] lg:text-[48px] tracking-[-0.64px] md:tracking-[-0.8px] lg:tracking-[-0.96px] leading-[40px] md:leading-[50px] lg:leading-[60px]">The U.K. is getting bigger!</p>
                    </div>
                    <div class="flex flex-col justify-center leading-[24px] text-[16px] text-[grey] w-full">
                        <p class="mb-[16px]">Obesity in the UK has become a significant public health challenge, with rates rising steadily across all age groups. Socioeconomic factors, lifestyle changes, and diet habits contribute to this growing epidemic. The consequences extend beyond individual health, placing immense pressure on the NHS and the economy. This report delves into the latest statistics on obesity, exploring disparities, health risks, and government interventions. Understanding these trends is crucial for developing effective policies and encouraging healthier choices nationwide.</p>
                    </div>
                </div>
                <div class="flex gap-[4px] items-center justify-start px-0 py-[10px] rounded-[8px]">
                    <p class="font-semibold leading-[20px] text-[#616161] text-[14px] whitespace-nowrap">Read full report</p>
                    <svg class="w-[20px] h-[20px]" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7.5 5L12.5 10L7.5 15" stroke="#616161" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
            <div class="h-[300px] md:h-[400px] lg:h-[450px] rounded-[20px] w-full lg:w-[544px] overflow-hidden">
                <img src="/new/images/Home-Image-1.png" alt="About Us" class="w-full h-full object-cover" />
            </div>
        </div>
    </div>

    <!-- Injections Section -->
    <div id="injections" class="bg-gradient-to-b from-[#ffffff] to-[#d4dfff] w-full flex flex-col items-center justify-center">
        <div class="flex flex-col gap-[40px] lg:gap-[50px] items-center px-[20px] md:px-[40px] lg:px-[15px] py-[60px] md:py-[80px] lg:py-[100px] w-full">
            <div class="flex flex-col gap-[10px] items-start justify-center w-full max-w-[1120px]">
                <div class="bg-[#afd136] flex gap-[10px] items-center justify-center overflow-clip px-[8px] py-[6px] rounded-[6px]">
                    <p class="font-semibold leading-[24px] md:leading-[30px] text-[18px] md:text-[20px] text-white whitespace-nowrap">Weight Loss Injections</p>
                </div>
                <p class="font-medium text-[#0d0d0d] text-[32px] md:text-[48px] lg:text-[60px] tracking-[-0.64px] md:tracking-[-0.96px] lg:tracking-[-1.2px] leading-[40px] md:leading-[56px] lg:leading-[72px]">Self-administered injections to help your weight loss journey.</p>
                <p class="text-[16px] md:text-[18px] lg:text-[20px] text-[grey] leading-[24px] md:leading-[28px] lg:leading-[30px]">Here is what to expect from us.</p>
            </div>
            <div class="flex flex-col gap-[24px] md:gap-[32px] items-center justify-center w-full">
                <div class="flex flex-col md:flex-row gap-[24px] md:gap-[32px] items-stretch justify-center w-full max-w-[1120px]">
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
        <div class="bg-white flex flex-col gap-[40px] lg:gap-[50px] items-center justify-center px-[20px] md:px-[40px] lg:px-[15px] py-[60px] md:py-[80px] lg:py-[100px] w-full">
            <div class="flex flex-col gap-[4px] items-start justify-center w-full max-w-[1120px]">
                <div class="bg-[#afd136] flex gap-[10px] items-center justify-center overflow-clip px-[8px] py-[6px] rounded-[6px]">
                    <p class="font-semibold leading-[24px] md:leading-[30px] text-[18px] md:text-[20px] text-white whitespace-nowrap">The Process</p>
                </div>
                <p class="font-medium leading-[40px] md:leading-[50px] lg:leading-[60px] text-[#0d0d0d] text-[32px] md:text-[40px] lg:text-[48px] tracking-[-0.64px] md:tracking-[-0.8px] lg:tracking-[-0.96px]">All you need to know about the coming months</p>
            </div>
            <div class="flex flex-col md:flex-row gap-[32px] md:gap-[40px] lg:gap-[74px] items-stretch w-full max-w-[1120px]">
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
            <div class="bg-white flex flex-col gap-[40px] lg:gap-[50px] items-center justify-center px-[20px] md:px-[40px] lg:px-[15px] py-[60px] md:py-[80px] lg:py-[100px] w-full">
                <div class="flex flex-col gap-[16px] lg:gap-[20px] items-center w-full max-w-[1120px]">
                    <div class="bg-[#afd136] flex gap-[10px] items-center justify-center overflow-clip px-[8px] py-[6px] rounded-[6px]">
                        <p class="font-semibold leading-[24px] md:leading-[30px] text-[18px] md:text-[20px] text-white whitespace-nowrap">Weight Loss Injections</p>
                    </div>
                    <p class="font-medium text-[#0d0d0d] text-[28px] md:text-[36px] lg:text-[48px] text-center tracking-[-0.56px] md:tracking-[-0.72px] lg:tracking-[-0.96px] leading-[36px] md:leading-[48px] lg:leading-[60px] px-[10px]">GIP and GLP-1 Hormone Receptor Medications</p>
                    <p class="text-[16px] text-[grey] text-center w-full max-w-[742px] leading-[24px] px-[10px]">Effective solutions to manage your weight loss and Type-2 diabetes.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-[20px] lg:gap-[10px] items-stretch w-full max-w-[1120px]">
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
                                <p class="font-semibold leading-[24px] text-[18px] text-[#3328bf]">Ozempic for Type 2 - Diabetes</p>
                                <p class="leading-[24px] text-[16px] text-[grey] w-full">Proven Treatment</p>
                            </div>
                            <div class="border-[#d6d6d6] border-b flex items-end pb-[24px] w-full">
                                <p class="font-medium leading-[48px] md:leading-[60px] text-[32px] md:text-[40px] tracking-[-0.64px] md:tracking-[-0.8px] text-[#0d0d0d] text-center w-full">On request</p>
                            </div>
                            <div class="flex flex-col gap-[16px] items-start w-full">
                                <p class="font-medium leading-[24px] text-[#0d0d0d] text-[16px]">Ozempic manages Type 2 diabetes by lowering blood sugar, stimulating insulin, and reducing appetite. The use of Ozempic, also offers secondary weight loss benefits in many users.</p>
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
        <div class="bg-white flex flex-col gap-[40px] lg:gap-[50px] items-center justify-center px-[20px] md:px-[40px] lg:px-[15px] py-[60px] md:py-[80px] lg:py-[100px] w-full">
            <div class="flex flex-col gap-[16px] lg:gap-[20px] items-start justify-center w-full max-w-[1120px]">
                <div class="bg-[#afd136] flex gap-[10px] items-center justify-center overflow-clip px-[8px] py-[6px] rounded-[6px]">
                    <p class="font-semibold leading-[24px] md:leading-[30px] text-[18px] md:text-[20px] text-white whitespace-nowrap">More success stories</p>
                </div>
                <p class="font-medium text-[#0d0d0d] text-[32px] md:text-[40px] lg:text-[48px] tracking-[-0.64px] md:tracking-[-0.8px] lg:tracking-[-0.96px] leading-[40px] md:leading-[50px] lg:leading-[60px]">People who already love us</p>
                <p class="text-[16px] text-[grey] w-full max-w-[742px] leading-[24px]">With each client having different triggers and objectives for starting their weight loss journey, we share a few of the success stories here;</p>
            </div>

            <!-- Mobile/Tablet Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-[20px] md:gap-[24px] w-full max-w-[1120px] lg:hidden">
                <div class="bg-[#f9f9fb] flex flex-col items-start justify-between p-[20px] md:p-[24px] rounded-[20px]">
                    <div class="mb-4">
                        <img src="/new/images/stars.svg" alt="5 stars" class="h-[18px] w-[122px]" />
                    </div>
                    <p class="text-[16px] text-[grey] leading-[24px] mb-6">I discovered an old bag of clothes and couldn't believe how many of them fit me where I would have had no chance before. I feel amazing. I think I look much better than I ever did.</p>
                    <div class="border-t border-[#d6d6d6] flex gap-[8px] items-center pt-[24px] w-full">
                        <p class="font-medium leading-[24px] text-[#0d0d0d] text-[16px] whitespace-nowrap">Andy, 55</p>
                    </div>
                </div>
                <div class="bg-[#f9f9fb] flex flex-col items-start justify-between p-[20px] md:p-[24px] rounded-[20px]">
                    <div class="mb-4">
                        <img src="/new/images/stars.svg" alt="5 stars" class="h-[18px] w-[122px]" />
                    </div>
                    <p class="text-[16px] text-[grey] leading-[24px] mb-6">I want to be in good shape to enjoy life, but be happy as I'm doing it, rather than miserable. Given where I am I think I've managed that with weight loss injections. In short I feel happier that I started and will continue with it.</p>
                    <div class="border-t border-[#d6d6d6] flex gap-[8px] items-center pt-[24px] w-full">
                        <p class="font-medium leading-[24px] text-[#0d0d0d] text-[16px] whitespace-nowrap">Mark, 59</p>
                    </div>
                </div>
                <div class="bg-[#f9f9fb] flex flex-col items-start justify-between p-[20px] md:p-[24px] rounded-[20px]">
                    <div class="mb-4">
                        <img src="/new/images/stars.svg" alt="5 stars" class="h-[18px] w-[122px]" />
                    </div>
                    <p class="text-[16px] text-[grey] leading-[24px] mb-6">I want to be in good shape to enjoy life, but be happy as I'm doing it, rather than miserable. Given where I am I think I've managed that with weight loss injections. In short I feel happier that I started and will continue with it.</p>
                    <div class="border-t border-[#d6d6d6] flex gap-[8px] items-center pt-[24px] w-full">
                        <p class="font-medium leading-[24px] text-[#0d0d0d] text-[16px] whitespace-nowrap">Sarah, 42</p>
                    </div>
                </div>
                <div class="bg-[#f9f9fb] flex flex-col items-start justify-between p-[20px] md:p-[24px] rounded-[20px]">
                    <div class="mb-4">
                        <img src="/new/images/stars.svg" alt="5 stars" class="h-[18px] w-[122px]" />
                    </div>
                    <p class="text-[16px] text-[grey] leading-[24px] mb-6">The support I received was incredible. I never felt alone in my journey and the results speak for themselves. I've lost over 30 pounds and feel healthier than I have in years.</p>
                    <div class="border-t border-[#d6d6d6] flex gap-[8px] items-center pt-[24px] w-full">
                        <p class="font-medium leading-[24px] text-[#0d0d0d] text-[16px] whitespace-nowrap">John, 48</p>
                    </div>
                </div>
                <div class="bg-[#f9f9fb] flex flex-col items-start justify-between p-[20px] md:p-[24px] rounded-[20px]">
                    <div class="mb-4">
                        <img src="/new/images/stars.svg" alt="5 stars" class="h-[18px] w-[122px]" />
                    </div>
                    <p class="text-[16px] text-[grey] leading-[24px] mb-6">Starting this program was the best decision I made. The injections made it easier to stick to my diet and the weight just started coming off. I'm more confident than ever!</p>
                    <div class="border-t border-[#d6d6d6] flex gap-[8px] items-center pt-[24px] w-full">
                        <p class="font-medium leading-[24px] text-[#0d0d0d] text-[16px] whitespace-nowrap">Emma, 37</p>
                    </div>
                </div>
            </div>

            <!-- Desktop Carousel -->
            <div class="relative w-full overflow-hidden hidden lg:block">
                <div id="testimonialCarousel" class="testimonial-carousel flex gap-[32px]">
                    <div class="bg-[#f9f9fb] flex flex-col items-start justify-between p-[24px] rounded-[20px] min-w-[544px]">
                        <div class="mb-4">
                            <img src="/new/images/stars.svg" alt="5 stars" class="h-[18px] w-[122px]" />
                        </div>
                        <p class="text-[16px] text-[grey] leading-[24px] mb-6">I discovered an old bag of clothes and couldn't believe how many of them fit me where I would have had no chance before. I feel amazing. I think I look much better than I ever did.</p>
                        <div class="border-t border-[#d6d6d6] flex gap-[8px] items-center pt-[24px] w-full">
                            <p class="font-medium leading-[24px] text-[#0d0d0d] text-[16px] whitespace-nowrap">Andy, 55</p>
                        </div>
                    </div>
                    <div class="bg-[#f9f9fb] flex flex-col items-start justify-between p-[24px] rounded-[20px] min-w-[544px]">
                        <div class="mb-4">
                            <img src="/new/images/stars.svg" alt="5 stars" class="h-[18px] w-[122px]" />
                        </div>
                        <p class="text-[16px] text-[grey] leading-[24px] mb-6">I want to be in good shape to enjoy life, but be happy as I'm doing it, rather than miserable. Given where I am I think I've managed that with weight loss injections. In short I feel happier that I started and will continue with it.</p>
                        <div class="border-t border-[#d6d6d6] flex gap-[8px] items-center pt-[24px] w-full">
                            <p class="font-medium leading-[24px] text-[#0d0d0d] text-[16px] whitespace-nowrap">Mark, 59</p>
                        </div>
                    </div>
                    <div class="bg-[#f9f9fb] flex flex-col items-start justify-between p-[24px] rounded-[20px] min-w-[544px]">
                        <div class="mb-4">
                            <img src="/new/images/stars.svg" alt="5 stars" class="h-[18px] w-[122px]" />
                        </div>
                        <p class="text-[16px] text-[grey] leading-[24px] mb-6">I want to be in good shape to enjoy life, but be happy as I'm doing it, rather than miserable. Given where I am I think I've managed that with weight loss injections. In short I feel happier that I started and will continue with it.</p>
                        <div class="border-t border-[#d6d6d6] flex gap-[8px] items-center pt-[24px] w-full">
                            <p class="font-medium leading-[24px] text-[#0d0d0d] text-[16px] whitespace-nowrap">Sarah, 42</p>
                        </div>
                    </div>
                    <div class="bg-[#f9f9fb] flex flex-col items-start justify-between p-[24px] rounded-[20px] min-w-[544px]">
                        <div class="mb-4">
                            <img src="/new/images/stars.svg" alt="5 stars" class="h-[18px] w-[122px]" />
                        </div>
                        <p class="text-[16px] text-[grey] leading-[24px] mb-6">The support I received was incredible. I never felt alone in my journey and the results speak for themselves. I've lost over 30 pounds and feel healthier than I have in years.</p>
                        <div class="border-t border-[#d6d6d6] flex gap-[8px] items-center pt-[24px] w-full">
                            <p class="font-medium leading-[24px] text-[#0d0d0d] text-[16px] whitespace-nowrap">John, 48</p>
                        </div>
                    </div>
                    <div class="bg-[#f9f9fb] flex flex-col items-start justify-between p-[24px] rounded-[20px] min-w-[544px]">
                        <div class="mb-4">
                            <img src="/new/images/stars.svg" alt="5 stars" class="h-[18px] w-[122px]" />
                        </div>
                        <p class="text-[16px] text-[grey] leading-[24px] mb-6">Starting this program was the best decision I made. The injections made it easier to stick to my diet and the weight just started coming off. I'm more confident than ever!</p>
                        <div class="border-t border-[#d6d6d6] flex gap-[8px] items-center pt-[24px] w-full">
                            <p class="font-medium leading-[24px] text-[#0d0d0d] text-[16px] whitespace-nowrap">Emma, 37</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div id="features" class="w-full flex flex-col items-center justify-center px-[20px] md:px-[40px] lg:px-[15px] pb-[60px] md:pb-[80px] lg:pb-[100px]">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-[20px] w-full max-w-[1800px]">
            <div class="border border-[#d6d6d6] flex flex-col gap-[12px] items-start justify-start p-[24px] rounded-[20px]">
                <div class="w-[40px] h-[40px] bg-[#3328bf] rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-[24px] h-[24px]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="10" stroke="white" stroke-width="2"/>
                    </svg>
                </div>
                <p class="font-semibold leading-[26px] md:leading-[30px] text-[#0d0d0d] text-[18px] md:text-[20px]">Ongoing Support</p>
                <p class="leading-[26px] md:leading-[30px] text-[#0d0d0d] text-[16px] md:text-[20px]">Always available via email/chat.</p>
            </div>
            <div class="border border-[#d6d6d6] flex flex-col gap-[12px] items-start justify-start p-[24px] rounded-[20px]">
                <div class="w-[40px] h-[40px] bg-[#3328bf] rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-[24px] h-[24px]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="10" stroke="white" stroke-width="2"/>
                    </svg>
                </div>
                <p class="font-semibold leading-[26px] md:leading-[30px] text-[#0d0d0d] text-[18px] md:text-[20px]">You are in Control</p>
                <p class="leading-[26px] md:leading-[30px] text-[#0d0d0d] text-[16px] md:text-[20px]">Each month you decide to continue or stop.</p>
            </div>
            <div class="border border-[#d6d6d6] flex flex-col gap-[12px] items-start justify-start p-[24px] rounded-[20px]">
                <div class="w-[40px] h-[40px] bg-[#3328bf] rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-[24px] h-[24px]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="10" stroke="white" stroke-width="2"/>
                    </svg>
                </div>
                <p class="font-semibold leading-[26px] md:leading-[30px] text-[#0d0d0d] text-[18px] md:text-[20px]">Additional Testing</p>
                <p class="leading-[26px] md:leading-[30px] text-[#0d0d0d] text-[16px] md:text-[20px]">We can arrange blood tests, through our partners</p>
            </div>
            <div class="border border-[#d6d6d6] flex flex-col gap-[12px] items-start justify-start p-[24px] rounded-[20px]">
                <div class="w-[40px] h-[40px] bg-[#3328bf] rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-[24px] h-[24px]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="10" stroke="white" stroke-width="2"/>
                    </svg>
                </div>
                <p class="font-semibold leading-[26px] md:leading-[30px] text-[#0d0d0d] text-[18px] md:text-[20px]">Health Hub</p>
                <p class="leading-[26px] md:leading-[30px] text-[#0d0d0d] text-[16px] md:text-[20px]">The health hub, an access point for news and tips.</p>
            </div>
            <div class="border border-[#d6d6d6] flex flex-col gap-[12px] items-start justify-start p-[24px] rounded-[20px]">
                <div class="w-[40px] h-[40px] bg-[#3328bf] rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-[24px] h-[24px]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="10" stroke="white" stroke-width="2"/>
                    </svg>
                </div>
                <p class="font-semibold leading-[26px] md:leading-[30px] text-[#0d0d0d] text-[18px] md:text-[20px]">Discreet Delivery</p>
                <p class="leading-[26px] md:leading-[30px] text-[#0d0d0d] text-[16px] md:text-[20px]">No names, no logos.</p>
            </div>
            <div class="border border-[#d6d6d6] flex flex-col gap-[12px] items-start justify-start p-[24px] rounded-[20px]">
                <div class="w-[40px] h-[40px] bg-[#3328bf] rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-[24px] h-[24px]" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="10" stroke="white" stroke-width="2"/>
                    </svg>
                </div>
                <p class="font-semibold leading-[26px] md:leading-[30px] text-[#0d0d0d] text-[18px] md:text-[20px]">Competitive Pricing</p>
                <p class="leading-[26px] md:leading-[30px] text-[#0d0d0d] text-[16px] md:text-[20px]">We constantly monitor prices.</p>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div id="faq" class="bg-white w-full flex flex-col items-center justify-center">
        <div class="bg-white flex flex-col lg:flex-row gap-[40px] lg:gap-[32px] items-start justify-center px-[20px] md:px-[40px] lg:px-[15px] py-[60px] md:py-[80px] lg:py-[100px] w-full max-w-[1120px]">
            <div class="flex flex-col gap-[24px] lg:gap-0 lg:h-[588px] items-start justify-between w-full lg:w-[418px]">
                <div class="flex flex-col gap-[10px] items-start justify-center">
                    <div class="bg-[#afd136] flex gap-[10px] items-center justify-center overflow-clip px-[8px] py-[6px] rounded-[6px]">
                        <p class="font-semibold leading-[24px] md:leading-[30px] text-[18px] md:text-[20px] text-white whitespace-nowrap">FAQs</p>
                    </div>
                    <p class="font-semibold leading-[40px] md:leading-[50px] lg:leading-[60px] text-[#0d0d0d] text-[32px] md:text-[40px] lg:text-[48px] tracking-[-0.64px] md:tracking-[-0.8px] lg:tracking-[-0.96px] w-full lg:w-[368px]">Your questions answered</p>
                </div>
                <div class="flex flex-col gap-[8px] items-start">
                    <p class="font-medium leading-[26px] md:leading-[30px] text-[18px] md:text-[20px] text-[grey]">Couldn't not find what you were looking for?</p>
                    <div class="flex flex-wrap gap-[6px] items-start">
                        <p class="font-medium leading-[26px] md:leading-[30px] text-[grey] text-[18px] md:text-[20px]">write to us at</p>
                        <p class="font-medium leading-[26px] md:leading-[30px] text-[#0d0d0d] text-[18px] md:text-[20px] break-all">help@getweightloss.co.uk</p>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-[#dbdbdb] flex flex-col items-start pb-[20px] pt-[30px] md:pt-[40px] px-0 rounded-[10px] w-full lg:w-[670px]">
                <div class="faq-item w-full">
                    <div class="faq-question flex gap-[16px] md:gap-[24px] items-center px-[20px] md:px-[30px] cursor-pointer transition-all duration-300 py-[8px]" data-index="0">
                        <div class="flex flex-col items-center justify-center rounded-[50px] w-[60px] md:w-[80px] min-w-[60px] md:min-w-[80px]">
                            <p class="font-semibold leading-[36px] md:leading-[44px] text-[#0d0d0d] text-[28px] md:text-[36px] text-center tracking-[-0.56px] md:tracking-[-0.72px] whitespace-nowrap">01</p>
                        </div>
                        <div class="flex-1 flex flex-col gap-[14px] items-start">
                            <p class="font-medium leading-[26px] md:leading-[30px] text-[#0d0d0d] text-[18px] md:text-[20px] w-full">What does the Weight Loss Programme involve?</p>
                            <div class="faq-answer active">
                                <p class="leading-[24px] md:leading-[28px] text-[#595959] text-[16px] md:text-[18px] w-full">The Weight Loss Programme combines the use of weight loss medication to help suppress appetite and should be used along with exercise and better nutritional habits. By addressing the key areas of weight, you'll have the tools to help you achieve sustainable weight loss.</p>
                            </div>
                        </div>
                    </div>
                    <div class="h-[1px] bg-[#dbdbdb] w-full my-[12px]"></div>
                </div>
                <div class="faq-item w-full">
                    <div class="faq-question flex gap-[16px] md:gap-[24px] items-center px-[20px] md:px-[30px] cursor-pointer transition-all duration-300 py-[8px]" data-index="1">
                        <div class="flex flex-col items-center justify-center rounded-[50px] w-[60px] md:w-[80px] min-w-[60px] md:min-w-[80px]">
                            <p class="font-semibold leading-[36px] md:leading-[44px] text-[#0d0d0d] text-[28px] md:text-[36px] text-center tracking-[-0.56px] md:tracking-[-0.72px] whitespace-nowrap">02</p>
                        </div>
                        <div class="flex-1 flex flex-col gap-[14px] items-start">
                            <p class="font-medium leading-[26px] md:leading-[30px] text-[#0d0d0d] text-[18px] md:text-[20px] w-full">What are weight loss injections?</p>
                            <div class="faq-answer">
                                <p class="leading-[24px] md:leading-[28px] text-[#595959] text-[16px] md:text-[18px] w-full">Weight loss injections are medications that help suppress appetite and promote weight loss. They work by mimicking natural hormones in your body that regulate hunger and metabolism.</p>
                            </div>
                        </div>
                    </div>
                    <div class="h-[1px] bg-[#dbdbdb] w-full my-[12px]"></div>
                </div>
                <div class="faq-item w-full">
                    <div class="faq-question flex gap-[16px] md:gap-[24px] items-center px-[20px] md:px-[30px] cursor-pointer transition-all duration-300 py-[8px]" data-index="2">
                        <div class="flex flex-col items-center justify-center rounded-[50px] w-[60px] md:w-[80px] min-w-[60px] md:min-w-[80px]">
                            <p class="font-semibold leading-[36px] md:leading-[44px] text-[#0d0d0d] text-[28px] md:text-[36px] text-center tracking-[-0.56px] md:tracking-[-0.72px] whitespace-nowrap">03</p>
                        </div>
                        <div class="flex-1 flex flex-col gap-[14px] items-start">
                            <p class="font-medium leading-[26px] md:leading-[30px] text-[#0d0d0d] text-[18px] md:text-[20px] w-full">How do weight loss injections work?</p>
                            <div class="faq-answer">
                                <p class="leading-[24px] md:leading-[28px] text-[#595959] text-[16px] md:text-[18px] w-full">Weight loss injections work by targeting specific receptors in your brain that control appetite and food intake. They help you feel fuller for longer and reduce cravings.</p>
                            </div>
                        </div>
                    </div>
                    <div class="h-[1px] bg-[#dbdbdb] w-full my-[12px]"></div>
                </div>
                <div class="faq-item w-full">
                    <div class="faq-question flex gap-[16px] md:gap-[24px] items-center px-[20px] md:px-[30px] cursor-pointer transition-all duration-300 py-[8px]" data-index="3">
                        <div class="flex flex-col items-center justify-center rounded-[50px] w-[60px] md:w-[80px] min-w-[60px] md:min-w-[80px]">
                            <p class="font-semibold leading-[36px] md:leading-[44px] text-[#0d0d0d] text-[28px] md:text-[36px] text-center tracking-[-0.56px] md:tracking-[-0.72px] whitespace-nowrap">04</p>
                        </div>
                        <div class="flex-1 flex flex-col gap-[14px] items-start">
                            <p class="font-medium leading-[26px] md:leading-[30px] text-[#0d0d0d] text-[18px] md:text-[20px] w-full">Are weight loss injections effective for losing weight?</p>
                            <div class="faq-answer">
                                <p class="leading-[24px] md:leading-[28px] text-[#595959] text-[16px] md:text-[18px] w-full">Yes, clinical studies have shown that weight loss injections can be very effective when combined with a healthy diet and regular exercise. Most people see significant results within the first few months.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Blog Section -->
    <div id="blog" class="w-full flex flex-col items-center justify-center">
        <div class="flex flex-col gap-[40px] lg:gap-[50px] items-center overflow-clip px-[20px] md:px-[40px] lg:px-[15px] py-[60px] md:py-[80px] lg:py-[100px] w-full">
            <div class="flex flex-col gap-[10px] items-start w-full max-w-[1120px]">
                <div class="bg-[#afd136] flex gap-[10px] items-center justify-center overflow-clip px-[8px] py-[6px] rounded-[6px]">
                    <p class="font-semibold leading-[24px] md:leading-[30px] text-[18px] md:text-[20px] text-white whitespace-nowrap">Health Hub & News</p>
                </div>
                <p class="font-medium leading-[40px] md:leading-[50px] lg:leading-[60px] text-[#0d0d0d] text-[32px] md:text-[40px] lg:text-[48px] tracking-[-0.64px] md:tracking-[-0.8px] lg:tracking-[-0.96px]">Weight loss: what you need to know</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-[24px] md:gap-[28px] lg:gap-[32px] w-full max-w-[1120px]">
                <a href="#blog" class="border border-[#d6d6d6] rounded-[20px] overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="flex flex-col h-full items-center w-full">
                        <div class="h-[200px] md:h-[220px] lg:h-[250px] w-full overflow-hidden">
                            <img src="/new/images/post-image-1.png" alt="Blog Post" class="w-full h-full object-cover" />
                        </div>
                        <div class="border-t border-[#d6d6d6] flex flex-col gap-[24px] md:gap-[30px] items-start px-[24px] md:px-[30px] py-[30px] md:py-[40px] w-full">
                            <p class="font-medium leading-[28px] md:leading-[32px] text-[#0d0d0d] text-[20px] md:text-[24px] w-full">Mounjaro Journey – Day 16: "It Just Works"</p>
                            <div class="flex items-center justify-between w-full">
                                <p class="font-medium leading-[24px] text-[#616161] text-[16px] whitespace-nowrap">Sep 4, 2025</p>
                                <svg class="w-[20px] h-[20px]" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7.5 5L12.5 10L7.5 15" stroke="#0d0d0d" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>
                <a href="#blog" class="border border-[#d6d6d6] rounded-[20px] overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="flex flex-col h-full items-center w-full">
                        <div class="h-[200px] md:h-[220px] lg:h-[250px] w-full overflow-hidden">
                            <img src="/new/post-image-2.png" alt="Blog Post" class="w-full h-full object-cover" />
                        </div>
                        <div class="border-t border-[#d6d6d6] flex flex-col gap-[24px] md:gap-[30px] items-start px-[24px] md:px-[30px] py-[30px] md:py-[40px] w-full">
                            <p class="font-medium leading-[28px] md:leading-[32px] text-[#0d0d0d] text-[20px] md:text-[24px] w-full">Mounjaro Journey – Day 16: "It Just Works"</p>
                            <div class="flex items-center justify-between w-full">
                                <p class="font-medium leading-[24px] text-[#616161] text-[16px] whitespace-nowrap">Sep 4, 2025</p>
                                <svg class="w-[20px] h-[20px]" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7.5 5L12.5 10L7.5 15" stroke="#0d0d0d" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>
                <a href="#blog" class="border border-[#d6d6d6] rounded-[20px] overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="flex flex-col h-full items-center w-full">
                        <div class="h-[200px] md:h-[220px] lg:h-[250px] w-full overflow-hidden">
                            <img src="/new/images/post-image-3.png" alt="Blog Post" class="w-full h-full object-cover" />
                        </div>
                        <div class="border-t border-[#d6d6d6] flex flex-col gap-[24px] md:gap-[30px] items-start px-[24px] md:px-[30px] py-[30px] md:py-[40px] w-full">
                            <p class="font-medium leading-[28px] md:leading-[32px] text-[#0d0d0d] text-[20px] md:text-[24px] w-full">Mounjaro Journey – Day 16: "It Just Works"</p>
                            <div class="flex items-center justify-between w-full">
                                <p class="font-medium leading-[24px] text-[#616161] text-[16px] whitespace-nowrap">Sep 4, 2025</p>
                                <svg class="w-[20px] h-[20px]" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M7.5 5L12.5 10L7.5 15" stroke="#0d0d0d" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="w-full flex flex-col items-center justify-center">
        <div class="bg-white flex flex-col items-center justify-center px-[20px] md:px-[40px] lg:px-[15px] py-0 w-full">
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-[32px] lg:gap-0 px-0 py-[60px] md:py-[80px] lg:py-[100px] w-full max-w-[1120px]">
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

    <!-- Footer -->
    <footer id="contact" class="bg-[#324ea0] w-full flex flex-col items-center justify-center">
        <div class="flex flex-col gap-[40px] md:gap-[50px] items-center justify-center pb-0 pt-[40px] md:pt-[60px] lg:pt-[70px] px-[20px] md:px-[40px] lg:px-[15px] w-full">
            <div class="border-b border-[#d6d6d6] flex flex-col md:flex-row items-center md:items-center justify-between gap-[24px] md:gap-0 px-0 py-[24px] w-full max-w-[1120px]">
                <a href="#hero" class="h-[80px] md:h-[90px] lg:h-[99.999px] w-auto no-glow">
                    <img src="/new/images/logo-2.svg" alt="GetWeightLoss" class="h-full w-auto object-contain" />
                </a>
                <div class="flex flex-col sm:flex-row gap-[12px] sm:gap-[20px] items-stretch sm:items-center w-full sm:w-auto">
                    <a href="#pricing" class="bg-[#afd136] flex gap-[6px] items-center justify-center overflow-clip px-[16px] py-[12px] rounded-[100px] shadow-[0px_1px_2px_0px_rgba(16,24,40,0.05)] btn-glow">
                        <p class="font-semibold leading-[24px] text-[16px] text-black whitespace-nowrap">Book a Demo</p>
                    </a>
                    <a href="#contact" class="bg-[#fcfcfc] border border-[#d6d6d6] rounded-[100px] flex gap-[6px] items-center justify-center overflow-clip px-[18px] py-[12px] btn-glow">
                        <p class="font-semibold leading-[24px] text-[#0d0d0d] text-[16px] whitespace-nowrap">Contact Us</p>
                    </a>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-[32px] md:gap-[24px] items-start w-full max-w-[1120px]">
                <div class="flex flex-col gap-[16px] items-start">
                    <p class="font-semibold leading-[28px] text-[#afd136] text-[18px] w-full">Company</p>
                    <div class="flex flex-col gap-[12px] items-start leading-[24px] text-[14px] md:text-[16px] text-white w-full">
                        <a href="#about" class="hover:text-[#afd136] transition-colors">About us</a>
                        <a href="#features" class="hover:text-[#afd136] transition-colors">Services</a>
                        <a href="#about" class="hover:text-[#afd136] transition-colors">Team</a>
                        <a href="#about" class="hover:text-[#afd136] transition-colors">Project</a>
                        <a href="#blog" class="hover:text-[#afd136] transition-colors">Blog</a>
                        <a href="#pricing" class="hover:text-[#afd136] transition-colors">Pricing</a>
                    </div>
                </div>
                <div class="flex flex-col gap-[16px] items-start">
                    <p class="font-semibold leading-[28px] text-[#afd136] text-[18px] w-full">Treatments</p>
                    <div class="flex flex-col gap-[12px] items-start leading-[24px] text-[14px] md:text-[16px] text-white w-full">
                        <a href="#injections" class="hover:text-[#afd136] transition-colors">Wegovy</a>
                        <a href="#injections" class="hover:text-[#afd136] transition-colors">Mounjaro</a>
                        <a href="#injections" class="hover:text-[#afd136] transition-colors">Ozempic</a>
                        <a href="#features" class="hover:text-[#afd136] transition-colors">Blood Tests</a>
                    </div>
                </div>
                <div class="flex flex-col gap-[16px] items-start">
                    <p class="font-semibold leading-[28px] text-[#afd136] text-[18px] w-full">Quick Link</p>
                    <div class="flex flex-col gap-[12px] items-start leading-[24px] text-[14px] md:text-[16px] text-white w-full">
                        <a href="#about" class="hover:text-[#afd136] transition-colors">Why Choose Us?</a>
                        <a href="#pricing" class="hover:text-[#afd136] transition-colors">Pricing Plan</a>
                        <a href="#blog" class="hover:text-[#afd136] transition-colors">News & Articles</a>
                        <a href="#faq" class="hover:text-[#afd136] transition-colors">FAQ's</a>
                        <a href="#pricing" class="hover:text-[#afd136] transition-colors">Appointment</a>
                        <a href="#testimonials" class="hover:text-[#afd136] transition-colors">Patients</a>
                    </div>
                </div>
                <div class="flex flex-col gap-[16px] items-start">
                    <p class="font-semibold leading-[28px] text-[#afd136] text-[18px] w-full">Social</p>
                    <div class="flex flex-col gap-[12px] items-start leading-[24px] text-[14px] md:text-[16px] text-white w-full">
                        <a href="#contact" class="hover:text-[#afd136] transition-colors">Twitter</a>
                        <a href="#contact" class="hover:text-[#afd136] transition-colors">LinkedIn</a>
                        <a href="#contact" class="hover:text-[#afd136] transition-colors">Facebook</a>
                        <a href="#contact" class="hover:text-[#afd136] transition-colors">GitHub</a>
                        <a href="#contact" class="hover:text-[#afd136] transition-colors">AngelList</a>
                        <a href="#contact" class="hover:text-[#afd136] transition-colors">Dribbble</a>
                    </div>
                </div>
                <div class="flex flex-col gap-[16px] items-start">
                    <p class="font-semibold leading-[28px] text-[#afd136] text-[18px] w-full">Legal</p>
                    <div class="flex flex-col gap-[12px] items-start leading-[24px] text-[14px] md:text-[16px] text-white w-full">
                        <a href="#terms" class="hover:text-[#afd136] transition-colors">Terms</a>
                        <a href="#privacy" class="hover:text-[#afd136] transition-colors">Privacy</a>
                        <a href="#contact" class="hover:text-[#afd136] transition-colors">Contact</a>
                        <a href="#licenses" class="hover:text-[#afd136] transition-colors">Licenses</a>
                        <a href="#coming-soon" class="hover:text-[#afd136] transition-colors">Coming Soon</a>
                        <a href="#404" class="hover:text-[#afd136] transition-colors">404</a>
                    </div>
                </div>
            </div>
            <div class="border-t border-[#d6d6d6] flex flex-col md:flex-row items-center justify-between gap-[24px] md:gap-0 px-0 py-[24px] w-full max-w-[1120px]">
                <p class="font-medium leading-[24px] md:leading-[32px] text-[12px] text-white text-center md:text-left">Copyright @ 2025 GetWeightLoss, All rights reserved.</p>
                <div class="flex gap-[10px] md:gap-[14px] items-center">
                    <a href="https://facebook.com" target="_blank" rel="noopener noreferrer">
                        <img src="/new/images/footer-facebook.svg" alt="Facebook" class="w-[50px] h-[50px] md:w-[60px] md:h-[60px] hover:opacity-80 transition-opacity" />
                    </a>
                    <a href="https://instagram.com" target="_blank" rel="noopener noreferrer">
                        <img src="/new/images/footer-instagram.svg" alt="Instagram" class="w-[50px] h-[50px] md:w-[60px] md:h-[60px] hover:opacity-80 transition-opacity" />
                    </a>
                    <a href="https://twitter.com" target="_blank" rel="noopener noreferrer">
                        <img src="/new/images/footer-x.svg" alt="X (Twitter)" class="w-[50px] h-[50px] md:w-[60px] md:h-[60px] hover:opacity-80 transition-opacity" />
                    </a>
                    <a href="https://linkedin.com" target="_blank" rel="noopener noreferrer">
                        <img src="/new/images/footer-linkedin.svg" alt="LinkedIn" class="w-[50px] h-[50px] md:w-[60px] md:h-[60px] hover:opacity-80 transition-opacity" />
                    </a>
                </div>
            </div>
        </div>
    </footer>
</div>

<script>
    // Mobile Menu Toggle
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const closeMobileMenuBtn = document.getElementById('closeMobileMenuBtn');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
    const mobileMenu = mobileMenuOverlay.querySelector('.mobile-menu');
    const mobileBackdrop = mobileMenuOverlay.querySelector('.mobile-backdrop');
    const mobileMenuLinks = document.querySelectorAll('.mobile-menu-link');

    function openMobileMenu() {
        mobileMenuOverlay.classList.remove('hidden');
        setTimeout(() => {
            mobileMenu.classList.add('active');
            mobileBackdrop.classList.add('active');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeMobileMenu() {
        mobileMenu.classList.remove('active');
        mobileBackdrop.classList.remove('active');
        setTimeout(() => {
            mobileMenuOverlay.classList.add('hidden');
        }, 300);
        document.body.style.overflow = '';
    }

    mobileMenuBtn.addEventListener('click', openMobileMenu);
    closeMobileMenuBtn.addEventListener('click', closeMobileMenu);
    mobileBackdrop.addEventListener('click', closeMobileMenu);

    mobileMenuLinks.forEach(link => {
        link.addEventListener('click', closeMobileMenu);
    });

    // FAQ Accordion
    const faqQuestions = document.querySelectorAll('.faq-question');

    faqQuestions.forEach(question => {
        question.addEventListener('click', () => {
            const answer = question.querySelector('.faq-answer');
            const isActive = answer.classList.contains('active');

            // Close all other FAQs
            document.querySelectorAll('.faq-answer').forEach(ans => {
                ans.classList.remove('active');
            });

            // Toggle current FAQ
            if (!isActive) {
                answer.classList.add('active');
            }
        });
    });

    // Testimonial Carousel (Desktop only)
    const carousel = document.getElementById('testimonialCarousel');
    if (carousel && window.innerWidth >= 1024) {
        let scrollPosition = 0;
        const scrollSpeed = 1;
        const cardWidth = 544 + 32; // card width + gap

        function autoScroll() {
            scrollPosition += scrollSpeed;
            carousel.style.transform = `translateX(-${scrollPosition}px)`;

            // Reset when first card is fully scrolled out
            if (scrollPosition >= cardWidth) {
                scrollPosition = 0;
                carousel.appendChild(carousel.firstElementChild);
                carousel.style.transform = `translateX(0)`;
            }

            requestAnimationFrame(autoScroll);
        }

        autoScroll();
    }
</script>
</body>
</html>
