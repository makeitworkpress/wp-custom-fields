<?php
 /** 
  * Displays a code field with stylized code
  */
namespace MakeitWorkPress\WP_Custom_Fields\Fields;
use MakeitWorkPress\WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class Code implements Field {
    
    public static function render( $field = array() ) {
        
        $mode       = isset($field['mode']) ? $field['mode'] : 'htmlmixed';
        $field_id   = sanitize_key( $field['id'] );
        
        // Only Enqueue if it is not enqueued yet
        if( apply_filters('wp_custom_fields_code_field_js', true) && ! wp_script_is('mirror-js', 'enqueued') ) {
            wp_enqueue_script('mirror-js');
        }
        
        $output = '<textarea class="wp-custom-fields-code-editor-value" id="' . $field['id'] . '" name="' . $field['name'] . '" data-mode="' . $mode . '">' . html_entity_decode($field['values']) . '</textarea>';
        
        return $output;
  
    }
    
    public static function configurations() {
        $configurations = array(
            'type'      => 'code',
            'defaults'  => ''
        );
            
        return $configurations;
    }
    
}