(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
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
    fields.init('.wpcf-framework');    
    options.init('.wpcf-framework');
    repeatable.init('.wpcf-framework');
    tabs.init();
    
}

// Boot WP_Custom_Fields on Document Ready
jQuery(document).ready(init);
},{"./fields":2,"./modules/repeatable":11,"./modules/tabs":14,"./options":15}],2:[function(require,module,exports){
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
},{"./modules/button":3,"./modules/code":4,"./modules/colorpicker":5,"./modules/datepicker":6,"./modules/dependency":7,"./modules/heading":8,"./modules/location":9,"./modules/media":10,"./modules/select":12,"./modules/slider":13}],3:[function(require,module,exports){
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
 
    jQuery(framework).find('.wpcf-code-editor-value').each(function (index, node) {

        window.wcfCodeMirror[node.id] = CodeMirror.fromTextArea(node, {
                mode: node.dataset.mode,
                lineNumbers: true
        });

    });
    
};
},{}],5:[function(require,module,exports){
/**
 * Our colorpicker module - because we included the alpha colorpicker script, this is already included by default
 */
module.exports.init = function(framework) {
    
    jQuery(framework).find('.wpcf-colorpicker').wpColorPicker({
        palettes: true
    });
    
};
},{}],6:[function(require,module,exports){
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
            datePicker = jQuery(framework).find('.wpcf-datepicker'),
            propertyName,
            propertyValue;

        // Grab our custom properties. For a description of these properties, see the datepicker.php file in the fields folder.
        ['enable-time', 'alt-format', 'date-format', 'locale', 'max-date', 'min-date', 'mode', 'no-calendar', 'week-numbers'].forEach( function(attribute) {

            propertyValue = jQuery(datePicker).data(attribute);

            if( propertyValue ) {
                propertyName = value.replace( /-([a-z])/g, function (g) { return g[1].toUpperCase(); } );
                config[propertyName] = propertyValue;
            }

        });

        // Initializes the datepicker
        jQuery(datePicker).flatpickr(config);

    }

}
},{}],7:[function(require,module,exports){
/**
 * Our button module, accepting custom ajax actions
 */
module.exports = {

    // Initializes the dependency module
    init: function(framework) {

        var self = this;

        jQuery(framework).find('.wpcf-dependent-field').each( function(index, item) {
        
            // Values from our dependency field
            var field = jQuery(item).hasClass('wpcf-repeatable-field') ? jQuery(item).find('.wpcf-repeatable-field-input') : jQuery(item).find('.wpcf-field-input'),
                equation = jQuery(field).data('equation'),
                source = jQuery(field).data('source'),
                value = jQuery(field).data('value');

            if( ! equation || ! source || ! value ) {
                return;
            } 
            
            // Target fields
            var selector = jQuery(item).hasClass('wpcf-repeatable-field') ? '.wpcf-repeatable-group' : '.wpcf-fields',
                target = jQuery(item).closest(selector).find('.field-id-' + source),
                input = jQuery(target).find('input'),
                select = jQuery(target).find('select');
        
            // Select fields (only supports single select fields)
            if( select.length > 0 ) {
                jQuery(select).change( function() {
                    self.compare(this, item, equation, value);
                });
            }
            
            // Input fields (only supports simple input fields)
            if( input.length > 0 ) {
                jQuery(input).change( function(event) {
                    self.compare(this, item, equation, value);
                });   
            }
        
        });      

    },

    // Compares values
    compare: function(changedField, dependentField, equation, value) {
 
        var changedFieldValue = changedField.value;

        // Checkboxes
        if( changedField.type == 'checkbox') {
            if( changedField.checked && changedField.dataset.key == value ) {
                changedFieldValue = value; 
            } else if( ! changedField.checked && changedField.dataset.key == value ) {
                changedFieldValue = '';    
            } 
        }
        
        if( equation == '=' ) {
            if( changedFieldValue == value ) {
                jQuery(dependentField).addClass('active');
            } else {
                jQuery(dependentField).removeClass('active');    
            }
        }

        if( equation == '!=' ) {
            if( changedFieldValue != value ) {
                jQuery(dependentField).addClass('active');
            } else {
                jQuery(dependentField).removeClass('active');    
            }
        }

    } 

};
},{}],8:[function(require,module,exports){
/**
 * Our heading module, supporting collapsible sections within the customizer
 */
module.exports.init = function(framework) {


    jQuery('.wpcf-heading-collapsible').each( function() {

        var collapsibleSections = jQuery(this).data('sections');

        // There are sections to collapse
        if( ! collapsibleSections ) {
            return;
        }

        collapsibleSections = collapsibleSections.split(',');

        // Hide on initiation
        collapsibleSections.forEach( function(element) {
            jQuery('li[id$="' + element + '"]').hide();
            jQuery('.wpcf-field.field-id-' + element).hide();
        } );

        // Toggle on click
        jQuery(this).click( function() {

            jQuery(this).toggleClass('active');
            
            collapsibleSections.forEach( function(element) {
                jQuery('li[id$="' + element + '"]').toggle();
                jQuery('.wpcf-field.field-id-' + element).toggle();
            } );

        } );

    });
    
}
},{}],9:[function(require,module,exports){
/**
 * Our location field
 */
module.exports.init = function(framework) {
    
    jQuery(framework).find('.wpcf-location').each(function (index) {
        var searchInput = jQuery('.wpcf-map-search', this).get(0),
            mapCanvas = jQuery('.wpcf-map-canvas', this).get(0),
            latitude = jQuery('.latitude', this),
            longitude = jQuery('.longitude', this),
            city = jQuery('.city', this),
            zip = jQuery('.postal_code', this),
            street = jQuery('.street', this),
            number = jQuery('.number', this),
            latLng = new google.maps.LatLng(52.2129918, 5.2793703),
            zoom = 7;            

        // Map
        if( latitude.val() && longitude.val() ) {
            latLng = new google.maps.LatLng(latitude.val(), longitude.val());
            zoom = 15;
        }

        // Map Options
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
},{}],10:[function(require,module,exports){
/**
 * Our jquery UI slider
 */
module.exports.init = function(framework) {
    
    /**
     * Enables Uploading using the Media-Uploader
     */
    jQuery(framework).find('.wpcf-upload-wrapper').each(function (index) {

        // Define the buttons for this specific group
        var add_media = jQuery(this).find('.wpcf-upload-add'),
            add_wrap = jQuery(this).find('.wpcf-single-media.empty'),
            button = jQuery(this).data('button'),
            multiple = jQuery(this).data('multiple'),   
            title = jQuery(this).data('title'),
            type = jQuery(this).data('type'),         
            url = jQuery(this).data('url'),         
            value_input = jQuery(this).find('.wpcf-upload-value'),
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
            frame = wp.media({

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
                var attachments     = frame.state().get('selection').toJSON(),
                    attachment_ids  = value_input.val(),
                    urlWrapper      = '',
                    src;

                // We store the ids for each image
                attachments.forEach(function (attachment) {
                    attachment_ids += attachment.id + ',';

                    if( attachment.type === 'image') {
                        src = attachment.sizes.thumbnail.url;
                    } else {
                        src = attachment.icon;
                    }

                    // Return the url wrapper, if url is defined as a feature
                    if( url ) {
                        urlWrapper = '<div class="wpcf-media-url"><i class="material-icons">link</i><input type="text" value="' + attachment.url + '"></div>';
                    }

                    add_wrap.before('<div class="wpcf-single-media type-' + type + '" data-id="' + attachment.id + '"><img src="' + src + '" />' + urlWrapper + '<a href="#" class="wpcf-upload-remove"><i class="material-icons">clear</i></a></div>');
                
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
        jQuery(this).on('click', '.wpcf-upload-remove', function (e) {
            e.preventDefault();

            var target = jQuery(this).closest('.wpcf-single-media'),
                target_id = target.data('id'),
                current_values = value_input.val(),
                new_values = current_values.replace(target_id + ',', '');

            target.remove();
            
            if( ! multiple )
                add_wrap.fadeIn();            

            value_input.val(new_values);

        });

    });

    /**
     * Make media items sortable
     */
    jQuery('.wpcf-media').sortable({
        placeholder: "wpcf-media-highlight",
        update: function(event, ui) {
            var input = jQuery(this).closest('.wpcf-upload-wrapper').find('.wpcf-upload-value'), values = [];
            
            jQuery(this).find('.wpcf-single-media').each( function(index, node) {
                values.push(node.dataset.id);        
            } );

            input.val( values.join(',') );

        }
    });
    
};
},{}],11:[function(require,module,exports){
/**
 * Our repeatable fields module
 * @todo Rewrite this in a more efficient manner.
 */
var fields = require('./../fields');

module.exports.init = function(framework) {

    /**
     * Groups are sortable
     */
    jQuery('.wpcf-repeatable-groups').sortable({
        placeholder: 'wpcf-highlight',
        update: function( event, ui ) { 
            jQuery(this).find('.wpcf-repeatable-group').each( function(index, node) {
                jQuery(node).html( function(n, node) {
                    return node.replace(/\[\d+\]/g, '[' + index + ']').replace(/\_\d+\_/g, '_' + index + '_');
                });
            });
        }
    });
    
    /**
     * Repeatable Groups 
     */
    jQuery('.wpcf-repeatable-add').on('click', function (e) {
        e.preventDefault();
        var codeNodes   = [],
            length      = jQuery(this).closest('.wpcf-repeatable-container').find('.wpcf-repeatable-group').length,
            group       = jQuery(this).closest('.wpcf-repeatable-container').find('.wpcf-repeatable-group').last();
            
        // Destroy our select2 instances, if it is defined of course
        if( typeof jQuery.fn.select2 !== 'undefined' && jQuery.fn.select2 ) {
            jQuery('.wpcf-select').select2('destroy');
        }

        // Destroy current codemirror instances
        jQuery(framework).find('.wpcf-code-editor-value').each(function (index, node) {

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
        newGroup.find('.wpcf-single-media').not('.empty').remove(); // Removes the media from the cloned group   
                
        // Finally, insert the newGroup after the current group
        group.after(newGroup);

        // Redraw the fields within the group
        fields.init(newGroup);  
        
        // Reinitialize old codemirror groups
        codeNodes.forEach( function(node) {
            if( typeof(window.wcfCodeMirror[node.id]) !== 'undefined' ) {
                window.wcfCodeMirror[node.id] = CodeMirror.fromTextArea(node, {mode: node.dataset.mode, lineNumbers: true});
            }
        });
        
    });
    
    // Remove the latest group
    jQuery('.wpcf-repeatable-remove-latest').on('click', function (e) {
        e.preventDefault();
        var groupLength = jQuery(this).closest('.wpcf-repeatable-container').find('.wpcf-repeatable-group').length,
            group = jQuery(this).closest('.wpcf-repeatable-container').find('.wpcf-repeatable-group').last();
        
        // Keep the first group
        if( groupLength < 2 ) {
            return;
        }
        
        group.fadeOut();
        setTimeout( function() {
            group.remove();
        }, 500);

    });

    /**
     * Remove the current group
     * @todo Make this dry - a lot of overlap with some earlier functions
     */
    jQuery(document).on('click', '.wpcf-repeatable-remove-group', function(e) {

        console.log(e);

        e.preventDefault();
        var groupLength = jQuery(this).closest('.wpcf-repeatable-container').find('.wpcf-repeatable-group').length,
            group = jQuery(this).closest('.wpcf-repeatable-group');
            groupContainer = jQuery(this).closest('.wpcf-repeatable-container');        
        
        // Only remove if not the first group
        if( groupLength < 2 ) {
            return;
        }

        // Fade-out and remove after a certain timeout
        group.fadeOut();

        setTimeout( function() {
            group.remove();

            // Update the numbering of items
            groupContainer.find('.wpcf-repeatable-group').each( function(index, node) {
                jQuery(node).html( function(n, node) {
                    return node.replace(/\[\d+\]/g, '[' + index + ']').replace(/\_\d+\_/g, '_' + index + '_');
                });
            });

        }, 500);

    });    
    
    // Open or close a group
    jQuery('body').on('click', '.wpcf-repeatable-toggle', function (e) {
        e.preventDefault();
        
        if( jQuery(this).find('i').text() === 'arrow_drop_down' ) {
            jQuery(this).find('i').text('arrow_drop_up');        
        } else if( jQuery(this).find('i').text() === 'arrow_drop_up' ) {
            jQuery(this).find('i').text('arrow_drop_down');    
        }
        jQuery(this).closest('.wpcf-repeatable-group').find('.wpcf-repeatable-fields').slideToggle('closed');
    });
    
};
},{"./../fields":2}],12:[function(require,module,exports){
/**
 * Our colorpicker module
 */
module.exports.init = function(framework) {

    // Execute if we do have select2 defined
    if( typeof jQuery.fn.select2 !== 'undefined' && jQuery.fn.select2 ) {
       
        // Regular selects
        jQuery('.wpcf-select').select2({});
        
        // Typography selects
        jQuery('.wpcf-typography-fonts').select2({
            templateResult: formatState,
            templateSelection: formatState            
        });
        
    }
    
};

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
    
};
},{}],13:[function(require,module,exports){
/**
 * Our jquery UI slider
 */
module.exports.init = function(framework) {
    
    /**
     * Adds jQuery UI Sliders
     */
    jQuery(framework).find('.wpcf-slider').each(function (index) {
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
    
};
},{}],14:[function(require,module,exports){
module.exports.init = function() {
    
    // Click handler for our tabs
    jQuery(".wpcf-tabs a").click(function (e) {
        
        e.preventDefault();
        
        var activeTab = jQuery(this).attr("href"),
            section = activeTab.replace('#', ''),
            frame = jQuery(this).closest('.wpcf-framework').attr('id');
        
        // Change our active section
        jQuery('#wp_custom_fields_section_' + frame).val(section);
		
        // Remove current active classes
        jQuery(this).closest('.wpcf-framework').find(".wpcf-tabs a").removeClass("active");
        jQuery(this).closest('.wpcf-framework').find(".wpcf-section").removeClass("active");
        
        // Add active class to our new things
        jQuery(this).addClass("active");      
        jQuery(activeTab).addClass("active");

    });
 
}
},{}],15:[function(require,module,exports){
/**
 * Functions for option pages
 */
module.exports.init = function(framework) {

    if( jQuery(framework).hasClass('wpcf-options-page') ) {

        var scrollHeader    = jQuery(framework).find('.wpcf-notifications'),
            scrollPosition  = 0,
            scrollWidth     = scrollHeader.width();

        jQuery(window).scroll( function() {

            scrollPosition = jQuery(window).scrollTop();

            if( scrollPosition > 50 ) {
                scrollHeader.width(scrollWidth);
                scrollHeader.closest('.wpcf-header').addClass('wpfc-header-scrolling');
            } else {
                scrollHeader.closest('.wpcf-header').removeClass('wpfc-header-scrolling');
            }

        } );
       
    }

};
},{}]},{},[1]);
