<?php 
/** 
 * Determines the implementation of setting input fields
 */
namespace Classes\Divergent\Fields;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) 
    die; 

interface Divergent_Field {
    
    public static function render( $field = array() );
    
    public static function configurations();
    
}