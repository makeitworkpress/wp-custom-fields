/**
 * Our colorpicker module
 */
module.exports.init = function(framework) {
    
    // Execute if we do have select2 defined
//    if( typeof select2 !== "undefined" ) {
        
        // Regular selects
        jQuery('.divergent-select').select2();
        
        // Typography selects
        jQuery('.divergent-typography-fonts').select2({
            templateResult: formatState,
            templateSelection: formatState            
        });
        
//    }
    
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