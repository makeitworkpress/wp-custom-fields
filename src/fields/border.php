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
    public static function render( $field = [] ) {
        
        // Basic Variables
        $border = isset( $field['borders'] ) ? esc_attr($field['borders']) : '';
        $configurations = self::configurations();

        if( $border == 'all' ) { 
            foreach( $configurations['properties']['sides'] as $key => $side ) { ?>
                <div class="wp-custom-fields-single-border">
                    <div class="wp-custom-fields-field-left">
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
                    <div class="wp-custom-fields-field-left">
                        <?php Select::render( [
                            'id'            => $field['id']  . '-' . $key. '-style',
                            'name'          => $field['name'] . '[' . $key . '][style]',
                            'options'       => $configurations['properties']['styles'],
                            'placeholder'   => isset($field['labels']['style']) ? $field['labels']['style'] : $configurations['labels']['style'],
                            'values'        => isset($field['values'][$key]['style']) ? $field['values'][$key]['style'] : ''
                        ] ); ?>
                    </div>
                    <div class="wp-custom-fields-field-left">
                        <?php Colorpicker::render( [
                            'id'        => $field['id'] . '-' . $key . '-color',
                            'name'      => $field['name']. '[' . $key . '][color]',
                            'values'    => isset($field['values'][$key]['color']) ? $field['values'][$key]['color'] : ''
                        ] ); ?>
                    </div>                    
                </div><!-- .wp-custom-fields-single-border -->
            <?php }
        } else { ?>
            <div class="wp-custom-fields-field-left">
                <?php Dimension::render( [
                    'icon'          => 'border_outer',
                    'id'            => $field['id'] . '-width',
                    'name'          => $field['name'] . '[width]',
                    'placeholder'   => isset($field['labels']['width']) ? $field['labels']['width'] : $configurations['labels']['width'],
                    'values'        => isset($field['values']['width']) ? $field['values']['width'] : [] 
                ] ); ?>
            </div>
            <div class="wp-custom-fields-field-left">
                <?php Select::render( [
                    'id'            => $field['id']  . '-style',
                    'name'          => $field['name'] . '[style]',
                    'options'       => $configurations['properties']['styles'],
                    'placeholder'   => isset($field['labels']['style']) ? $field['labels']['style'] : $configurations['labels']['style'],
                    'values'        => isset($field['values']['style']) ? $field['values']['style'] : ''
                ] ); ?>
            </div>
            <div class="wp-custom-fields-field-left">
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
    public static function configurations() {
        $configurations = [
            'type'          => 'border',
            'defaults'      => [],
            'labels'        => [
                'style' => __('Border Style', 'wp-custom-fields'),
                'width' => __('Border Width', 'wp-custom-fields')
            ],
            'properties'    => [
                'sides' => [
                    'top'       => __('Top Width', 'wp-custom-fields'), 
                    'right'     => __('Right Width', 'wp-custom-fields'), 
                    'bottom'    => __('Bottom Width', 'wp-custom-fields'), 
                    'left'      => __('Left Width', 'wp-custom-fields')                    
                ],
                'styles' => [
                    'solid'     => __('Solid', 'wp-custom-fields'), 
                    'dotted'    => __('Dotted', 'wp-custom-fields'),  
                    'dashed'    => __('Dashed', 'wp-custom-fields'),  
                    'double'    => __('Double', 'wp-custom-fields'),  
                    'groove'    => __('Groove', 'wp-custom-fields'),  
                    'ridge'     => __('Ridge', 'wp-custom-fields'), 
                    'inset'     => __('Inset', 'wp-custom-fields'),  
                    'outset'    => __('Outset', 'wp-custom-fields')
                ]
            ]
        ];
            
        return apply_filters( 'wp_custom_fields_border_config', $configurations );
    }
    
}