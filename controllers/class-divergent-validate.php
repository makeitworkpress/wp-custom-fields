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

namespace Controllers;

trait Divergent_Validate {
    
    /**
     * Sanitizes input
     *
     * @param array $field_value The value of the field
     * @param string $field_type The field type provided by the field
     * @param string $field_subtype The field's eventual subtype, such as e-mail or number for the text field
     */
    public static function sanitize($field_value, $field) {
        
        global $allowedposttags;
            
        $field_type = $field['type'];
        $field_subtype = isset($field['subtype']) ? $field['subtype'] : '';
        
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
     * @param array $field_value The value of the field
     * @param string $field_type The validation type as provided by the field
     */    
    public static function validate($field_value, $validation_type) {
        
        $response = '';
        
        switch($validation_type) {
            // E-mail validation
            case 'email':
                if ( ! is_email( $field_value ) ) {
                    $response = __( 'Please enter a valid e-mail address.', 'divergent' );
                }
                break;
            case 'numeric':
                if ( ! is_numeric( $field_value ) ) {
                    $response = __( 'Please enter a valid number.', 'divergent' );  
                }
                break;
            case 'required':
                if ( empty( $field_value ) ) {
                    $response = __( 'This field is required.', 'divergent' );  
                }
                break;
        }
        
        // If there is a response, return it, otherwise return true
        if( ! empty($response) ) {
            return $response;
        } else {
            return true;
        }
               
    }  
   
}