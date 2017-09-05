<?php
 /** 
  * Displays a divider
  */
namespace Divergent\Fields;
use Divergent\Divergent_Field as Divergent_Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class Divider implements Divergent_Field {
    
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