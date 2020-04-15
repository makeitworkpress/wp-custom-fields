/**
 * Functions for option pages
 */
module.exports.init = function(framework) {

    if( jQuery(framework).hasClass('wpcf-options-page') ) {

        var scrollHeader    = jQuery(framework).find('.wpcf-notifications'),
            scrollPosition  = 0,
            scrollWidth     = scrollHeader.width();

        jQuery(window).scroll( function() {

            scrollPosition = jQuery(window).scrollTop();

            if( scrollPosition > 50 ) {
                scrollHeader.width(scrollWidth);
                scrollHeader.closest('.wpcf-header').addClass('wpfc-header-scrolling');
            } else {
                scrollHeader.closest('.wpcf-header').removeClass('wpfc-header-scrolling');
            }

        } );
       
    }

};