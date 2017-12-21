/**
 * Our colorpicker module
 */
module.exports.init = function(framework) {
    
    // Execute if we do have select2 defined
    if( jQuery.fn.select2 ) {
        
        // Regular selects
        jQuery('.wp-custom-fields-select').select2();
        
        // Typography selects
        jQuery('.wp-custom-fields-typography-fonts').select2({
            templateResult: formatState,
            templateSelection: formatState            
        });
        
    }
    
}

// Formats a state for the select2 toolbox
var formatState = function(state) {
    if ( ! state.id ) { 
        return state.text; 
    }
    
    var newState = jQuery(
        '<img src="' + state.element.dataset.display + '" class="img-flag" />'
    );
    
    return newState; 
    
}