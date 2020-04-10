/**
 * Our colorpicker module - because we included the alpha colorpicker script, this is already included by default
 */
module.exports.init = function(framework) {
    
    var colorOptions = {
        palettes: true
    };
    
    jQuery(framework).find('.wpcf-colorpicker').alphaColorPicker();
    
}