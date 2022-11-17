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
     * Contains the $meta_box array for each of the option pages
     * 
     * @var array
     * @access public
     */
    public $meta_box;

    /**
     * If we are saving as single keys, this value is true
     * 
     * @var bool
     * @access public
     */
    public $single = false;    

    /**
     * Saves the type of metabox (user, term or post)
     * 
     * @var string
     * @access public
     */
    public $type;

    /**
     * Examines if we have validated
     * 
     * @var bool
     * @access public
     */
    public $validated = false;     
    
    /**
     * Constructor
     *
     * @param array $group The array with settings, sections and fields
     * @return WP_Error|void Returns a WP_Error if something is wrong in the configurations, otherwise nothing
     */    
    public function __construct( array $group = [] ) {

        // Default properties
        $this->meta_box  = $group;
        $this->single   = isset( $this->meta_box['single'] ) ? $this->meta_box['single'] : false;
        $this->type     = isset( $this->meta_box['type'] ) ? $this->meta_box['type'] : 'post';        

        // This can only be executed with the right capabilities
        if( ! current_user_can('edit_posts') || ! current_user_can('edit_pages') ) {
            return;
        }

        // For user editing, a user must be able to edit users
        if( $this->type == 'user' && ! current_user_can('edit_users') ) {
            return;  
        }  

        // Our type should be in a predefined array
        if( ! in_array($this->type, ['post', 'term', 'user']) ) {
            $this->validated = new WP_Error( 'wrong', __('You are using a wrong type for adding meta fields! Use either post, term or user.', 'wpcf') );
        }

        // We should have an id
        if( ! isset($group['id']) || ! $group['id'] ) {
            $this->validated = new WP_Error( 'wrong', __('Your meta configurations require an id.', 'wpcf') );
        }    
        
        // Validate for our type being post
        if( $this->type == 'post' ) {
            $this->validated = $this->validate_configurations( $group, ['id', 'title', 'screen', 'context', 'priority'] );
        } 

        // if there is an error, return the error
        if( is_wp_error($this->validated) ) {
            return;
        }        

        $this->register_hooks();

    }   
    
    /**
     * Register WordPress Hooks
     * 
     * @access protected
     */
    protected function register_hooks(): void {
        
        // Post type metabox
        if( $this->type == 'post' ) {
            add_action( 'add_meta_boxes', [$this, 'add'], 10, 1 );
            add_action( 'save_post', [$this, 'save'], 10, 1 );
        }
        
        // Taxonomy metabox @todo add check for existing taxonomies
        if( $this->type == 'term' && isset($this->meta_box['taxonomy']) ) {
            add_action( $this->meta_box['taxonomy'] . '_edit_form', [$this, 'add'], 20, 1 );
            add_action( 'edited_' . $this->meta_box['taxonomy'], [$this, 'save'], 10, 1 );   
        }

        // User metabox
        if( $this->type == 'user' ) {
            add_action( 'show_user_profile', [$this, 'add'] );
            add_action( 'edit_user_profile', [$this, 'add'] );
            add_action( 'personal_options_update', [$this, 'save'] );
            add_action( 'edit_user_profile_update', [$this, 'save'] );   
        }      
      
    }
    
    /**
     * Adds the specific metaboxes to a certain post or any other type
     * 
     * @param   object $object  The object as passed through the save function
     */    
    public function add( $object ) {
        
        // We should have an id
        if( ! isset($this->meta_box['id']) ) {
            return;
        }
        
        // Post type metabox uses the add meta box function
        if( $this->type == 'post' ) {
            add_meta_box( $this->meta_box['id'], $this->meta_box['title'], [$this, 'render'], $this->meta_box['screen'], $this->meta_box['context'], $this->meta_box['priority'] );
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

            $values = [];

            // We should have sections
            if( ! isset($this->meta_box['sections']) ) {
                return;
            }

            foreach( $this->meta_box['sections'] as $section ) {

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

            $values             = get_metadata( $this->type, $object->ID, $this->meta_box['id'], true );
        }
        
        $frame                  = new Frame( $this->meta_box, $values );
        $frame->type            = $this->type;
        $frame->settings_fields = wp_nonce_field( 'wp-custom-fields-metaboxes-' . $frame->id, 'wp-custom-fields-metaboxes-nonce-' . $frame->id, true, false );
        
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
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return; 
        }
        
        // Some pages do not have the nonce
        if( ! isset($_POST['wp-custom-fields-metaboxes-nonce-' . $this->meta_box['id']]) ) {
            return;
        }

        // Check our user capabilities
        if( ! current_user_can( 'edit_posts', $id ) || ! current_user_can( 'edit_pages', $id ) ) {
            return;
        }

        // If we are editing users, we are more limited
        if( $this->type == 'user' && ! current_user_can('edit_users') ) {
            return;  
        }  
         
        // Check our nonces
        if( ! wp_verify_nonce( $_POST['wp-custom-fields-metaboxes-nonce-' . $this->meta_box['id']], 'wp-custom-fields-metaboxes-' . $this->meta_box['id'] ) ) {
            return;
        }
        
        // Retrieve our current meta values
        $current    = get_metadata( $this->type, $id, $this->meta_box['id'], true ); 
        $output     = $this->format( $this->meta_box, $_POST, $this->type );
        
        // Return if nothing has changed
        if( $current == $output ) {
            return;
        }

        // Saves relations (before updating the actual meta values)
        $this->save_relations( $output, $id );        

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
                delete_metadata( $this->type, $id, $this->meta_box['id'] );
                return;
            } 
            
            // Update meta data
            update_metadata( $this->type, $id, $this->meta_box['id'], $output);  
        
        
        }
        
    }

    /**
     * Searches for relational posts and saves relations
     * 
     * @param array $output The output that is being saved
     * @param int $id The id of the current object being saved
     * @return void
     */
    private function save_relations( $output, $id ) {

        /**
         * Searches for relational fields and updates relations accordingly
         */
        foreach( $this->meta_box['sections'] as $section) {
 
            foreach( $section['fields'] as $field ) {

                // Key for retrieving and saving new values
                $meta_key = $this->single ? $field['id'] : $this->meta_box['id'];

                // Only select fields can be relational
                if( $field['type'] != 'select') {
                    continue;
                }

                // The field should be relational
                if( ! isset($field['relational']) || ! $field['relational'] ) {
                    continue;
                }

                // The field should have an object (user, post, term)
                if( ! isset($field['object']) || ! $field['object'] || $field['object'] != $this->type ) {
                    continue;
                } 

                // We should have updated output
                if( ! isset($output[$field['id']]) ) {
                    continue;
                }

                // The post we're saving should be from the same post type as the field source
                if( $this->type == 'post' && (! isset($field['source']) || $field['source'] != get_post_type($id)) ) {
                    continue;
                }

                // As well as the term...
                if( $this->type == 'term' ) {

                    if( ! isset($field['source']) ) {
                        continue;
                    }

                    $term = get_term($id);
                    if( $term->taxonomy != $field['source'] ) {
                        continue;
                    }

                }

                // First, remove our relations
                $this->remove_relations($meta_key, $output[$field['id']], $field, $id);

                // If there is no output, let's move on
                if( ! $output[$field['id']] ) {
                    continue;
                }

                // A field with multiple relational values
                if( is_array($output[$field['id']]) ) {

                    foreach( $output[$field['id']] as $target_id ) {

                        // We can't enforce relations if the target is the same as the origin
                        if( $target_id == $id ) {
                            continue;
                        }

                        // The current value at the relational post
                        $current_target = get_metadata( $this->type, $target_id, $meta_key, true ); 
                        
                        // Adds the id of the item we're saving to the array 
                        if( $this->single ) {
                            $current_target = is_array($current_target) ? array_push($current_target, $id) : [$id];
                        } else {
                            $current_target[$field['id']] = is_array($current_target[$field['id']]) ? array_push($current_target[$field['id']], $id) : [$id];
                        }

                        update_metadata( $this->type, $target_id, $meta_key, $current_target); 

                    }

                // We're just updating a single select value (the select can't have multiple options)
                } elseif( is_numeric($output[$field['id']]) && $output[$field['id']] != $id ) {

                    $target_id = intval($output[$field['id']]);

                    if( $this->single ) {
                        $current_target                = $id;
                    } else {
                        $current_target                = get_metadata( $this->type, $target_id, $meta_key, true ); 
                        $current_target[$field['id']]  = $id;   
                    }

                    update_metadata( $this->type, $target_id, $meta_key, $current_target);
                    
                }                

            }
            
        }        

    }

    /**
     * Removes relations from certain posts
     * 
     * @param string $meta_key The output that is being saved
     * @param array $output The output that is being saved
     * @param array $field The current field we're saving for
     * @param int $id The id of the current object being saved
     * @return void
     */
    private function remove_relations( $meta_key, $output, $field, $id ) {

        // Update our relations based on the current meta values
        $current            = get_metadata( $this->type, $id, $meta_key, true );
        $currentRelations   = $this->single ? $current : $current[$field['id']];

        // There should be something to compare against
        if( ! $currentRelations ) {
            return;
        }

        // Multiple relations
        if( is_array($currentRelations) ) {

            $output = is_array($output) ? $output : [];

            foreach( $currentRelations as $target_id ) {

                // If the current relations are still in the future relations (output), we continue
                if( in_array($target_id, $output) ) {
                    continue;
                }

                // The current value at the relational post
                $current_target = get_metadata( $this->type, $target_id, $meta_key, true ); 

                // Otherwise, we remove our relations from the destination
                if( $this->single ) {
                    $current_target = is_array($current_target) ? array_diff($current_target, [$id]) : [];
                } else {
                    $current_target[$field['id']] = is_array($current_target[$field['id']]) ? array_diff($current_target[$field['id']], [$id]) : [];
                }
                
                update_metadata( $this->type, $target_id, $meta_key, $current_target);
                 
            }

        // Single relations with empty outputs
        } elseif( ! is_array($currentRelations) && ! $output ) {

            $target_id = $currentRelations;

            if( $this->single ) {
                $current_target = isset($field['multiple']) && $field['multiple'] ? [] : ''; 
            } else {
                $current_target = get_metadata( $this->type, $target_id, $meta_key, true );
                $current_target[$field['id']] = isset($field['multiple']) && $field['multiple'] ? [] : ''; 
            }
            
            update_metadata( $this->type, $target_id, $meta_key, $current_target);

        }

    }
    
}