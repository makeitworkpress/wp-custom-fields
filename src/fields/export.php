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
        
        // An option id should be provided
        if( ! isset($field['option_id']) )
            return;
        
        global $pagenow;

        switch( $pagenow ) {
            case 'post.php':
                global $post;
                $options = get_post_meta( $post->ID, $field['option_id'], true );
                break;
            case 'profile.php';
            case 'user-edit.php';
                $user = $pagenow == 'profile.php' ? get_current_user_id() : $_GET['user_id']; 
                get_term_meta( $user, $field['option_id'], true );
                break;
            case 'term.php';
                get_term_meta(  $_GET['tag_ID'], $field['option_id'], true );
                break;
            default:
                $options = get_option($field['option_id']);
        }
        
        $output = '<div class="wp-custom-fields-export">';
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