<?php
 /** 
  * Displays a text input field
  */
namespace MakeitWorkPress\WP_Custom_Fields\Fields;
use MakeitWorkPress\WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class Datepicker implements Field {
    
    public static function render( $field = array() ) {

        // Only Enqueue if it is not enqueued yet
        if( apply_filters('wp_custom_fields_datepicker_field_js', true) && ! wp_script_is('flatpicker-js', 'enqueued') ) {
            wp_enqueue_script('flatpicker-js');
        }
        
        $configurations = self::configurations();
        
        $attributes     = '';
        $clear          = isset($field['labels']['clear']) ? $field['labels']['clear'] : $configurations['labels']['clear'];
        $placeholder    = isset($field['placeholder']) && $field['placeholder'] ? ' placeholder="' . $field['placeholder'] . '"' : '';
        $toggle         = isset($field['labels']['toggle']) ? $field['labels']['toggle'] : $configurations['labels']['toggle'];

        /**
         * Accepts different optional configurations according to the flatpickr config, but not camelcase here
         * 
         * enable-time  (boolean) Allows to pick a time - is still buggy
         * alt-format   Determines how a date is displayed (which may be different than the format that is stored), using common date-formats
         * date-format  The format in which the date is stored, using common date-formats. By default, this is the unix timestamp.
         * locale       Enter the string of a custom locale. This requires a localization file to be loaded as well, according to https://flatpickr.js.org/localization/ 
         * max-date     The maximum date that can be picked in the selector, using common date-formats - is still buggy
         * min-date     The minimum date that may be picked in the selector, using common date-formats  - is still buggy
         * mode         The mode for the datapicker, either 'single', 'multiple' or 'range'
         * no-calendar  (boolean) To hide the calendar. Can be used if only time should be picked
         * week-numbers (boolean) Allow for weeknumbers to be displayed
         */
        foreach( array('enable-time', 'alt-format', 'date-format', 'locale', 'max-date', 'min-date', 'mode', 'no-calendar', 'week-numbers') as $attribute ) {
            if( isset($field[$attribute]) && $field[$attribute] !== '' ) {
                $attributes .= ' data-' . $attribute . '="' . $field[$attribute] . '"';  
            }
        }
        
        $output = '<div class="wp-custom-fields-datepicker"' . $attributes . '>';
        $output .= '    <input id="' . $field['id'] . '" name="' . $field['name']  . '" type="input" value="' . $field['values'] . '" data-input="true"' . $placeholder . ' />';
        $output .= '    <a class="wp-custom-fields-input-button" title="' . $toggle . '" data-toggle="true"><i class="material-icons">calendar_today</i></a>';
        $output .= '    <a class="wp-custom-fields-input-button input-button-clear" title="' . $clear . '" data-clear="true"><i class="material-icons">clear</i></a>';
        $output .= '</div>';

        return $output;  
          
    }
    
    public static function configurations() {
        $configurations = array(
            'type'      => 'datepicker',
            'defaults'  => '',         
            'labels'        => array(
                'clear'     => __('Clear', 'wp-custom-fields'),
                'toggle'    => __('Toggle', 'wp-custom-fields')
            ),
        );
            
        return $configurations;
    }
    
}