<?php
 /** 
  * Displays a background input field
  */
namespace Divergent\Fields;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) 
    die;

class Divergent_Field_Background implements Divergent_Field {
    
    public static function render( $field = array() ) {
             
        // Background Colorpicker
        $colorpicker['values'] = isset( $field['values']['color'] ) ? $field['values']['color'] : '';
        $colorpicker['name']   = $field['name'] . '[color]';
        $colorpicker['id']     = $field['id'] . '-color';

        // Media Upload
        $upload_custom['subtype']   = 'image';
        $upload_custom['button']    = __('Add Background', 'divergent');
        $upload_custom['title']     = __('Select a Background', 'divergent');
        $upload_custom['multiple']  = false; 
        $upload_custom['id']        = $field['id'] . '-upload';
        $upload_custom['name']      = $field['name'] . '[upload]';
        $upload_custom['values']    = isset($field['values']['upload']) ? $field['values']['upload'] : '';
        
        $output = '<div class="divergent-background-image divergent-field-left">';
        $output .=  Divergent_Field_Media::render( $upload_custom );        
        $output .= '</div>';

        
        $output .= '<div class="divergent-background-attributes divergent-field-left">';         
        $output .=  Divergent_Field_Colorpicker::render( $colorpicker );
        
        // Background Select Attributes
        $background_attributes = array(
            'repeat' => array(
                'placeholder' => __('Repeat', 'divergent'),
                'options'  => array(
                    'no-repeat' => __('No Repeat', 'divergent'),
                    'repeat' => __('Repeat', 'divergent'),
                    'repeat-x' => __('Repeat Horizontally', 'divergent'),
                    'repeat-y' => __('Repeat Vertically', 'divergent'),
                    'inherit' => __('Inherit', 'divergent')
                )
            ),
            'attachment' => array(
                'placeholder' => __('Attachment', 'divergent'),
                'options'  => array(
                    'fixed' => __('Fixed', 'divergent'),
                    'scroll' => __('Scroll', 'divergent'),
                    'inherit' => __('Inherit', 'divergent') 
                )
            ),
            'size' => array(
                'placeholder' => __('Size', 'divergent'),
                'options'  => array(
                    'cover' => __('Cover', 'divergent'),
                    'contain' => __('Contain', 'divergent'),
                    '100%' => __('100%', 'divergent'),
                    'inherit' => __('Inherit', 'divergent')
                )
            ),            
            'position' => array(
                'placeholder' => __('Position', 'divergent'),
                'options'  => array(
                    'center top' => __('Center Top', 'divergent'),
                    'center center' => __('Center Center', 'divergent'),
                    'center bottom' => __('Center Bottom', 'divergent'),
                    'left top' => __('Left Top', 'divergent'),
                    'left center' => __('Left Center', 'divergent'),   
                    'left bottom' => __('Left Bottom', 'divergent'), 
                    'right top' => __('Right Top', 'divergent'), 
                    'right center' => __('Right Center', 'divergent'), 
                    'right bottom' => __('Right Bottom', 'divergent')
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