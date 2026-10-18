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

    /**
     * Prevents background page from scrolling while video modal / lightbox is open
     */
    function initLightboxScrollLock() {
        var scrollPosition = 0;

        function isLightboxActive() {
            var $modal = $('.elementor-lightbox, .dialog-widget.dialog-lightbox-widget');
            if (!$modal.length) return false;
            // Check if any modal is visible and not display:none
            var visible = false;
            $modal.each(function () {
                if ($(this).is(':visible') && $(this).css('display') !== 'none' && $(this).css('visibility') !== 'hidden') {
                    visible = true;
                    return false; // break
                }
            });
            return visible;
        }

        function lockScroll() {
            if (!$('body').hasClass('elementor-lightbox--open')) {
                scrollPosition = window.pageYOffset || document.documentElement.scrollTop;
                $('body, html').addClass('elementor-lightbox--open');
            }
        }

        function unlockScroll() {
            if ($('body').hasClass('elementor-lightbox--open')) {
                $('body, html').removeClass('elementor-lightbox--open');
            }
        }

        function updateScrollLockState() {
            if (isLightboxActive()) {
                lockScroll();
            } else {
                unlockScroll();
            }
        }

        // MutationObserver to detect when lightbox appears or changes visibility
        var observer = new MutationObserver(function () {
            updateScrollLockState();
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['style', 'class', 'aria-hidden']
        });

        // Click on close button or backdrop
        $(document).on('click', '.dialog-close-button, .elementor-lightbox', function (e) {
            if ($(e.target).closest('.dialog-close-button').length || $(e.target).hasClass('elementor-lightbox')) {
                setTimeout(updateScrollLockState, 100);
            }
        });

        // Escape key to close modal
        $(document).on('keydown', function (e) {
            if (e.key === 'Escape' || e.keyCode === 27) {
                setTimeout(updateScrollLockState, 100);
            }
        });
    }

    $(document).ready(function () {
        initStickyHeader();
        initLightboxScrollLock();
    });

})(jQuery);
