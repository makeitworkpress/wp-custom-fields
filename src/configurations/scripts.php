<?php
/**
 * Registers scripts
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

$suffix = defined('WP_DEBUG') && WP_DEBUG ? '' : '.min';

$scripts[] = array(
    'handle'    => 'alpha-color-picker',
    'src'       => WP_CUSTOM_FIELDS_ASSETS_URL . 'js/vendor/alpha-color-picker.min.js',
    'deps'      => array( 'jquery', 'wp-color-picker' ),
    'ver'       => '',
    'in_footer' => 'true', 
    'action'    => 'register',
);      

$scripts[] = array(
    'handle'    => 'wp-custom-fields-js',
    'src'       => WP_CUSTOM_FIELDS_ASSETS_URL . 'js/wp-custom-fields' . $suffix . '.js',
    'deps'      => array( 'jquery' ),
    'ver'       => null,
    'in_footer' => true,  
    'action'    => 'register',
    'localize'  => [
        'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
        'debug'     => defined('WP_DEBUG') && WP_DEBUG ? true : false,
        'nonce'     => wp_create_nonce('wp-custom-fields') 
    ],
    'object'    => 'wpcf'
);  

$scripts[] = array(
    'handle'    => 'google-maps-js',
    'src'       => 'https://maps.googleapis.com/maps/api/js?libraries=places&key=' . GOOGLE_MAPS_KEY,
    'deps'      => array(),
    'ver'       => '3',
    'in_footer' => true,  
    'action'    => 'register'
); 

$scripts[] = array(
    'handle'    => 'mirror-js',
    'src'       => WP_CUSTOM_FIELDS_ASSETS_URL . 'js/vendor/codemirror.min.js',
    'deps'      => array(),
    'ver'       => null,
    'in_footer' => true,  
    'action'    => 'register'
);

$scripts[] = array(
    'handle'    => 'select2-js',
    'src'       => WP_CUSTOM_FIELDS_ASSETS_URL . 'js/vendor/select2.min.js',
    'deps'      => array(),
    'ver'       => null,
    'in_footer' => true,  
    'action'    => 'register'
);