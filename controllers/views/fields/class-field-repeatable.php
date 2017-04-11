<?php
 /** 
  * Displays a repeatable field group
  */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) { 
    die; 
}

class Divergent_Field_Repeatable implements Divergent_Field {
    
    public static function render($field = array()) {
        
        $add = isset($field['add']) ? $field['add'] : __('Add Group', 'divergent');
        $remove = isset($field['remove']) ? $field['remove'] : __('Remove Group', 'divergent');
        $fields = isset($field['fields']) ? $field['fields'] : array();
        $field['values'] = empty($field['values']) ? array() : $field['values'];
        $arrow = isset($field['closed']) ? '  fa-rotate-180' : '';   
        $display = isset($field['closed']) ? '  hidden' : '';
        
        // Prepare the array with data
        if( empty($field['values']) ) {
            $field_groups[0] = $fields;
        } elseif( ! empty($field['values']) ) {
            foreach($field['values'] as $key => $group) {
                foreach($group as $field_id => $field_value) {
                    foreach($fields as $rkey => $rfield) {
                        $unique_id = $field['id'] . '-' . $key . '-' . $rfield['id'];
                        if($unique_id == $field_id) {
                            $fields[$rkey]['values'] = $field_value;    
                        }    
                    }

                }
                $field_groups[$key] = $fields;
            }
        }
        
        // Output the containers
        $output = '<div class="divergent-repeatable-container">';
        
        foreach($field_groups as $key => $group) {

            $output .= '<div class="divergent-repeatable-group">';

            if( isset($field['title']) ) {             
                $output .= '<h4>' .$field['title'].' - '. __('Group', 'divergent') .' <span>'.$key.'</span><a class="divergent-repeatable-toggle" href="#"><i class="fa fa-chevron-down'.$arrow.'"></i></a></h4>';
            }

            $output .= '<div class="divergent-repeatable-group-fields' . $display . '">';

            // Loop through each of the saved fields
            foreach($group as $fkey => $subfield) {

                // Render each field based upon the values
                $subfield['id']       = $field['id'] . '-' . $key  . '-' . $subfield['id'];
                $subfield['name']     = $field['name'] . '[' . $key . ']' . '[' . $subfield['id'] . ']';
                $output .= Divergent_Fields::render($subfield);

            }
            $output .= '</div><!-- .divergent-repeatable-group-fields -->';
            $output .= '</div><!-- .divergent-repeatable-group -->';
        }            
        
        $output .= '<a href="#" class="button divergent-repeatable-add"><i class="fa fa-plus-circle"></i> ' . $add . '</a>';
        $output .= '<a href="#" class="button divergent-repeatable-remove"><i class="fa fa-minus-circle"></i> ' . $remove . '</a>';
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