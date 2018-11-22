/**
 * Our jquery UI slider
 */
module.exports.init = function(framework) {
    
    /**
     * Adds jQuery UI Sliders
     */
    jQuery(framework).find('.wp-custom-fields-slider').each(function (index) {
        var sliderTarget = jQuery(this).data('id'),
            sliderMin = jQuery(this).data('min'),
            sliderMax = jQuery(this).data('max'),
            sliderStep = jQuery(this).data('step'),
            sliderValue = jQuery(this).data('value');

        jQuery(this).slider({
            value: sliderValue,
            min: sliderMin,
            max: sliderMax,
            step: sliderStep,
            slide: function (event, ui) {
                jQuery('#' + sliderTarget).val(ui.value);
            }
        });

    });
    
}