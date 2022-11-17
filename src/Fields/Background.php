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
    public static function render( array $field = [] ): void {
             
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
             
            <?php 
                $properties = isset($field['properties']) ? $field['properties'] : $configurations['properties'];
                foreach($properties as $key => $attribute) { 
                    // We use the select field class to display our recurring select fields.
                    Select::render( [
                        'options'       => $attribute['options'],
                        'placeholder'   => $attribute['placeholder'],
                        'id'            => $field['id']  . '-' . $key,
                        'name'          => $field['name']. '[' . $key . ']',
                        'values'        => isset($field['values'][$key]) ? $field['values'][$key] : ''
                    ] );
                } 
            ?>
        
        </div>
  
    <?php }
    
    /**
     * Returns the global configurations for this field
     *
     * @return array $configurations The configurations
     */ 
    public static function configurations(): array {
        $configurations = [
            'type'          => 'background',
            // Default values
            'defaults'      => [],
            // Default labels
            'labels'        => [
                'add'           => __('Select', 'wpcf'),
                'button'        => __('Add Background', 'wpcf'),
                'placeholder'   => __('— Select —', 'wpcf'),
                'title'         => __('Select a Background', 'wpcf')
            ],
            // Properties
            'properties'    => [
                'repeat' => [
                    'placeholder'   => __('Background Repeat', 'wpcf'),
                    'options'       => [
                        'no-repeat' => __('No Repeat', 'wpcf'),
                        'repeat'    => __('Repeat', 'wpcf'),
                        'repeat-x'  => __('Repeat Horizontally', 'wpcf'),
                        'repeat-y'  => __('Repeat Vertically', 'wpcf'),
                        'inherit'   => __('Inherit', 'wpcf')
                    ]
                ],
                'attachment' => [
                    'placeholder'   => __('Background Attachment', 'wpcf'),
                    'options'       => [
                        'fixed'     => __('Fixed', 'wpcf'),
                        'scroll'    => __('Scroll', 'wpcf'),
                        'inherit'   => __('Inherit', 'wpcf') 
                    ]
                ],
                'size' => [
                    'placeholder'   => __('Background Size', 'wpcf'),
                    'options'       => [
                        'cover'     => __('Cover', 'wpcf'),
                        'contain'   => __('Contain', 'wpcf'),
                        '100%'      => __('100%', 'wpcf'),
                        'inherit'   => __('Inherit', 'wpcf')
                    ]
                ],            
                'position' => [
                    'placeholder'   => __('Background Position', 'wpcf'),
                    'options'       => [
                        'center top' => __('Center Top', 'wpcf'),
                        'center center' => __('Center Center', 'wpcf'),
                        'center bottom' => __('Center Bottom', 'wpcf'),
                        'left top' => __('Left Top', 'wpcf'),
                        'left center' => __('Left Center', 'wpcf'),   
                        'left bottom' => __('Left Bottom', 'wpcf'), 
                        'right top' => __('Right Top', 'wpcf'), 
                        'right center' => __('Right Center', 'wpcf'), 
                        'right bottom' => __('Right Bottom', 'wpcf')
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