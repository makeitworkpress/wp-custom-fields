<?php
 /** 
  * Displays a text input field
  */
namespace WP_Custom_Fields\Fields;
use WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class Radio implements Field {
    
    public static function render($field = array()) {
        
        $options    = isset($field['options']) ? $field['options'] : array();
        $style      = isset($field['style']) ? $field['style'] : '';
        
        $output = '<div class="wp-custom-fields-field-radio-wrapper ' . $style . '">';
        
        // This field accepts an array of options
        foreach($options as $option) {
            
            // Check label
            $label  = isset($option['label']) ? $option['label'] : '';
            $icon   = isset($option['icon']) ? '<i class="material-icons">' . $option['icon'] . '</i> ' : '';
            
            // Output of form
            $output .= '<input type="radio" id="' . $field['id'] .  $option['id'] . '" name="' . $field['name'] . '" value="' . $option['id'] . '" ' . checked(  $field['values'], $option['id'], false ) . ' />';
            
            if( ! empty($label) ) {
                $output .= '<label for="' . $field['id'] . $option['id'] . '">' . $icon . $label . '</label>';
            }
        }
        
        $output .= '</div>';
        
        return $output;    
    }
    
    public static function configurations() {
        $configurations = array(
            'type' => 'radio'
        );
            
        return $configurations;
    }
    
}