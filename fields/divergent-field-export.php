<?php 
/**
 * Displays an importer and exporter of saved option data 
 */
namespace Divergent\Fields;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class Divergent_Field_Export implements Divergent_Field {
    
    public static function render($field = array()) {
        
        global $post;
        $screen     = get_current_screen();
        $options    = $screen->parent_file == 'edit.php' ? get_post_meta($post->ID, $field['option_id'], true) : get_option($field['option_id']);
        
        $output = '<div class="divergent-export">';
        $output .= '    <label for="' . $field['id'] . '-export">' . __('Exportable Settings', 'divergent') . '</label>';
        $output .= '    <textarea id="' . $field['id'] . '-export">' . base64_encode( serialize($options) ) . '</textarea>';      
        $output .= '    <label for="' . $field['id'] . '-import">' . __('Import Settings', 'divergent') . '</label>';        
        $output .= '    <textarea id="' . $field['id'] . '-import" name="import_value">' . base64_encode( serialize($options) ) . '</textarea>';
        $output .= '    <input id="' . $field['id'] . '-import" name="import_submit" class="button divergent-import-settings" type="submit" value="' . __('Import', 'divergent') . '" />'; 
        $output .= '</div>';
        
        return $output;    
    }
    
    public static function configurations() {
        $configurations = array(
            'type' => 'export'
        );
            
        return $configurations;
    }
    
}