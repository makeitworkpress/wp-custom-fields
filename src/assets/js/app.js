/**
 * This script bundles all the modules from the WP_Custom_Fields Application
 */
'use strict';

var fields      = require('./fields');
var repeatable  = require('./modules/repeatable');
var tabs        = require('./modules/tabs');

var init = function() {
    
    // Boot our fields
    fields.init('.wp-custom-fields-framework');    
    repeatable.init('.wp-custom-fields-framework');
    tabs.init();
    
}

// Boot WP_Custom_Fields on Document Ready
jQuery(document).ready(init);