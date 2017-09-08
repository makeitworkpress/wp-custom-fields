<?php
 /** 
  * Displays a divider
  */
namespace WP_Custom_Fields\Fields;
use WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class Divider implements Field {
    
    public static function render($field = array()) {
        return '<hr />';    
    }
    
    public static function configurations() {
        $configurations = array(
            'type'      => 'divider',
            'defaults'  => ''
        );
            
        return $configurations;
    }
    
}