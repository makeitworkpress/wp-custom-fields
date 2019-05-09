<?php 
/** 
 * Determines the implementation of setting input fields
 */
namespace MakeitWorkPress\WP_Custom_Fields;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) 
    die; 

interface Field {
    
    public static function render( $field = [] );
    
    public static function configurations();
    
}