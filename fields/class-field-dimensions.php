<?php
 /** 
  * Displays a dimensions field
  */
namespace Classes\Divergent\Fields;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die; 

class Divergent_Field_Dimensions implements Divergent_Field {
    
    public static function render($field = array()) {
        
        // Basic Variables
        $output = '';
        $border = isset($field['borders']) ? $field['borders'] : '';
        
        // Control each side of the box model
        if( $border == 'all' ) {
            
            $sides = array(
                'top' => __('Top', 'divergent'), 
                'right' => __('Right', 'divergent'), 
                'bottom' => __('Bottom', 'divergent'), 
                'left' => __('Left', 'divergent')
            );
            
            foreach($sides as $key => $side) {
                
                $dimension                  = array();
                $dimension['icon']          = 'border_' . $key; 
                $dimension['id']            = $field['id'] . '-' . $key. '-width'; 
                $dimension['name']          = $field['name'] . '[' . $key . '][width]'; 
                $dimension['placeholder']   = $side; 
                $dimension['values']        = isset($field['values'][$key]['width']) ? $field['values'][$key]['width'] : array();                
                
                $output                    .= '<div class="divergent-field-left">';
                $output                    .= Divergent_Field_Dimension::dimension_field( $dimension );
                $output                    .= '</div>';
                
            }
            
        // One control
        } else {
            
            $dimension['icon']   = 'border_outer'; 
            $dimension['id']     = $field['id'] . '-width'; 
            $dimension['name']   = $field['name'] . '[width]'; 
            $dimension['values'] = isset($field['values']['width']) ? $field['values']['width'] : array();
            
            $output             .= Divergent_Field_Dimension::render( $dimension );        

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