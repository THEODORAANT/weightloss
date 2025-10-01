
$('.responsive').slick({
    dots: true,
    infinite: true,
    speed: 300,
    slidesToShow: 4,
    slidesToScroll: 4,
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 3,
          infinite: true,
          dots: true
        }
      },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 2
        }
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      }
      // You can unslick at a given breakpoint now by adding:
      // settings: "unslick"
      // instead of a settings object
    ]
  });




$('.responsiv').slick({
    dots: true,
    infinite: true,
    speed: 300,
    slidesToShow: 4,
    slidesToScroll: 4,
    autoplay: true,
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 3,
          infinite: true,
          dots: true
        }
      },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 2
        }
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      }
      // You can unslick at a given breakpoint now by adding:
      // settings: "unslick"
      // instead of a settings object
    ]
  });


$('.responsi').slick({
    dots: true,
    infinite: true,
    speed: 300,
    slidesToShow: 4,
    slidesToScroll: 4,
    // autoplay: true,
    responsive: [
      {
        breakpoint: 1024,
        settings: {
          slidesToShow: 3,
          slidesToScroll: 3,
          infinite: true,
          dots: true
        }
      },
      {
        breakpoint: 600,
        settings: {
          slidesToShow: 2,
          slidesToScroll: 2
        }
      },
      {
        breakpoint: 480,
        settings: {
          slidesToShow: 1,
          slidesToScroll: 1
        }
      }
      // You can unslick at a given breakpoint now by adding:
      // settings: "unslick"
      // instead of a settings object
    ]
  });



  document.addEventListener("DOMContentLoaded", function () {
      let oldPrice = document.querySelector(".old-price");
      let newPrice = document.querySelector(".new-price");
      oldPrice.style.textDecoration = "line-through";
      oldPrice.style.color = "gray";
      newPrice.style.fontWeight = "bold";
  });



document.addEventListener("DOMContentLoaded", function() {
  console.log("Weight Loss Journey Page Loaded");
});




$(document).ready(function(){
  $('.accordion-list > li > .answer').hide();
    
  $('.accordion-list > li').click(function() {
    if ($(this).hasClass("active")) {
      $(this).removeClass("active").find(".answer").slideUp();
    } else {
      $(".accordion-list > li.active .answer").slideUp();
      $(".accordion-list > li.active").removeClass("active");
      $(this).addClass("active").find(".answer").slideDown();
    }
    return false;
  });
  
});









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



      /////////////////===============header section What we treat ▼ section=================//////////

      document.getElementById('treatment-toggle').addEventListener('click', function () {
        /*  const scrollTop = window.scrollY;
          const scrollHeight = document.documentElement.scrollHeight;
          const clientHeight = window.innerHeight;

          if (scrollTop + clientHeight >= scrollHeight - 5) {
              window.scrollTo({ top: 0, behavior: "smooth" });

          }*/
          const dropdown = document.getElementById('dropdown-section');
          dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
      });

      document.querySelectorAll('#treatment-options li').forEach(item => {
          item.addEventListener('click', function () {
              document.querySelectorAll('.content-section').forEach(section => section.style.display = 'none');
              document.getElementById(this.getAttribute('data-target')).style.display = 'block';
          });
      });
      
      // for destop & mobile menu showing and hide
      
      // for destop & mobile menu showing and hide
      /////////////////===============header section What we treat ▼ section=================//////////
      
      
      // JavaScript for dynamic dropdown behavior
      // for button click on middle section showing
      
      document.addEventListener('DOMContentLoaded', () => {
          const treatmentLinks = document.querySelectorAll('.page_links');
          const contentSections = document.querySelectorAll('.content-section');
          const activeClass = 'active-link';
      
          // Initially hide all content sections
          contentSections.forEach(section => section.style.display = 'none');
      
          treatmentLinks.forEach(link => {
              link.addEventListener('click', () => {
                  // Remove active class from all links
                  treatmentLinks.forEach(l => l.classList.remove(activeClass));
                  // Add active class to the clicked link
                  link.classList.add(activeClass);
      
                  // Hide all content sections
                  contentSections.forEach(section => section.style.display = 'none');
      
                  // Show the corresponding content section
                  const targetSection = document.getElementById(link.dataset.target);
                  if (targetSection) {
                      targetSection.style.display = 'block';
                  }
              });
          });
      });
      
      // mobile responsive
      
      document.getElementById('check').addEventListener('click', function () {
          const dropdown1 = document.getElementById('dropdown-section');
          dropdown1.style.display = dropdown1.style.display === 'none' ? 'block' : 'none';
      });
      
      document.querySelectorAll('#treatment-options li').forEach(item => {
          item.addEventListener('click', function () {
              document.querySelectorAll('.content-section').forEach(section => section.style.display = 'none');
              document.getElementById(this.getAttribute('data-target')).style.display = 'block';
          });
      });
