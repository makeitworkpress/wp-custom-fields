/**
 * This script bundles all the modules from the Divergent Application
 */
'use strict';

var fields      = require('./fields');
var repeatable  = require('./modules/repeatable');
var tabs        = require('./modules/tabs');

var init = function() {
    
    // Boot our fields
    fields.fields('.divergent-framework');
    repeatable.repeatable();
    tabs.tabs();
    
}

// Boot divergent
jQuery(document).ready(init);