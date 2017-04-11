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
 */  
$styles[] = array(
    'handle'    => 'fontawesome-css',
    'src'       => 'https://fonts.googleapis.com/icon?family=Material+Icons',
    'deps'      => array(),
    'ver'       => null,
    'media'     => 'all'
);     

$styles[] = array(
    'handle'    => 'admin-css',
    'src'       => DIVERGENT_ASSETS_URL . 'css/admin.min.css',
    'deps'      => array(),
    'ver'       => null,
    'media'     => 'all'
);

$styles[] = array(
    'handle'    => 'wp-color-picker',
    'src'       => '',
    'deps'      => array(),
    'ver'       => '',
    'media'     => ''
);

$styles[] = array(
    'handle'    => 'alpha-color-picker',
    'src'       => DIVERGENT_ASSETS_URL . 'css/vendor/alpha-color-picker.min.css',
    'deps'      => array( 'wp-color-picker' ),
    'ver'       => '',
    'media'     => ''
);    

$styles[] = array(
    'handle'    => 'mirror-css',
    'src'       => DIVERGENT_ASSETS_URL . 'css/vendor/codemirror.min.css',
    'deps'      => array(),
    'ver'       => '',
    'media'     => ''
);     

/**
 * Registers scripts
 */
$scripts[] = array(
    'handle'    => 'jquery-validate',
    'src'       => DIVERGENT_ASSETS_URL . 'js/vendor/jquery-validate.min.js',
    'deps'      => array('jquery'),
    'ver'       => null,
    'in_footer' => true,  
    'context'   => 'enqueue'
);

$scripts[] = array(
    'handle'    => 'alpha-color-picker',
    'src'       => DIVERGENT_ASSETS_URL . 'js/vendor/alpha-color-picker.min.js',
    'deps'      => array( 'jquery', 'wp-color-picker' ),
    'ver'       => '',
    'in_footer' => 'true', 
    'context'   => 'enqueue',
);      

$scripts[] = array(
    'handle'    => 'admin-js',
    'src'       => DIVERGENT_ASSETS_URL . 'js/options-framework.js',
    'deps'      => array('jquery', 'alpha-color-picker'),
    'ver'       => null,
    'in_footer' => true,  
    'context'   => 'enqueue'
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