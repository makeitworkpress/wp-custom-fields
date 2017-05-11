/**
 * Executres Field modules
 */
var colorpicker = require('./modules/colorpicker');
var location = require('./modules/location');
var media = require('./modules/media');
var slider = require('./modules/slider');

module.exports.fields = function(framework) {
    colorpicker.colorpicker(framework);
    location.location(framework);
    media.media(framework);
    slider.slider(framework);   
};