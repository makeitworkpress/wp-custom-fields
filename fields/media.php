<?php
 /** 
  * Displays a text input field
  *
  * @todo Extend video preview capabilities / display
  */
namespace WP_Custom_Fields\Fields;
use WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class Media implements Field {
    
    public static function render( $field = array() ) {
        
        $add            = isset($field['add']) ? $field['add'] : __('Add', 'wp-custom-fields');
        $type           = isset($field['subtype']) ? $field['subtype'] : '';
        $button         = isset($field['button']) ? $field['button'] : __('Insert', 'wp-custom-fields');
        $title          = isset($field['title']) ? $field['title'] : __('Add Media', 'wp-custom-fields');
        $multiple       = isset($field['multiple']) ? $field['multiple'] : true;
        $url            = isset($field['url']) ? $field['url'] : false;
        $media          = ! empty($field['values']) ? explode(',', rtrim($field['values'], ',')) : array();
        
        $output = '<div class="wp-custom-fields-upload-wrapper" data-type="' . $type . '" data-button="' . $button . '" data-title="' . $title . '" data-multiple="' . $multiple . '">';
        
        foreach($media as $medium) {
            
            if( empty($medium) )
                continue;
            
            $output .= '    <div class="wp-custom-fields-single-media" data-id="' . $medium . '">';
            $output .= wp_get_attachment_image($medium, 'thumbnail', true);

            if( $url ) {
                $attachment_url = wp_get_attachment_url( $medium );
                $output .= '        <div class="wp-custom-fields-media-url">';
                $output .= '            <i class="material-icons">link</i>';
                $output .= '            <input type="text" readonly="readonly" value="' . $attachment_url . '" />';
                $output .= '        </div>';              
            }

            $output .= '        <a href="#" class="wp-custom-fields-upload-remove"><i class="material-icons">clear</i></a>'; 
            $output .= '    </div>';
            
        }
        
        $output .= '    <div class="wp-custom-fields-single-media empty">';
        $output .= '        <a href="#" class="wp-custom-fields-upload-add">';
        $output .= '            <i class="material-icons">add</i> ';
        $output .=              $add;
        $output .=          '</a>'; 
        $output .= '    </div>';
        $output .= '    <input id="' . $field['id'] . '" name="' . $field['name']  . '" class="wp-custom-fields-upload-value" type="hidden" value="' . $field['values'] . '" />'; 
        $output .= '</div>';
        
        return $output;    
    }
    
    public static function configurations() {
        $configurations = array(
            'type'      => 'media',
            'defaults'  => ''
        );
            
        return $configurations;
    }
    
}