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