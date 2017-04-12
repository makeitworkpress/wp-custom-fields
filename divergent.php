<?php

/**
 *  Description: The Divergent Framework is a slim options framework for generating option pages, metaboxes, category metaboxes and custom user meta fields
 *  Version:     1.0.0
 *  Author:      Make it WorkPress
 *  Author URI:  https://www.makeitworkpress.com
 *  Domain Path: /languages
 *  Text Domain: divergent
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) { 
    die; 
}

namespace Classes;

class Divergent extends Divergent_Abstract {

    // These properties hold all configurations for the custom fields for each frame.
    protected $frames; 
    
    // Contains icons available in the frame
    public static $icons;
    
    // Contains the fonts available in the frame
    public static $fonts; 
    
    // Contains the styles that need to be enqueued
    private $styles;
    
    // Contains the scripts  that need to be enqueued
    private $scripts;      
    
    /**
     * Initializes the plugin 
     */
    protected function initialize() {
        
        // Define Constants
        defined( 'DIVERGENT_ASSETS_URL' ) or define( 'DIVERGENT_ASSETS_URL', plugin_dir_url( __FILE__ ) . 'assets/' );
        defined( 'DIVERGENT_PATH' ) or define( 'DIVERGENT_PATH', plugin_dir_path( __FILE__ ) );        
        
    }
    
    /**
     * Adds functions to WordPress hooks - is automatically performed at a new instance
     */
    protected function registerHooks() {           
        $this->actions = array(
            array( 'after_setup_theme', 'setup', 20 ),
            array( 'admin_enqueue_scripts', 'enqueue' )
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

        foreach( $this->styles as $style )
            wp_enqueue_style( $style['handle'], $style['src'], $style['deps'], $style['ver'], $style['media'] );     
        
        foreach( $this->scripts as $script ) {
            $action = 'wp_' . $script['context'] . '_script';
            $action( $script['handle'], $script['src'], $script['deps'], $script['ver'], $script['in_footer'] );    
        }
    }
    
    /**
     * Adds necessary filters for adjusting configurations and load our basic configurations
     */
    final private function addConfigurations() {
        
        // Load our configurations
        require_once( DIVERGENT_PATH . 'configurations.php' );
        
        $this->scripts          = $scripts;
        $this->styles           = $styles;
        
        self::$icons            = apply_filters( 'divergentIcons', $icons );
        self::$fonts            = apply_filters( 'divergentFonts', $fonts );        
                
        // Setup the supported datatypes
        $types = apply_filters('divergentFrames', array('Meta', 'Options') );
        
        // Adds filterable data for the various types.
        foreach($types as $type) {
            $this->frames[$type]  = apply_filters( 'divergent' . $type, isset($this->frames[$type]) ? $this->frames[$type] : array() );
        }
        
    }
    
    /**
     * Set-up the option pages for the framework
     */
    final private function frame() {
        
        // Initiates the various option or meta types
        foreach( $this->frames as $frame => $optionsGroups) {
            
            // We should have something defined
            if( empty($optionsGroups) )
                continue;
            
            // Create a new instance
            foreach( $optionsGroups as $group ) {
                $instance = ${'Divergent_' . $frame}::instance( $group );
            }
            
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