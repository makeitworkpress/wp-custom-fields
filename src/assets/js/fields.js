/**
 * Executes Field modules
 */
// var colorpicker = require('./modules/colorpicker');
var button = require('./modules/button');
var location = require('./modules/location');
var media = require('./modules/media');
var select = require('./modules/select');
var slider = require('./modules/slider');

module.exports.init = function(framework) {
    button.init(framework);
    location.location(framework);
    media.media(framework);
    select.init(framework);   
    slider.slider(framework);   
};