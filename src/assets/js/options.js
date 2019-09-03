/**
 * Functions for option pages
 */
module.exports.init = function(framework) {

    if( jQuery(framework).hasClass('wpcf-options-page') ) {

        var scrollHeader    = jQuery(framework).find('.wp-custom-fields-notifications'),
            scrollPosition  = 0,
            scrollWidth     = scrollHeader.width();

        jQuery(window).scroll( function() {

            scrollPosition = jQuery(window).scrollTop();

            if( scrollPosition > 50 ) {
                scrollHeader.width(scrollWidth);
                scrollHeader.closest('.wp-custom-fields-header').addClass('wpfc-header-scrolling');
            } else {
                scrollHeader.closest('.wp-custom-fields-header').removeClass('wpfc-header-scrolling');
            }

        } );
       
    }

};