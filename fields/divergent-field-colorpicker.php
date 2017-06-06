<?php
 /** 
  * Displays a colorpicker input field
  */
namespace Divergent\Fields;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class Divergent_Field_Colorpicker implements Divergent_Field {
    
    public static function render($field = array()) {
        
        $palette = isset($field['palette']) ? 'data-palette-="' . $field['palette'] . '"' : '';
        $default = isset($field['default_color']) ? 'data-default-color="' . $field['default_color'] . '"' : '';
        $opacity = isset($field['show_opacity']) && $field['opacity'] == false ? '' : 'data-show-opacity="true"';
        
        $output = '<div class="divergent-colorpicker-wrapper">';
        $output .= '<input id="' . $field['id'] . '" class="divergent-colorpicker alpha-color-picker" name="' . $field['name']  . '" type="text" value="' . $field['values'] . '" />'; 
        $output .= '</div>'; 
        
        return $output;
    }
    
    public static function configurations() {
        $configurations = array(
            'type' => 'colorpicker'
        );
            
        return $configurations;
    }
    
}