<?php
 /** 
  * Displays a background input field
  */
namespace WP_Custom_Fields\Fields;
use WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) 
    die;

class Background implements Field {
    
    public static function render( $field = array() ) {
             
        // Background Colorpicker
        $colorpicker['values'] = isset( $field['values']['color'] ) ? $field['values']['color'] : '';
        $colorpicker['name']   = $field['name'] . '[color]';
        $colorpicker['id']     = $field['id'] . '-color';

        // Media Upload
        $upload_custom['subtype']   = 'image';
        $upload_custom['button']    = __('Add Background', 'wp-custom-fields');
        $upload_custom['title']     = __('Select a Background', 'wp-custom-fields');
        $upload_custom['multiple']  = false; 
        $upload_custom['id']        = $field['id'] . '-upload';
        $upload_custom['name']      = $field['name'] . '[upload]';
        $upload_custom['values']    = isset($field['values']['upload']) ? $field['values']['upload'] : '';
        
        $output = '<div class="wp-custom-fields-background-image wp-custom-fields-field-left">';
        $output .=  Media::render( $upload_custom );        
        $output .= '</div>';

        
        $output .= '<div class="wp-custom-fields-background-attributes wp-custom-fields-field-left">';         
        $output .=  Colorpicker::render( $colorpicker );
        
        // Background Select Attributes
        $background_attributes = array(
            'repeat' => array(
                'placeholder' => __('Repeat', 'wp-custom-fields'),
                'options'  => array(
                    'no-repeat' => __('No Repeat', 'wp-custom-fields'),
                    'repeat' => __('Repeat', 'wp-custom-fields'),
                    'repeat-x' => __('Repeat Horizontally', 'wp-custom-fields'),
                    'repeat-y' => __('Repeat Vertically', 'wp-custom-fields'),
                    'inherit' => __('Inherit', 'wp-custom-fields')
                )
            ),
            'attachment' => array(
                'placeholder' => __('Attachment', 'wp-custom-fields'),
                'options'  => array(
                    'fixed' => __('Fixed', 'wp-custom-fields'),
                    'scroll' => __('Scroll', 'wp-custom-fields'),
                    'inherit' => __('Inherit', 'wp-custom-fields') 
                )
            ),
            'size' => array(
                'placeholder' => __('Size', 'wp-custom-fields'),
                'options'  => array(
                    'cover' => __('Cover', 'wp-custom-fields'),
                    'contain' => __('Contain', 'wp-custom-fields'),
                    '100%' => __('100%', 'wp-custom-fields'),
                    'inherit' => __('Inherit', 'wp-custom-fields')
                )
            ),            
            'position' => array(
                'placeholder' => __('Position', 'wp-custom-fields'),
                'options'  => array(
                    'center top' => __('Center Top', 'wp-custom-fields'),
                    'center center' => __('Center Center', 'wp-custom-fields'),
                    'center bottom' => __('Center Bottom', 'wp-custom-fields'),
                    'left top' => __('Left Top', 'wp-custom-fields'),
                    'left center' => __('Left Center', 'wp-custom-fields'),   
                    'left bottom' => __('Left Bottom', 'wp-custom-fields'), 
                    'right top' => __('Right Top', 'wp-custom-fields'), 
                    'right center' => __('Right Center', 'wp-custom-fields'), 
                    'right bottom' => __('Right Bottom', 'wp-custom-fields')
                )
            )
        );        
        
        // Loop through all the defined attributes
        foreach($background_attributes as $key => $attribute) {
            
            // Store the value of the current select group
            $field_custom[$key]['options']      = $attribute['options'];
            $field_custom[$key]['placeholder']  = $attribute['placeholder'];
            $field_custom[$key]['id']           = $field['id']  . '-' . $key ;
            $field_custom[$key]['name']         = $field['name']. '[' . $key . ']';
            $field_custom[$key]['values']       = isset($field['values'][$key]) ? $field['values'][$key] : '';
            
            // We use the select field class to display our recurring select fields. Easy, isn't it?
            $output .= Select::render($field_custom[$key]);
        }
        
        $output .= '</div>';
        
        return $output;    
    }
    
    public static function configurations() {
        $configurations = array(
            'type'      => 'background',
            'defaults'  => array()
        );
            
        return $configurations;
    }
    
}