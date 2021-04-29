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
        $lat    = isset($field['values']['lat']) ? floatval($field['values']['lat']) : '';
        $lng    = isset($field['values']['lng']) ? floatval($field['values']['lng']) : '';
        $name   = esc_attr($field['name']);        
        
        // Retrieve scripts
        if( apply_filters('wp_custom_fields_location_field_js', true) && ! wp_script_is('google-maps-js', 'enqueued') ) {
            wp_enqueue_script('google-maps-js');
        } ?>
        
            <div class="wpcf-location">
                <div class="wpcf-location-search grid flex">
                    <input class="regular-text wpcf-half wpcf-map-search" type="search" placeholder="<?php echo $config['placeholders']['search']; ?>" />
                    <input class="regular-text wpcf-fourth latitude" id="<?php echo $id; ?>-lat" name="<?php echo $name; ?>[lat]" type="text" readonly="readonly" value="<?php echo $lat; ?>" />
                    <input class="regular-text wpcf-fourth longitude" id="<?php echo $id; ?>-long" name="<?php echo $name; ?>[lng]" type="text" readonly="readonly" value="<?php echo $lng; ?>" />
                </div>
                <div class="wpcf-map-canvas"></div>       
                <div class="wpcf-location-details grid flex">
                    <?php foreach( $config['labels'] as $key => $label ) { ?>
                        <?php $column = in_array($key, ['country', 'state']) ? 'half' : 'fourth'; ?>
                        <div class="wpcf-location-detail wpcf-<?php echo $column; ?>">
                            <label for="<?php echo $id . '-' . $key; ?>"><?php echo $label; ?></label>
                            <input type="text" class="regular-text <?php echo $key; ?>" id="<?php echo $id . '-' . $key; ?>" name="<?php echo $name .'[' . $key .']'; ?>" value="<?php if( isset($field['values'][$key]) ) { echo esc_attr($field['values'][$key]); } ?>" />
                        </div>
                    <?php } ?>
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
            'defaults'  => [
                'city'          => '',
                'country'       => '',
                'lat'           => '',
                'lng'           => '',
                'number'        => '',
                'postal_code'   => '',                
                'state'         => '',                
                'street'        => ''
            ],
            'labels' => [
                'street'        => __('Street Address', 'wp-custom-fields'),
                'number'        => __('Street Number', 'wp-custom-fields'),
                'postal_code'   => __('Postal Code', 'wp-custom-fields'),
                'city'          => __('City', 'wp-custom-fields'),
                'state'         => __('State', 'wp-custom-fields'),
                'country'       => __('Country', 'wp-custom-fields')
            ],
            'placeholders'      => [
                'search'        => __('Search for a location', 'wp-custom-fields')
            ],
            'type'              => 'location'
        ];
            
        return apply_filters( 'wp_custom_fields_location_config', $configurations );

    }
    
}