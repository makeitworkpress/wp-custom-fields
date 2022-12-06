/**
 * Our heading module, supporting collapsible sections within the customizer
 */
module.exports.init = function(framework) {

    var searchFields = jQuery(framework).find('.wpcf-icons-search');
    var iconNodes = {};

    jQuery(searchFields).on('input', function(event) {

        var fieldId = event.currentTarget.closest('.wpcf-field').dataset.id;
        var search = event.currentTarget.value;

        if( typeof iconNodes[fieldId] === 'undefined' ) {
            iconNodes[fieldId] = jQuery(event.currentTarget).closest('.wpcf-field-input').find('.wpcf-icon-list li');
        }

        for( var icon of iconNodes[fieldId] ) {
            // Reset visibility
            if( ! search ) {
                icon.classList.remove('hidden');
                continue;
            }

            // Hide non matching icons
            if( icon.dataset.icon.includes(search) ) {
                icon.classList.remove('hidden');
            } else {
                icon.classList.add('hidden');
            }
        }
    });
    
}