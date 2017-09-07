/**
 * Our colorpicker module
 */
module.exports.colorpicker = function(framework) {
    
    var colorOptions = {
        palettes: true
    };
    
    jQuery(framework).find('.wp-custom-fields-colorpicker').alphaColorPicker();
    
}