<?php
 /** 
  * Displays a text input field
  */
namespace Divergent\Fields;
use Divergent\Divergent_Field as Divergent_Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) 
    die;

class Checkbox implements Divergent_Field {
    
    public static function render($field = array()) {
        
        $options    = isset($field['options']) ? $field['options'] : array();
        $style      = isset($field['style']) ? $field['style'] : ''; // Accepts an optional buttonset style, for a set of styled buttons
        
        $output = '<ul class="divergent-field-checkbox-wrapper ' . $style . '">';
        
        // This field accepts an array of options
        foreach($options as $key => $option) {
            
            // Determine if a box should be checked

            // Check label
            $label = isset($option['label']) ? $option['label'] : '';
            $icon = isset($option['icon']) ? '<i class="material-icons">' . $option['icon']  . '</i> ' : '';
            
            if( ! $icon )
                $output .= '<li class="divergent-field-checkbox-input">';
            
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
            'type' => 'checkbox'
        );
            
        return $configurations;
    }
    
}