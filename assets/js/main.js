(function($) {

    // Animated button
    $(window).on("scroll", function(){
        $('.shimmer-animation').each(function() {
            if( $(window).scrollTop() + $(window).height() - 68 >= $(this).offset().top ) {
                $(this).addClass('animated');
            }
        });
    });

    // Mobile menu
    $('.mobile-menu__toggle').click(function() {
        $(this).toggleClass('active');
        $('.menu').toggleClass('opened');
    });

    $('.menu ul li a').click(function() {
        $('.menu').removeClass('opened');
    });

    // Testimonials slider
    $('#testimonials_slider').owlCarousel({
        loop: true,
        nav: true,
        dots: true,
        navContainer: '#testimonials_slider_nav',
        responsive:{
            0:{
                items: 1,
                margin: 40,
            },
            576:{
                items: 1,
                margin: 40,
            },
            768:{
                items: 1,
                margin: 0,
            },
            992:{
                items: 2,
                margin: 0,
            },
        }
    })

    // Videos slider
    $('#videos_slider').owlCarousel({
        loop: true,
        nav: true,
        dots: true,
        navContainer: '#videos_slider_nav',
        responsive:{
            0:{
                items: 1,
                margin: 40,
            },
            576:{
                items: 1,
                margin: 40,
            },
            768:{
                items: 1,
                margin: 40,
            },
            992:{
                items: 2,
                margin: 40,
            },
            1200:{
                items: 2,
                margin: 52,
            },
        }
    })

    // Video inside bootstrap modal
    $(document).ready(function() {
        $('#youtubeVideo').on('hidden.bs.modal', function() {
            var $this = $(this).find('iframe'),
            tempSrc = $this.attr('src');
            $this.attr('src', "");
            $this.attr('src', tempSrc);
        });

        $('#html5Video').on('hidden.bs.modal', function() {
            var html5Video = document.getElementById("htmlVideo");
            if (html5Video != null) {
                html5Video.pause();
                html5Video.currentTime = 0;
            }
        });
    });

})(jQuery);