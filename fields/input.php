<?php
 /** 
  * Displays a text input field
  */
namespace Divergent\Fields;
use Divergent\Divergent_Field as Divergent_Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class Input implements Divergent_Field {
    
    public static function render($field = array()) {
        
        $type = isset($field['subtype']) ? $field['subtype'] : 'text';
        
        return '<input class="regular-text" id="' . $field['id'] . '" name="' . $field['name']  . '" type="' . $type . '" value="' . $field['values'] . '" />';    
    }
    
    public static function configurations() {
        $configurations = array(
            'type' => 'input'
        );
            
        return $configurations;
    }
    
}