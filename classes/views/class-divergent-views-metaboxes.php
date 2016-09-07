<?php
/** 
 * Displays metaboxes
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) { 
    die; 
} 

class Divergent_Views_Metaboxes { 
        
    /**
     * Constructor for the metaboxes callback
     *
     * @param object WP_Post $post The post object for the current post type
     * @param array $metabox The array of passed through the callback function in $this->add_metaboxes
     * @param array $meta_values The meta values for the current meta box
     */
    public function __construct( $post = '', $metaboxes = array(), $meta_values = array() ) {
        $this->render($post, $metaboxes, $meta_values);
    }
    
    /**
     * Displays the wrapper for the metaboxes
     *
     * @param object WP_Post $post The post object for the current post type
     * @param array $metabox The array passed through the callback function in $this->add_metaboxes
     * @param array $meta_value The current value of the metaboxes
     */
    private function render( $post = '', $metaboxes = array(), $meta_values = array() ) {
        
        // Create a nonce field for saving safety
        wp_nonce_field( 'divergent-metaboxes', 'divergent-metaboxes-nonce' );
        
        $id = $metaboxes['args']['id'];
        $sections  = isset( $metaboxes['args']['sections'] ) ? $metaboxes['args']['sections'] : array();
                        
        $output = '<div class="divergent-metaboxes divergent-framework">';
        
        if( ! empty($sections) ) {
                                
            $output .= '    <ul class="divergent-tabs">';

                foreach( $sections as $key => $section ) {
                    
                    $active = $sections[0]['id'] == $section['id'] ? ' active' : '';
                    
                    $icon = ( ! empty( $section['icon'] ) ) ? '<i class="divergent-icon '. $section['icon'] .'"></i>' : '';

                    $output .= '       <li><a class="divergent-tab' . $active . '" href="#'. $section['id'] .'" data-section="'. $section['id'] .'">'. $icon . $section['title'] .'</a></li>';           
                }

            $output .= '    </ul>';
            
            $output .= '   <div class="divergent-sections">';
            
                foreach( $sections as $key => $section ) {
                    
                    $active = $sections[0]['id'] == $section['id'] ? ' active' : '';
                    
                    $title = isset($section['title']) ? $section['title'] : '';
                                        
                    $output .= '       <section id="' . $section['id'] . '" class="divergent-section' . $active . '">';
                    
                    if( ! empty($title) ) { 
                        $output .= '            <h4 class="divergent-section-title">' . $section['title'] . '</h4>';
                    }
                    
                    if( isset($section['fields']) ) {
                    
                        // As these fields are repeated often, static methods are used, preventing that there are a large number of instances in the memory.
                        foreach($section['fields'] as $field) {
                            
                            $field['values']     = isset($meta_values[$field['id']]) ? $meta_values[$field['id']] : '';
                            $field['section_id'] = $section['id'];
                            $field['option_id']  = $id;

                            $output .= Divergent_Fields::render($field);
                        }
                        
                    }
                    
                    $output .= '       </section>';
                }            
            $output .= '   </div>';
            
        } else {
            $output .= '<div class="error"><p>' . __('You have not added sections for one of the custom metaboxes (properly). Please review your metabox configurations.', 'divergent') . '</p></div>';
        }           
        
        $output .= '</div>';
        
        echo $output;
        
    }
}