<?php
 /** 
  * Displays a repeatable field group
  */
namespace MakeitWorkPress\WP_Custom_Fields\Fields;
use MakeitWorkPress\WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die; 

class Repeatable implements Field {
    
    public static function render($field = array()) {
        
        $add                = isset($field['add'])      ? $field['add']     : __('Add Group', 'wp-custom-fields');
        $remove             = isset($field['remove'])   ? $field['remove']  : __('Remove Group', 'wp-custom-fields');
        $display            = isset($field['closed']) && $field['closed']   ? ' hidden'         : '';
        
        // Prepare the array with data
        if( empty($field['values']) ) {
            $groups[0] = $field['fields'];
        } elseif( ! empty($field['values']) ) {
            
            // The values determine our groups
            foreach( $field['values'] as $key => $groupValues ) {
  
                // Link our fields to the values
                foreach($field['fields'] as $subkey => $subfield) {

                    $groups[$key][$subfield['id']]           = $subfield;    
                    $groups[$key][$subfield['id']]['values'] = $groupValues[$subfield['id']];    

                }

            }
            
        }
        
        // Output the containers
        $output = '<div class="wp-custom-fields-repeatable-container">';
        
        foreach( $groups as $key => $fields) {

            $output .= '<div class="wp-custom-fields-repeatable-group">';
            $output .= '<a class="wp-custom-fields-repeatable-toggle" href="#"><i class="material-icons">arrow_drop_down</i></a>';
            $output .= '<div class="wp-custom-fields-repeatable-fields grid flex' . $display . '">';

            // Loop through each of the saved fields
            foreach($fields as $subkey => $subfield) {

                // The type should be defined
                if( ! isset($subfield['type']) ) {
                    continue;
                }

                // The ID should be defined
                if( ! isset($subfield['id']) ) {
                    continue;
                }                

                // Render each field based upon the values
                $subfield['columns']  = isset($subfield['columns']) ? 'wcf-' . $subfield['columns'] : 'wcf-full';
                $subfield['values']   = isset($subfield['values']) ? $subfield['values'] : '';
                $subfield['name']     = $field['name'] . '[' . $key . ']' . '[' . $subfield['id'] . ']';
                $subfield['id']       = $field['id'] . '_' . $key  . '_' . $subfield['id'];
                
                $class                = 'MakeitWorkPress\WP_Custom_Fields\Fields\\' . ucfirst( $subfield['type'] );
                
                if( class_exists($class) ) {
                    $output .= '<div class="wp-custom-fields-repeatable-field wp-custom-fields-option-field field-' . $subfield['type'] . ' ' . $subfield['columns'] . '">';
                        $output .= '<h5>' . $subfield['title'] . '</h5>';

                        $output .= $class::render($subfield);

                        if( isset($subfield['description']) ) {
                            $output .= '<div class="wp-custom-fields-field-description"><p>' . $subfield['description'] . '</p></div>';  
                        }

                    $output .= '</div>';
                }

            }
        
            $output .= '</div><!-- .wp-custom-fields-repeatable-fields -->';
            $output .= '</div><!-- .wp-custom-fields-repeatable-group -->';
        }            
        
        $output .= '<a href="#" class="button wp-custom-fields-repeatable-add"><i class="material-icons">add</i> ' . $add . '</a>';
        $output .= '<a href="#" class="button wp-custom-fields-repeatable-remove"><i class="material-icons">remove</i> ' . $remove . '</a>';
        $output .= '</div><!-- .wp-custom-fields-repeatable-container -->';
        
        return $output;    
    }
    
    public static function configurations() {
        $configurations = array(
            'type'      => 'repeatable',
            'defaults'  => array()
        );
            
        return $configurations;
    }
    
}