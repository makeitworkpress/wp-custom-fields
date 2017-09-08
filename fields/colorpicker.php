<?php
 /** 
  * Displays a colorpicker input field
  */
namespace WP_Custom_Fields\Fields;
use WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class Colorpicker implements Field {
    
    public static function render($field = array()) {
        
        $palette = isset($field['palette']) ? 'data-palette-="' . $field['palette'] . '"' : '';
        $default = isset($field['default_color']) ? 'data-default-color="' . $field['default_color'] . '"' : '';
        $opacity = isset($field['show_opacity']) && $field['opacity'] == false ? '' : 'data-show-opacity="true"';
        
        $output = '<div class="wp-custom-fields-colorpicker-wrapper">';
        $output .= '<input id="' . $field['id'] . '" class="wp-custom-fields-colorpicker alpha-color-picker" name="' . $field['name']  . '" type="text" value="' . $field['values'] . '" />'; 
        $output .= '</div>'; 
        
        return $output;
    }
    
    public static function configurations() {
        $configurations = array(
            'type'      => 'colorpicker',
            'defaults'  => ''
        );
            
        return $configurations;
    }
    
}