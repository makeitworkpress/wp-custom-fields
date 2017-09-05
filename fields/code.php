<?php
 /** 
  * Displays a code field with stylized code
  */
namespace Divergent\Fields;
use Divergent\Divergent_Field as Divergent_Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class Code implements Divergent_Field {
    
    public static function render( $field = array() ) {
        
        $mode       = isset($field['mode']) ? $field['mode'] : 'htmlmixed';
        $field_id   = $field['id'];
        
        if( apply_filters('divergent_code_field_js', true) && ! wp_script_is('mirror-js', 'enqueued') )
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
        
        $output = '<textarea class="divergent-code-editor-value" id="' . $field['id'] . '" name="' . $field['name'] . '">' . $field['values'] . '</textarea>';
        
        return $output;
  
    }
    
    public static function configurations() {
        $configurations = array(
            'type' => 'code'
        );
            
        return $configurations;
    }
    
}