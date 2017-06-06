<?php
 /** 
  * Displays a border field
  */
namespace Divergent\Fields;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class Divergent_Field_Select implements Divergent_Field {
    
    public static function render($field = array()) {
        
        $options = isset($field['options']) ? $field['options'] : array();
        $subtype = isset($field['subtype']) ? $field['subtype'] : '';
        
        if(isset($field['multiple'])) { 
            $multiple = 'multiple="multiple"';
            $namekey = '[]';
        } else { 
            $multiple = '';
            $namekey = '';            
        }  
        
        if( ! empty($subtype) ) {
            
            $posts = get_posts(array('post_type' => $subtype, 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC')); 
            $options = array();
            
            foreach($posts as $post) {
                $options[$post->ID] = $post->post_title;
            }
        }
        
        $output = '<select class="divergent-select" id="' . $field['id']  . '" name="' . $field['name'] . $namekey . '" ' . $multiple .'>';

        // An empty option serves as placeholder
        if( ! empty($field['placeholder']) ) { 
            $output .= '    <option value="">' . $field['placeholder'] . '</option>';
        }
        foreach($options as $key => $option) {
            if($multiple && is_array($field['values'])) {
                $selected = in_array($key, $field['values']) ? 'selected="selected"' : '';
            } else {
                $selected = $key == $field['values'] ? 'selected="selected"' : '';
            }
            $output .= '    <option value="' . $key . '" ' . $selected . '>' . $option . '</option>';
        }
        $output .= '</select>';
        
        return $output;    
    }
    
    public static function configurations() {
        $configurations = array(
            'type' => 'select'
        );
            
        return $configurations;
    }
    
}