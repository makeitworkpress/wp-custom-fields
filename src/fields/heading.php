<?php
 /** 
  * The heading display is determined in the class-views-fields.php file. Hence, this file is empty.
  */
namespace MakeitWorkPress\WP_Custom_Fields\Fields;
use MakeitWorkPress\WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class Heading implements Field {
    
    public static function render($field = array()) { 
        $output = isset($field['subtitle']) ? '<p>' . $field['subtitle'] . '</p>' : '';
        return $output;    
    }
    
    public static function configurations() {
        $configurations = array(
            'type'      => 'heading',
            'defaults'  => ''
        );
            
        return $configurations;
    }
    
}