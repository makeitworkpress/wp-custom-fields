<?php
 /** 
  * Displays an editor field
  */
namespace Divergent\Fields;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die; 

class Divergent_Field_Editor implements Divergent_Field {
    
    public static function render($field = array()) {
        
        $settings['textarea_name'] = $field['name'];
        
        ob_start();
        wp_editor($field['values'], $field['id'], $settings);
        return ob_get_clean();
        
    }
    
    public static function configurations() {
        $configurations = array(
            'type' => 'editor'
        );
            
        return $configurations;
    }
    
}