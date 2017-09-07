<?php
 /** 
  * Displays a location field, including a google map
  */
namespace WP_Custom_Fields\Fields;
use WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class Location implements Field {
    
    public static function render($field = array()) {
        
        $addJS = apply_filters('wp_custom_fields_location_field_js', true);
        
        if($addJS && ! wp_script_is('google-maps-js', 'enqueued') )
            wp_enqueue_script('google-maps-js');
        
        $output = '<div class="wp-custom-fields-location">';
        $output .= '<input class="regular-text wp-custom-fields-map-search" type="text" />';
        $output .= '<div class="wp-custom-fields-map-canvas"></div>';        
        $output .= '<input class="latitude" id="' . $field['id'] . '-lat" name="' . $field['name']  . '[lat]" type="hidden" value="' . $field['values']['lat'] . '" />';
        $output .= '<input class="longitude" id="' . $field['id'] . '-long" name="' . $field['name']  . '[lng]" type="hidden" value="' . $field['values']['lng'] . '" />';
        
        $location_fields = array(
            'street'        => __('Street Address', 'wp-custom-fields'),
            'number'        => __('Street Number', 'wp-custom-fields'),
            'postal_code'   => __('Postal Code', 'wp-custom-fields'),
            'city'          => __('City', 'wp-custom-fields')          
        );
        
        foreach( $location_fields as $loc ) {
            $output .= '<div class="wp-custom-fields-field-left">';
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