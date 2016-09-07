<?php
 /** 
  * Displays a location field, including a google map
  */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) { 
    die; 
}

class Divergent_Field_Boxshadow implements Divergent_Field {
    
    public static function render($field = array()) {
        
        $output = '<div class="divergent-boxshadow">';
        
        $output .= '<div class="divergent-boxshadow-dimensions divergent-boxshadow-field">';
        $output .= '<label>' . __('Boxshadow Offset, blur and spread', DIVERGENT_LANGUAGE) . '</label>';
        $pixel_values = array(
            array('id' => 'x',      'placeholder' => __('x-offset', DIVERGENT_LANGUAGE) ),
            array('id' => 'y',      'placeholder' => __('y-offset', DIVERGENT_LANGUAGE) ),
            array('id' => 'blur',   'placeholder' => __('blur', DIVERGENT_LANGUAGE) ),
            array('id' => 'spread', 'placeholder' => __('spread', DIVERGENT_LANGUAGE) )
        );
        
        foreach($pixel_values as $el) {
            $el_value = isset($field['values'][$el['id']]) ? $field['values'][$el['id']] : '';
            $output .= '<input class="small-text" id="' . $field['id'].'-'.$el['id'] . '" name="' . $field['name']  . '['.$el['id'].']" type="text" placeholder="' . $el['placeholder'] . '" value="' . $el_value . '" />';
        }
        $output .= '</div>';
        
        $output .= '<div class="divergent-boxshadow-color divergent-boxshadow-field">';
        $output .= '<label>' . __('Boxshadow Color', DIVERGENT_LANGUAGE) . '</label>';
        $output .= Divergent_Field_Colorpicker::render($field);
        $output .= '</div>';
        
        $output .= '<div class="divergent-boxshadow-color divergent-boxshadow-field">';
        $output .= '<label>' . __('Boxshadow Style', DIVERGENT_LANGUAGE) . '</label>';
        
        // Add select field for the boxshadow
        $select_field = array(             
            'placeholder' => __('Select Type', DIVERGENT_LANGUAGE),
            'options'  => array(
                '' => __('Default', DIVERGENT_LANGUAGE),
                'inset' => __('Inset', DIVERGENT_LANGUAGE)
            )
        );
        $select_field['values'] = isset($field['values']['type']) ? $field['values']['type'] : ''; 
        $select_field['id']     = $field['id']  . '-type';
        $select_field['name']   = $field['name']. '[type]';
        $output .= Divergent_Field_Select::render($select_field); 
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