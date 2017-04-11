<?php
/**
 * This is the container class for the plugin, containing all necessary configuration
 *
 * @package Divergent
 * @author Michiel Tramper 
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) { 
    die; 
}

namespace Controllers;

class Divergent extends Divergent_Abstract {
    
    /**
     * Use our validation functions
     */
    use Divergent_Validate;

    /**
     * These properties hold all configurations for the custom fields for each frame.
     */
    protected $frames; 
    
    /**
     * Initializes the plugin 
     */
    protected function initialize() {}
    
    /**
     * Adds functions to WordPress hooks - is automatically performed at a new instance
     */
    protected function registerHooks() {           
        $this->actions = array(
            array('after_setup_theme', 'setup', 20),
            array('admin_enqueue_scripts', 'enqueue')
        );
    }
    
    /**
     * Set-ups the filters that allow external configurations to drip
     * Set-ups up all divergent modules 
     * Set-ups all back-end option screens, providing they are requested by the configurations
     */
    final public function setup() {
        
        // Add our configurations
        $this->addConfigurations();
        
        // Setup our framework
        if( is_admin() )
            $this->frame(); 
        
    }
    
    /**
     * Enqueues our scripts and styles
     */
    final public function enqueue() {
        require_once(DIVERGENT_PATH . 'configurations.php');
        
        foreach( $styles as $style )
            wp_enqueue_style( $style['handle'], $style['src'], $style['deps'], $style['ver'], $style['media'] );     
        
        foreach( $scripts as $script ) {
            $action = 'wp_' . $script['context'] . '_script';
            $action( $script['handle'], $script['src'], $script['deps'], $script['ver'], $script['in_footer'] );    
        }
    }
    
    /**
     * Adds necessary filters for adjusting configurations
     */
    final private function addConfigurations() {
                
        // Setup the supported datatypes
        $types = apply_filters('divergentFrames', array('meta', 'options') );
        
        // Adds filterable data for the various types.
        foreach($types as $type) {
            $this->frames[$type]  = apply_filters( 'divergent' . $type, isset($this->frames[$type]) ? $this->frames[$type] : array() );
        }
        
    }
    
    /**
     * Set-up the option pages for the framework
     */
    final private function frame() {
        
        global $pagenow;
        
        // Initiates the various option or meta types
        foreach( $this->frames as $frame => $values) {
            if( ! empty($frame) )
                $instance = ${'Divergent_' . ucfirst($frame)}::instance( $values );
        }      

    } 
                       
    /**
     * Retrieves certain configurations
     *
     * @param string $type The kind of configurations to get
     * @return array $configurations The array of configurations for the respective type
     */
    public function get($type) {
        return $this->frames[$type];
    }     
        
    /**
     * Allows to adds certain data, such as data for fields. 
     * If hooked late on after_setup_theme but before init, this will add fields.
     *
     * @param string $type The type to which you want to add values
     * @param array $values The respective values in form of an associative array;
     */
    public function set($type, $values) {
        $this->frames[$type][] = $values;    
    }    
       
}