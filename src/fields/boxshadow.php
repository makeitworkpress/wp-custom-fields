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

            <div class="wpcf-boxshadow">
                <div class="wpcf-boxshadow-dimensions wpcf-field-left">
                    <label><?php echo isset($field['labels']['dimensions']) ? $field['labels']['dimensions'] : $configurations['labels']['dimensions']; ?></label>
                        <?php 
                            
                            $pixels = isset($field['pixels']) && $field['pixels'] ? $field['pixels'] : $configurations['properties']['pixels'];
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
                <div class="wpcf-boxshadow-color wpcf-field-left">
                    <label><?php echo isset($field['labels']['color']) ? $field['labels']['color'] : $configurations['labels']['color']; ?></label>
                    <?php 
                        Colorpicker::render( [
                            'id'     => $field['id'] . '-color',   
                            'name'   => $field['name'] . '[color]',
                            'values' => isset($field['values']['color']) ? $field['values']['color'] : ''     
                        ] );
                    ?>
                </div>
                <div class="wpcf-boxshadow-type wpcf-field-left">
                    <label><?php echo isset($field['labels']['type']) ? $field['labels']['type'] : $configurations['labels']['type']; ?></label>
                    <?php
                        Select::render( [
                            'id'            => $field['id']  . '-type',
                            'name'          => $field['name']. '[type]',
                            'options'       => isset($field['types']) && $field['types'] ? $field['types'] : $configurations['properties']['types'],             
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
                'color'         => __('Boxshadow Color', 'wpcf'),
                'dimensions'    => __('Boxshadow X-Offset, Y-Offset, Blur and Spread', 'wpcf'), 
                'placeholder'   => __('Select Type', 'wpcf'),
                'type'          => __('Boxshadow Type', 'wpcf'),
            ],
            'properties'    => [
                'pixels'    => [
                    'x'         => __('x-offset', 'wpcf'),
                    'y'         => __('y-offset', 'wpcf'),
                    'blur'      => __('blur', 'wpcf'),
                    'spread'    => __('spread', 'wpcf')
                ],
                'types'     => [ '' => __('Default', 'wpcf'), 'inset' => __('Inset', 'wpcf') ],              
            ]
        ];
            
        return apply_filters( 'wp_custom_fields_boxshadow_config', $configurations );
        
    }
    
}