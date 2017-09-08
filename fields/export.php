<?php 
/**
 * Displays an importer and exporter of saved option data 
 */
namespace WP_Custom_Fields\Fields;
use WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class Export implements Field {
    
    public static function render($field = array()) {
        
        // An option id should be provied
        if( ! isset($field['option_id']) )
            return;
        
        global $post;
        $screen     = get_current_screen();
        $options    = $screen->parent_file == 'edit.php' ? get_post_meta($post->ID, $field['option_id'], true) : get_option($field['option_id']);
        
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