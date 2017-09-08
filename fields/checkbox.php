<?php
 /** 
  * Displays a text input field
  */
namespace WP_Custom_Fields\Fields;
use WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) 
    die;

class Checkbox implements Field {
    
    public static function render($field = array()) {
        
        $options    = isset($field['options']) ? $field['options'] : array();
        $style      = isset($field['style']) ? $field['style'] : ''; // Accepts an optional buttonset style, for a set of styled buttons
        
        $output = '<ul class="wp-custom-fields-field-checkbox-wrapper ' . $style . '">';
        
        // This field accepts an array of options
        foreach($options as $key => $option) {
            
            // Determine if a box should be checked
            $field['values'][$key] = isset($field['values'][$key]) ? $field['values'][$key] : '';

            // Check label
            $label = isset($option['label']) ? $option['label'] : '';
            $icon = isset($option['icon']) ? '<i class="material-icons">' . $option['icon']  . '</i> ' : '';
            
            if( ! $icon )
                $output .= '<li class="wp-custom-fields-field-checkbox-input">';
            
            // Output of form
            $output .= '<input type="checkbox" id="' . $field['id'] . '_' . $key . '" name="' . $field['name'] . '[' . $key . ']" ' . checked($field['values'][$key], true, false) . ' />';
            
            if( ! empty($label) ) {
                $output .= '<label for="' . $field['id'] . '_' . $key . '">' . $icon . $label . '</label>';
            }
            
            if( ! $icon )
                $output .= '</li>';            
            
        }
        
        $output .= '</ul>';
        
        return $output;    
    }
    
    public static function configurations() {
        $configurations = array(
            'type'      => 'checkbox',
            'defaults'  => array()
        );
            
        return $configurations;
    }
    
}