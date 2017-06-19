<?php
 /** 
  * Displays a dimensions field
  */
namespace Divergent\Fields;

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
                'top'       => __('Top', 'divergent'), 
                'right'     => __('Right', 'divergent'), 
                'bottom'    => __('Bottom', 'divergent'), 
                'left'      => __('Left', 'divergent')
            );
            
            foreach($sides as $key => $side) {
             
                $output            .= '<div class="divergent-field-left">';
                $output            .= Divergent_Field_Dimension::render( array(
                    'step'          => isset($field['step']) ? $field['step'] : 1,
                    'icon'          => 'border_' . $key,
                    'id'            => $field['id'] . '-' . $key,
                    'name'          => $field['name'] . '[' . $key . ']',
                    'placeholder'   => $side,
                    'values'        => isset($field['values'][$key]) ? $field['values'][$key] : array()               
                ) );
                $output            .= '</div>';
                
            }
            
        // One control
        } else {
            
            $dimension['icon']   = 'border_outer'; 
            $dimension['id']     = $field['id']; 
            $dimension['name']   = $field['name']; 
            $dimension['values'] = isset($field['values']) ? $field['values'] : array();
            
            $output             .= Divergent_Field_Dimension::render( array(
                'step'      => isset($field['step']) ? $field['step'] : 1,
                'icon'      => 'border_outer',
                'id'        => $field['id'],
                'name'      => $field['name'],
                'values'    => isset($field['values']) ? $field['values'] : array()
            ) );        

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