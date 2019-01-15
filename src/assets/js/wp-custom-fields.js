(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
/**
 * This script bundles all the modules from the WP_Custom_Fields Application
 */
'use strict';

var fields          = require('./fields');
var repeatable      = require('./modules/repeatable');
var tabs            = require('./modules/tabs');

window.wcfCodeMirror   = {}; // Contains all the global wcfCodeMirror instance

var init = function() {
    
    // Boot our fields
    fields.init('.wp-custom-fields-framework');    
    repeatable.init('.wp-custom-fields-framework');
    tabs.init();
    
}

// Boot WP_Custom_Fields on Document Ready
jQuery(document).ready(init);
},{"./fields":2,"./modules/repeatable":8,"./modules/tabs":11}],2:[function(require,module,exports){
/**
 * Executes Field modules
 * @todo Convert in a loop
 */
// var colorpicker = require('./modules/colorpicker');
var button = require('./modules/button');
var datepicker = require('./modules/datepicker');
var code = require('./modules/code');
var location = require('./modules/location');
var media = require('./modules/media');
var select = require('./modules/select');
var slider = require('./modules/slider');

module.exports.init = function(framework) {
    button.init(framework);
    code.init(framework);
    datepicker.init(framework);
    location.init(framework);
    media.init(framework);
    select.init(framework);   
    slider.init(framework);   
};
},{"./modules/button":3,"./modules/code":4,"./modules/datepicker":5,"./modules/location":6,"./modules/media":7,"./modules/select":9,"./modules/slider":10}],3:[function(require,module,exports){
/**
 * Our button module, accepting custom ajax actions
 */
module.exports.init = function(framework) {
    
    jQuery('.wpcf-button').click( function(event) {

        event.preventDefault();

        var action  = jQuery(this).data('action'),
            data    = jQuery(this).data('data'),
            message = jQuery(this).data('message'),
            self    = this;

        if( ! action ) {
            return;
        }

        jQuery.ajax({
            beforeSend: function() {
                jQuery(self).addClass('wpcf-loading');
            },
            complete: function() {
                jQuery(self).removeClass('wpcf-loading');

                setTimeout( function() {
                    jQuery(self).next('.wpcf-button-message').fadeOut();
                }, 3000);

                setTimeout( function() {
                    jQuery(self).next('.wpcf-button-message').remove();
                }, 3500);                

            },
            data: {
                action: action,
                data: data, 
                nonce: wpcf.nonce
            },
            error: function(response) {
                if( wpcf.debug ) {
                    console.log(response);
                }
            },
            success: function(response) {

                if( wpcf.debug ) {
                    console.log(response);
                }
                
                if( message && typeof(response.data) !== 'undefined' ) {

                    var style = response.success ? 'updated' : 'error'; 

                    jQuery(self).after('<div class="wpcf-button-message ' + style + '"><p>' + response.data + '</p></div>');
                }

            },
            type: 'POST',
            url: wpcf.ajaxUrl
        });

    });
    
}
},{}],4:[function(require,module,exports){
/**
 * Our colorpicker module - because we included the alpha colorpicker script, this is already included by default
 */
module.exports.init = function(framework) {
 
    jQuery(framework).find('.wp-custom-fields-code-editor-value').each(function (index, node) {

        window.wcfCodeMirror[node.id] = CodeMirror.fromTextArea(node, {
                mode: node.dataset.mode,
                lineNumbers: true
        });

    });
    
};
},{}],5:[function(require,module,exports){
/**
 * Initializes our datepicker using the flatpickr library
 * @param {The class for the framework} framework 
 */
module.exports.init = function(framework) {

    if( jQuery.fn.flatpickr ) {

        var config = {
                altFormat: 'F j, Y',
                altInput: true,
                dateFormat: 'U',
                time_24hr: true,
                wrap: true
            },
            datePicker = jQuery(framework).find('.wp-custom-fields-datepicker'),
            propertyName,
            propertyValue;

        // Grab our custom properties. For a description of these properties, see the datepicker.php file in the fields folder.
        ['enable-time', 'alt-format', 'date-format', 'locale', 'max-date', 'min-date', 'mode', 'no-calendar', 'week-numbers'].forEach( function(value) {

            propertyValue = jQuery(datePicker).data(value);

            if( propertyValue ) {
                propertyName = value.replace( /-([a-z])/g, function (g) { return g[1].toUpperCase(); } );
                config[propertyName] = propertyValue;
            }

        });

        // Initializes the datepicker
        jQuery(datePicker).flatpickr(config);

    }

}
},{}],6:[function(require,module,exports){
/**
 * Our location field
 */
module.exports.init = function(framework) {
    
    jQuery(framework).find('.wp-custom-fields-location').each(function (index) {
        var searchInput = jQuery('.wp-custom-fields-map-search', this).get(0),
            mapCanvas = jQuery('.wp-custom-fields-map-canvas', this).get(0),
            latitude = jQuery('.latitude', this),
            longitude = jQuery('.longitude', this),
            city = jQuery('.city', this),
            zip = jQuery('.postal_code', this),
            street = jQuery('.street', this),
            number = jQuery('.number', this),
            latLng = new google.maps.LatLng(52.2129918, 5.2793703),
            zoom = 7;            

        // Map
        if (latitude.val().length > 0 && longitude.val().length > 0) {
            latLng = new google.maps.LatLng(latitude.val(), longitude.val());
            zoom = 15;
        }

        var mapOptions = {
                scrollwheel: false,
                center: latLng,
                zoom: zoom,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            },
            map = new google.maps.Map(mapCanvas, mapOptions),
            markerOptions = {
                map: map,
                draggable: false,
            },
            marker = new google.maps.Marker(markerOptions),
            autocomplete = new google.maps.places.Autocomplete(searchInput, {
                types: ['geocode']
            });

        if (latitude.val().length > 0 && longitude.val().length > 0) {
            marker.setPosition(latLng);
        }

        // Search
        autocomplete.bindTo('bounds', map);

        google.maps.event.addListener(autocomplete, 'place_changed', function() {
            var place = autocomplete.getPlace(),
                components = place.address_components;

            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);
            }

            marker.setPosition(place.geometry.location);
            latitude.val(place.geometry.location.lat());
            longitude.val(place.geometry.location.lng());

            if (components) {
                for (var i = 0; i < components.length; i++) {
                    var component = components[i],
                        types = component.types;

                    if (types.indexOf('street_number') != -1) {
                        number.val(component.long_name);
                    } else if (types.indexOf('route') != -1) {
                        street.val(component.long_name);
                    } else if (types.indexOf('locality') != -1) {
                        city.val(component.long_name);
                    } else if (types.indexOf('postal_code') != -1) {
                        zip.val(component.long_name);
                    }
                }
            }

        }); 

    });  
}
},{}],7:[function(require,module,exports){
/**
 * Our jquery UI slider
 */
module.exports.init = function(framework) {
    
    /**
     * Enables Uploading using the Media-Uploader
     */
    jQuery(framework).find('.wp-custom-fields-upload-wrapper').each(function (index) {

        // Define the buttons for this specific group
        var add_media = jQuery(this).find('.wp-custom-fields-upload-add'),
            remove_media = jQuery(this).find('.wp-custom-fields-upload-remove'),
            value_input = jQuery(this).find('.wp-custom-fields-upload-value'),
            title = jQuery(this).data('title'),
            type = jQuery(this).data('type'),
            button = jQuery(this).data('button'),
            multiple = jQuery(this).data('multiple'),
            add_wrap = jQuery(this).find('.wp-custom-fields-single-media.empty'),
            initator = this,
            frame;

        // Click function
        add_media.on('click', function (e) {

            e.preventDefault();

            // If the media frame already has been opened before, it can just be reopened.
            if (frame) {
                frame.open();
                return;
            }

            // Create the media frame.
            frame = wp.media.frames.frame = wp.media({



                // Determine the title for the modal window
                title: title,

                // Show only the provided types
                library: {
                    type: type
                },

                // Determine the submit button text
                button: {
                    text: button
                },

                // Can we select multiple or only one?
                multiple: multiple

            });

            // If media is selected, add the input value
            frame.on('select', function () {

                // Grab the selected attachment.
                var attachments = frame.state().get('selection').toJSON(),
                    attachment_ids = value_input.val(),
                    loop_counter = 0,
                    src;

                // We store the ids for each image
                attachments.forEach(function (attachment) {
                    attachment_ids += attachment.id + ',';

                    if( attachment.type === 'image') {
                        src = attachment.sizes.thumbnail.url;
                    } else {
                        src = attachment.icon;
                    }

                    add_wrap.before('<div class="wp-custom-fields-single-media" data-id="' + attachment.id + '"><img src="' + src + '" /><a href="#" class="wp-custom-fields-upload-remove"><i class="material-icons">clear</i></a></div>');
                });

                // Remove the , for single attachments
                if( ! multiple ) {
                    attachment_ids.replace(',', '');
                }

                value_input.val(attachment_ids);

            });

            // Open the media upload modal
            frame.open();

        });

        /**
         * Remove attachments
         */
        jQuery(this).on('click', '.wp-custom-fields-upload-remove', function (e) {
            e.preventDefault();

            var target = jQuery(this).closest('.wp-custom-fields-single-media'),
                target_id = target.data('id'),
                current_values = value_input.val(),
                new_values = current_values.replace(target_id + ',', '');

            target.remove();
            
            if( ! multiple )
                add_wrap.fadeIn();            

            value_input.val(new_values);

        });

    });
    
}
},{}],8:[function(require,module,exports){
/**
 * Our repeatable fields module
 */
var fields = require('./../fields');

module.exports.init = function(framework) {
    
    /**
     * Repeatable Groups 
     */
    jQuery('.wp-custom-fields-repeatable-add').on('click', function (e) {
        e.preventDefault();
        var codeNodes   = [],
            length      = jQuery(this).closest('.wp-custom-fields-repeatable-container').find('.wp-custom-fields-repeatable-group').length,
            group       = jQuery(this).closest('.wp-custom-fields-repeatable-container').find('.wp-custom-fields-repeatable-group').last();
            
        // Destroy our select2 instances
        jQuery('.wp-custom-fields-select').select2('destroy');

        // Destroy current codemirror instances
        jQuery(framework).find('.wp-custom-fields-code-editor-value').each(function (index, node) {

            if( typeof(window.wcfCodeMirror[node.id]) !== 'undefined' ) {
                window.wcfCodeMirror[node.id].toTextArea(node);

                codeNodes.push(node);
            }

        });       

        // Build our newgroup
        var newGroup = group.clone(true, true);
        
        // Clone the current group and replace the current keys by new ones. The length is always one bigger as the current array, so it matches the key for the new group.
        newGroup.html(function (i, oldGroup) {
            return oldGroup.replace(/\[\d+\]/g, '[' + length + ']').replace(/\_\d+\_/g, '_' + length + '_');
        }); 

        // Empty inputs in our  new group
        newGroup.find('input').val('');
        newGroup.find('textarea').val('');
        newGroup.find('option').attr('selected', false);        
                
        // Finally, insert the newGroup after the current group
        group.after(newGroup);

        // Redraw the fields within the group
        fields.init(newGroup);  
        
        // Reinitialize old codemirror groups
        codeNodes.forEach( function(node) {
            if( typeof(window.wcfCodeMirror[node.id]) !== 'undefined' ) {
                window.wcfCodeMirror[node.id] = CodeMirror.fromTextArea(node, {ode: node.dataset.mode, lineNumbers: true});
            }
        });
        
    });
    
    // Remove the container
    jQuery('.wp-custom-fields-repeatable-remove').on('click', function (e) {
        e.preventDefault();
        var length = jQuery(this).closest('.wp-custom-fields-repeatable-container').find('.wp-custom-fields-repeatable-group').length,
            group = jQuery(this).closest('.wp-custom-fields-repeatable-container').find('.wp-custom-fields-repeatable-group').last();
        
        // Keep the first group
        if (length > 1) {
            group.remove();
        }
    });
    
    // Open or close a group
    jQuery('body').on('click', '.wp-custom-fields-repeatable-toggle', function (e) {
        e.preventDefault();
        
        if( jQuery(this).find('i').text() === 'arrow_drop_down' ) {
            jQuery(this).find('i').text('arrow_drop_up');        
        } else if( jQuery(this).find('i').text() === 'arrow_drop_up' ) {
            jQuery(this).find('i').text('arrow_drop_down');    
        }
        jQuery(this).closest('.wp-custom-fields-repeatable-group').find('.wp-custom-fields-repeatable-fields').slideToggle('closed');
    });
    
}
},{"./../fields":2}],9:[function(require,module,exports){
/**
 * Our colorpicker module
 */
module.exports.init = function(framework) {

    // Execute if we do have select2 defined
    if( jQuery.fn.select2 ) {
       
        // Regular selects
        jQuery('.wp-custom-fields-select').select2();     
        
        // Typography selects
        jQuery('.wp-custom-fields-typography-fonts').select2({
            templateResult: formatState,
            templateSelection: formatState            
        });
        
    }
    
}

/**
 *  Formats a state for the select2 toolbox, allowing us to add custom images
 */
var formatState = function(state) {
    if ( ! state.id ) { 
        return state.text; 
    }
    
    var newState = jQuery(
        '<img src="' + state.element.dataset.display + '" class="img-typography" />'
    );
    
    return newState; 
    
}
},{}],10:[function(require,module,exports){
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
},{}],11:[function(require,module,exports){
module.exports.init = function() {
    
    // Click handler for our tabs
    jQuery(".wp-custom-fields-tabs a").click(function (e) {
        
        e.preventDefault();
        
        var activeTab = jQuery(this).attr("href"),
            section = activeTab.replace('#', ''),
            frame = jQuery(this).closest('.wp-custom-fields-framework').attr('id');
        
        // Change our active section
        jQuery('#wp_custom_fields_section_' + frame).val(section);
		
        // Remove current active classes
        jQuery(this).closest('.wp-custom-fields-framework').find(".wp-custom-fields-tabs a").removeClass("active");
        jQuery(this).closest('.wp-custom-fields-framework').find(".wp-custom-fields-section").removeClass("active");
        
        // Add active class to our new things
        jQuery(this).addClass("active");      
        jQuery(activeTab).addClass("active");

    });
 
}
},{}]},{},[1]);
