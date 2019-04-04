<?php
 /** 
  * Displays a border field
  */
namespace MakeitWorkPress\WP_Custom_Fields\Fields;
use MakeitWorkPress\WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class Select implements Field {
    
    public static function render($field = array()) {
        
        $options = isset($field['options']) ? $field['options'] : array();
        $object  = isset($field['object']) ? $field['object'] : 'posts';
        $source  = isset($field['source']) ? $field['source'] : '';
        
        // Load the select2 script if we have a select field
        if( apply_filters('wp_custom_fields_select_field_js', true) && ! wp_script_is('select2-js', 'enqueued') ) {
            wp_enqueue_script('select2-js'); 
        }       
        
        // Set-up if we have a multiple checkbox
        if( isset($field['multiple']) && $field['multiple'] ) { 
            $multiple = 'multiple="multiple"';
            $namekey = '[]';
        } else { 
            $multiple = '';
            $namekey = '';            
        }  
        
        // Load an array of posts
        if( $object == 'posts' && ! empty($source) ) {

            $options = array();
            $posts = get_posts(
                array(
                    'ep_integrate'      => true,
                    'post_type'         => $source, 
                    'posts_per_page'    => -1, 
                    'orderby'           => 'title', 
                    'order'             => 'ASC'
                )
            );
            
            foreach( $posts as $post ) {
                $options[$post->ID] = $post->post_title;
            }                

        } elseif( $object == 'users' ) {

            $options = array();
            $users = get_users(
                array(
                    'fields'            => ['ID', 'display_name'], 
                    'orderby'           => 'display_name', 
                    'order'             => 'ASC'
                )
            );
            
            foreach( $users as $user ) {
                $options[$user->ID] = $user->display_name;
            }

        } elseif( $object == 'terms' && ! empty($source) ) {

            $options = array();
            $terms = get_terms(
                array(
                    'fields'            => 'id=>name', 
                    'hide_empty'        => false, 
                    'order'             => 'ASC',
                    'taxonomy'          => $source
                )
            );
            
            foreach( $terms as $ID => $name ) {
                $options[$ID] = $name;
            }                
            
        }
        
        $output = '<select class="wp-custom-fields-select" id="' . $field['id']  . '" name="' . $field['name'] . $namekey . '" ' . $multiple .'>';

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
            'type'      => 'select',
            'defaults'  => ''
        );
            
        return $configurations;
    }
    
}