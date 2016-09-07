<?php
/**
 * This class can register custom posts and taxonomies
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) { 
    die; 
} 

class Divergent_Posts extends Divergent {
    
    /**
     * Hold taxonomies and post types
     */
    private $post_types;
    private $taxonomies;    
    
    /**
     * Constructor
     */
    protected function initialize(Array $params) {
        
        $this->post_types = $params['post_types'];
        $this->taxonomies = $params['taxonomies'];       

    }
    
    /**
     * As the instance of this class is hooked upon init, no hooking is needed
     */
    protected function register_hooks() {
        
        if( ! empty($this->post_types) ) {
            $this->actions[] = array('init', 'add_post_types');
        }
        
        if( ! empty($this->taxonomies) ) {
            $this->actions[] = array('init', 'add_taxonomies');
        }     
    }
        
    /**
     * Adds post types foreach post type defined in the settings
     */
    public function add_post_types() {
        foreach($this->post_types as $key => $post_type) {
            
            if($post_type['arguments']['labels'] == 'auto') {
                $post_type['arguments']['labels'] = {
                    'name'              => $post_type['arguments']['plural_name'],
                    'singular_name'     => $post_type['arguments']['singular_name'],
		            'menu_name'         => $post_type['arguments']['plural_name'],
		            'name_admin_bar'    => $post_type['arguments']['singular_name'],                    
                    'add_new'           => sprintf( __('Add New', 'divergent'), $post_type['singular_name']),
                    'add_new_item'      => sprintf( __('Add New %s', 'divergent'), $post_type['singular_name']),
                    'new_item'          => sprintf( __('New %s', 'divergent'), $post_type['singular_name']),
                    'edit_item'         => sprintf( __('Edit %s', 'divergent'), $post_type['singular_name']),
                    'view_item'         => sprintf( __('View %s', 'divergent'), $post_type['singular_name']),
                    'all_items'         => sprintf( __('All %s', 'divergent'), $post_type['plural_name']),
                    'search_items'      => sprintf( __('Search %s', 'divergent'), $post_type['plural_name']),
                    'parent_item_colon' => sprintf( __('Parent %s:', 'divergent' ), $post_type['plural_name']),
                    'not_found'         => sprintf( __('No %s found.', 'divergent' ), $post_type['plural_name']),
                    'not_found_in_trash' => sprintf( __('No %s found in Trash.', 'divergent'), $post_type['plural_name'])                   
                }
            }
            register_post_type($post_type['name'], $post_type['arguments']);
        }
    }
    
    /**
     * Adds taxonomies for each taxonomy defined in the settings
     */
    public function add_taxonomies() {
        foreach($this->taxonomies as $taxonomy) {
            
            if($taxonomy['arguments']['labels'] == 'auto') {
                $taxonomy['arguments']['labels'] = {
                    'name'                       => $taxonomy['arguments']['plural_name'],
                    'singular_name'              => $taxonomy['arguments']['singular_name'],
                    'search_items'               => sprintf( __('Search %s', 'divergent'), $taxonomy['arguments']['plural_name']),
                    'popular_items'              => sprintf( __('Popular %s', 'divergent'), $taxonomy['arguments']['plural_name']),
                    'all_items'                  => sprintf( __('All %s', 'divergent'), $taxonomy['arguments']['plural_name']),
	                'parent_item'                => sprintf( __('Parent %s', 'divergent'), $taxonomy['arguments']['plural_name']),
		            'parent_item_colon'          => sprintf( __('Parent %s:', 'divergent'), $taxonomy['arguments']['plural_name']),
                    'edit_item'                  => sprintf( __('Edit %s', 'divergent'), $taxonomy['arguments']['singular_name']),
                    'update_item'                => sprintf( __('Update %s', 'divergent'), $taxonomy['arguments']['singular_name']),
                    'add_new_item'               => sprintf( __('Add New %s', 'divergent'), $taxonomy['arguments']['singular_name']),
                    'new_item_name'              => sprintf( __('New %s Name', 'divergent'), $taxonomy['arguments']['singular_name']),
                    'separate_items_with_commas' => sprintf( __('Separate %s with commas', 'divergent'), $taxonomy['arguments']['plural_name']),
                    'add_or_remove_items'        => sprintf( __('Add or remove %s', 'divergent'), $taxonomy['arguments']['plural_name']),
                    'choose_from_most_used'      => sprintf( __('Choose from the most used %s', 'divergent'), $taxonomy['arguments']['plural_name']),
                    'not_found'                  => sprintf( __('No %s found.', 'divergent'), $taxonomy['arguments']['plural_name']),
                    'menu_name'                  => $taxonomy['arguments']['plural_name']
                }
            }            
            
            register_taxonomy($taxonomy['name'], $taxonomy['post_type'], $taxonomy['arguments']);
        }         
    }

}