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

class Html implements Field {
    
    /**
     * Prepares the variables and renders the field
     * 
     * @param   array $field The array with field attributes data-alpha
     * @return  void
     */    
    public static function render( $field = [] ) {

        global $allowedposttags;

        if( isset($field['html']) ) { ?> 
            <p><?php echo wp_kses($field['html'], $allowedposttags); ?></p>
        <?php }

    }

    /**
     * Returns the global configurations for this field
     *
     * @return array $configurations The configurations
     */      
    public static function configurations() {

        // var_dump( wp_kses_allowed_html() );

        $configurations = [
            'type'          => 'html',
            'defaults'      => ''
        ];
            
        return apply_filters( 'wp_custom_fields_html_config', $configurations );

    }
    
}