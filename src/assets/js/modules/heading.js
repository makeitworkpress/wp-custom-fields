/**
 * Our heading module, supporting collapsible sections within the customizer
 */
module.exports.init = function(framework) {


    jQuery('.wpcf-heading-collapsible').each( function() {

        var collapsibleSections = jQuery(this).data('sections');

        // There are sections to collapse
        if( ! collapsibleSections ) {
            return;
        }

        collapsibleSections = collapsibleSections.split(',');

        // Hide on initiation
        collapsibleSections.forEach( function(element) {
            jQuery('li[id$="' + element + '"]').hide();
            jQuery('.wpcf-field.field-id-' + element).hide();
        } );

        // Toggle on click
        jQuery(this).click( function() {

            jQuery(this).toggleClass('active');
            
            collapsibleSections.forEach( function(element) {
                jQuery('li[id$="' + element + '"]').toggle();
                jQuery('.wpcf-field.field-id-' + element).toggle();
            } );

        } );

    });
    
}