<?php
 /** 
  * The heading display is determined in the class-views-fields.php file. Hence, this file is empty.
  */
namespace Divergent\Fields;
use Divergent\Divergent_Field as Divergent_Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class Heading implements Divergent_Field {
    
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