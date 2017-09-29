<?php
/**
 *  Description: The WP Custom Fields Framework is a slim options framework for generating option pages, metaboxes, category metaboxes and custom user meta fields
 *  Version:     1.0.0
 *  Author:      Make it WorkPress
 *  Author URI:  https://www.makeitworkpress.com
 *  Domain Path: /languages
 *  Text Domain: wp-custom-fields
 */
namespace WP_Custom_Fields;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) 
    die;

class Framework extends Base {
    
    // Contains icons available in the frame
    public static $icons;
    
    // Contains the fonts available in the frame
    public static $fonts;
    // These properties hold all configurations for the custom fields for each frame. A frame is an options screen, anywhere.
    protected $frames; 

    // Contains the frame types
    private $types;    
    
    // Contains the styles that need to be enqueued
    private $styles;
    
    // Contains the scripts  that need to be enqueued
    private $scripts;      
    
    /**
     * Initializes the plugin 
     */
    protected function initialize() {        
        
        $defaults = array(
            'google_maps_key' => ''
        );
        
        // Merge params with the defaults
        $this->params = wp_parse_args( $this->params, $defaults );
        
        // Set the folder for the framework, assuming it will be within wp-content.
        $folder = wp_normalize_path( substr( dirname(__FILE__), strpos(__FILE__, 'wp-content') + strlen('wp-content') ) );      
        
        // Define Constants
        defined( 'WP_CUSTOM_FIELDS_ASSETS_URL' ) or define( 'WP_CUSTOM_FIELDS_ASSETS_URL', content_url() . $folder . '/assets/' );
        defined( 'WP_CUSTOM_FIELDS_PATH' ) or define( 'WP_CUSTOM_FIELDS_PATH', plugin_dir_path( __FILE__ ) );
        defined( 'GOOGLE_MAPS_KEY' ) or define( 'GOOGLE_MAPS_KEY', $this->params['google_maps_key'] );
        
        // Our default types
        $this->types = array('meta', 'options', 'customizer');

    }
    
    /**
     * Adds functions to WordPress hooks - is automatically performed at a new instance
     */
    protected function registerHooks() {           
        $this->actions = array(
            array( 'after_setup_theme', 'setup', 20 ),
            array( 'admin_enqueue_scripts', 'enqueue' )
        );
        
        // Setup our styling
        if( ! is_admin() || is_customize_preview() )
            Styling::instance( array() ); 
        
    }
    
    /**
     * Set-ups the filters that allow external configurations to drip
     * Set-ups up all divergent modules 
     * Set-ups all back-end option screens, providing they are requested by the configurations
     */
    final public function setup() {
        
        // Add our configurations arrays
        $this->addConfigurations();        

        // Setup our framework, but only in the environment where needed
        if( is_admin() || is_customize_preview() ) {           
            $this->frame();
        }

        // Execute other necessary things
        add_theme_support( 'customize-selective-refresh-widgets' );
        
    }
    
    /**
     * Adds necessary filters for adjusting configurations and load our basic configurations
     */
    private function addConfigurations() {
        
        // Load our configurations
        require_once( WP_CUSTOM_FIELDS_PATH . 'configurations/scripts.php' );
        require_once( WP_CUSTOM_FIELDS_PATH . 'configurations/styles.php' );
        require_once( WP_CUSTOM_FIELDS_PATH . 'configurations/fonts.php' );
        require_once( WP_CUSTOM_FIELDS_PATH . 'configurations/icons.php' );
        
        $this->scripts          = $scripts;
        $this->styles           = $styles;
        
        // Icons are only used in field elements, thus in back-end or customizer.
        if( is_admin() || is_customize_preview() ) {   
            self::$icons        = apply_filters( 'wp_custom_fields_icons', $icons );
        }
        
        // Fonts are used in front-end styling
        self::$fonts            = apply_filters( 'wp_custom_fields_fonts', $fonts ); 
                
        // Setup the supported datatypes
        $this->types            = apply_filters( 'wp_custom_fields_frames',  $this->types );
        
        // Adds filterable data for the various types.
        foreach($this->types as $type) {
            $this->frames[$type] = apply_filters( 'wp_custom_fields_frame_' . $type, isset($this->frames[$type]) ? $this->frames[$type] : array() );
        }
        
    }
    
    /**
     * Set-up the option pages for the framework
     */
    private function frame() {
        
        // Initiates the various option or meta types
        foreach( $this->frames as $frame => $optionsGroups) {
            
            // Only predefined frames are allowed            
            if( ! in_array($frame, $this->types) )
                continue;
            
            // We should have something defined
            if( empty($optionsGroups) )
                continue;
            
            // Option and meta pages are only visible on admin
            if( ! is_admin() && ($frame == 'meta' || $frame == 'options') )
                continue;
            
            // And our customizer only on preview
            if( ! is_customize_preview() && $frame == 'customizer' )
                continue;
            
            // Create a new instance for each group
            foreach( $optionsGroups as $group ) {
                $class    = 'WP_Custom_Fields\\' . ucfirst( $frame );
                $instance = new $class( $group );
            }
            
        }      

    }     
    
    /**
     * Enqueues our scripts and styles
     */
    final public function enqueue() {

        // Enqueue Styles
        foreach( $this->styles as $style )
            wp_enqueue_style( $style['handle'], $style['src'], $style['deps'], $style['ver'], $style['media'] );     
        
        // Enqueue Scripts
        foreach( $this->scripts as $script ) {
            $action = 'wp_' . $script['action'] . '_script';
            $action( $script['handle'], $script['src'], $script['deps'], $script['ver'], $script['in_footer'] );
            
            // Localize a script
            if( isset($script['localize']) && isset($script['object']) )
                wp_localize_script( $script['handle'], $script['object'], $script['localize'] );
            
        }
        
    }
                
    /**
     * Retrieves certain configurations
     *
     * @param string $type The kind of configurations to get
     * @return array $configurations The array of configurations for the respective type
     */
    public function get( $type ) {
        return $type == 'all' ? $this->frames : $this->frames[$type];
    }     
        
    /**
     * Allows to adds certain data, such as data for fields. 
     * If hooked late on after_setup_theme but before init, this will add fields.
     *
     * @param string    $type   The type to which you want to add values, Meta or Options
     * @param array     $values The respective values in form of an associative array
     */
    public function add( $type, $values ) {
        
        // Only predefined frames are allowed            
        if( ! in_array($type, $this->types) )
            return;
        
        $this->frames[$type][] = $values;
        
    }     
       
}