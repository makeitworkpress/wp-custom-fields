<?php
 /** 
  * Displays a dimensions field
  */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) { 
    die; 
} 

class Divergent_Field_Dimensions implements Divergent_Field {
    
    public static function render($field = array()) {
        
        // Basic Variables
        $output = '';
        $border = isset($field['borders']) ? $field['borders'] : '';
        
        // Control each side of the box model
        if($border == 'all') {
            
            $sides = array(
                'top' => __('Top', DIVERGENT_LANGUAGE), 
                'right' => __('Right', DIVERGENT_LANGUAGE), 
                'bottom' => __('Bottom', DIVERGENT_LANGUAGE), 
                'left' => __('Left', DIVERGENT_LANGUAGE)
            );
            
            foreach($sides as $key => $side) {
                
                $icon = 'border_' . $key;
                
                $output .= '<div class="divergent-field-left">';
                
                $borderwidth[$key] = isset($field['values'][$key]['width']) ? $field['values'][$key]['width'] : array(); 
                $output .= Divergent_Fields::dimension_field( $field['id'] . '-' . $key. '-width' , $field['name'] . '[' . $key . '][width]', $borderwidth[$key], '', $side, $icon);
                
                $output .= '</div>';
            }
            
        // One control
        } else {
            
            $icon = 'border_outer';
            
            $borderwidth = isset($field['values']['width']) ? $field['values']['width'] : array(); 
            $output .= Divergent_Fields::dimension_field( $field['id'] . '-width', $field['name'] . '[width]', $borderwidth);        

        }
        
        return $output;    
    }
    
    public static function configurations() {
        $configurations = array(
            'type' => 'dimensions'
        );
            
        return $configurations;
    }
    
}