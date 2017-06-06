<?php
 /** 
  * Displays a text input field
  */
namespace Divergent\Fields;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class Divergent_Field_Radio implements Divergent_Field {
    
    public static function render($field = array()) {
        
        $options    = isset($field['options']) ? $field['options'] : array();
        $style      = isset($field['style']) ? $field['style'] : '';
        
        $output = '<div class="divergent-field-radio-wrapper ' . $style . '">';
        
        // This field accepts an array of options
        foreach($options as $option) {
            
            // Determine if a box should be checked
            if($field['values'] == $option['id']) {
                $checked = 'checked="checked"';
            } else {
                $checked = '';
            }
            
            // Check label
            $label  = isset($option['label']) ? $option['label'] : '';
            $icon   = isset($option['icon']) ? '<i class="material-icons">' . $option['icon'] . '</i> ' : '';
            
            // Output of form
            $output .= '<input type="radio" id="' . $field['id'] .  $option['id'] . '" name="' . $field['name'] . '" value="' . $option['id'] . '" ' . $checked . ' />';
            
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