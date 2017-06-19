(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
/**
 * This script bundles all the modules from the Divergent Application
 */
'use strict';

var fields      = require('./fields');
var repeatable  = require('./modules/repeatable');
var tabs        = require('./modules/tabs');

var init = function() {
    fields.fields('.divergent-framework');
    repeatable.repeatable();
    tabs.tabs();
}

// Boot divergent
jQuery(document).ready(init);
},{"./fields":2,"./modules/repeatable":6,"./modules/tabs":8}],2:[function(require,module,exports){
/**
 * Executres Field modules
 */
var colorpicker = require('./modules/colorpicker');
var location = require('./modules/location');
var media = require('./modules/media');
var slider = require('./modules/slider');

module.exports.fields = function(framework) {
    colorpicker.colorpicker(framework);
    location.location(framework);
    media.media(framework);
    slider.slider(framework);   
};
},{"./modules/colorpicker":3,"./modules/location":4,"./modules/media":5,"./modules/slider":7}],3:[function(require,module,exports){
/**
 * Our colorpicker module
 */
module.exports.colorpicker = function(framework) {
    
    var colorOptions = {
        palettes: true
    };
    
    jQuery(framework).find('.divergent-colorpicker').alphaColorPicker();
    
}
},{}],4:[function(require,module,exports){
/**
 * Our location field
 */
module.exports.location = function(framework) {
    
    jQuery(framework).find('.divergent-location').each(function (index) {
        var searchInput = jQuery('.divergent-map-search', this).get(0),
            mapCanvas = jQuery('.divergent-map-canvas', this).get(0),
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
},{}],5:[function(require,module,exports){
/**
 * Our jquery UI slider
 */
module.exports.media = function(framework) {
    
    /**
     * Enables Uploading using the Media-Uploader
     */
    jQuery(framework).find('.divergent-upload-wrapper').each(function (index) {

        // Define the buttons for this specific group
        var add_media = jQuery(this).find('.divergent-upload-add'),
            remove_media = jQuery(this).find('.divergent-upload-remove'),
            value_input = jQuery(this).find('.divergent-upload-value'),
            title = jQuery(this).data('title'),
            type = jQuery(this).data('type'),
            button = jQuery(this).data('button'),
            multiple = jQuery(this).data('multiple'),
            add_wrap = jQuery(this).find('.divergent-single-media.empty'),
            initator = this,
            frame;
        
        // Hide if we already have a value
        if( value_input.val() && ! multiple )
            add_wrap.hide();

        // Click function
        add_media.live('click', function (e) {

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
                        src = attachment.url;
                    } else {
                        src = attachment.icon;
                    }

                    add_wrap.before('<div class="divergent-single-media" data-id="' + attachment.id + '"><img src="' + src + '" /><a href="#" class="divergent-upload-remove"><i class="material-icons">clear</i></a></div>');
                });
                
                if( ! multiple )
                    add_wrap.hide();

                value_input.val(attachment_ids);

            });

            // Open the media upload modal
            frame.open();

        });

        /**
         * Remove attachments
         */
        remove_media.live('click', function (e) {
            e.preventDefault();

            var target = jQuery(this).closest('.divergent-single-media'),
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
},{}],6:[function(require,module,exports){
/**
 * Our repeatable fields module
 */
var fields = require('./../fields');

module.exports.repeatable = function(framework) {
    
    /**
     * Repeatable Groups 
     *
     * @todo Add function to reset fields to default value
     */
    jQuery('.divergent-repeatable-add').on('click', function (e) {
        e.preventDefault();
        var length = jQuery(this).closest('.divergent-repeatable-container').find('.divergent-repeatable-group').length,
            group = jQuery(this).closest('.divergent-repeatable-container').find('.divergent-repeatable-group').last(),
            newGroup = group.clone(true, true),
            newGroupNumber = newGroup.find('h4 span');
        
        // The title should follow the numbering
        newGroupNumber.text(length);
        
        // Clone the current group and replace the current keys by new ones
        newGroup.html(function (i, oldGroup) {
            return oldGroup.replace(/\[\d\]/g, '[' + length + ']').replace(/\-\d\-/g, '-' + length + '-');
        });
        
        // Redraw the fields within the group
        fields.fields(newGroup);
                
        // Finally, insert the newGroup after the current group
        group.after(newGroup);
        
    });
    
    jQuery('.divergent-repeatable-remove').on('click', function (e) {
        e.preventDefault();
        var length = jQuery(this).closest('.divergent-repeatable-container').find('.divergent-repeatable-group').length,
            group = jQuery(this).closest('.divergent-repeatable-container').find('.divergent-repeatable-group').last();
        
        // Keep the first group
        if (length > 1) {
            group.remove();
        }
    });
    
    jQuery('body').on('click', '.divergent-repeatable-toggle', function (e) {
        e.preventDefault();
        
        console.log('YUP');
        
        if( jQuery(this).find('i').text() === 'arrow_drop_down' ) {
            jQuery(this).find('i').text('arrow_drop_up');        
        } else if( jQuery(this).find('i').text() === 'arrow_drop_up' ) {
            jQuery(this).find('i').text('arrow_drop_down');    
        }
        jQuery(this).closest('.divergent-repeatable-group').find('.divergent-repeatable-fields').slideToggle('closed');
    });
    
}
},{"./../fields":2}],7:[function(require,module,exports){
/**
 * Our jquery UI slider
 */
module.exports.slider = function(framework) {
    
    /**
     * Adds jQuery UI Sliders
     */
    jQuery(framework).find('.divergent-slider').each(function (index) {
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
},{}],8:[function(require,module,exports){
module.exports.tabs = function() {
    
    jQuery(".divergent-tabs a").click(function (e) {
        
        e.preventDefault();
        
        var activeTab = jQuery(this).attr("href"),
            section = activeTab.replace('#', '');
        
        // Change our active section
        jQuery('input[name="divergentSection"]').val(section);
		
        // Remove current active classes
        jQuery(".divergent-tabs a").removeClass("active");
        jQuery(".divergent-section").removeClass("active");
        
        // Add active class to our new things
        jQuery(this).addClass("active");      
        jQuery(activeTab).addClass("active");

	});
    
}
},{}]},{},[1]);
