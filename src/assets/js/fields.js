/**
 * Executes Field modules
 */
// var colorpicker = require('./modules/colorpicker');
var location = require('./modules/location');
var media = require('./modules/media');
var select = require('./modules/select');
var slider = require('./modules/slider');

module.exports.init = function(framework) {
    // colorpicker.colorpicker(framework);
    location.location(framework);
    media.media(framework);
    select.init(framework);   
    slider.slider(framework);   
};