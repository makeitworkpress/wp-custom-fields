<?php
 /** 
  * Displays a location field, including a google map
  */
namespace Divergent\Fields;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class Divergent_Field_Boxshadow implements Divergent_Field {
    
    public static function render($field = array()) {
        
        $output = '<div class="divergent-boxshadow">';
        
        // Dimensions
        $output .= '<div class="divergent-boxshadow-dimensions divergent-field-left">';
        $output .= '<label>' . __('Boxshadow Offset, Blur and Spread', 'divergent') . '</label>';
        $pixels  = array(
            'x'         => __('x-offset', 'divergent'),
            'y'         => __('y-offset', 'divergent'),
            'blur'      => __('blur', 'divergent'),
            'spread'    => __('spread', 'divergent')
        );
        
        foreach( $pixels as $key => $h ) {
            $n = isset($field['values'][$key]) ? $field['values'][$key] : '';
            $output .= '<input id="' . $field['id'] . '-' . $key . '" name="' . $field['name']  . '['.$key.']" type="number" placeholder="' . $h  . '" value="' . $n . '" />';
        }
        $output .= '</div>';
        
        // Color
        $output .= '<div class="divergent-boxshadow-color divergent-field-left">';
        $output .= '<label>' . __('Boxshadow Color', 'divergent') . '</label>';
        $output .= Divergent_Field_Colorpicker::render( array(
            'id'     => $field['id'] . '-color',   
            'name'   => $field['name'] . '[color]',
            'values' => isset($field['values']['color']) ? $field['values']['color'] : ''     
        ) );
        $output .= '</div>';
        
        // Type of boxshadow
        $output .= '<div class="divergent-boxshadow-type divergent-field-left">';
        $output .= '<label>' . __('Boxshadow Style', 'divergent') . '</label>';
        $output .= Divergent_Field_Select::render( array(
            'id'        => $field['id']  . '-type',
            'name'      => $field['name']. '[type]',
            'options'  => array( '' => __('Default', 'divergent'), 'inset' => __('Inset', 'divergent') ),             
            'placeholder' => __('Select Type', 'divergent'),         
            'values'    => isset($field['values']['type']) ? $field['values']['type'] : ''
        
        ) ); 
        $output .= '</div>';
        
        $output .= '</div>';
        
        return $output;
  
    }
    
    public static function configurations() {
        $configurations = array(
            'type' => 'boxshadow'
        );
            
        return $configurations;
    }
    
}