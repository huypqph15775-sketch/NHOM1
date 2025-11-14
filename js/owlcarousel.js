        // Product Details Pic Slider
        $(document).ready(function () {
            $(".small-img-group").owlCarousel();
        });
        $(".small-img-group").owlCarousel({
            loop: true,
            margin: 10,
            items: 4,
            dots: true,
            smartSpeed: 1200,
            autoHeight: false,
            autoplay: true
        });
    
    //owl-carousel-banner
    $(document).ready(function () {
        $(".owl-carousel-banner").owlCarousel();
    });

    $('.owl-carousel-banner').owlCarousel({
        loop: true,   
        margin: 10,
        nav: true,     
        dots: false,
        autoplay:true,
        navText: [
            "<i class='fas fa-angle-left'></i>",
            "<i class='fas fa-angle-right'></i>"
        ],
        responsive: {
            0: {
                items: 1
            },
            600:{
                items: 2
            },
            900: {
                items: 3
            }
        }
    })

    //owl-carousel-hotsale
    $(document).ready(function () {
        $(".owl-carousel").owlCarousel();
    });

    $('.owl-carousel').owlCarousel({
        loop: true,   
        margin: 10,
        nav: true, 
        navText: [
            "<i class='fas fa-angle-left'></i>",
            "<i class='fas fa-angle-right'></i>"
        ],
        responsive: {
            0: {
                items: 1
            },
            600: {
                items: 2
            },
            780: {
                items: 3
            },
            1000: {
                items: 4
            },
            1400: {
                items: 5
            }
        }
    })
  
