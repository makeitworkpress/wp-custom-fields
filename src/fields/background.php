<?php
 /** 
  * Displays a background input field
  */
namespace MakeitWorkPress\WP_Custom_Fields\Fields;
use MakeitWorkPress\WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    die;
}

class Background implements Field {
 
    /**
     * Prepares the variables and renders the field
     * 
     * @param array $field The array with field attributes
     * @return void
     */
    public static function render( $field = [] ) {
             
        // Properties
        $configurations = self::configurations(); ?>
        
        <div class="wpcf-background-image wpcf-field-left">
            <?php 
                Media::render( [
                    'subtype'   => 'image',
                    'add'       => isset($field['labels']['add']) ? $field['labels']['add'] : $configurations['labels']['add'],
                    'button'    => isset($field['labels']['button']) ? $field['labels']['button'] : $configurations['labels']['button'],
                    'id'        => $field['id'] . '-upload',
                    'multiple'  => false,
                    'name'      => $field['name'] . '[upload]',
                    'title'     => isset($field['labels']['title']) ? $field['labels']['title'] : $configurations['labels']['title'],
                    'values'    => isset($field['values']['upload']) ? $field['values']['upload'] : ''
                ] ); 
            ?>       
        </div>

        
        <div class="wpcf-background-attributes wpcf-field-left">        
            <?php 
                Colorpicker::render( [
                    'values'    => isset( $field['values']['color'] ) ? $field['values']['color'] : '', 
                    'name'      => $field['name'] . '[color]', 
                    'id'        => $field['id'] . '-color'
                ] ); 
            ?>
             
            <?php foreach($configurations['properties'] as $key => $attribute) { 
                // We use the select field class to display our recurring select fields.
                Select::render( [
                    'options'       => $attribute['options'],
                    'placeholder'   => $attribute['placeholder'],
                    'id'            => $field['id']  . '-' . $key,
                    'name'          => $field['name']. '[' . $key . ']',
                    'values'        => isset($field['values'][$key]) ? $field['values'][$key] : ''
                ] );
            } ?>
        
        </div>
  
    <?php }
    
    /**
     * Returns the global configurations for this field
     *
     * @return array $configurations The configurations
     */ 
    public static function configurations() {
        $configurations = [
            'type'          => 'background',
            // Default values
            'defaults'      => [],
            // Default labels
            'labels'        => [
                'add'           => __('Select', 'wp-custom-fields'),
                'button'        => __('Add Background', 'wp-custom-fields'),
                'placeholder'   => __('— Select —', 'wp-custom-fields'),
                'title'         => __('Select a Background', 'wp-custom-fields')
            ],
            // Properties
            'properties'    => [
                'repeat' => [
                    'placeholder'   => __('Background Repeat', 'wp-custom-fields'),
                    'options'       => [
                        'no-repeat' => __('No Repeat', 'wp-custom-fields'),
                        'repeat'    => __('Repeat', 'wp-custom-fields'),
                        'repeat-x'  => __('Repeat Horizontally', 'wp-custom-fields'),
                        'repeat-y'  => __('Repeat Vertically', 'wp-custom-fields'),
                        'inherit'   => __('Inherit', 'wp-custom-fields')
                    ]
                ],
                'attachment' => [
                    'placeholder'   => __('Background Attachment', 'wp-custom-fields'),
                    'options'       => [
                        'fixed'     => __('Fixed', 'wp-custom-fields'),
                        'scroll'    => __('Scroll', 'wp-custom-fields'),
                        'inherit'   => __('Inherit', 'wp-custom-fields') 
                    ]
                ],
                'size' => [
                    'placeholder'   => __('Background Size', 'wp-custom-fields'),
                    'options'       => [
                        'cover'     => __('Cover', 'wp-custom-fields'),
                        'contain'   => __('Contain', 'wp-custom-fields'),
                        '100%'      => __('100%', 'wp-custom-fields'),
                        'inherit'   => __('Inherit', 'wp-custom-fields')
                    ]
                ],            
                'position' => [
                    'placeholder'   => __('Background Position', 'wp-custom-fields'),
                    'options'       => [
                        'center top' => __('Center Top', 'wp-custom-fields'),
                        'center center' => __('Center Center', 'wp-custom-fields'),
                        'center bottom' => __('Center Bottom', 'wp-custom-fields'),
                        'left top' => __('Left Top', 'wp-custom-fields'),
                        'left center' => __('Left Center', 'wp-custom-fields'),   
                        'left bottom' => __('Left Bottom', 'wp-custom-fields'), 
                        'right top' => __('Right Top', 'wp-custom-fields'), 
                        'right center' => __('Right Center', 'wp-custom-fields'), 
                        'right bottom' => __('Right Bottom', 'wp-custom-fields')
                    ]
                ]
            ],
            'settings' => [
                '[color]', 
                '[upload]', 
                '[repeat]', 
                '[attachment]', 
                '[size]',
                '[position]'
            ]            
        ];
            
        return apply_filters( 'wp_custom_fields_background_config', $configurations );
        
    }
    
}