<?php
 /** 
  * Displays a repeatable field group
  */
namespace Divergent\Fields;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die; 

class Divergent_Field_Repeatable implements Divergent_Field {
    
    public static function render($field = array()) {
        
        $add                = isset($field['add'])      ? $field['add']     : __('Add Group', 'divergent');
        $remove             = isset($field['remove'])   ? $field['remove']  : __('Remove Group', 'divergent');
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
        $output = '<div class="divergent-repeatable-container">';
        
        foreach( $groups as $key => $fields) {

            $output .= '<div class="divergent-repeatable-group">';
            $output .= '<a class="divergent-repeatable-toggle" href="#"><i class="material-icons">arrow_drop_down</i></a>';
            $output .= '<div class="divergent-repeatable-fields grid flex' . $display . '">';

            // Loop through each of the saved fields
            foreach($fields as $subkey => $subfield) {

                // Render each field based upon the values
                $subfield['columns']  = isset($subfield['columns']) ? $subfield['columns'] : 'full';
                $subfield['values']   = isset($subfield['values']) ? $subfield['values'] : '';
                $subfield['name']     = $field['name'] . '[' . $key . ']' . '[' . $subfield['id'] . ']';
                $subfield['id']       = $field['id'] . '-' . $key  . '-' . $subfield['id'];
                
                $class                = 'Divergent\Fields\Divergent_Field_' . ucfirst( $subfield['type'] );
                
                if( class_exists($class) ) {
                    $output .= '<div class="divergent-repeatable-field divergent-option-field ' . $subfield['columns'] . '">';
                        $output .= '<h5>' . $subfield['title'] . '</h5>';

                        if( isset($subfield['description']) ) 
                            $output .= '<p>' . $subfield['description'] . '</p>';

                        $output .= $class::render($subfield);
                    $output .= '</div>';
                }

            }
        
            $output .= '</div><!-- .divergent-repeatable-fields -->';
            $output .= '</div><!-- .divergent-repeatable-group -->';
        }            
        
        $output .= '<a href="#" class="button divergent-repeatable-add"><i class="material-icons">add</i> ' . $add . '</a>';
        $output .= '<a href="#" class="button divergent-repeatable-remove"><i class="material-icons">remove</i> ' . $remove . '</a>';
        $output .= '</div><!-- .divergent-repeatable-container -->';
        
        return $output;    
    }
    
    public static function configurations() {
        $configurations = array(
            'type' => 'repeatable'
        );
            
        return $configurations;
    }
    
}