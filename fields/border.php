<?php
 /** 
  * Displays a border field
  */
namespace WP_Custom_Fields\Fields;
use WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class Border implements Field {
    
    public static function render($field = array()) {
        
        // Basic Variables
        $output = '';
        $border = isset($field['borders']) ? $field['borders'] : '';
        $type['options'] = array(
            'solid'     => __('Solid', 'wp-custom-fields'), 
            'dotted'    => __('Dotted', 'wp-custom-fields'),  
            'dashed'    => __('Dashed', 'wp-custom-fields'),  
            'double'    => __('Double', 'wp-custom-fields'),  
            'groove'    => __('Groove', 'wp-custom-fields'),  
            'ridge'     => __('Ridge', 'wp-custom-fields'), 
            'inset'     => __('Inset', 'wp-custom-fields'),  
            'outset'    => __('Outset', 'wp-custom-fields'), 
        );
        $type['placeholder'] = __('Border Style', 'wp-custom-fields');
        
        // Control each side of the box
        if( $border == 'all' ) {
            
            $sides = array(
                'top'       => __('Top', 'wp-custom-fields'), 
                'right'     => __('Right', 'wp-custom-fields'), 
                'bottom'    => __('Bottom', 'wp-custom-fields'), 
                'left'      => __('Left', 'wp-custom-fields')
            );
            
            foreach($sides as $key => $side) {

                $output             .= '<div class="wp-custom-fields-single-border">';

                
                // Dimensions
                $output             .= ' <div class="wp-custom-fields-field-left">';
                $output             .= Dimension::render( array(
                    'icon'           => 'border_' . $key,
                    'id'             => $field['id'] . '-' . $key . '-width',
                    'name'           => $field['name'] . '[' . $key . '][width]',
                    'placeholder'    => $side,
                    'values'         => isset($field['values'][$key]['width']) ? $field['values'][$key]['width'] : array()               
                ) );
                $output                 .= ' </div>';

                
                // Border Type
                $type['id']             = $field['id']  . '-' . $key. '-style';
                $type['name']           = $field['name'] . '[' . $key . '][style]';
                $type['values']         = isset($field['values'][$key]['style']) ? $field['values'][$key]['style'] : '';
                $type['placeholder']    = __('Border Style', 'wp-custom-fields');
                
                $output                .= ' <div class="wp-custom-fields-field-left">';
                $output                .= Select::render($type);
                $output                .= ' </div>';
                
                
                // Colorpicker
                $colorpicker['values']  = isset($field['values'][$key]['color']) ? $field['values'][$key]['color'] : '';
                $colorpicker['id']      = $field['id'] . '-' . $key . '-color';
                $colorpicker['name']    = $field['name']. '[' . $key . '][color]';
                
                $output                .= ' <div class="wp-custom-fields-field-left">';
                $output                .= Colorpicker::render($colorpicker);                
                $output                .= ' </div>';
                
                
                $output                .= '</div><!-- .wp-custom-fields-single-border -->';
                
            }
            
        // One control
        } else {
          
            // Dimensions       
            $dimension['icon']          = 'border_outer'; 
            $dimension['id']            = $field['id'] . '-width'; 
            $dimension['name']          = $field['name'] . '[width]'; 
            $dimension['placeholder']   = __('Border Width', 'wp-custom-fields'); 
            $dimension['values']        = isset($field['values']['width']) ? $field['values']['width'] : array();            
            
            $output                    .= '<div class="wp-custom-fields-field-left">';
            $output                    .= Dimension::render( $dimension );
            $output                    .= '</div>';
            
            
            // Border Type
            $type['id']                 = $field['id']  . '-style'; 
            $type['name']               = $field['name'] . '[style]'; 
            $type['values']             = isset($field['values']['style']) ? $field['values']['style'] : ''; 
            $type['placeholder']        = __('Border Style', 'wp-custom-fields');       
            
            $output                    .= '<div class="wp-custom-fields-field-left">';
            $output                    .= Select::render($type);   
            $output                    .= '</div>';
            

            // Colorpicker
            $colorpicker['values']      = isset($field['values']['color']) ? $field['values']['color'] : '';
            $colorpicker['id']          = $field['id'] . '-color';
            $colorpicker['name']        = $field['name'] . '[color]';              
            
            $output                    .= '<div class="wp-custom-fields-field-left">';
            $output                    .= Colorpicker::render($colorpicker);
            $output                    .= '</div>';                 

        }
        
        return $output;    
    }
    
    public static function configurations() {
        $configurations = array(
            'type'      => 'border',
            'defaults'  => array()
        );
            
        return $configurations;
    }
    
}