<?php
 /** 
  * Displays a location field, including a google map
  */
namespace WP_Custom_Fields\Fields;
use WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class Boxshadow implements Field {
    
    public static function render($field = array()) {
        
        $output = '<div class="wp-custom-fields-boxshadow">';
        
        // Dimensions
        $output .= '<div class="wp-custom-fields-boxshadow-dimensions wp-custom-fields-field-left">';
        $output .= '<label>' . __('Boxshadow Offset, Blur and Spread', 'wp-custom-fields') . '</label>';
        $pixels  = array(
            'x'         => __('x-offset', 'wp-custom-fields'),
            'y'         => __('y-offset', 'wp-custom-fields'),
            'blur'      => __('blur', 'wp-custom-fields'),
            'spread'    => __('spread', 'wp-custom-fields')
        );
        
        foreach( $pixels as $key => $h ) {
            $n = isset($field['values'][$key]) ? $field['values'][$key] : '';
            $output .= '<input id="' . $field['id'] . '-' . $key . '" name="' . $field['name']  . '['.$key.']" type="number" placeholder="' . $h  . '" value="' . $n . '" />';
        }
        $output .= '</div>';
        
        // Color
        $output .= '<div class="wp-custom-fields-boxshadow-color wp-custom-fields-field-left">';
        $output .= '<label>' . __('Boxshadow Color', 'wp-custom-fields') . '</label>';
        $output .= Colorpicker::render( array(
            'id'     => $field['id'] . '-color',   
            'name'   => $field['name'] . '[color]',
            'values' => isset($field['values']['color']) ? $field['values']['color'] : ''     
        ) );
        $output .= '</div>';
        
        // Type of boxshadow
        $output .= '<div class="wp-custom-fields-boxshadow-type wp-custom-fields-field-left">';
        $output .= '<label>' . __('Boxshadow Style', 'wp-custom-fields') . '</label>';
        $output .= Select::render( array(
            'id'        => $field['id']  . '-type',
            'name'      => $field['name']. '[type]',
            'options'  => array( '' => __('Default', 'wp-custom-fields'), 'inset' => __('Inset', 'wp-custom-fields') ),             
            'placeholder' => __('Select Type', 'wp-custom-fields'),         
            'values'    => isset($field['values']['type']) ? $field['values']['type'] : ''
        
        ) ); 
        $output .= '</div>';
        
        $output .= '</div>';
        
        return $output;
  
    }
    
    public static function configurations() {
        $configurations = array(
            'type'      => 'boxshadow',
            'defaults'  => array()
        );
            
        return $configurations;
    }
    
}