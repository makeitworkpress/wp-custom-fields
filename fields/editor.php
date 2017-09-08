<?php
 /** 
  * Displays an editor field
  */
namespace WP_Custom_Fields\Fields;
use WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die; 

class Editor implements Field {
    
    public static function render($field = array()) {
        
        $settings['textarea_name'] = $field['name'];
        
        ob_start();
        wp_editor($field['values'], $field['id'], $settings);
        return ob_get_clean();
        
    }
    
    public static function configurations() {
        $configurations = array(
            'type'      => 'editor',
            'defaults'  => ''
        );
            
        return $configurations;
    }
    
}