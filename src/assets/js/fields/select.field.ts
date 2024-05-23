/**
 * Our select field
 */
declare var jQuery;

export const SelectField = () => {

    // Execute if we do have select2 defined
    if( typeof jQuery.fn.select2 !== 'undefined' && jQuery.fn.select2 ) {
       
        // Regular selects
        jQuery('.wpcf-select-advanced').select2({});
        
        // Typography selects
        jQuery('.wpcf-typography-fonts').select2({
            templateResult: formatState,
            templateSelection: formatState            
        });
        
    }
    
};

/**
 *  Formats a state for the select2 toolbox, allowing us to add custom images
 */
const formatState = (state) => {
    if ( ! state.id ) { 
        return state.text; 
    }
    
    const newState = jQuery(
        '<img src="' + state.element.dataset.display + '" class="img-typography" />'
    );
    
    return newState; 
    
};