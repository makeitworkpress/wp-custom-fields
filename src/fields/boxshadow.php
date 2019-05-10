<?php
 /** 
  * Displays a location field, including a google map
  */
namespace MakeitWorkPress\WP_Custom_Fields\Fields;
use MakeitWorkPress\WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class Boxshadow implements Field {
    
    public static function render( $field = [] ) {

        $configurations = self::configurations(); ?>

            <div class="wp-custom-fields-boxshadow">
                <div class="wp-custom-fields-boxshadow-dimensions wp-custom-fields-field-left">
                    <label><?php echo isset($field['labels']['dimensions']) ? $field['labels']['dimensions'] : $configurations['labels']['dimensions']; ?></label>
                        <?php 
                            foreach( $configurations['properties']['pixels'] as $key => $label ) {
                                $id     = esc_attr($field['id'] . '-' . $key);
                                $name   = esc_attr($field['name']  . '['.$key.']');
                                $value  = isset($field['values'][$key]) && $field['values'][$key] ? intval($field['values'][$key]) : '';
                        ?>
                            <input id="<?php echo $id; ?>'" name="<?php echo $name; ?>" type="number" placeholder="<?php echo $label; ?>" value="<?php echo $value; ?>" />
                        <?php 
                            } 
                        ?>
                </div>
                <div class="wp-custom-fields-boxshadow-color wp-custom-fields-field-left">
                    <label><?php echo isset($field['labels']['color']) ? $field['labels']['color'] : $configurations['labels']['color']; ?></label>
                    <?php 
                        Colorpicker::render( [
                            'id'     => $field['id'] . '-color',   
                            'name'   => $field['name'] . '[color]',
                            'values' => isset($field['values']['color']) ? $field['values']['color'] : ''     
                        ] );
                    ?>
                </div>
                <div class="wp-custom-fields-boxshadow-type wp-custom-fields-field-left">
                    <label><?php echo isset($field['labels']['type']) ? $field['labels']['type'] : $configurations['labels']['type']; ?></label>
                    <?php
                        Select::render( [
                            'id'            => $field['id']  . '-type',
                            'name'          => $field['name']. '[type]',
                            'options'       => ['' => __('Default', 'wp-custom-fields'), 'inset' => __('Inset', 'wp-custom-fields')],             
                            'placeholder'   => isset($field['labels']['placeholder']) ? $field['labels']['placeholder'] : $configurations['labels']['placeholder'],         
                            'values'        => isset($field['values']['type']) ? $field['values']['type'] : ''
                        ] );
                    ?>
                </div>
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
            'type'      => 'boxshadow',
            'defaults'  => [],
            'labels'    => [
                'color'         => __('Boxshadow Color', 'wp-custom-fields'),
                'dimensions'    => __('Boxshadow X-Offset, Y-Offset, Blur and Spread', 'wp-custom-fields'), 
                'placeholder'   => __('Select Type', 'wp-custom-fields'),
                'type'          => __('Boxshadow Type', 'wp-custom-fields'),
            ],
            'properties'    => [
                'pixels'    => [
                    'x'         => __('x-offset', 'wp-custom-fields'),
                    'y'         => __('y-offset', 'wp-custom-fields'),
                    'blur'      => __('blur', 'wp-custom-fields'),
                    'spread'    => __('spread', 'wp-custom-fields')
                ],
                'types'     => [ '' => __('Default', 'wp-custom-fields'), 'inset' => __('Inset', 'wp-custom-fields') ],              
            ]
        ];
            
        return apply_filters( 'wp_custom_fields_boxshadow_config', $configurations );
        
    }
    
}