<?php
 /** 
  * Displays a dimensions field
  */
namespace MakeitWorkPress\WP_Custom_Fields\Fields;
use MakeitWorkPress\WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    die; 
}

class Dimensions implements Field {
    
    /**
     * Prepares the variables and renders the field
     * 
     * @param   array $field The array with field attributes data-alpha
     * @return  void
     */     
    public static function render( array $field = [] ): void {
        
        // Basic Variables
        $configurations = self::configurations();
        $borders        = isset( $field['borders'] ) ? esc_attr($field['borders']) : '';
        $step           = isset( $field['step'] ) ? floatval($field['step']) : 1;
        $units          = isset( $field['units'] ) && is_array($field['units']) ? $field['units'] : false;
        
        // Control each side of the box model
        if( $borders == 'all' ) {
            
            foreach( $configurations['properties']['sides'] as $key => $side ) { ?>
             
                <div class="wpcf-field-left">
                    <?php 
                        Dimension::render( array(
                            'step'          => $step,
                            'icon'          => 'border_' . $key,
                            'id'            => $field['id'] . '-' . $key,
                            'name'          => $field['name'] . '[' . $key . ']',
                            'placeholder'   => $side,
                            'units'         => $units,
                            'values'        => isset($field['values'][$key]) ? $field['values'][$key] : []               
                        ) ); 
                    ?>
                </div>
                
            <?php }
            
        // One control
        } else {
            
            Dimension::render( [
                'step'      => $step,
                'icon'      => 'border_outer',
                'id'        => $field['id'],
                'name'      => $field['name'],
                'units'     => $units,
                'values'    => isset($field['values']) ? $field['values'] : []
             ] );        

        }
   
    }
   
    /**
     * Returns the global configurations for this field
     *
     * @return array $configurations The configurations
     */      
    public static function configurations(): array {
        
        $configurations = [
            'type'          => 'dimensions',
            'defaults'      => [],
            'properties'    => [
                'sides' => [
                    'top'       => __('Top', 'wpcf'), 
                    'right'     => __('Right', 'wpcf'), 
                    'bottom'    => __('Bottom', 'wpcf'), 
                    'left'      => __('Left', 'wpcf')      
                ]
            ]
        ];
            
        return apply_filters( 'wp_custom_fields_dimensions_config', $configurations );

    }
    
}