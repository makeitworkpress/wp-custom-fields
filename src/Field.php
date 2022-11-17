<?php 
/** 
 * Determines the implementation of setting input fields
 */
namespace MakeitWorkPress\WP_Custom_Fields;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    die; 
}
interface Field {
    
    public static function render( array $field = [] ): void;
    
    public static function configurations(): array;
    
}