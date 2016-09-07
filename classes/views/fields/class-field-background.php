<?php
 /** 
  * Displays a background input field
  */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) { 
    die; 
} 

class Divergent_Field_Background implements Divergent_Field {
    
    public static function render($field = array()) {
             
        // Background Colorpicker
        $colorpicker['values'] = isset($field['values']['color']) ? $field['values']['color'] : '';
        $colorpicker['name']   = $field['name'] . '[color]';
        $colorpicker['id']     = $field['id'] . '-color';

        // Media Upload
        $upload_custom['subtype']   = 'image';
        $upload_custom['button']    = __('Add Background', DIVERGENT_LANGUAGE);
        $upload_custom['title']     = __('Select a Background', DIVERGENT_LANGUAGE);
        $upload_custom['multiple']  = false; 
        $upload_custom['id']        = $field['id'] . '-upload';
        $upload_custom['name']      = $field['name'] . '[upload]';
        $upload_custom['values']    = isset($field['values']['upload']) ? $field['values']['upload'] : '';
        
        $output = '<div class="divergent-background-image divergent-field-left">';
        $output .=  Divergent_Field_Upload::render($upload_custom);        
        $output .= '</div>';

        
        $output .= '<div class="divergent-background-attributes divergent-field-left">';         
        $output .=  Divergent_Field_Colorpicker::render($colorpicker);
        
        // Background Select Attributes
        $background_attributes = array(
            'repeat' => array(
                'placeholder' => __('Repeat', DIVERGENT_LANGUAGE),
                'options'  => array(
                    'no-repeat' => __('No Repeat', DIVERGENT_LANGUAGE),
                    'repeat' => __('Repeat', DIVERGENT_LANGUAGE),
                    'repeat-x' => __('Repeat Horizontally', DIVERGENT_LANGUAGE),
                    'repeat-y' => __('Repeat Vertically', DIVERGENT_LANGUAGE),
                    'inherit' => __('Inherit', DIVERGENT_LANGUAGE)
                )
            ),
            'attachment' => array(
                'placeholder' => __('Attachment', DIVERGENT_LANGUAGE),
                'options'  => array(
                    'fixed' => __('Fixed', DIVERGENT_LANGUAGE),
                    'scroll' => __('Scroll', DIVERGENT_LANGUAGE),
                    'inherit' => __('Inherit', DIVERGENT_LANGUAGE) 
                )
            ),
            'size' => array(
                'placeholder' => __('Size', DIVERGENT_LANGUAGE),
                'options'  => array(
                    'cover' => __('Cover', DIVERGENT_LANGUAGE),
                    'contain' => __('Contain', DIVERGENT_LANGUAGE),
                    '100%' => __('100%', DIVERGENT_LANGUAGE),
                    'inherit' => __('Inherit', DIVERGENT_LANGUAGE)
                )
            ),            
            'position' => array(
                'placeholder' => __('Position', DIVERGENT_LANGUAGE),
                'options'  => array(
                    'center top' => __('Center Top', DIVERGENT_LANGUAGE),
                    'center center' => __('Center Center', DIVERGENT_LANGUAGE),
                    'center bottom' => __('Center Bottom', DIVERGENT_LANGUAGE),
                    'left top' => __('Left Top', DIVERGENT_LANGUAGE),
                    'left center' => __('Left Center', DIVERGENT_LANGUAGE),   
                    'left bottom' => __('Left Bottom', DIVERGENT_LANGUAGE), 
                    'right top' => __('Right Top', DIVERGENT_LANGUAGE), 
                    'right center' => __('Right Center', DIVERGENT_LANGUAGE), 
                    'right bottom' => __('Right Bottom', DIVERGENT_LANGUAGE)
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
            
            // We use the divergent select field class to display our recurring select fields. Easy, isn't it?
            $output .= Divergent_Field_Select::render($field_custom[$key]);
        }
        
        $output .= '</div>';
        
        return $output;    
    }
    
    public static function configurations() {
        $configurations = array(
            'type' => 'background'
        );
            
        return $configurations;
    }
    
}