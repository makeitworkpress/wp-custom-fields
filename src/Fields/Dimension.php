<?php
 /** 
  * Displays a single dimension field
  */
namespace MakeitWorkPress\WP_Custom_Fields\Fields;
use MakeitWorkPress\WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die; 

class Dimension implements Field {
    
    /**
     * Prepares the variables and renders the field
     * 
     * @param   array $field The array with field attributes data-alpha
     * @return  void
     */  
    public static function render( array $field = [] ): void {
        
        $configurations = self::configurations();
        
        $amount         = isset($field['values']['amount']) && $field['values']['amount'] ? floatval($field['values']['amount']) : '';
        $icon           = isset( $field['icon'] ) ? esc_html($field['icon']) : false;
        $id             = esc_attr($field['id']);     
        $label          = isset( $field['label'] ) ? esc_html($field['label']) : false;
        $measure        = isset($field['values']['unit']) ? esc_attr($field['values']['unit']) : '';  
        $name           = esc_attr($field['name']);
        $step           = isset($field['step']) ? floatval($field['step']) : 1;
        $placeholder    = ! empty($field['placeholder']) ? ' placeholder="' . esc_attr($field['placeholder']) . '"' : '';        
        $measurements   = isset($field['units']) && is_array($field['units']) ? $field['units'] : $configurations['properties']['units']; ?>

            <div class="wpcf-dimensions-input">
                <?php if( $label ) { ?><label for="<?php echo $id; ?>"><?php echo $label; ?></label><?php } ?>
                <?php if( $icon ) { ?><i class="material-icons"><?php echo $icon; ?></i><?php } ?>
                <input id="<?php echo $id; ?>" type="number" name="<?php echo $name; ?>[amount]" value="<?php echo $amount; ?>" step="<?php echo $step; ?>" <?php echo $placeholder; ?> />
                <select name="<?php echo $name; ?>[unit]">
                    <?php foreach( $measurements as $measurement ) { ?>
                        <option value="<?php echo $measurement; ?>" selected($measurement, $measure)><?php echo $measurement; ?></option>
                    <?php } ?>               
                </select>
            </div> 
       
        <?php                
        
    }
    
    /**
     * Returns the global configurations for this field
     *
     * @return array $configurations The configurations
     */    
    public static function configurations(): array {
        $configurations = [
            'type'          => 'dimension',
            'defaults'      => [],
            'properties'    => [
                'units' => ['', 'px', 'em', '%', 'rem', 'vh', 'vw']
            ],
            'settings'  => [
                '[amount]',
                '[unit]'
            ]
        ];
            
        return apply_filters( 'wp_custom_fields_dimension_config', $configurations );
        
    }    
    
}