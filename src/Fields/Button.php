<?php 
/**
 * Displays an importer and exporter of saved option data 
 */
namespace MakeitWorkPress\WP_Custom_Fields\Fields;
use MakeitWorkPress\WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class Button implements Field {

    /**
     * This field supports action, label, message and style as attributes
     * The label contains the contents of this button
     * The message attribute relates the the content of a message. If set to true, it will use the data output from an ajax action into a notification.
     * The data label accepts additional data to be processed by the ajax function
     * The style attribute accepts additional button classes, such as button-primary
     * 
     * @param array $field The array with field attributes
     * @return void
     */
    public static function render( array $field = [] ): void {
        
        // At least, an action should be provided
        if( ! isset($field['action']) ) {
            return;
        }

        $action     = $field['action'];
        foreach( ['action', 'data', 'label', 'message', 'style'] as $att ) {
            $function   = $att == 'data' ? 'json_encode' : 'esc_attr';
            ${$att}     = isset( $field[$att] ) ? call_user_func($function, $field[$att]) : '';
        } ?>
            <button data-action="<?php echo $action; ?>" data-data='<?php echo $data; ?>' data-message="<?php echo $message; ?>" class="wpcf-button button <?php echo $style; ?>">
                <?php echo $label; ?>
            </button>
        <?php
 
    }

    /**
     * Returns the global configurations for this field
     *
     * @return array $configurations The configurations
     */      
    public static function configurations(): array {

        $configurations = [
            'type'      => 'button',
            'defaults'  => ''
        ];
            
        return apply_filters( 'wp_custom_fields_button_config', $configurations );

    }
    
}