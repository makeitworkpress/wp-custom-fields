<?php
 /** 
  * Displays a text input field
  */
namespace MakeitWorkPress\WP_Custom_Fields\Fields;
use MakeitWorkPress\WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class Radio implements Field {
    
    public static function render($field = array()) {
        
        $options    = isset($field['options']) ? $field['options'] : array();
        $style      = isset($field['style']) ? $field['style'] : ''; // Accepts an optional .buttonset style, for a set of styled buttons or .switcher style for a switch display
        
        $output = '<div class="wp-custom-fields-field-radio-wrapper ' . $style . '">';
        
        // This field accepts an array of options
        foreach($options as $key => $option) {
            
            // Check label
            $label  = isset($option['label']) ? $option['label'] : '';
            $icon   = isset($option['icon']) ? '<i class="material-icons">' . $option['icon'] . '</i> ' : '';
            
            // Output of form
            $output .= '<input type="radio" id="' . $field['id'] .  $key . '" name="' . $field['name'] . '" value="' . $key . '" ' . checked( $field['values'], $key, false ) . ' />';
            
            if( ! empty($label) ) {
                $output .= '<label for="' . $field['id'] . $key . '">' . $icon . $label . '</label>';
            }
        }
        
        $output .= '</div>';
        
        return $output;    
    }
    
    public static function configurations() {
        $configurations = array(
            'type'      => 'radio',
            'defaults'  => ''
        );
            
        return $configurations;
    }
    
}