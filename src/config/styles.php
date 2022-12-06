<?php
/**
 * Registers styles
 */

// Bail if accessed directly
if ( ! defined('ABSPATH') ) {
    die; 
}

$styles[] = [
    'handle'    => 'wpc-material-css',
    'src'       => 'https://fonts.googleapis.com/icon?family=Material+Icons',
    'deps'      => [],
    'ver'       => null,
    'media'     => 'all'
];  

$styles[] = [
    'handle'    => 'wpc-font-awesome-css',
    'src'       => WP_CUSTOM_FIELDS_ASSETS_URL . 'css/vendor/font-awesome.min.css',
    'deps'      => [],
    'ver'       => null,
    'media'     => 'all'
];

$styles[] = [
    'handle'    => 'wp-color-picker',
    'src'       => '',
    'deps'      => [],
    'ver'       => '',
    'media'     => ''
];  

$styles[] = [
    'handle'    => 'wpc-flatpicker-css',
    'src'       => WP_CUSTOM_FIELDS_ASSETS_URL . 'css/vendor/flatpicker.min.css',
    'deps'      => [],
    'ver'       => '',
    'media'     => ''
]; 

$styles[] = [
    'handle'    => 'wpc-mirror-css',
    'src'       => WP_CUSTOM_FIELDS_ASSETS_URL . 'css/vendor/codemirror.min.css',
    'deps'      => [],
    'ver'       => '',
    'media'     => ''
]; 

$styles[] = [
    'handle'    => 'wpc-select2-css',
    'src'       => WP_CUSTOM_FIELDS_ASSETS_URL . 'css/vendor/select2.min.css',
    'deps'      => [],
    'ver'       => '',
    'media'     => ''
]; 

$styles[] = [
    'handle'    => 'wpc-css',
    'src'       => WP_CUSTOM_FIELDS_ASSETS_URL . 'css/wp-custom-fields.min.css',
    'deps'      => [],
    'ver'       => null,
    'media'     => 'all'
];