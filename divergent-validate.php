<?php
/**
 * Container class that contains helper functions for validating user input.
 *
 * @author Michiel
 * @package Divergent
 * @since 1.0.0
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) { 
    die; 
}

namespace Divergent;

trait Divergent_Validate {
    
    /**
     * Formats the output by sanitizing and validating
     *
     * @param array $frame   The frame to format
     * @param array $output  The post output generated
     * @param array $type    The type to format for
     */
    public static function format( $frame, $output, $type = '' ) {
        
        // Checks in which tab we are
        $currentTab = strip_tags( $_POST['divergentSection'] );
        
        // Sets the transient for the current section
        set_transient('divergent_current_section_' . $frame['id'], $currentTab, 10);
        
        /**
         * Restore the fields for a current section
         */
        if( isset($_POST[$frame['id'] . '_restore']) ) {
                                          
            foreach( $frame['sections'] as $section ) { 
                
                // Fields are just saved if the are not restored
                if( $section['id'] !== $currentTab ) {
                    
                    foreach($section['fields'] as $field) {
                        $output[$field['id']] = self::sanitize( $output[$field['id']], $field );
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
        if( isset($_POST['divergent_options_reset']) ) {
            
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
        if( isset($_POST['import_submit']) ) {
            
            $output = unserialize( base64_decode($_POST['import_value']) );
            
            if( $type = 'Options' )
                add_settings_error( $frame['id'], 'divergent-notification', __('Settings Imported!', 'divergent'), 'update' );
            
            return $output;
        }
        
        /**
         * Default formatting of data
         */
        foreach( $frame['sections'] as $section ) {

            foreach($section['fields'] as $field) {
                
                $output[$field['id']] = self::sanitize($output[$field['id']], $field);
                
            }
            
        }
        
        if( $type = 'Options' )
            add_settings_error( $frame['id'], 'divergent-notification', __('Settings saved!', 'divergent'), 'update' ); 
        
        return $output;
        
    }
    
    /**
     * Sanitizes input
     *
     * @param string $field The field type provided by the field
     */
    private static function sanitize( $field ) {
        
        global $allowedposttags;
            
        $field_type     = $field['type'];
        $field_subtype  = isset($field['subtype']) ? $field['subtype'] : '';
        $field_value    = $field['values'];
        
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
            // @todo Improve sanitization for borders and special types
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
    
    /**
     * Validates input
     *
     * @param array $field The field array
     */    
    private static function validate( $field ) {
        
        $response   = true;
        $validation = isset( $field['validation'] ) ? $field['validation'] : '';
            
        switch( $validation ) {
            // E-mail validation
            case 'email':
                if ( ! is_email( $field['values'] ) ) {
                    $response = __( 'Please enter a valid e-mail address.', 'divergent' );
                }
                break;
            case 'numeric':
                if ( ! is_numeric( $field['values'] ) ) {
                    $response = __( 'Please enter a valid number.', 'divergent' );  
                }
                break;
            case 'required':
                if ( empty( $field['values'] ) ) {
                    $response = __( 'This field is required.', 'divergent' );  
                }
                break;
        }
        
        // If there is a response, return it, otherwise return true
        return $response;
        
    }  
   
}