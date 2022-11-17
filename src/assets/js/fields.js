/**
 * Executes Field modules
 * @todo Convert in a loop
 */
var button = require('./modules/button');
var code = require('./modules/code');
var colorpicker = require('./modules/colorpicker');
var datepicker = require('./modules/datepicker');
var heading = require('./modules/heading');
var location = require('./modules/location');
var media = require('./modules/media');
var select = require('./modules/select');
var slider = require('./modules/slider');

var dependency = require('./modules/dependency');

module.exports.init = function(framework) {

    // Fields that require JS
    button.init(framework);
    colorpicker.init(framework);
    code.init(framework);
    datepicker.init(framework);
    heading.init(framework);
    location.init(framework);
    media.init(framework);
    select.init(framework);   
    slider.init(framework); 

    // Dependent fields
    dependency.init(framework); 
    
};