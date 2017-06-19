<?php
 /** 
  * Displays a text input field
  */
namespace Divergent\Fields;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) 
    die;

class Divergent_Field_Checkbox implements Divergent_Field {
    
    public static function render($field = array()) {
        
        $options    = isset($field['options']) ? $field['options'] : array();
        $style      = isset($field['style']) ? $field['style'] : '';
        
        $output = '<div class="divergent-field-checkbox-wrapper ' . $style . '">';
        
        // This field accepts an array of options
        foreach($options as $option) {
            
            // Determine if a box should be checked
            $checked = isset($field['values'][$option['id']]) && $field['values'][$option['id']] == true ? 'checked="checked"' : '';

            
            // Check label
            $label = isset($option['label']) ? $option['label'] : '';
            $icon = isset($option['icon']) ? '<i class="material-icons">' . $option['icon']  . '</i> ' : '';
            
            if( ! $icon )
                $output .= '<div class="divergent-field-checkbox-input">';
            
            // Output of form
            $output .= '<input type="checkbox" id="' . $field['id'] . '_' . $option['id'] . '" name="' . $field['name'] . '[' . $option['id'] . ']" ' . $checked . ' />';
            
            if( ! empty($label) ) {
                $output .= '<label for="' . $field['id'] . '_' . $option['id'] . '">' . $icon . $label . '</label>';
            }
            
            if( ! $icon )
                $output .= '</div>';            
            
        }
        
        $output .= '</div>';
        
        return $output;    
    }
    
    public static function configurations() {
        $configurations = array(
            'type' => 'checkbox'
        );
            
        return $configurations;
    }
    
}