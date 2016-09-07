<?php
 /** 
  * Displays a link styling field
  */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) { 
    die; 
}

class Divergent_Field_Links implements Divergent_Field {
    
    public static function render($field = array()) {
        
        $output = '';
        
        $link_states = array(
            'link' => __('Default Link Color', DIVERGENT_LANGUAGE),
            'hover' => __('Hover Link Color', DIVERGENT_LANGUAGE),
            'visited' => __('Visited Link Color', DIVERGENT_LANGUAGE),
            'active' => __('Selected Link Color', DIVERGENT_LANGUAGE)
        );
                        
        // Background Colorpicker
        foreach($link_states as $key => $link_state) {
            
            $colorpicker['values']  = isset($field['values'][$key]) ? $field['values'][$key] : '';
            $colorpicker['name']    = $field['name'] . '[' . $key . ']';
            $colorpicker['id']      = $field['id'] . '-' . $key;
            
            $output .= '<div class="divergent-field-left link-state-' . $key . '">';
            $output .= '    <p>' . $link_state . '</p>';
            $output .=      Divergent_Field_Colorpicker::render($colorpicker);
            $output .= '</div>';
        }

        return $output;    
    }
    
    public static function configurations() {
        $configurations = array(
            'type' => 'links'
        );
            
        return $configurations;
    }
    
}