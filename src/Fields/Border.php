<?php
 /** 
  * Displays a border field
  */
namespace MakeitWorkPress\WP_Custom_Fields\Fields;
use MakeitWorkPress\WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class Border implements Field {
 
    /**
     * Prepares the variables and renders the field
     * 
     * @param array $field The array with field attributes
     * @return void
     */    
    public static function render( array $field = [] ): void {
        
        // Basic Variables
        $borders = isset( $field['borders'] ) ? esc_attr($field['borders']) : '';
        $configurations = self::configurations();

        if( $borders == 'all' ) { 
            foreach( $configurations['properties']['sides'] as $key => $side ) { ?>
                <div class="wpcf-single-border">
                    <div class="wpcf-field-left">
                        <?php 
                            Dimension::render( [
                                'icon'          => 'border_' . $key,
                                'id'            => $field['id'] . '-' . $key . '-width',
                                'name'          => $field['name'] . '[' . $key . '][width]',
                                'placeholder'   => $side,
                                'values'        => isset($field['values'][$key]['width']) ? $field['values'][$key]['width'] : []                                   
                            ] ); 
                        ?>
                    </div>
                    <div class="wpcf-field-left">
                        <?php Select::render( [
                            'id'            => $field['id']  . '-' . $key. '-style',
                            'name'          => $field['name'] . '[' . $key . '][style]',
                            'options'       => $configurations['properties']['styles'],
                            'placeholder'   => isset($field['labels']['style']) ? $field['labels']['style'] : $configurations['labels']['style'],
                            'values'        => isset($field['values'][$key]['style']) ? $field['values'][$key]['style'] : ''
                        ] ); ?>
                    </div>
                    <div class="wpcf-field-left">
                        <?php Colorpicker::render( [
                            'id'        => $field['id'] . '-' . $key . '-color',
                            'name'      => $field['name']. '[' . $key . '][color]',
                            'values'    => isset($field['values'][$key]['color']) ? $field['values'][$key]['color'] : ''
                        ] ); ?>
                    </div>                    
                </div><!-- .wpcf-single-border -->
            <?php }
        } else { ?>
            <div class="wpcf-field-left">
                <?php Dimension::render( [
                    'icon'          => 'border_outer',
                    'id'            => $field['id'] . '-width',
                    'name'          => $field['name'] . '[width]',
                    'placeholder'   => isset($field['labels']['width']) ? $field['labels']['width'] : $configurations['labels']['width'],
                    'values'        => isset($field['values']['width']) ? $field['values']['width'] : [] 
                ] ); ?>
            </div>
            <div class="wpcf-field-left">
                <?php Select::render( [
                    'id'            => $field['id']  . '-style',
                    'name'          => $field['name'] . '[style]',
                    'options'       => $configurations['properties']['styles'],
                    'placeholder'   => isset($field['labels']['style']) ? $field['labels']['style'] : $configurations['labels']['style'],
                    'values'        => isset($field['values']['style']) ? $field['values']['style'] : ''
                ] ); ?>
            </div>
            <div class="wpcf-field-left">
                <?php Colorpicker::render( [
                    'id'        => $field['id'] . '-color',
                    'name'      => $field['name'] . '[color]',
                    'values'    => isset($field['values']['color']) ? $field['values']['color'] : ''
                ] ); ?>
            </div>              
        <?php }
  
    }

    /**
     * Returns the global configurations for this field
     *
     * @return array $configurations The configurations
     */    
    public static function configurations(): array {
        $configurations = [
            'type'          => 'border',
            'defaults'      => [],
            'labels'        => [
                'style' => __('Border Style', 'wpcf'),
                'width' => __('Border Width', 'wpcf')
            ],
            'properties'    => [
                'sides' => [
                    'top'       => __('Top Width', 'wpcf'), 
                    'right'     => __('Right Width', 'wpcf'), 
                    'bottom'    => __('Bottom Width', 'wpcf'), 
                    'left'      => __('Left Width', 'wpcf')                    
                ],
                'styles' => [
                    'solid'     => __('Solid', 'wpcf'), 
                    'dotted'    => __('Dotted', 'wpcf'),  
                    'dashed'    => __('Dashed', 'wpcf'),  
                    'double'    => __('Double', 'wpcf'),  
                    'groove'    => __('Groove', 'wpcf'),  
                    'ridge'     => __('Ridge', 'wpcf'), 
                    'inset'     => __('Inset', 'wpcf'),  
                    'outset'    => __('Outset', 'wpcf')
                ]
            ]
        ];
            
        return apply_filters( 'wp_custom_fields_border_config', $configurations );
    }
    
}