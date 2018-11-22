<?php
 /** 
  * Displays an editor field
  */
namespace MakeitWorkPress\WP_Custom_Fields\Fields;
use MakeitWorkPress\WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die; 

class Editor implements Field {
    
    public static function render($field = array()) {

        if( isset($field['settings']) ) {
            foreach( $field['settings'] as $key => $setting ) {
                // The keys should be in the supported format
                if( ! in_array($key, ['wpautop', 'media_buttons', 'textarea_rows', 'tabindex', 'editor_css', 'editor_class', 'editor_height', 'teeny', 'dfw', 'tinymce', 'quicktags', 'drag_drop_upload']) ) {
                    continue;
                }

                $settings[$key] = $setting;
            }
        }
        
        $settings['textarea_name'] = $field['name'];
        
        ob_start();
        wp_editor($field['values'], $field['id'], $settings);
        return ob_get_clean();
        
    }
    
    public static function configurations() {
        $configurations = array(
            'type'      => 'editor',
            'defaults'  => ''
        );
            
        return $configurations;
    }
    
}