<?php
/** 
 * This class is responsible for controlling the display of metaboxes within taxonomy context
 * 
 * @author Michiel
 * @since 1.0.0
 * @package Divergent
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) { 
    die; 
}

class Divergent_Taxmeta extends Divergent {
 
    /**
     * Constructs the metaboxes and boot up the view script responsible for displaying the metaboxes
     *
     * @param mixed $params The parameters passed to this object
     */
    protected function initialize(Array $params) {
        
        do_action('divergent_taxmeta_initialize', $this);
        
        $this->tax_fields = $params;
        
        do_action('after_divergent_taxmeta_initialize', $this);
    }
    
    protected function register_hooks() {
        
        foreach($this->tax_fields as $field) {
            
            // Do not proceed for empty or untargeted configurations
            if( ! isset($metabox['id']) || ! isset($metabox['taxonomy']) || ! $metabox['id'] || ! $metabox['taxonomy'] ) 
                continue;
            
            $this->action[] = array( $field['taxonomy'] . '_edit_form_fields', 'add_metaboxes' );    
            $this->action[] = array( 'edited_' . $field['taxonomy'], 'save_metaboxes', 10, 1 );    
        }
        
    }    
    
    public function add_metaboxes() {
        
    }
    
    public function save_metaboxes() {
        
    }
    
}