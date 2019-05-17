<?php
 /** 
  * Displays a repeatable field group
  */
namespace MakeitWorkPress\WP_Custom_Fields\Fields;
use MakeitWorkPress\WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined('ABSPATH') ) {
    die; 
}

class Repeatable implements Field {
    

    /**
     * Prepares the variables and renders the field
     * 
     * @param   array $field The array with field attributes data-alpha
     * @return  void
     */        
    public static function render( $field = [] ) {

        $configurations     = self::configurations();
        $add                = isset($field['labels']['add'])      ? esc_html($field['labels']['add'])     : $configurations['labels']['add'];
        $remove             = isset($field['labels']['remove'])   ? esc_html($field['labels']['remove'])  : $configurations['labels']['remove'];
        $display            = isset($field['closed']) && $field['closed']   ? ' hidden'         : '';
        
        // Prepare the array with data
        if( empty($field['values']) ) {
            $groups[0] = $field['fields'];
        } elseif( ! empty($field['values']) ) {
            
            // The values determine our groups
            foreach( $field['values'] as $key => $groupValues ) {
  
                // Link our fields to the values
                foreach($field['fields'] as $subkey => $subfield) {

                    $groups[$key][$subfield['id']]           = $subfield;    
                    $groups[$key][$subfield['id']]['values'] = $groupValues[$subfield['id']];    

                }

            }
            
        } ?>
        
            <div class="wp-custom-fields-repeatable-container">
        
                <?php foreach( $groups as $key => $fields) { ?>

                    <div class="wp-custom-fields-repeatable-group">
                        <a class="wp-custom-fields-repeatable-toggle" href="#"><i class="material-icons">arrow_drop_down</i></a>
                        <div class="wp-custom-fields-repeatable-fields grid flex <?php echo $display; ?>">

                            <?php 
                                // Loop through each of the saved fields
                                foreach($fields as $subkey => $subfield) {

                                    // The type should be defined
                                    if( ! isset($subfield['type']) ) {
                                        continue;
                                    }

                                    // The ID should be defined
                                    if( ! isset($subfield['id']) ) {
                                        continue;
                                    }                

                                    // Render each field based upon the values
                                    $subfield['columns']  = isset($subfield['columns']) ? 'wcf-' . $subfield['columns'] : 'wcf-full';
                                    $subfield['values']   = isset($subfield['values']) ? $subfield['values'] : '';
                                    $subfield['name']     = $field['name'] . '[' . $key . ']' . '[' . $subfield['id'] . ']';
                                    $subfield['id']       = $field['id'] . '_' . $key  . '_' . $subfield['id'];
                                
                                    $class                = 'MakeitWorkPress\WP_Custom_Fields\Fields\\' . ucfirst( $subfield['type'] );
                                
                                
                                if( class_exists($class) ) { ?>
                                    <div class="wp-custom-fields-repeatable-field wp-custom-fields-option-field field-<?php echo esc_attr($subfield['type'] . ' ' . $subfield['columns']); ?>">
                                        <h5><?php esc_html_e($subfield['title']); ?></h5>
                                        <?php $class::render($subfield); ?>
                                        <?php if( isset($subfield['description']) ) {  ?>
                                            <div class="wp-custom-fields-field-description"><p><?php echo esc_textarea($subfield['description']); ?></p></div>
                                        <?php } ?>
                                    </div>
                                <?php }

                            } ?>
        
                        </div><!-- .wp-custom-fields-repeatable-fields -->
                    </div><!-- .wp-custom-fields-repeatable-group -->

                <?php } ?>           
        
                <div class="wp-custom-fields-repeatable-buttons">';
                    <a href="#" class="button wp-custom-fields-repeatable-remove" title="<?php _e('Remove', 'wp-custom-fields'); ?>"><?php echo $remove; ?></a>
                    <a href="#" class="button wp-custom-fields-repeatable-add button-primary" title="<?php _e('Add', 'wp-custom-fields'); ?>"><?php echo $add; ?></a>
                </div>

            </div><!-- .wp-custom-fields-repeatable-container -->
        
        <?php
  
    }
    
    /**
     * Returns the global configurations for this field
     *
     * @return array $configurations The configurations
     */   
    public static function configurations() {
                
        $configurations = [
            'type'          => 'repeatable',
            'defaults'      => [],
            'labels'        => [
                'add'       => '<i class="dashicons dashicons-plus"></i>',
                'remove'    => '<i class="dashicons dashicons-minus"></i>'
            ],            
        ];
            
        return apply_filters( 'wp_custom_fields_repeatable_config', $configurations );

    }
    
}