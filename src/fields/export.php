<?php 
/**
 * Displays an importer and exporter of saved option data 
 */
namespace MakeitWorkPress\WP_Custom_Fields\Fields;
use MakeitWorkPress\WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined('ABSPATH') ) {
    die;
}

class Export implements Field {

    /**
     * Prepares the variables and renders the field
     * 
     * @param   array $field The array with field attributes data-alpha
     * @return  void
     */      
    public static function render( $field = [] ) {
        
        // Check before proceeding
        if( ! isset($field['key']) || ! isset($field['context']) || ! is_user_logged_in() ) {
            return;
        }

        $configurations = self::configurations();
        $button = isset( $field['labels']['button'] ) ? esc_html($field['labels']['button']) : $configurations['labels']['button'];
        $label  = isset( $field['labels']['label'] ) ? esc_html($field['labels']['label']) : $configurations['labels']['label'];
        $id     = esc_attr($field['id']);
        $key    = sanitize_key($field['key']); 

        switch( $field['context'] ) {
            case 'post':

                if( ! current_user_can('edit_posts') || ! current_user_can('edit_pages') ) {
                    return;
                }

                global $post;
                $options = get_post_meta( $post->ID, $key, true );
                break;
            case 'user':

                if( ! current_user_can('edit_users') ) {
                    return;
                }

                global $pagenow;
                $user = $pagenow == 'profile.php' ? get_current_user_id() : $_GET['user_id']; 
                $options = get_term_meta( intval($user), $key, true );
                
                break;
            case 'term':

                if( ! current_user_can('edit_posts') || ! current_user_can('edit_pages') ) {
                    return;
                }            

                $options = get_term_meta( intval($_GET['tag_ID']), $key, true );
                
                break;
            case 'options':

                if( ! current_user_can('manage_options') ) {
                    return;
                } 

                $options = get_option( $field['key'] );

        } 
        
        // We should have options
        if( ! isset($options) ) {
            return; 
        } ?>

            <div class="wp-custom-fields-export">  
                <label for="<?php echo $id; ?>-import"><?php echo $label; ?></label>       
                <textarea id="<?php echo $id; ?>-import" name="import_value"><?php echo base64_encode( serialize($options) ); ?></textarea>
                <input id="<?php echo $id; ?>-import" name="import_submit" class="button wp-custom-fields-import-settings" type="submit" value="<?php echo $button; ?>" /> 
            </div>
        
        <?php

    }
    
    /**
     * Returns the global configurations for this field
     *
     * @return array $configurations The configurations
     */     
    public static function configurations() {

        $configurations = [
            'type'      => 'export',
            'defaults'  => '',
            'labels'    => [
                'button'    => __('Import', 'wp-custom-fields'),
                'label'     => __('The Current Settings. Replace these with a different encoded string to import new settings.', 'wp-custom-fields')
            ]
        ];
            
        return apply_filters( 'wp_custom_fields_export_config', $configurations );
        
    }
    
}