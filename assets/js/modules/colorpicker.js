/**
 * Our colorpicker module
 */
module.exports.colorpicker = function(framework) {
    
    var colorOptions = {
        palettes: true
    };
    
    jQuery(framework).find('.divergent-colorpicker').alphaColorPicker();
    
}