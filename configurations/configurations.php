<?php
/**
 * Loads the initial configurations for the plugin
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) { 
    die; 
}

/**
 * Registers styles
 *
 * @param array $styles The multidimensional array with styles
 */
function divergent_styles($styles) { 
        
//    $styles[] = array(
//        'handle'    => 'fontawesome-css',
//        'src'       => 'https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css',
//        'deps'      => array(),
//        'ver'       => null,
//        'media'     => 'all', 
//        'context'   => 'admin'
//    ); 
    
    $styles[] = array(
        'handle'    => 'fontawesome-css',
        'src'       => 'https://fonts.googleapis.com/icon?family=Material+Icons',
        'deps'      => array(),
        'ver'       => null,
        'media'     => 'all', 
        'context'   => 'admin'
    );     
    
    $styles[] = array(
        'handle'    => 'admin-css',
        'src'       => DIVERGENT_ASSETS_URL . 'css/admin.min.css',
        'deps'      => array(),
        'ver'       => null,
        'media'     => 'all', 
        'context'   => 'admin'
    );
    
    $styles[] = array(
        'handle'    => 'wp-color-picker',
        'src'       => '',
        'deps'      => array(),
        'ver'       => '',
        'media'     => '', 
        'context'   => 'admin'
    );
    
    $styles[] = array(
        'handle'    => 'alpha-color-picker',
        'src'       => DIVERGENT_ASSETS_URL . 'css/vendor/alpha-color-picker.min.css',
        'deps'      => array( 'wp-color-picker' ),
        'ver'       => '',
        'media'     => '', 
        'context'   => 'admin'
    );    
    
    $styles[] = array(
        'handle'    => 'mirror-css',
        'src'       => DIVERGENT_ASSETS_URL . 'css/vendor/codemirror.min.css',
        'deps'      => array(),
        'ver'       => '',
        'media'     => '', 
        'context'   => 'admin'
    );     
        
    return $styles;
    
}
add_filter('divergent_styles', 'divergent_styles');

/**
 * Registers scripts
 *
 * @param array $scripts The multidimensional array with scripts 
 */
function divergent_scripts($scripts) { 
    
    $scripts[] = array(
        'handle'    => 'jquery-validate',
        'src'       => DIVERGENT_ASSETS_URL . 'js/vendor/jquery-validate.min.js',
        'deps'      => array('jquery'),
        'ver'       => null,
        'in_footer' => true,  
        'context'   => 'admin'
    );
    
    $scripts[] = array(
        'handle'    => 'alpha-color-picker',
        'src'       => DIVERGENT_ASSETS_URL . 'js/vendor/alpha-color-picker.min.js',
        'deps'      => array( 'jquery', 'wp-color-picker' ),
        'ver'       => '',
        'in_footer' => 'true', 
        'context'   => 'admin',
    );      
    
    $scripts[] = array(
        'handle'    => 'admin-js',
        'src'       => DIVERGENT_ASSETS_URL . 'js/options-framework.js',
        'deps'      => array('jquery', 'alpha-color-picker'),
        'ver'       => null,
        'in_footer' => true,  
        'context'   => 'admin'
    );  
           
    $scripts[] = array(
        'handle'    => 'google-maps-js',
        'src'       => 'https://maps.googleapis.com/maps/api/js?libraries=places',
        'deps'      => null,
        'ver'       => '3',
        'in_footer' => true,  
        'context'   => 'admin',
        'action'    => 'register'
    ); 
    
    $scripts[] = array(
        'handle'    => 'mirror-js',
        'src'       => DIVERGENT_ASSETS_URL . 'js/vendor/codemirror.min.js',
        'deps'      => null,
        'ver'       => null,
        'in_footer' => true,  
        'context'   => 'admin',
        'action'    => 'register'
    );
        
    return $scripts;
    
}
add_filter('divergent_scripts', 'divergent_scripts');