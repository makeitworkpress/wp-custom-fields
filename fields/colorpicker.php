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
        
        $alpha      = isset($field['alpha']) ? ' data-alpha="' . $field['alpha'] . '"' : ' data-alpha="true"';
        // $palette    = isset($field['palette']) ? ' data-palette="' . $field['palette'] . '"' : '';
        // $default    = isset($field['default']) && $field['default'] ? ' data-default-color="' . $field['default'] . '"' : '';
        // $opacity    = isset($field['opacity']) && $field['opacity'] == false ? '' : ' data-show-opacity="true"';
        
        $output = '<div class="wp-custom-fields-colorpicker-wrapper">';
        $output .= '<input id="' . $field['id'] . '" class="wp-custom-fields-colorpicker color-picker" name="' . $field['name']  . '" type="text" value="' . $field['values'] . '"' . $alpha . ' />'; 
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