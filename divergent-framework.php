<?php 
/**
 *  Plugin Name: Divergent Framework
 *  Plugin URI:  #
 *  Description: The Divergent Framework is a very extensive options framework for generating option pages, metaboxes, category metaboxes and custom user meta fields
 *  Version:     0.0.1
 *  Author:      Make it WorkPress
 *  Author URI:  http://www.makeitworkpress.com
 *  Domain Path: /languages
 *  Text Domain: divergent
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) { 
    die; 
} 

/**
 * Initializes the plugin
 */
if( ! class_exists( 'Divergent' ) ) {
    
    /*
     * Defines Plugin Constants
     */
    defined( 'DIVERGENT_LANGUAGE' ) or define( 'DIVERGENT_LANGUAGE', 'divergent' );
    defined( 'DIVERGENT_CONFIG_PATH' ) or define( 'DIVERGENT_CONFIG_PATH', plugin_dir_path( __FILE__ ) . 'configurations/' );
    defined( 'DIVERGENT_INCLUDES_PATH' ) or define( 'DIVERGENT_INCLUDES_PATH', plugin_dir_path( __FILE__ ) . 'classes/' );
    defined( 'DIVERGENT_ASSETS_URL' ) or define( 'DIVERGENT_ASSETS_URL', plugin_dir_url( __FILE__ ) . 'assets/' );
    defined( 'DIVERGENT_PATH' ) or define( 'DIVERGENT_PATH', plugin_dir_path( __FILE__ ) );

    /**
     * Include required assets and boot the plugin
     */
    require_once( DIVERGENT_INCLUDES_PATH . 'class-divergent-abstract.php');
    require_once( DIVERGENT_INCLUDES_PATH . 'class-divergent-helpers.php');
    require_once( DIVERGENT_INCLUDES_PATH . 'class-divergent.php');
    require_once( DIVERGENT_CONFIG_PATH . 'configurations.php');

    $divergent = Divergent::instance();

}