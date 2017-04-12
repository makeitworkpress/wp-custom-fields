<?php
/**
 * Container class that contains helper functions for validating and saving user input.
 *
 * @author Michiel
 * @package Divergent
 * @since 1.0.0
 */
namespace Classes\Divergent;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die; 

trait Divergent_Validate {
    
    /**
     * Formats the output by sanitizing and validating
     *
     * @param array $frame   The frame to format
     * @param array $input   The $_Post $input generated
     * @param array $type    The type to format for
     */
    public static function format( $frame, $input, $type = '' ) {
        
        // Checks in which tab we are
        $currentTab = strip_tags( $input['divergentSection'] );
        
        // Sets the transient for the current section
        set_transient('divergent_current_section_' . $frame['id'], $currentTab, 10);
        
        /**
         * Restore the fields for a current section
         */
        if( isset($output[$frame['id'] . '_restore']) ) {
                                          
            foreach( $frame['sections'] as $section ) { 
                
                // Fields are just saved if the are not restored
                if( $section['id'] !== $currentTab ) {
                    
                    foreach($section['fields'] as $field) {
                        $output[$field['id']] = self::sanitize( $input[$field['id']], $field );
                    } 
                    
                    continue;
                    
                }
                  
                foreach($section['fields'] as $field) {               
                    
                    $default = isset($field['default']) ? $field['default'] : '';
                    $output[$field['id']] = $default;
                    
                }               
                
            }
            
            // Add a notification for option pages
            if( $type = 'Options' )
                add_settings_error( $frame['id'], 'divergent-notification', __('Settings restored for this section.', 'divergent'), 'update' );
            
            return $output;
            
        }
        
        /**
         * Restore the complete section
         */
        if( isset($output['divergent_options_reset']) ) {
            
            foreach($frame['sections'] as $section) {
                
                foreach($section['fields'] as $field) {                
                    
                    $default                = isset($field['default']) ? $field['default'] : '';
                    $output[$field['id']]   = $default;
                    
                }
                
            }
            
            if( $type = 'Options' )
                add_settings_error( $frame['id'], 'divergent-notification', __('All settings are restored.', 'divergent'), 'update' );
            
            return $output;
        
        }        
        
        /**
         * Import data
         */
        if( isset($output['import_submit']) ) {
            
            $output = unserialize( base64_decode($input['import_value']) );
            
            if( $type = 'Options' )
                add_settings_error( $frame['id'], 'divergent-notification', __('Settings Imported!', 'divergent'), 'update' );
            
            return $output;
        }
        
        /**
         * Default formatting of data
         */
        foreach( $frame['sections'] as $section ) {

            foreach($section['fields'] as $field) {

                $output[$field['id']] = self::sanitize( $input[$field['id']], $field );
                
            }
            
        }
        
        if( $type = 'Options' )
            add_settings_error( $frame['id'], 'divergent-notification', __('Settings saved!', 'divergent'), 'update' );
        
        return $output;
        
    }
    
    /**
     * Sanitizes input
     *
     * @param array     $input The input data for the field
     * @param string    $field The field array
     *
     * @todo Improve sanitization for borders and special types
     */
    private static function sanitize( $input, $field ) {
        
        global $allowedposttags;
            
        $field_type     = $field['type'];
        $field_subtype  = isset($field['subtype']) ? $field['subtype'] : '';
        $field_value    = $input;
        
        $return_value = '';
        
        // Switch for various field types
        switch($field_type) {
            
            // Default textual input
            case 'text':
            case 'textarea':
                switch($field_subtype) {
                    case 'url':
                        $return_value = esc_url($field_value); 
                        break;
                    case 'email':
                        $return_value = is_email($field_value) ? sanitize_email($field_value) : ''; 
                        break;
                    case 'number':
                        $return_value = is_numeric($field_value) ? floatval($field_value) : '';               
                        break;
                    default:
                        $return_value = wp_kses($field_value, $allowedposttags);    
                }
                break;
            case 'editor':
                $return_value = wp_kses($field_value, $allowedposttags);
                break;            
            // Checkboxes
            case 'checkbox':
                foreach( $field['options'] as $option ) {
                    if( isset($field_value[$option['id']]) && $field_value[$option['id']] == 'on') {
                        $return_value[$option['id']] = true;
                    } else {
                        $return_value[$option['id']] = false;
                    }
                }
                
                break;
            
            // Border Values
            case 'border':
            case 'colorpicker':
            case 'dimensions':
            case 'links':
                $return_value = $field_value;
                break;
            // Slider
            case 'slider':
                $return_value = is_numeric($field_value) ? floatval($field_value) : '';  
                break;
            // Repeatable sanitization
            case 'repeatable':
                $return_value = $field_value; 
                break;
            case 'code':
                $return_value = esc_html($field_value); 
                break;
            // Default sanitization            
            default:
                $return_value = wp_kses($field_value, $allowedposttags);
                break;
        } 
        
        return apply_filters('divergent_sanitized_value', $return_value, $field_value, $field);
        
    }
   
}