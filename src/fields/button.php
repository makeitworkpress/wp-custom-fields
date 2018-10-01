<?php 
/**
 * Displays an importer and exporter of saved option data 
 */
namespace MakeitWorkPress\WP_Custom_Fields\Fields;
use MakeitWorkPress\WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class button implements Field {

    /**
     * This field supports action, label, message and style as attributes
     * The label contains the contents of this button
     * The message attribute relates the the content of a message. If set to true, it will use the data output from an ajax action into a notification.
     * The style attribute accepts additional button classes, such as button-primary
     * 
     * @param array $field The array with field attributes
     */
    public static function render($field = array()) {
        
        // At least, an action should be provided
        if( ! isset($field['action']) ) {
            return;
        }

        $action     = $field['action'];
        $data       = isset( $field['data'] ) ? json_encode($field['data']) : '';
        $label      = isset( $field['label'] ) ? sanitize_text_field($field['label']) : '';
        $message    = isset( $field['message'] ) ? sanitize_text_field($field['message']) : '';
        $style      = isset( $field['style'] ) ? sanitize_text_field($field['style']) : '';
        
        $output = '<button data-action="' . $action . '" data-data=\'' . $data . '\' data-message="' . $message . '" class="wpcf-button button ' . $style . '">' . $label  . '</button>';

        
        return $output;    
    }
    
    public static function configurations() {
        $configurations = array(
            'type'      => 'button',
            'defaults'  => ''
        );
            
        return $configurations;
    }
    
}