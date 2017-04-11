<?php
 /** 
  * Displays a divider
  */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) { 
    die; 
}

class Divergent_Field_Divider implements Divergent_Field {
    
    public static function render($field = array()) {
        return '<hr />';    
    }
    
    public static function configurations() {
        $configurations = array(
            'type' => 'divider'
        );
            
        return $configurations;
    }
    
}