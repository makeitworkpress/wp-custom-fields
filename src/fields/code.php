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

    /**
     * Prepares the variables and renders the field
     * 
     * @param   array $field The array with field attributes
     * @return  void
     */       
    public static function render( $field = [] ) {
        
        $id         = esc_attr( $field['id'] );
        $name       = esc_attr( $field['name'] );
        $mode       = isset($field['mode']) ? esc_attr($field['mode']) : 'htmlmixed';
        $values     = html_entity_decode( $field['values'] );
        
        // Only Enqueue if it is not enqueued yet
        if( apply_filters('wp_custom_fields_code_field_js', true) && ! wp_script_is('mirror-js', 'enqueued') ) {
            wp_enqueue_script('mirror-js');
        } ?>
        
            <textarea class="wp-custom-fields-code-editor-value" id="<?php echo $id; ?>" name="<?php echo $name; ?>" data-mode="<?php echo $mode; ?>"> 
                <?php echo $values; ?>
            </textarea>

        <?php 

    }

    /**
     * Returns the global configurations for this field
     *
     * @return array $configurations The configurations
     */  
    public static function configurations() {

        $configurations = [
            'type'      => 'code',
            'defaults'  => ''
        ];
            
        return apply_filters( 'wp_custom_fields_code_config', $configurations );

    }
    
}