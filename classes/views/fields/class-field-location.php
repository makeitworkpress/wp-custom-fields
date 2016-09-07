<?php
 /** 
  * Displays a location field, including a google map
  */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) { 
    die; 
}

class Divergent_Field_Location implements Divergent_Field {
    
    public static function render($field = array()) {
        
        $add_js = apply_filters('divergent_location_field_js', true);
        
        if($add_js && ! wp_script_is('google-maps-js', 'enqueued') )
            wp_enqueue_script('google-maps-js');
        
        $output = '<div class="divergent-location">';
        $output .= '<input class="regular-text divergent-map-search" type="text" />';
        $output .= '<div class="divergent-map-canvas"></div>';        
        $output .= '<input class="latitude" id="' . $field['id'] . '-lat" name="' . $field['name']  . '[lat]" type="hidden" value="' . $field['values']['lat'] . '" />';
        $output .= '<input class="longitude" id="' . $field['id'] . '-long" name="' . $field['name']  . '[long]" type="hidden" value="' . $field['values']['long'] . '" />';
        
        $location_fields = array(
            array(
                'id'    => 'street',
                'label' => __('Street Address', DIVERGENT_LANGUAGE)
            ),
            array(
                'id'    => 'number',
                'label' => __('Street Number', DIVERGENT_LANGUAGE)
            ),
            array(
                'id'    => 'postal_code',
                'label' => __('Postal Code', DIVERGENT_LANGUAGE)
            ),
            array(
                'id'    => 'city',
                'label' => __('City', DIVERGENT_LANGUAGE)
            ),            
        );
        
        foreach($location_fields as $loc) {
            $output .= '<div class="divergent-field-left">';
            $output .= '    <label for="' . $field['id'] . '-' . $loc['id'] . '">' . $loc['label'] . '</label><br />';
            $output .= '    <input type="text" class="regular-text '.$loc['id'].'" id="'.$field['id'].'-'.$loc['id'].'" name="'.$field['name'].'['.$loc['id'].']" value="'.$field['values'][$loc['id']].'"  />';
            $output .= '</div>';
        }
        
        $output .= '</div>';
        
        return $output;
  
    }
    
    public static function configurations() {
        $configurations = array(
            'type' => 'location'
        );
            
        return $configurations;
    }
    
}