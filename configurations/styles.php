<?php
/**
 * Registers styles
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

$styles[] = array(
    'handle'    => 'fontawesome-css',
    'src'       => 'https://fonts.googleapis.com/icon?family=Material+Icons',
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
    'src'       => WP_CUSTOM_FIELDS_ASSETS_URL . 'css/vendor/alpha-color-picker.min.css',
    'deps'      => array( 'wp-color-picker' ),
    'ver'       => '',
    'media'     => ''
);    

$styles[] = array(
    'handle'    => 'mirror-css',
    'src'       => WP_CUSTOM_FIELDS_ASSETS_URL . 'css/vendor/codemirror.min.css',
    'deps'      => array(),
    'ver'       => '',
    'media'     => ''
); 

$styles[] = array(
    'handle'    => 'select2-css',
    'src'       => WP_CUSTOM_FIELDS_ASSETS_URL . 'css/vendor/select2.min.css',
    'deps'      => array(),
    'ver'       => '',
    'media'     => ''
); 

$styles[] = array(
    'handle'    => 'wp-custom-fields-css',
    'src'       => WP_CUSTOM_FIELDS_ASSETS_URL . 'css/wp-custom-fields.min.css',
    'deps'      => array(),
    'ver'       => null,
    'media'     => 'all'
);