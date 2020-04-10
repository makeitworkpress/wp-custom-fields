<?php
 /** 
  * Displays a colorpicker input field
  */
namespace MakeitWorkPress\WP_Custom_Fields\Fields;
use MakeitWorkPress\WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined('ABSPATH') ) {
    die;
}

class Colorpicker implements Field {
    
    /**
     * Prepares the variables and renders the field
     * 
     * @param   array $field The array with field attributes data-alpha
     * @return  void
     */     
    public static function render( $field = [] ) {
        
        $alpha  = isset($field['alpha']) ? esc_attr($field['alpha']) : 'true';
        $id     = esc_attr($field['id']);
        $name   = esc_attr($field['name']);
        $value  = esc_attr($field['values']); ?>
        
            <div class="wpcf-colorpicker-wrapper">
                <input id="<?php echo $id; ?>" class="wpcf-colorpicker color-picker" name="<?php echo $name; ?>" type="text" value="<?php echo $value; ?>" data-alpha="<?php echo $alpha; ?>" /> 
            </div> 
            
        <?php
    }
    
    /**
     * Returns the global configurations for this field
     *
     * @return array $configurations The configurations
     */    
    public static function configurations() {
        
        $configurations = [
            'type'      => 'colorpicker',
            'defaults'  => ''
        ];
            
        return apply_filters( 'wp_custom_fields_colorpicker_config', $configurations );

    }
    
}