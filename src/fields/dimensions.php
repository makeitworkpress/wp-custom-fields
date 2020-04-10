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
    public static function render( $field = [] ) {
        
        // Basic Variables
        $configurations = self::configurations();
        $border         = isset( $field['borders'] ) ? esc_attr($field['borders']) : '';
        $step           = isset( $field['step'] ) ? floatval($field['step']) : 1;
        $units          = isset( $field['units'] ) && is_array($field['units']) ? $field['units'] : false;
        
        // Control each side of the box model
        if( $border == 'all' ) {
            
            foreach( $sides as $key => $side ) { ?>
             
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
    public static function configurations() {
        
        $configurations = [
            'type'          => 'dimensions',
            'defaults'      => [],
            'properties'    => [
                'sides' => [

                ]
            ]
        ];
            
        return apply_filters( 'wp_custom_fields_dimensions_config', $configurations );

    }
    
}