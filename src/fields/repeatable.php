<?php
 /** 
  * Displays a repeatable field group
  */
namespace MakeitWorkPress\WP_Custom_Fields\Fields;
use MakeitWorkPress\WP_Custom_Fields\Field as Field;
use MakeitWorkPress\WP_Custom_Fields\Framework as Framework;

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
                    $groups[$key][$subfield['id']]['values'] = isset($groupValues[$subfield['id']]) ? $groupValues[$subfield['id']] : '';

                }

            }
            
        } ?>
        
            <div class="wpcf-repeatable-container">

                <div class="wpcf-repeatable-groups">
        
                    <?php foreach( $groups as $key => $fields) { ?>

                        <div class="wpcf-repeatable-group">
                            <a class="wpcf-repeatable-toggle" href="#"><i class="material-icons">arrow_drop_down</i></a>
                            <a class="wpcf-repeatable-remove-group" href="#"><i class="material-icons">clear</i></a>
                            <div class="wpcf-repeatable-fields grid flex <?php echo $display; ?>">

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
                                        $subfield['classes']        = isset($subfield['columns']) ? ' wpcf-' . esc_attr($subfield['columns']) : 'wpcf-full';
                                        $subfield['classes']       .= ' field-' . esc_attr($subfield['type']) . ' field-id-' . $subfield['id'];
                                        $subfield['values']         = isset($subfield['values']) ? $subfield['values'] : '';
                                        $subfield['name']           = $field['name'] . '[' . $key . ']' . '[' . esc_attr($subfield['id']) . ']';
                                        $subfield['id']             = $field['id'] . '_' . $key  . '_' . esc_attr($subfield['id']);
                                        $subfield['dependency']     = isset($subfield['dependency']) ? $subfield['dependency'] : [];

                                        // Additional classes
                                        if( $subfield['dependency'] ) {
                                            $subfield['classes']   .= ' wpcf-dependent-field' . Framework::returnDependencyClass($subfield['dependency'], [['fields' => $fields]], $field['values'][$key]);   
                                        }

                                        // If our dependency is fullfilled, the active class should be added
                                    
                                        $class                = 'MakeitWorkPress\WP_Custom_Fields\Fields\\' . ucfirst( $subfield['type'] );
                                    
                                    
                                    if( class_exists($class) ) { ?>
                                        <div class="wpcf-repeatable-field wpcf-field <?php echo $subfield['classes']; ?>">
                                            <h5><?php echo esc_html($subfield['title']); ?></h5>
                                            <div class="wpcf-repeatable-field-input" <?php foreach($subfield['dependency'] as $k => $v) { ?> data-<?php echo $k; ?>="<?php echo $v; ?>" <?php } ?>>
                                                <?php $class::render($subfield); ?>
                                            </div>
                                            <?php if( isset($subfield['description']) ) {  ?>
                                                <div class="wpcf-field-description"><p><?php echo esc_textarea($subfield['description']); ?></p></div>
                                            <?php } ?>
                                        </div>
                                    <?php }

                                } ?>
            
                            </div><!-- .wpcf-repeatable-fields -->
                        </div><!-- .wpcf-repeatable-group -->

                    <?php } ?>

                </div><!-- .wpcf-repeatable-groups -->           
        
                <div class="wpcf-repeatable-buttons">
                    <a href="#" class="button wpcf-repeatable-remove-latest" title="<?php echo $configurations['labels']['remove_title']; ?>"><?php echo $remove; ?></a>
                    <a href="#" class="button wpcf-repeatable-add button-primary" title="<?php echo $configurations['labels']['add_title']; ?>"><?php echo $add; ?></a>
                </div>

            </div><!-- ..wpcf-repeatable-container -->
        
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
                'add'           => '<i class="dashicons dashicons-plus"></i>',
                'add_title'     => __('Add', 'wp-custom-fields'),
                'remove'        => '<i class="dashicons dashicons-minus"></i>',
                'remove_title'  => __('Remove', 'wp-custom-fields')
            ],            
        ];
            
        return apply_filters( 'wp_custom_fields_repeatable_config', $configurations );

    }
    
}