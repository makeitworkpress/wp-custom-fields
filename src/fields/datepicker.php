<?php
 /** 
  * Displays a text input field
  */
namespace MakeitWorkPress\WP_Custom_Fields\Fields;
use MakeitWorkPress\WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    die;
}

/**
 * Prepares the variables and renders the field
 * 
 * @param   array $field The array with field attributes data-alpha
 * @return  void
 */  
class Datepicker implements Field {
    
    public static function render( $field = [] ) {

        // Only enqueue if it is not enqueued yet
        if( apply_filters('wp_custom_fields_datepicker_field_js', true) && ! wp_script_is('flatpicker-js', 'enqueued') ) {
            wp_enqueue_script('flatpicker-js');
        }

        // Also enqueue our locale script is registered and set-up
        if( isset($field['locale']) && wp_script_is('flatpicker-i18n-' . $field['locale'], 'registered') && ! wp_script_is('flatpicker-i18n-' . $field['locale']) ) {
            wp_enqueue_script('flatpicker-i18n-' . $field['locale']);
        }
        
        $configurations = self::configurations();
        
        $attributes     = '';
        $clear          = isset($field['labels']['clear']) ? esc_attr($field['labels']['clear']) : $configurations['labels']['clear'];
        $id             = esc_attr($field['id']);
        $name           = esc_attr($field['name']);       
        $placeholder    = isset($field['placeholder']) && $field['placeholder'] ? ' placeholder="' . esc_attr($field['placeholder']) . '"' : '';
        $toggle         = isset($field['labels']['toggle']) ? esc_attr($field['labels']['toggle']) : $configurations['labels']['toggle'];
        $value          = esc_attr($field['values']);

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
                $attributes .= ' data-' . $attribute . '="' . esc_attr($field[$attribute]) . '"';  
            }
        } ?>
        
            <div class="wpcf-datepicker" <?php echo $attributes; ?>>
                <input id="<?php echo $id; ?>" name="<?php echo $name; ?>" type="input" value="<?php echo $value; ?>" data-input="true" <?php echo $placeholder; ?>/>
                <a class="wpcf-input-button" title="<?php echo $toggle; ?>" data-toggle="true"><i class="material-icons">calendar_today</i></a>
                <a class="wpcf-input-button input-button-clear" title="<?php echo $clear; ?>" data-clear="true"><i class="material-icons">clear</i></a>
            </div>

        <?php 
          
    }
    
    /**
     * Returns the global configurations for this field
     *
     * @return array $configurations The configurations
     */    
    public static function configurations() {

        $configurations = [
            'type'      => 'datepicker',
            'defaults'  => '',         
            'labels'        => [
                'clear'     => __('Clear', 'wp-custom-fields'),
                'toggle'    => __('Toggle', 'wp-custom-fields')
            ],
        ];
            
        return apply_filters( 'wp_custom_fields_datepicker_config', $configurations );

    }
    
}