<?php
/**
 * Registers scripts
 */

// Bail if accessed directly
if ( ! defined('ABSPATH') ) {
    die; 
}

$scripts[] = [
    'handle'    => 'alpha-color-picker',
    'src'       => WP_CUSTOM_FIELDS_ASSETS_URL . 'js/vendor/alpha-color-picker.min.js',
    'deps'      => ['jquery', 'wp-color-picker' ],
    'ver'       => '',
    'in_footer' => 'true', 
    'action'    => 'register',
];

$scripts[] = [
    'handle'    => 'wp-custom-fields-js',
    'src'       => WP_CUSTOM_FIELDS_ASSETS_URL . 'js/wpcf.js',
    'deps'      => ['jquery', 'wp-color-picker' ],
    'ver'       => null,
    'in_footer' => true, 
    'action'    => 'register',
    'localize'  => [
        'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
        'debug'     => defined('WP_DEBUG') && WP_DEBUG ? true : false,
        'nonce'     => wp_create_nonce('wp-custom-fields') 
    ],
    'object'    => 'wpcf'
];  

$scripts[] = [
    'handle'    => 'google-maps-js',
    'src'       => 'https://maps.googleapis.com/maps/api/js?libraries=places&key=' . GOOGLE_MAPS_KEY,
    'deps'      => [],
    'ver'       => '3',
    'in_footer' => true,  
    'action'    => 'register'
]; 

$scripts[] = [
    'handle'    => 'flatpicker-js',
    'src'       => WP_CUSTOM_FIELDS_ASSETS_URL . 'js/vendor/flatpicker.min.js',
    'deps'      => [],
    'ver'       => null,
    'in_footer' => true,  
    'action'    => 'register'
];

$scripts[] = [
    'handle'    => 'flatpicker-i18n-nl',
    'src'       => WP_CUSTOM_FIELDS_ASSETS_URL . 'js/vendor/flatpicker-i18n/nl.js',
    'deps'      => [],
    'ver'       => null,
    'in_footer' => true,  
    'action'    => 'register'
];

$scripts[] = [
    'handle'    => 'select2-js',
    'src'       => WP_CUSTOM_FIELDS_ASSETS_URL . 'js/vendor/select2.min.js',
    'deps'      => [],
    'ver'       => null,
    'in_footer' => true,  
    'action'    => 'register'
];