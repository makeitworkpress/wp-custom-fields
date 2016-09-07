<?php
 /** 
  * Displays a text input field
  *
  * @todo Extend video preview capabilities / display
  */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) { 
    die; 
} 

class Divergent_Field_Upload implements Divergent_Field {
    
    public static function render($field = array()) {
        
        $type = isset($field['subtype']) ? $field['subtype'] : '';
        $button = isset($field['button']) ? $field['button'] : __('Insert', DIVERGENT_LANGUAGE);
        $title = isset($field['title']) ? $field['title'] : __('Add Media', DIVERGENT_LANGUAGE);
        $multiple = isset($field['multiple']) ? $field['multiple'] : true;
        $url = isset($field['url']) ? $field['url'] : false;
        $media = ! empty($field['values']) ? explode(',', rtrim($field['values'], ',')) : array();
        
        $output = '<div class="divergent-upload-wrapper" data-type="' . $type . '" data-button="' . $button . '" data-title="' . $title . '" data-multiple="' . $multiple . '">';
        foreach($media as $medium) {
            if( ! empty($medium) ) {
                $output .= '    <div class="divergent-single-media" data-id="' . $medium . '">';
                $output .= wp_get_attachment_image($medium, 'thumbnail', true);

                if( $url ) {
                    $attachment_url = wp_get_attachment_url( $medium );
                    $output .= '        <div class="divergent-media-url"><i class="fa fa-chain"></i><input type="text" readonly="readonly" value="' . $attachment_url . '" /></div>';              
                }

                $output .= '        <a href="#" class="divergent-upload-remove"><i class="fa fa-times-circle"></i></a>'; 
                $output .= '    </div>';
            }
        }
        $output .= '    <div class="divergent-single-media empty">';
        $output .= '        <a href="#" class="divergent-upload-add"><i class="fa fa-plus-circle"></i> ' . __('Add', DIVERGENT_LANGUAGE) . '</a>'; 
        $output .= '    </div>';
        $output .= '    <input id="' . $field['id'] . '" name="' . $field['name']  . '" class="divergent-upload-value" type="hidden" value="' . $field['values'] . '" />'; 
        $output .= '</div>';
        
        return $output;    
    }
    
    public static function configurations() {
        $configurations = array(
            'type' => 'upload'
        );
            
        return $configurations;
    }
    
}