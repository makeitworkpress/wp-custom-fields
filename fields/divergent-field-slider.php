<?php
 /** 
  * Displays a text input field
  */
namespace Divergent\Fields;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class Divergent_Field_Slider implements Divergent_Field {
    
    public static function render($field = array()) {
        
        $min = isset($field['min']) ? $field['min'] : 0;
        $max = isset($field['max']) ? $field['max'] : 10;
        $step = isset($field['step']) ? $field['step'] : 1;
        
        // Revert to 0 if value is empty
        if ( empty($field['values']) ) 
            $field['values'] = 0;
        
        $output = '<div class="divergent-slider-wrapper">';
        $output .= '    <div class="divergent-slider" data-id="' . $field['id'] . '" data-value="' . $field['values'] . '" data-min="' . $min . '" data-max="' . $max . '" data-step="' . $step . '"></div>';
        $output .= '    <input class="divergent-slider-value small-text" type="number" readonly="readonly" id="' . $field['id'] . '" name="' . $field['name'] . '" value="' . $field['values'] . '" />'; 
        $output .= '</div>';       
        
        return $output;    
    }
    
    public static function configurations() {
        $configurations = array(
            'type' => 'slider'
        );
            
        return $configurations;
    }
    
}