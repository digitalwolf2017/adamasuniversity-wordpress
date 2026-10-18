/**
 * Custom Header Scroll & Interactive Animations
 * Adamas University
 */
(function ($) {
    'use strict';

    function initStickyHeader() {
        var $masthead = $('#masthead');
        if (!$masthead.length) {
            return;
        }

        var scrollThreshold = 40;

        function checkScroll() {
            var currentScroll = $(window).scrollTop() || $(document).scrollTop();
            if (currentScroll > scrollThreshold) {
                if (!$masthead.hasClass('is-scrolled')) {
                    $masthead.addClass('is-scrolled');
                    $('body').addClass('has-scrolled-header');
                }
            } else {
                if ($masthead.hasClass('is-scrolled')) {
                    $masthead.removeClass('is-scrolled');
                    $('body').removeClass('has-scrolled-header');
                }
            }
        }

        // Run on load and on scroll
        checkScroll();
        $(window).on('scroll.stickyHeader', checkScroll);
    }

    $(document).ready(function () {
        initStickyHeader();
    });

})(jQuery);
