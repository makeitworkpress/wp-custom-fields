<?php
 /** 
  * Displays a dimensions field
  */
namespace MakeitWorkPress\WP_Custom_Fields\Fields;
use MakeitWorkPress\WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die; 

class Dimensions implements Field {
    
    public static function render($field = array()) {
        
        // Basic Variables
        $output = '';
        $border = isset( $field['borders'] ) ? $field['borders'] : '';
        $step   = isset( $field['step'] ) ? $field['step'] : 1;
        $units  = isset( $field['units'] ) ? $field['units'] : false;
        
        // Control each side of the box model
        if( $border == 'all' ) {
            
            $sides = array(
                'top'       => __('Top', 'wp-custom-fields'), 
                'right'     => __('Right', 'wp-custom-fields'), 
                'bottom'    => __('Bottom', 'wp-custom-fields'), 
                'left'      => __('Left', 'wp-custom-fields')
            );
            
            foreach( $sides as $key => $side ) {
             
                $output            .= '<div class="wp-custom-fields-field-left">';
                $output            .= Dimension::render( array(
                    'step'          => $step,
                    'icon'          => 'border_' . $key,
                    'id'            => $field['id'] . '-' . $key,
                    'name'          => $field['name'] . '[' . $key . ']',
                    'placeholder'   => $side,
                    'units'         => $units,
                    'values'        => isset($field['values'][$key]) ? $field['values'][$key] : array()               
                ) );
                $output            .= '</div>';
                
            }
            
        // One control
        } else {
            
            $output             .= Dimension::render( array(
                'step'      => $step,
                'icon'      => 'border_outer',
                'id'        => $field['id'],
                'name'      => $field['name'],
                'units'     => $units,
                'values'    => isset($field['values']) ? $field['values'] : array()
            ) );        

        }
        
        return $output;    
    }
    
    public static function configurations() {
        $configurations = array(
            'type'      => 'dimensions',
            'defaults'  => array()
        );
            
        return $configurations;
    }
    
}