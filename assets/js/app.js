/**
 * This script bundles all the modules from the Divergent Application
 */
'use strict';

var fields      = require('./fields');
var repeatable  = require('./modules/repeatable');
var tabs        = require('./modules/tabs');

var init = function() {
    
    // Boot our fields
    fields.init('.divergent-framework');    
    repeatable.init('.divergent-framework');
    tabs.init();
    
}

// Boot Divergent on Document Ready
jQuery(document).ready(init);