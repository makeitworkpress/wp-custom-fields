<?php
 /** 
  * Displays a border field
  */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) { 
    die; 
} 

class Divergent_Field_Border implements Divergent_Field {
    
    public static function render($field = array()) {
        
        // Basic Variables
        $output = '';
        $border = isset($field['borders']) ? $field['borders'] : '';
        $type['options'] = array(
            'solid'     => __('Solid', DIVERGENT_LANGUAGE), 
            'dotted'    => __('Dotted', DIVERGENT_LANGUAGE),  
            'dashed'    => __('Dashed', DIVERGENT_LANGUAGE),  
            'double'    => __('Double', DIVERGENT_LANGUAGE),  
            'groove'    => __('Groove', DIVERGENT_LANGUAGE),  
            'ridge'     => __('Ridge', DIVERGENT_LANGUAGE), 
            'inset'     => __('Inset', DIVERGENT_LANGUAGE),  
            'outset'    => __('Outset', DIVERGENT_LANGUAGE), 
        );
        $type['placeholder'] = __('Border Style', DIVERGENT_LANGUAGE);
        
        // Control each side of the box
        if($border == 'all') {
            
            $sides = array(
                'top'       => __('Top', DIVERGENT_LANGUAGE), 
                'right'     => __('Right', DIVERGENT_LANGUAGE), 
                'bottom'    => __('Bottom', DIVERGENT_LANGUAGE), 
                'left'      => __('Left', DIVERGENT_LANGUAGE)
            );
            
            foreach($sides as $key => $side) {

                $output .= '<div class="divergent-single-border">';

                // Dimensions
                $borderwidth[$key] = isset($field['values'][$key]['width']) ? $field['values'][$key]['width'] : ''; 
                $icon              = 'border_' . $key;

                $output .= '<div class="divergent-field-left">';
                $output .= Divergent_Fields::dimension_field( 
                    $field['id'] . '-' . $key. '-width' , 
                    $field['name'] . '[' . $key . '][width]', 
                    $borderwidth[$key], 
                    '',
                    __('Border Width', DIVERGENT_LANGUAGE),
                    $icon
                );
                $output .= '</div>';

                // Border Type
                $type['id']          = $field['id']  . '-' . $key. '-style';
                $type['name']        = $field['name'] . '[' . $key . '][style]';
                $type['values']      = isset($field['values'][$key]['style']) ? $field['values'][$key]['style'] : '';
                $type['placeholder'] = __('Border Style', DIVERGENT_LANGUAGE);
                
                $output .= '<div class="divergent-field-left">';
                $output .= Divergent_Field_Select::render($type);
                $output .= '</div>';
                
                // Colorpicker
                $colorpicker['values']  = isset($field['values'][$key]['color']) ? $field['values'][$key]['color'] : '';
                $colorpicker['id']      = $field['id'] . '-' . $key . '-color';
                $colorpicker['name']    = $field['name']. '[' . $key . '][color]';
                
                $output .= '<div class="divergent-field-left">';
                $output .= Divergent_Field_Colorpicker::render($colorpicker);                
                $output .= '</div>';
                
                $output .= '</div><!-- .divergent-single-border -->';
            }
            
        // One control
        } else {

            // Colorpicker
            $colorpicker['values']  = isset($field['values']['color']) ? $field['values']['color'] : '';
            $colorpicker['id']      = $field['id'] . '-color';
            $colorpicker['name']    = $field['name'] . '[color]';            
            
            // Dimensions       
            $borderwidth = isset($field['values']['width']) ? $field['values']['width'] : ''; 
            $icon        = 'border_outer';
            
            $output .= '<div class="divergent-field-left">';
            $output .= Divergent_Fields::dimension_field( $field['id'] . '-border', $field['name'] . '[width]', $borderwidth, '', __('Border Width', DIVERGENT_LANGUAGE), $icon);
            $output .= '</div>';
            
            // Border Type
            $type['id']          = $field['id']  . '-style'; 
            $type['name']        = $field['name'] . '[style]'; 
            $type['values']      = isset($field['values']['style']) ? $field['values']['style'] : ''; 
            $type['placeholder'] = __('Border Style', DIVERGENT_LANGUAGE);       
            
            $output .= '<div class="divergent-field-left">';
            $output .= Divergent_Field_Select::render($type);   
            $output .= '</div>';
            
            $output .= '<div class="divergent-field-left">';
            $output .= Divergent_Field_Colorpicker::render($colorpicker);
            $output .= '</div>';                 

        }
        
        return $output;    
    }
    
    public static function configurations() {
        $configurations = array(
            'type' => 'border'
        );
            
        return $configurations;
    }
    
}