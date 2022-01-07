<?php
 /** 
  * The heading display is determined in the class-views-fields.php file. Hence, this file is empty.
  */
namespace MakeitWorkPress\WP_Custom_Fields\Fields;
use MakeitWorkPress\WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined('ABSPATH') ) {
    die;
}

class Heading implements Field {
    
    /**
     * Prepares the variables and renders the field
     * 
     * @param   array $field The array with field attributes data-alpha
     * @return  void
     */    
    public static function render( array $field = [] ): void {
        
        if( isset($field['subtitle']) ) { ?> 
            <p><?php echo esc_textarea($field['subtitle']); ?></p>
        <?php }

    }

    /**
     * Returns the global configurations for this field
     *
     * @return array $configurations The configurations
     */      
    public static function configurations(): array {

        $configurations = [
            'type'      => 'heading',
            'defaults'  => ''
        ];
            
        return apply_filters( 'wp_custom_fields_heading_config', $configurations );

    }
    
}