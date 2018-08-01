/**
 * Executes Field modules
 * @todo Convert in a loop
 */
// var colorpicker = require('./modules/colorpicker');
var button = require('./modules/button');
var datepicker = require('./modules/datepicker');
var location = require('./modules/location');
var media = require('./modules/media');
var select = require('./modules/select');
var slider = require('./modules/slider');

module.exports.init = function(framework) {
    button.init(framework);
    datepicker.init(framework);
    location.location(framework);
    media.media(framework);
    select.init(framework);   
    slider.slider(framework);   
};