<?php 
/**
 *  Description: The Divergent Framework is a very extensive options framework for generating option pages, metaboxes, category metaboxes and custom user meta fields
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

/**
 * Registers the function for autoloading this framework
 */
spl_autoload_register( function($classname) {

    $class     = str_replace( '\\', DIRECTORY_SEPARATOR, str_replace( '_', '-', strtolower($classname) ) );
    
    $file_path = plugin_dir_path( __FILE__ ) . DIRECTORY_SEPARATOR . 'class-' . $class . '.php';
    
    if ( file_exists( $file_path ) ) {
        require_once $file_path;
    }    
} );

/**
 * Initializes the plugin
 */
if( ! class_exists( 'Divergent' ) ) {
    
    /*
     * Define Constants
     */
    defined( 'DIVERGENT_ASSETS_URL' ) or define( 'DIVERGENT_ASSETS_URL', plugin_dir_url( __FILE__ ) . 'assets/' );
    defined( 'DIVERGENT_PATH' ) or define( 'DIVERGENT_PATH', plugin_dir_path( __FILE__ ) );

    /**
     * Boot our application
     */
    $divergent = Controllers\Divergent::instance();

}
