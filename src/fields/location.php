<?php
 /** 
  * Displays a location field, including a google map
  */
namespace MakeitWorkPress\WP_Custom_Fields\Fields;
use MakeitWorkPress\WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined('ABSPATH') ) {
    die;
}

class Location implements Field {

    /**
     * Prepares the variables and renders the field
     * 
     * @param   array $field The array with field attributes data-alpha
     * @return  void
     */      
    public static function render( $field = [] ) {

        $config = self::configurations();
        $id     = esc_attr($field['id']);
        $lat    = floatval($field['values']['lat']);
        $lng    = floatval($field['values']['lng']);
        $name   = esc_attr($field['name']);        
        
        // Retrieve scripts
        if( apply_filters('wp_custom_fields_location_field_js', true) && ! wp_script_is('google-maps-js', 'enqueued') ) {
            wp_enqueue_script('google-maps-js');
        } ?>
        
            <div class="wpcf-location">
                <input class="regular-text wpcf-map-search" type="text" />
                <div class="wpcf-map-canvas"></div>        
                <input class="latitude" id="<?php echo $id; ?>-lat" name="<?php echo $name; ?>[lat]" type="hidden" value="<?php echo $lat; ?>" />
                <input class="longitude" id="<?php echo $id; ?>-long" name="<?php echo $name; ?>[lng]" type="hidden" value="<?php echo $lng; ?>'" />
            
                <?php foreach( $config['properties'] as $key => $label ) { ?>
                    <div class="wpcf-field-left">
                        <label for="<?php echo $id . '-' . $key; ?>"><?php echo $label; ?></label>
                        <input type="text" class="regular-text <?php echo $key; ?>'" id="<?php echo $id . '-' . $key; ?>" name="<?php echo $name .'[' . $key .']'; ?>" value="<?php echo esc_attr($field['values'][$key]); ?>" />
                    </div>
                <?php } ?>
            
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
            'defaults'  => [
                'city'          => '',
                'lat'           => '',
                'lng'           => '',
                'number'        => '',
                'postal_code'   => '',                
                'street'        => ''
            ],
            'properties' => [
                'street'        => __('Street Address', 'wp-custom-fields'),
                'number'        => __('Street Number', 'wp-custom-fields'),
                'postal_code'   => __('Postal Code', 'wp-custom-fields'),
                'city'          => __('City', 'wp-custom-fields')
            ],
            'type'      => 'location'
        ];
            
        return apply_filters( 'wp_custom_fields_location_config', $configurations );

    }
    
}