/**
 * This script bundles all the modules from the WP_Custom_Fields Application
 */
'use strict';

var fields          = require('./fields');
var options         = require('./options');
var repeatable      = require('./modules/repeatable');
var tabs            = require('./modules/tabs');

window.wcfCodeMirror   = {}; // Contains all the global wcfCodeMirror instance

var init = function() {
    
    // Boot our fields
    fields.init('.wp-custom-fields-framework');    
    options.init('.wp-custom-fields-framework');
    repeatable.init('.wp-custom-fields-framework');
    tabs.init();
    
}

// Boot WP_Custom_Fields on Document Ready
jQuery(document).ready(init);