<?php 
/** 
 * Determines the implementation of setting input fields
 */
namespace WP_Custom_Fields;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) 
    die; 

interface Field {
    
    public static function render( $field = array() );
    
    public static function configurations();
    
}