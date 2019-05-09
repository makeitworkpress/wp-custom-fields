<?php
 /** 
  * Displays a divider
  */
namespace MakeitWorkPress\WP_Custom_Fields\Fields;
use MakeitWorkPress\WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    die;
}

class Divider implements Field {
    
    /**
     * Prepares the variables and renders the field
     * 
     * @param   array $field The array with field attributes data-alpha
     * @return  void
     */     
    public static function render( $field = [] ) { ?>
        <hr />    
    <?php }
    
    /**
     * Returns the global configurations for this field
     *
     * @return array $configurations The configurations
     */      
    public static function configurations() {
        
        $configurations = [
            'type'      => 'divider',
            'defaults'  => ''
        ];
            
        return apply_filters( 'wp_custom_fields_divider_config', $configurations );

    }
    
}