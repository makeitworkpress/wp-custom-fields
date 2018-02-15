<?php 
/** 
 * This class is responsible for controlling the display of metaboxes
 * 
 * @author Michiel
 * @since 1.0.0
 */
namespace MakeitWorkPress\WP_Custom_Fields;
use WP_Error as WP_Error;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) 
    die;

class Meta {
    
    /**
     * Use our validation functions
     */
    use Validate;
    
    /**
     * Contains the $metaBox array for each of the option pages
     * @access public
     */
    public $metaBox;

    /**
     * If we are saving as single keys, this value is true
     * @access public
     */
    public $single = false;    

    /**
     * Saves the type of metabox (user, term or post)
     * @access public
     */
    public $type;

    /**
     * Examines if we have validated
     * @access public
     */
    public $validated = false;     
    
    /**
     * Constructor
     *
     * @param array $group The array with settings, sections and fields
     * @return WP_Error|void Returns a WP_Error if something is wrong in the configurations, otherwise nothing
     */    
    public function __construct( $group = array() ) {
        
        $this->metaBox  = $group;
        $this->single   = isset( $this->metaBox['single'] ) ? $this->metaBox['single'] : false;
        $this->type     = isset( $this->metaBox['type'] ) ? $this->metaBox['type'] : 'post';

        // Our type should be in a predefined array
        if( ! in_array($this->type, array('post', 'term', 'user')) ) {
            $this->validated = new WP_Error( 'wrong', __('You are using a wrong type for adding meta fields! Use either post, term or user.', 'wp-custom-fields') );
        }

        // We should have an id
        if( ! isset($group['id']) || ! $group['id'] ) {
            $this->validated = new WP_Error( 'wrong', __('Your meta configurations require an id.', 'wp-custom-fields') );
        }    
        
        // Validate for our type being post
        if( $this->type == 'post' ) {
            $this->validated = Validate::configurations( $group, ['id', 'title', 'screen', 'context', 'priority'] );
        } 
        
        // if there is an error, return the error
        if( is_wp_error($this->validated) ) {
            return;
        }        

        $this->registerHooks();

    }   
    
    /**
     * Register WordPress Hooks
     * 
     * @access protected
     */
    protected function registerHooks() {
        
        // Post type metabox
        if( $this->type == 'post' ) {
            add_action( 'add_meta_boxes', array($this, 'add'), 10, 1 );
            add_action( 'save_post', array($this, 'save'), 10, 1 );
        }
        
        // Taxonomy metabox @todo add check for existing taxonomies
        if( $this->type == 'term' && isset($this->metaBox['taxonomy']) ) {
            add_action( $this->metaBox['taxonomy'] . '_edit_form', array($this, 'add'), 20, 1 );
            add_action( 'edited_' . $this->metaBox['taxonomy'], array($this, 'save'), 10, 1 );   
        }

        // User metabox
        if( $this->type == 'user' ) {
            add_action( 'show_user_profile', array($this, 'add') );
            add_action( 'edit_user_profile', array($this, 'add') );
            add_action( 'personal_options_update', array($this, 'save') );
            add_action( 'edit_user_profile_update', array($this, 'save') );   
        }      
      
    }
    
    /**
     * Adds the specific metaboxes to a certain post or any other type
     * 
     * @access  public
     * @param   object $object  The object as passed through the save function
     */    
    public function add( $object ) {
        
        // We should have an id
        if( ! isset($this->metaBox['id']) )
            return;
        
        // Post type metabox uses the add meta box function
        if( $this->type == 'post' ) {
            add_meta_box( 
                $this->metaBox['id'], 
                $this->metaBox['title'], 
                array( $this, 'render' ), 
                $this->metaBox['screen'], 
                $this->metaBox['context'], 
                $this->metaBox['priority']
            );
        }

        // We just render for other types
        if( $this->type == 'term' || $this->type == 'user' ) {

            // Cast our id to the term id
            if( isset($object->term_id) ) {
                $object->ID = $object->term_id;
            }

             // Cast our id to the user id
             if( isset($object->user_id) ) {
                $object->ID = $object->user_id;
            }

            $this->render( $object );

        }
        
    }
    
    /**
     * Callback for rendering the specific metaboxes, using any of the specified classes.
     *
     * @param object    $object     The post, term or user object for the current post type
     */
    public function render( $object ) {

        // This grabs our metavalues from a single box
        if( $this->single ) {

            $values = array();

            // We should have sections
            if( ! isset($this->metaBox['sections']) ) {
                return;
            }

            foreach( $this->metaBox['sections'] as $section ) {

                // We should have fields
                if( ! isset($section['fields']) ) {
                    continue;
                } 
                
                foreach( $section['fields'] as $field ) {

                    // We should have fields
                    if( ! isset($field['id']) ) {
                        continue;
                    } 

                    $field['id']                = str_replace( '[', '_', esc_attr($field['id']) ); 
                    $field['id']                = str_replace( ']', '', esc_attr($field['id']) );
                    $value                      = get_metadata( $this->type, $object->ID, $field['id'], true );
                    
                    // Only add to the fields if we have an actual value
                    if( $value ) {
                        $values[$field['id']]   = $value ;
                    }

                }
                
            }

        } else {

            $values             = get_metadata( $this->type, $object->ID, $this->metaBox['id'], true );
        }
        
        $frame                  = new Frame( $this->metaBox, $values );
        $frame->settingsFields  = wp_nonce_field( 'wp-custom-fields-metaboxes-' . $frame->id, 'wp-custom-fields-metaboxes-nonce-' . $frame->id, true, false );
        
        // Render our output
        $frame->render();
        
        return;
        
    }
    
    /**
     * Callback for saving the specific metaboxes
     *
     * @param int $id The id for the current object we are saving
     */      
    public function save( $id ) {

        // Do not save on autosaves
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
            return $id; 
        
        // Some pages do not have the nonce
        if( ! isset($_POST['wp-custom-fields-metaboxes-nonce-' . $this->metaBox['id']]) )
            return $id;

        // Check our user capabilities
        if ( ! current_user_can( 'edit_posts', $id ) || ! current_user_can( 'edit_pages', $id ) )
            return $id;

        // If we are editing users, we are more limited
        if (  $this->type == 'user' && ! current_user_can('edit_users') )
            return;    
         
        // Check our nonces
        if ( ! wp_verify_nonce( $_POST['wp-custom-fields-metaboxes-nonce-' . $this->metaBox['id']], 'wp-custom-fields-metaboxes-' . $this->metaBox['id'] ) ) 
            return $id;
        
        // Retrieve our current meta values
        $current    = get_metadata( $this->type, $id, $this->metaBox['id'], true ); 
        $output     = Validate::format( $this->metaBox, $_POST );
        
        // Return if nothing has changed
        if( $current == $output )
            return;

        // Saves our metaboxes as seperate values
        if( $this->single ) {

            foreach( $output as $meta => $values ) {

                if( empty($values) ) {
                    delete_metadata( $this->type, $id, $meta );    
                } else {
                    update_metadata( $this->type, $id, $meta, $values);     
                }

            }

        // Saves under one key
        } else {
        
            // Delete metadata if the output is empty
            if( empty($output) ) {
                delete_metadata( $this->type, $id, $this->metaBox['id'] );
                return;
            } 
            
            // Update meta data
            update_metadata( $this->type, $id, $this->metaBox['id'], $output);  
        
        }
        
    }
    
}