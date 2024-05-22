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
     * @param array $field The array with field attributes data-alpha
     * @return void
     */
    public static function render( array $field = [] ): void {
        
        $id     = esc_attr($field['id']);
        $name   = esc_attr($field['name']);
        $min    = isset($field['min']) ? intval($field['min']) : 0;
        $max    = isset($field['max']) ? intval($field['max']) : 10;
        $step   = isset($field['step']) ? floatval($field['step']) : 1;
        $value  = $field['values'] ? floatval($field['values']) : 0; ?>

        <input type="range" id="<?php echo $id; ?>" name="<?php echo $name; ?>" min="<?php echo $min; ?>" max="<?php echo $max; ?>" value="<?php echo $value; ?>" step="<?php echo $step; ?>" class="wpcf-slider-input">
        <span class="wpcf-slider-value"><?php echo $value; ?></span>
        
        <?php
         
    }
    
    /**
     * Returns the global configurations for this field
     *
     * @return array $configurations The configurations
     */
    public static function configurations(): array {
        $configurations = [
            'type'      => 'slider',
            'defaults'  => ''
        ];
            
        return apply_filters( 'wp_custom_fields_slider_config', $configurations );
    }
    
}
