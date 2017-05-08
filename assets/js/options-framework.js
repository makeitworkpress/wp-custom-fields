/**
 * Admin Functions for the Divergent Theme
 *
 * @author Michiel
 * @package Divergent
 * @since 1.0.0
 */
jQuery(document).ready(function ($) {
    
    'use strict';
    
    /**
     * General Variables
     */
    var colorOptions = {
        palettes: true
    };
    
    // Hide tabs on clicking
    $(".divergent-tabs a").click(function (e) {
        
        e.preventDefault();
        
        var activeTab = $(this).attr("href"),
            section = activeTab.replace('#', '');
        
        // Change our active section
        $('input[name="divergentSection"]').val(section);
		
        // Remove current active classes
        $(".divergent-tabs a").removeClass("active");
        $(".divergent-section").removeClass("active");
        
        // Add active class to our new things
        $(this).addClass("active");      
        $(activeTab).addClass("active");

	});
    
    /**
     * Main Options Framework Function
     */
    $.fn.divergentInit = function() {
    
        /**
         * Adds colorpickers
         */
        $(this).find('.divergent-colorpicker').alphaColorPicker();         
        
        // Enable sortable multi select fields
        if ($(this).find('.divergent-select').data('sortable')) {
            $(this).find('.divergent-select').select2Sortable();
        }        

        /**
         * Adds jQuery UI Sliders
         */
        $(this).find('.divergent-slider').each(function (index) {
            var sliderTarget = $(this).data('id'),
                sliderMin = $(this).data('min'),
                sliderMax = $(this).data('max'),
                sliderStep = $(this).data('step'),
                sliderValue = $(this).data('value');
            
            $(this).slider({
                value: sliderValue,
                min: sliderMin,
                max: sliderMax,
                step: sliderStep,
                slide: function (event, ui) {
                    $('#' + sliderTarget).val(ui.value);
                }
            });

        });
        
        /**
         * Adds Google Maps
         */
        $(this).find('.divergent-location').each(function (index) {
            var searchInput = $('.divergent-map-search', this).get(0),
                mapCanvas = $('.divergent-map-canvas', this).get(0),
                latitude = $('.latitude', this),
                longitude = $('.longitude', this),
                city = $('.city', this),
                zip = $('.postal_code', this),
                street = $('.street', this),
                number = $('.number', this),
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

        /**
         * Enables Uploading using the Media-Uploader
         */
        $(this).find('.divergent-upload-wrapper').each(function (index) {

            // Define the buttons for this specific group
            var add_media = $(this).find('.divergent-upload-add'),
                remove_media = $(this).find('.divergent-upload-remove'),
                value_input = $(this).find('.divergent-upload-value'),
                title = $(this).data('title'),
                type = $(this).data('type'),
                button = $(this).data('button'),
                multiple = $(this).data('multiple'),
                add_wrap = $(this).find('.divergent-single-media.empty'),
                initator = this,
                frame;

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
                    
                    console.log(attachments);

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

                var target = $(this).closest('.divergent-single-media'),
                    target_id = target.data('id'),
                    current_values = value_input.val(),
                    new_values = current_values.replace(target_id + ',', '');

                target.remove();

                value_input.val(new_values);

            });

        });
        
    }
    
    /**
     * Repeatable Groups 
     *
     * @todo Add function to reset fields to default value
     */
    $('.divergent-repeatable-add').on('click', function (e) {
        e.preventDefault();
        var length = $(this).closest('.divergent-repeatable-container').find('.divergent-repeatable-group').length,
            group = $(this).closest('.divergent-repeatable-container').find('.divergent-repeatable-group').last(),
            newGroup = group.clone(true, true),
            newGroupNumber = newGroup.find('h4 span');
        
        // The title should follow the numbering
        newGroupNumber.text(length);
        
        // Clone the current group and replace the current keys by new ones
        newGroup.html(function (i, oldGroup) {
            return oldGroup.replace(/\[\d\]/g, '[' + length + ']').replace(/\-\d\-/g, '-' + length + '-');
        });
        
        // Redraw the elements
        newGroup.divergentInit();
                
        // Finally, insert the newGroup after the current group
        group.after(newGroup);
        
    });
    
    $('.divergent-repeatable-remove').on('click', function (e) {
        e.preventDefault();
        var length = $(this).closest('.divergent-repeatable-container').find('.divergent-repeatable-group').length,
            group = $(this).closest('.divergent-repeatable-container').find('.divergent-repeatable-group').last();
        
        // Keep the first group
        if (length > 1) {
            group.remove();
        }
    });
    
    $('.divergent-repeatable-toggle').live('click', function (e) {
        e.preventDefault();
        
        if( $(this).find('i').text() === 'arrow_drop_down' ) {
            $(this).find('i').text('arrow_drop_up');        
        } else if( $(this).find('i').text() === 'arrow_drop_up' ) {
            $(this).find('i').text('arrow_drop_down');    
        }
        $(this).closest('h4').next('.divergent-repeatable-group-fields').slideToggle('closed');
    });
    
    // Initialize the options framework
    $('.divergent-framework').divergentInit();
    
});