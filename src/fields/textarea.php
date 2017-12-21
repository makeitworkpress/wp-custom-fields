<?php
 /** 
  * Displays a text input field
  */
namespace MakeitWorkPress\WP_Custom_Fields\Fields;
use MakeitWorkPress\WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) 
    die;

class Textarea implements Field {
    
    public static function render($field = array()) {
        
        $rows = isset($field['rows']) ? $field['rows'] : 7;
        $cols = isset($field['cols']) ? $field['cols'] : 70;
        
        return '<textarea id="' . $field['id'] . '" name="' . $field['name']  . '" rows="' . $rows . '" cols="' . $cols . '">' . $field['values'] . '</textarea>';    
    }
    
    public static function configurations() {
        $configurations = array(
            'type'      => 'textarea',
            'defaults'  => ''
        );
            
        return $configurations;
    }
    
}