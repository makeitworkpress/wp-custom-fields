<?php
 /** 
  * The heading display is determined in the class-views-fields.php file. Hence, this file is empty.
  */
namespace Divergent\Fields;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class Divergent_Field_Heading implements Divergent_Field {
    
    public static function render($field = array()) { 
        $output = isset($field['subtitle']) ? '<p>' . $field['subtitle'] . '</p>' : '';
        return $output;    
    }
    
    public static function configurations() {
        $configurations = array(
            'type' => 'heading'
        );
            
        return $configurations;
    }
    
}