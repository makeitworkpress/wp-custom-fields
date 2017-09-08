<?php
 /** 
  * Displays a code field with stylized code
  */
namespace WP_Custom_Fields\Fields;
use WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class Code implements Field {
    
    public static function render( $field = array() ) {
        
        $mode       = isset($field['mode']) ? $field['mode'] : 'htmlmixed';
        $field_id   = $field['id'];
        
        if( apply_filters('wp_custom_fields_code_field_js', true) && ! wp_script_is('mirror-js', 'enqueued') )
            wp_enqueue_script('mirror-js');
        
        add_action('admin_print_footer_scripts', function() use ($field_id, $mode) {
            echo '<script>
                var editor_'.$field_id.' = document.getElementById("'.$field_id.'"),
                    myCodeMirror'.$field_id.' = CodeMirror.fromTextArea(editor_'.$field_id.', {
                      mode:  "' . $mode . '",
                      lineNumbers: true
                    }); 
                jQuery
                myCodeMirror'.$field_id.'.save();
            </script>';                
        });
        
        $output = '<textarea class="wp-custom-fields-code-editor-value" id="' . $field['id'] . '" name="' . $field['name'] . '">' . $field['values'] . '</textarea>';
        
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