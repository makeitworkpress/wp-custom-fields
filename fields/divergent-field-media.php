<?php
 /** 
  * Displays a text input field
  *
  * @todo Extend video preview capabilities / display
  */
namespace Divergent\Fields;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class Divergent_Field_Media implements Divergent_Field {
    
    public static function render($field = array()) {
        
        $type           = isset($field['subtype']) ? $field['subtype'] : '';
        $button         = isset($field['button']) ? $field['button'] : __('Insert', 'divergent');
        $title          = isset($field['title']) ? $field['title'] : __('Add Media', 'divergent');
        $multiple       = isset($field['multiple']) ? $field['multiple'] : true;
        $url            = isset($field['url']) ? $field['url'] : false;
        $media          = ! empty($field['values']) ? explode(',', rtrim($field['values'], ',')) : array();
        
        $output = '<div class="divergent-upload-wrapper" data-type="' . $type . '" data-button="' . $button . '" data-title="' . $title . '" data-multiple="' . $multiple . '">';
        
        foreach($media as $medium) {
            
            if( empty($medium) )
                continue;
            
            $output .= '    <div class="divergent-single-media" data-id="' . $medium . '">';
            $output .= wp_get_attachment_image($medium, 'thumbnail', true);

            if( $url ) {
                $attachment_url = wp_get_attachment_url( $medium );
                $output .= '        <div class="divergent-media-url">';
                $output .= '            <i class="material-icons">link</i>';
                $output .= '            <input type="text" readonly="readonly" value="' . $attachment_url . '" />';
                $output .= '        </div>';              
            }

            $output .= '        <a href="#" class="divergent-upload-remove"><i class="material-icons">clear</i></a>'; 
            $output .= '    </div>';
            
        }
        
        $output .= '    <div class="divergent-single-media empty">';
        $output .= '        <a href="#" class="divergent-upload-add">';
        $output .= '            <i class="material-icons">add</i> ';
        $output .=              __('Add', 'divergent');
        $output .=          '</a>'; 
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