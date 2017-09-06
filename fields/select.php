<?php
 /** 
  * Displays a border field
  */
namespace Divergent\Fields;
use Divergent\Divergent_Field as Divergent_Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class Select implements Divergent_Field {
    
    public static function render($field = array()) {
        
        $options = isset($field['options']) ? $field['options'] : array();
        $subtype = isset($field['subtype']) ? $field['subtype'] : '';
        
        // Load the select2 script if we have a select field
        if( apply_filters('divergent_select_field_js', true) && ! wp_script_is('select2-js', 'enqueued') )
            wp_enqueue_script('select2-js');        
        
        // Set-up if we have a multiple checkbox
        if(isset($field['multiple'])) { 
            $multiple = 'multiple="multiple"';
            $namekey = '[]';
        } else { 
            $multiple = '';
            $namekey = '';            
        }  
        
        // Load an array of posts
        if( ! empty($subtype) ) {
            
            $posts = get_posts(
                array(
                    'post_type'         => $subtype, 
                    'posts_per_page'    => -1, 
                    'orderby'           => 'title', 
                    'order'             => 'ASC'
                )
            ); 
            $options = array();
            
            foreach( $posts as $post ) {
                $options[$post->ID] = $post->post_title;
            }
        }
        
        $output = '<select class="divergent-select" id="' . $field['id']  . '" name="' . $field['name'] . $namekey . '" ' . $multiple .'>';

        // An empty option serves as placeholder
        if( ! empty($field['placeholder']) ) { 
            $output .= '    <option value="">' . $field['placeholder'] . '</option>';
        }
        foreach ($options as $key => $option ) {
            if( $multiple && is_array($field['values']) ) {
                $selected = in_array($key, $field['values']) ? 'selected="selected"' : '';
            } else {
                $selected = selected( $key, $field['values'], false );
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