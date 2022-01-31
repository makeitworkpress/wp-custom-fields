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
    
    /**
     * Prepares the variables and renders the field
     * 
     * @param   array $field The array with field attributes data-alpha
     * @return  void
     */      
    public static function render( array $field = [] ): void {
        
        $id             = esc_attr($field['id']);
        $name           = esc_attr($field['name']);    
        $mode           = isset($field['mode']) && in_array($field['mode'], ['advanced', 'plain']) ? $field['mode'] : 'advanced'; 
        $options        = isset($field['options']) ? $field['options'] : [];
        $object         = isset($field['object']) ? sanitize_key($field['object']) : '';
        $placeholder    = isset($field['placeholder']) && $field['placeholder'] ? esc_attr($field['placeholder']) : '';
        $source         = isset($field['source']) ? sanitize_text_field($field['source']) : '';
        
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

        // Retrieve select options from built-in WordPress types
        if( $object ) {

            // Retrieve our options from the cache. Will use persistent object caching (redis/memcached) if available.
            $options = wp_cache_get('wpc_select_field_cache_' . $object . $source);

            if( ! $options ) {
                $options = [];

                // Load an array of posts
                if( ($object == 'posts' || $object == 'post') && $source ) {

                    $posts = get_posts( ['ep_integrate' => true, 'post_type' => $source, 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC'] );
                    
                    foreach( $posts as $post ) {
                        $options[$post->ID] = $post->post_title;
                    }                

                } elseif( $object == 'users' || $object == 'user' ) {

                    $users = get_users( ['fields' => ['ID', 'display_name'], 'orderby' => 'display_name', 'order' => 'ASC'] );
                    
                    foreach( $users as $user ) {
                        $options[$user->ID] = $user->display_name;
                    }

                } elseif( ($object == 'terms' || $object == 'term') && $source ) {

                    $terms = get_terms( ['fields' => 'id=>name', 'hide_empty' => false, 'order' => 'ASC', 'taxonomy' => $source] );
                    
                    foreach( $terms as $term_id => $term_name ) {
                        $options[$term_id] = $term_name;
                    }                
                    
                }                

                wp_cache_add('wpc_select_field_cache_' . $object . $source, $options);
                
            }

        } ?>
        
            <select class="wpcf-select wpcf-select-<?php echo $mode; ?>" id="<?php echo $id; ?>" name="<?php echo $name . $namekey; ?>" <?php echo $multiple; if( $placeholder ) { ?> data-placeholder="<?php echo $placeholder; ?>"<?php } ?>>

                <?php if( $placeholder ) { ?>
                    <option class="wpcf-placeholder" value=""><?php echo $placeholder; ?></option>
                <?php } ?>

                <?php foreach ($options as $key => $option ) { ?>
                    
                    <?php 
                        if( $multiple && is_array($field['values']) ) { 
                            $selected = in_array($key, $field['values']) ? 'selected="selected"' : '';
                        } else {
                            $selected = selected( $key, $field['values'], false );
                        }
                    ?>

                    <?php if( is_array($option) ) { ?>
                        <optgroup label="<?php echo esc_attr( str_replace('_', '', $key) ); ?>">
                        <?php foreach( $option as $value => $name ) { ?>
                            <option value="<?php echo esc_attr($value); ?>" <?php echo $selected; ?>><?php echo esc_html($name); ?></option>
                        <?php } ?>   
                    <?php } else { ?>
                        <option value="<?php echo esc_attr($key); ?>" <?php echo $selected; ?>><?php echo esc_html($option); ?></option>
                    <?php } ?>

                <?php } ?>
            </select>
        
        <?php

    }
    
    /**
     * Returns the global configurations for this field
     *
     * @return array $configurations The configurations
     */      
    public static function configurations(): array {

        $configurations = [
            'type'      => 'select',
            'defaults'  => ''
        ];
            
        return apply_filters( 'wp_custom_fields_select_config', $configurations );
        
    }
    
}