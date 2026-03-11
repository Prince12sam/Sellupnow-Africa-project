<script>
    function slickSliderConfiguration() {
        let global = document.querySelectorAll('.global-slick-init');
        global.forEach(function (element, index){
            let parentBoxWidth = element.clientWidth;
            let childCount = element.querySelectorAll('.category-slider-item, .testimonial-item')?.length ?? 0;
            let childItemWidth = element.querySelector('.category-slider-item, .testimonial-item')?.clientWidth ?? 0;
            if(childCount !== 0 && childItemWidth !== 0){
                if((childCount * childItemWidth) < parentBoxWidth){
                    let targetSwipeDiv = element.parentElement.parentElement.parentElement.querySelector('.testimonial-arrows');
                    targetSwipeDiv.classList.add('d-none');
                    targetSwipeDiv.parentElement.classList.remove('mt-5')
                }
            }
        })

        // Keep browse categories cards readable even if stale slider config sets too many desktop items.
        if (window.jQuery && window.jQuery.fn && window.jQuery.fn.slick) {
            window.jQuery('.exploreCategories .global-slick-init.slick-initialized').each(function () {
                let $slider = window.jQuery(this);
                let desktopSlides = parseInt($slider.attr('data-slidestoshow') || $slider.data('slidestoshow') || 5, 10);

                if (!Number.isFinite(desktopSlides) || desktopSlides < 1) {
                    desktopSlides = 5;
                }

                // Cap desktop slides so cards do not become too narrow.
                desktopSlides = Math.min(desktopSlides, 5);

                $slider.slick('slickSetOption', {
                    slidesToShow: desktopSlides,
                    responsive: [
                        { breakpoint: 1600, settings: { slidesToShow: Math.min(desktopSlides, 5) } },
                        { breakpoint: 1400, settings: { slidesToShow: 4 } },
                        { breakpoint: 1200, settings: { slidesToShow: 3 } },
                        { breakpoint: 991, settings: { slidesToShow: 3 } },
                        { breakpoint: 768, settings: { slidesToShow: 2 } },
                        { breakpoint: 576, settings: { slidesToShow: 2 } }
                    ]
                }, true);
            });
        }
    }
    window.addEventListener('load', slickSliderConfiguration,false);
    window.addEventListener('resize', slickSliderConfiguration,false);
</script>
