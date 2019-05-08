<?php 
/**
 * Displays an importer and exporter of saved option data 
 */
namespace MakeitWorkPress\WP_Custom_Fields\Fields;
use MakeitWorkPress\WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class Export implements Field {
    
    public static function render($field = array()) {

        $output = '';
        
        // An option id should be provided to output anything
        if( ! isset($field['option_id']) ) {
            return $output;
        }
        
        global $pagenow;

        switch( $pagenow ) {
            case 'post.php':

                if( ! current_user_can('edit_posts') || ! current_user_can('edit_pages') ) {
                    return $output;
                }

                global $post;
                $options = get_post_meta( $post->ID, $field['option_id'], true );
                break;
            case 'profile.php';
            case 'user-edit.php';

                if( ! current_user_can('edit_users') ) {
                    return $output;
                }

                $user = $pagenow == 'profile.php' ? get_current_user_id() : $_GET['user_id']; 
                $options = get_term_meta( intval($user), $field['option_id'], true );
                
                break;
            case 'term.php';

                if( ! current_user_can('edit_posts') || ! current_user_can('edit_pages') ) {
                    return $output;
                }            

                $options = get_term_meta( intval($_GET['tag_ID']), $field['option_id'], true );
                
                break;
            default:

                if( ! current_user_can('manage_options') ) {
                    return $output;
                } 

                $options = get_option( $field['option_id'] );

        }

        
        $output .= '<div class="wp-custom-fields-export">';
        $output .= '    <label for="' . $field['id'] . '-export">' . __('Exportable Settings', 'wp-custom-fields') . '</label>';
        $output .= '    <textarea id="' . $field['id'] . '-export">' . base64_encode( serialize($options) ) . '</textarea>';      
        $output .= '    <label for="' . $field['id'] . '-import">' . __('Import Settings', 'wp-custom-fields') . '</label>';        
        $output .= '    <textarea id="' . $field['id'] . '-import" name="import_value">' . base64_encode( serialize($options) ) . '</textarea>';
        $output .= '    <input id="' . $field['id'] . '-import" name="import_submit" class="button wp-custom-fields-import-settings" type="submit" value="' . __('Import', 'wp-custom-fields') . '" />'; 
        $output .= '</div>';
        
        return $output;    
    }
    
    public static function configurations() {
        $configurations = array(
            'type'      => 'export',
            'defaults'  => ''
        );
            
        return $configurations;
    }
    
}