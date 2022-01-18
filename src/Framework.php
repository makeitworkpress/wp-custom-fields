<?php
/**
 *  Description: The WP Custom Fields Framework is a slim options framework for generating option pages, metaboxes, category metaboxes and custom user meta fields
 *  Version:     1.0.0
 *  Author:      Make it WorkPress
 *  Author URI:  https://www.makeitworkpress.com
 *  Domain Path: /languages
 *  Text Domain: wp-custom-fields
 */
namespace MakeitWorkPress\WP_Custom_Fields;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) 
    die;

class Framework extends Base {

    // Contains the css generator module
    public $css;    
    
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
    protected function initialize(): void {

        $defaults = ['google_maps_key' => ''];
        
        // Merge params with the defaults
        $this->params = wp_parse_args( $this->params, $defaults );
        
        // Set the folder for the framework, assuming it will be within wp-content.
        $folder = wp_normalize_path( substr( dirname(__FILE__), strpos(__FILE__, 'wp-content') + strlen('wp-content') ) );      
        
        // Define Constants
        defined( 'WP_CUSTOM_FIELDS_ASSETS_URL' ) or define( 'WP_CUSTOM_FIELDS_ASSETS_URL', content_url() . $folder . '/assets/' );
        defined( 'WP_CUSTOM_FIELDS_PATH' ) or define( 'WP_CUSTOM_FIELDS_PATH', plugin_dir_path( __FILE__ ) );
        defined( 'GOOGLE_MAPS_KEY' ) or define( 'GOOGLE_MAPS_KEY', $this->params['google_maps_key'] );
        
        // Our default frame types
        $this->types = ['meta', 'options', 'customizer'];

    }
    
    /**
     * Adds functions to WordPress hooks - is automatically performed at a new instance
     */
    protected function register_hooks(): void {  

        $this->actions = [
            ['after_setup_theme', 'setup', 20],
            ['admin_enqueue_scripts', 'enqueue']
        ];
        
        // Setup our styling
        $this->css = Styling::instance( [] );
        
    }
    
    /**
     * Set-ups the filters that allow external configurations to drip. This is hooked upon after_theme_setup so themes can take advantage.
     * Set-ups all back-end option screens, providing they are requested by the configurations
     */
    final public function setup(): void {    
        
        // Load our default configurations
        $this->add_configurations();          

        // Setup our framework, but only in the environment where needed. The correct access is determined by each module individually
        if( is_admin() || is_customize_preview() ) {           
            $this->frame();
        }

        // Execute other necessary things
        add_theme_support( 'customize-selective-refresh-widgets' );
        
    }
    
    /**
     * Adds necessary filters for adjusting configurations and load our basic configurations
     */
    private function add_configurations(): void {
        
        // Back-end assets
        if( is_admin() || is_customize_preview() ) {  
            require_once( WP_CUSTOM_FIELDS_PATH . 'config/scripts.php' );
            require_once( WP_CUSTOM_FIELDS_PATH . 'config/styles.php' );
            require_once( WP_CUSTOM_FIELDS_PATH . 'config/icons.php' );     
                    
            $this->scripts              = $scripts;
            $this->styles               = $styles;        
            self::$icons                = apply_filters( 'wp_custom_fields_icons', $icons );
        }
        
        // Fonts are also used in front-end styling
        require_once( WP_CUSTOM_FIELDS_PATH . 'config/fonts.php' );
        self::$fonts                    = apply_filters( 'wp_custom_fields_fonts', $fonts ); 
                
        if( is_admin() || is_customize_preview() ) { 
            
            // Setup the supported datatypes
            $this->types                = apply_filters( 'wp_custom_fields_frames',  $this->types );
        
            // Adds filterable data for the various types.
            foreach( $this->types as $type ) {
                $this->frames[$type]    = apply_filters( 'wp_custom_fields_frame_' . $type, isset($this->frames[$type]) ? $this->frames[$type] : [] );
            }

        }
        
    }
    
    /**
     * Set-up the option frames for the framework
     */
    private function frame(): void {
        
        // Initiates the various option or meta types
        foreach( $this->frames as $frame => $options_groups ) {
            
            // Only predefined frames are allowed            
            if( ! in_array($frame, $this->types) ) {
                continue;
            }
            
            // We should have something defined
            if( empty($options_groups) ) {
                continue;
            }
            
            // Option and meta pages are only visible on admin
            if( ! is_admin() && ($frame == 'meta' || $frame == 'options') ) {
                continue;
            }
            
            // And our customizer only on preview
            if( ! is_customize_preview() && $frame == 'customizer' ) {
                continue;
            }
            
            // Create a new instance for each group
            foreach( $options_groups as $group ) {
                $class    = 'MakeitWorkPress\WP_Custom_Fields\\' . ucfirst( $frame );

                if( class_exists($class ) && $group ) {
                    $instance = new $class( $group );
                }

                // If our configurations are not valid, we report back with an error
                if( isset($instance->validated) && is_wp_error($instance->validated ) ) {
                    add_action( 'admin_notices', function() use($instance) {
                        echo '<div class="error notice">';
                        echo '  <p>' . sprintf( __('Error in WP Custom Fields: %s', 'wpcf'), $instance->validated->get_error_message() ) . '</p>';
                        echo '</div>';
                    });
                }
            }
            
        }      

    }     
    
    /**
     * Enqueues our scripts and styles
     */
    final public function enqueue(): void {

        // Enqueue Styles
        foreach( $this->styles as $style ) {
            wp_enqueue_style( $style['handle'], $style['src'], $style['deps'], $style['ver'], $style['media'] );     
        }
        
        // Enqueue Scripts
        foreach( $this->scripts as $script ) {

            $action = 'wp_' . $script['action'] . '_script';
            $action( $script['handle'], $script['src'], $script['deps'], $script['ver'], $script['in_footer'] );
            
            // Localize a script
            if( isset($script['localize']) && isset($script['object']) ) {
                wp_localize_script( $script['handle'], $script['object'], $script['localize'] );
            }
            
        }
        
    }
                
    /**
     * Retrieves certain configurations
     *
     * @param string $type The kind of configurations to get
     * @return array $configurations The array of configurations for the respective type, accepts 'meta', 'options', 'customizer' or 'all'
     */
    public function get( string $type ): array {
        if( $type == 'all' ) {
            return $this->frames;
        } elseif( isset($this->frames[$type]) ) {
            return $this->frames[$type];
        } else {
            return [];
        }
    }     
        
    /**
     * Allows to adds certain data, such as data for fields. 
     * If hooked late on after_setup_theme but before init, this will add fields.
     *
     * @param string    $type   The type to which you want to add, accepts 'meta', 'options', 'customizer'
     * @param array     $values The respective values in form of an associative array
     */
    public function add( string $type, array $values ): void {
        
        // Only predefined frames are allowed            
        if( ! in_array($type, $this->types) )
            return;
        
        $this->frames[$type][] = $values;
        
    }  
    
    /**
     * Allows to adds certain data, such as data for fields. 
     * If hooked late on after_setup_theme but before init, this will be able to edit fields.
     *
     * @param string    $type       The type to which you want to add values, accepts 'meta', 'options', 'customizer'
     * @param array     $values     The respective values in form of an associative array
     * @param string    $id         The id of the option to edit
     * @param string    $section    The section of the option to edit
     * @param string    $field      The field id of the option to edit
     * @param string    $key        The key of the field to edit
     */
    public function edit( string $type, array $values, string $id = '', string $section = '', string $field = '', string $key = '' ): void {
        
        // Only predefined frames are allowed            
        if( ! in_array($type, $this->types) )
            return;
        
        // Our id should always exist
        if( ! isset($this->frames[$type][$id]) ) {
            return;
        }

        if( $section && ! isset($this->frames[$type][$id][$section]) ) {
            return;
        }
        
        if( $field && ! isset($this->frames[$type][$id][$section][$field]) ) {
            return;
        }
        
        // A bit undry, but good for now
        if( $field ) {
            if( $key ) {
                $this->frames[$type][$id][$section][$field][$key] = $values;
            } else {
                $this->frames[$type][$id][$section][$field] = $values;
            }
        } elseif( $section ) {
            if( $key ) {
                $this->frames[$type][$id][$section][$key] = $values;
            } else {
                $this->frames[$type][$id][$section] = $values;
            }
        } else {
            if( $key ) {
                $this->frames[$type][$id][$key] = $values;
            } else {
                $this->frames[$type][$id] = $values;
            }
        }     
        
    }
    
    /**
     * Looks for fields that for display depend on the values of other fields
     * 
     * @param   array       $dependency The dependency values for the dependent field
     * @param   array       $sections The sections with fields to look in for
     * @param   array       $values The saved values for the fields
     * @return  string      $class Returns active if a dependency is fulfilled on page load
     */
    public static function return_dependency_Class( array $dependency, array $sections = [], array $values = [] ): string {

        $class          = '';
        $source_field   =  [];

        // Checks if everything is there
        foreach( ['equation', 'source', 'value'] as $key ) {
            if( ! isset($dependency[$key]) || ! $dependency[$key] ) {
                return $class;    
            }           
        }

        // Let's find the field we're looking for
        foreach( $sections as $section ) {
            foreach( $section['fields'] as $field ) {
                if( $field['id'] == $dependency['source'] ) {
                    $source_field = $field;
                    break;
                }    
            }
        }

        // Let's return our field
        if( ! $source_field || ! isset($values[$source_field['id']]) ) {
            return $class;
        }

        $value = maybe_unserialize($values[$source_field['id']]);

        // Retrieve our equation
        if( $dependency['equation'] == '=' ) {
            if( $dependency['value'] == $value || (is_array($value) && in_array($dependency['value'], $value)) ) {
                $class = ' active';
            }
        } else if( $dependency['equation'] == '!=' ) {
            if( $dependency['value'] != $value || (is_array($value) && ! in_array($dependency['value'], $value)) ) {
                $class = ' active';
            }
        }

        return $class;

    }    
       
}