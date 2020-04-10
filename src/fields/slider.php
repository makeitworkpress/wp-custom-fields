<?php
 /** 
  * Displays a text input field
  */
namespace MakeitWorkPress\WP_Custom_Fields\Fields;
use MakeitWorkPress\WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined('ABSPATH') ) {
    die;
}

class Slider implements Field {
    
    /**
     * Prepares the variables and renders the field
     * 
     * @param   array $field The array with field attributes data-alpha
     * @return  void
     */      
    public static function render( $field = [] ) {
        
        $id     = esc_attr($field['id']);
        $name   = esc_attr($field['name']);        
        $min    = isset($field['min']) ? intval($field['min']) : 0;
        $max    = isset($field['max']) ? intval($field['max']) : 10;
        $step   = isset($field['step']) ? floatval($field['step']) : 1;
        $value  = $field['values'] ? floatval($field['values']) : 0; ?>
        
            <div class="wpcf-slider-wrapper">
                <div class="wpcf-slider" data-id="<?php echo $id; ?>" data-value="<?php echo $value; ?>" data-min="<?php echo $min; ?>" data-max="<?php echo $max; ?>" data-step="<?php echo $step; ?>"></div>
                <input class="wpcf-slider-value small-text" type="number" readonly="readonly" id="<?php echo $id; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>" /> 
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
            'type'      => 'slider',
            'defaults'  => ''
        ];
            
        return apply_filters( 'wp_custom_fields_slider_config', $configurations );
    }
    
}