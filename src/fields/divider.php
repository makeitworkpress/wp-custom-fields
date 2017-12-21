<?php
 /** 
  * Displays a divider
  */
namespace MakeitWorkPress\WP_Custom_Fields\Fields;
use MakeitWorkPress\WP_Custom_Fields\Field as Field;

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