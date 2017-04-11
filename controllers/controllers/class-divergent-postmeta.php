<?php
/** 
 * This class is responsible for controlling the display of metaboxes
 * 
 * @author Michiel
 * @since 1.0.0
 * @package Divergent
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) { 
    die; 
} 

class Divergent_Postmeta extends Divergent {
            
    /**
     * Contains the rendered metaboxes object
     * @access private
     */
    protected $meta_render;  
    
    /**
     * Contains the metavalues for the current post object
     * @access public
     */
    protected $meta_values; 
    
    /**
     * Contains the metavalues for the current post object
     * @access public
     */
    protected $meta_fields;     
    
    /**
     * Constructs the metaboxes and boot up the view script responsible for displaying the metaboxes
     *
     * @param mixed $params The parameters passed to this object
     */
    protected function initialize(Array $params) {
        
        do_action('divergent_postmeta_initialize', $this);
        
        $this->meta_fields = $params;
        
        do_action('after_divergent_postmeta_initialize', $this);
    }
    
    protected function register_hooks() {
        $this->actions = array(
            array('add_meta_boxes', 'add_metaboxes'),
            array('save_post', 'save_metaboxes', 10, 1),
        );
    }
        
    /**
     * Adds the specific metaboxes to a certain post
     *
     * @uses add_meta_box. Adds a metabox, using an id by which the meta values of this box can be queried. 
     * Subsequent values are the title above this metabox, the callback function for displaying metaboxes, the post types
     */
    public function add_metaboxes() {           
        foreach($this->meta_fields as $metabox) {
            
            if( ! isset($metabox['id']) || ! isset($metabox['post_type']) || ! $metabox['id'] || ! $metabox['post_type'] ) 
                continue;
            
            add_meta_box($metabox['id'], $metabox['title'], array( $this, 'render_metaboxes' ), $metabox['post_type'], $metabox['context'], $metabox['priority'], $metabox);
        }
    }
    
    /**
     * Callback for rendering the specific metaboxes, using any of the specified classes.
     *
     * @param object WP_Post $post The post object for the current post type
     * @param array $metaboxes The array passed through the callback function in $this->add_metaboxes
     */
    public function render_metaboxes( $post, $metabox ) {
        
        // Retrieve the metaboxes from the property, so that we only have to query once, and check if the key exists
        $meta_values = isset($metabox['id']) ? get_post_meta($post->ID, $metabox['id'], true) : array();
                
        // Display the metaboxes and pass the current meta values for the respective id.
        $this->meta_render = new Divergent_Views_Metaboxes($post, $metabox, $meta_values);
        
    }
    
    /**
     * Callback for saving the specific metaboxes
     * Validation is done by the the jQuery validation plugin
     *
     * @param int $post_id The post id for the current object we are saving
     */    
    public function save_metaboxes( $post_id ) {
                
        // Do not save on autosaves
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
            return $post_id; 

        if ( ! current_user_can( 'edit_posts', $post_id ) ||  ! current_user_can( 'edit_page', $post_id ) || ! wp_verify_nonce( $_POST['divergent-metaboxes-nonce'], 'divergent-metaboxes' ) ) 
            return $post_id; 

        
        // If we import custom settings, these define the meta values
        if( isset($_POST['import_submit']) ) {
            $new_meta_value = unserialize(base64_decode($_POST['import_value']));
            
        // Otherwise, it does the normal thing   
        } else {
            // For each metabox that is defined, pursue the save actions
            foreach( $this->meta_fields as $metabox) {
                $id = $metabox['id'];
                $current_meta_value = get_post_meta($post_id, $id, true); 

                foreach($metabox['sections'] as $section) {

                    foreach($section['fields'] as $field) {

                        // Make sure the field type and id are set before saving
                        if( ! empty( $field['type'] ) && ! empty( $field['id'] ) ) {

                            $field_value = ( isset( $_POST[$field['id']] ) ) ? $_POST[$field['id']] : '';

                            // Sanitize field if not disabled
                            if( $field['sanitize'] != 'disabled' ) {
                                $field_value = Divergent_Validate::sanitize($field_value, $field);
                            } 

                            $new_meta_value[$field['id']] = $field_value;

                        }
                    }
                }

                // Now save or update the meta values!
                if( empty($new_meta_value) && empty($current_meta_value) ) {
                    delete_post_meta($post_id, $id);
                } else {
                    $save = update_post_meta($post_id, $id, $new_meta_value);
                }

            }
        }
        
    }
    
}