(function ($) {
    $(function () {

      var aLazyLoad = new LazyLoad();
        $('.feed-slider-active').slick({
            centerMode: true,
            arrows: false,
            slidesToShow: 8,
            prevArrow: '<button class="slick-btn slick-prev slick-arrow"><svg fill="#000000" height="800px" width="800px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"  viewBox="0 0 330 330" xml:space="preserve"> <path id="XMLID_92_" d="M111.213,165.004L250.607,25.607c5.858-5.858,5.858-15.355,0-21.213c-5.858-5.858-15.355-5.858-21.213,0.001 l-150,150.004C76.58,157.211,75,161.026,75,165.004c0,3.979,1.581,7.794,4.394,10.607l150,149.996 C232.322,328.536,236.161,330,240,330s7.678-1.464,10.607-4.394c5.858-5.858,5.858-15.355,0-21.213L111.213,165.004z"/> </svg></button>',
            nextArrow: '<button class="slick-btn slick-next slick-arrow"><svg width="800px" height="800px" viewBox="-4.5 0 20 20" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><title>arrow_right [#336]</title>  <desc>Created with Sketch.</desc> <defs> </defs> <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"> <g id="Dribbble-Light-Preview" transform="translate(-305.000000, -6679.000000)" fill="#000000"> <g id="icons" transform="translate(56.000000, 160.000000)"> <path d="M249.365851,6538.70769 L249.365851,6538.70769 C249.770764,6539.09744 250.426289,6539.09744 250.830166,6538.70769 L259.393407,6530.44413 C260.202198,6529.66364 260.202198,6528.39747 259.393407,6527.61699 L250.768031,6519.29246 C250.367261,6518.90671 249.720021,6518.90172 249.314072,6519.28247 L249.314072,6519.28247 C248.899839,6519.67121 248.894661,6520.31179 249.302681,6520.70653 L257.196934,6528.32352 C257.601847,6528.71426 257.601847,6529.34685 257.196934,6529.73759 L249.365851,6537.29462 C248.960938,6537.68437 248.960938,6538.31795 249.365851,6538.70769" id="arrow_right-[#336]"></path></g> </g> </g> </svg></button>',
            responsive: [
              {
                breakpoint: 991,
                settings: {
                  centerMode: true,
                  slidesToShow: 4
                }
              },
              {
                breakpoint: 768,
                settings: {
                  centerMode: true,
                  slidesToShow: 3
                }
              },
              {
                breakpoint: 480,
                settings: {
                  centerMode: true,
                  slidesToShow: 1
                }
              }
            ]
          });

          $('.feed-slider-active').on('beforeChange', function(event, { slideCount: count }, currentSlide, nextSlide){
            var aLazyLoad = new LazyLoad();
          });
          $('.feed-slider-active').on('afterChange', function(event, slick, currentSlide, nextSlide){
            var aLazyLoad = new LazyLoad();
        });

        $('.feed-slider-active').on('setPosition', function(event, slick){
          var aLazyLoad = new LazyLoad();
      });


          if ($(".curreny-dropdown").length) {
            $(document).on("click", ".curreny-dropdown .dropdown-item", function (e) {
                e.preventDefault();

        
                if (!$(this).hasClass("active")) {
                  $(".curreny-dropdown .dropdown-item").removeClass("active");
               
                  $(this).addClass("active");
                  $(this)
                      .parents(".curreny-dropdown")
                      .find(".btn-currency")
                      .html($(this).find('span.flag').html());
              }


            });
        }
    });
})(jQuery)