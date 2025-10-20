 <!DOCTYPE html>
 <html lang="en">
     <head>
         <meta charset="UTF-8" />
         <meta name="viewport" content="width=device-width, initial-scale=1.0" />
         <title>Get Weight Loss</title>
         <link rel="shortcut icon" href="/asset/logo-final.png" type="image/x-icon">
         <link rel="preconnect" href="https://fonts.googleapis.com" />
         <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin /><link
           href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Poppins:wght@700;800&display=swap"rel="stylesheet"/>
         <link rel="stylesheet" href="/css/bootstrap.min.css" />
         <link rel="stylesheet" href="/css/custom-font.css" />
         <link href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css" rel="stylesheet" />
         <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
         <link rel="stylesheet" href="/css/style.css">
         <link rel="stylesheet" href="/css/style-2.css">
       </head>
 <body>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Get Weight Loss</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Poppins:wght@700;800&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="/css/bootstrap.min.css" />
    <link rel="stylesheet" href="/css/custom-font.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="/css/style.css">
</head>

<body>

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
        <!-- <div id="treatment-toggle" class="what_treat">
             Menu <i class="fas fa-chevron-down"></i>
       </div>-->
       <!-- <div class="logo">
        <a href="/home"><img src="/asset/logo-final.png" style="height: 300px; width: 300px;" alt=""></a>
       </div>-->
       <nav class="navbar">
           <div style="margin-top:10px" >        <a href="/home"><img src="/asset/logo-final.png" style="height: 300px; width: 300px;" alt=""></a>
</div>
           <ul style="margin-top:10px" class="nav-links">
               <li><a href="/">Home</a></li>
               <li class="dropdown">
                   <a href="#weight-loss">Weight Loss</a>
                   <ul class="dropdown-menu">
                       <li><a href="/medications/mounjaro">Mounjaro</a></li>
                       <li><a href="/medications/ozempic">Ozempic</a></li>
                       <li><a href="/medications/wegovy">Wegovy</a></li>
                       <li><a style="text-decoration: none;" href="/knowledge/review-answers">Results</a></li>

                   </ul>
               </li>
               <li class="dropdown">
                   <a href="#knowledge">Knowledge</a>
                   <ul class="dropdown-menu">
                       <li><a href="/knowledge/nutrition">Nutrition</a></li>
                       <li><a href="/knowledge/exercise">Exercise</a></li>
                       <li><a href="/knowledge/stress">Stress</a></li>
                       <li><a href="/knowledge/sleep">Sleep</a></li>
                   </ul>
               </li>
               <li><a href="/blog">Health Hub</a></li>
               <li><a href="/about-us">About Us</a></li>
           </ul>
           <div class="hamburger">
               <span></span>
               <span></span>
               <span></span>
           </div>
             <div id="user-icon" class="user_account">
                 <a href="/client">     <i class="fas fa-user"></i></a>
                  </div>
       </nav>

   </header>
   <!--/////////////=============What we treat main section start////////////
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
                   <li class="page_links" data-target="health-advice"><a href="all-treatments">All Treatments</a></li>
               </ul>

               <div class="meet_getweightloss">
                 <div class="meet_title mt-md-4 mb-md-3">
                     <h6 class="treatment_title">Resources & Information</h6>
                 </div>
                 <div class="meet_content">
                     <ul>
                         <a style="text-decoration: none;" href="/blog"><li>Health Hub</li></a>
                        <a  style="text-decoration: none;" href="/contact-us"> <li>Help & support</li></a>
                       <a style="text-decoration: none;"  href="/about-us"><li>About Us</li></a>
                         <!-- <li><a href="#">Careers</a></li>
                         <li><a href="#">Reviews</a></li>
                         <li><a href="#">Media enquiries</a></li>
                         <li><a href="#">Clinical research</a></li>
                         <li><a href="#">Experts</a></li>
                     </ul>
                 </div>
                  <div class="social_icons_section">
                     <a href="#"><i class="fa-brands fa-square-facebook"></i></a>
                     <a href="#"><i class="fa-brands fa-instagram"></i></a>
                     <a href="#"><i class="fa-brands fa-square-twitter"></i></a>
                  </div>
             </div>
           </div>


           <div class="col-md-4" id="dynamic-content">
               <section id="weight-loss" class="content-section my_content">
                   <div class="multiple_links">
                   <h5 class="sub_link"><a href="#">Weight Loss</a></h5>
                     <ul>
                       <a style="text-decoration: none;" href="/medications/mounjaro"><li class="list_link">Mounjaro</li></a>
                       <a style="text-decoration: none;" href="/medications/ozempic"><li class="list_link">Ozempic </li></a>
                       <a style="text-decoration: none;" href="/medications/wegovy"><li class="list_link">Wegovy</li></a>
                     </ul>
                   </div>
                   <div class="multiple_links">
                     <h6 class="sub_link"><a style="text-decoration: none;" href="/knowledge/review-answers">Results</a></h6>
                     <ul>
                     </ul>
                   </div>
                   <div class="multiple_links">
                    <h6 class="sub_link"><a href="#">Knowledge</a></h6>
                    <ul>
                   <a style="text-decoration: none;" href="/knowledge/nutrition"><li class="list_link">Nutrition</li></a>
                      <a style="text-decoration: none;" href="/knowledge/exercise"><li class="list_link">Exercise</li></a>
                      <a style="text-decoration: none;" href="/knowledge/stress"><li class="list_link">Stress</li></a>
                     <a style="text-decoration: none;" href="/knowledge/sleep"><li class="list_link">Sleep</li></a>
                    </ul>
                  </div>
-->

               </section>





    <!-- ==================================================================coding Start====================================================================================================== -->




    <!-- ===================================================================header section Start=============================================================================================  -->




   <!--/////////////==========What we treat main section END==========//////////////-->

    <!--================================================================== Weekly injection section start ============================================================================================================-->

 <style>
/* Base styles */
.my_header {
    width: 100%;
    background: white;
    position: fixed;
    top: 0;
    left: 0;
    padding: 10px 20px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

/* Hide menu icons initially */
#btn, #cancel {
    font-size: 24px;
    color: #333;
    cursor: pointer;
    display: none;
}

/* Mobile menu button visibility */
#check {
    display: none;
}

label[for="check"] {
    display: none;
    cursor: pointer;
    position: absolute;
    right: 20px;
    top: 20px;
}

/* Responsive styles for mobile */
@media (max-width: 768px) {
    .navbar {
        flex-direction: column;
        align-items: flex-start;
        padding: 0;
    }

    .navbar > div:first-child {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .nav-links {
        flex-direction: column;
        width: 100%;
        display: none;
        margin: 0;
        padding: 0;
    }

    .nav-links li {
        width: 100%;
        padding: 10px 20px;
        border-top: 1px solid #eee;
    }

    .nav-links li a {
        display: block;
        width: 100%;
    }

    #check:checked ~ nav .nav-links {
        display: flex;
    }

    label[for="check"] {
        display: block;
    }

    #btn {
        display: inline;
    }

    #check:checked + label #btn {
        display: none;
    }

    #check:checked + label #cancel {
        display: inline;
    }

    #cancel {
        display: none;
    }

    .dropdown-menu {
        display: none;
        flex-direction: column;
        background: #f9f9f9;
        padding-left: 20px;
    }

    .dropdown:hover .dropdown-menu {
        display: block;
    }

    .user_account {
        position: absolute;
        top: 20px;
        left: 20px;
    }

    .logo img {
        height: 80px !important;
        width: auto !important;
    }
}


     .navbar {
width: 100%;
     }

     .logo {
         color: #fff;
         font-size: 24px;
         font-weight: bold;
     }

     .nav-links {
         list-style: none;
         display: flex;
         flex-wrap: wrap;
         align-items: center;
         justify-content: flex-end;
     }

     .nav-links li {
         position: relative;
     }

     .nav-links a {
         color: #000;
         padding: 10px 15px;
         text-decoration: none;
         display: block;
     }

     .nav-links a:hover {
        /*background-color: #575757;*/
         color: #0d6efd;
     }

     .dropdown-menu {
         display: none;
         position: absolute;
         /*background-color: #bad630;*/
         top: 100%;
         left: 0;
         min-width: 150px;
         z-index: 1000;
     }

     .dropdown-menu li {
         width: 100%;
     }

     .dropdown:hover .dropdown-menu {
         display: block;
     }

     .hamburger {
         display: none;
         flex-direction: column;
         cursor: pointer;
     }

     .hamburger span {
         height: 3px;
         width: 25px;
         background: #fff;
         margin: 4px 0;
         border-radius: 2px;
     }

     @media (max-width: 768px) {
         .nav-links {
             flex-direction: column;
             display: none;
             width: 100%;
             background-color: #333;
         }

         .nav-links.active {
             display: flex;
         }

         .hamburger {
             display: flex;
             position: absolute;
             right: 20px;
             top: 15px;
         }

         .dropdown:hover .dropdown-menu {
             position: static;
         }
     }

 </style>
