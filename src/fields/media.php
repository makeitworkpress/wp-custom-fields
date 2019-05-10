<?php
 /** 
  * Displays a text input field
  *
  * @todo Extend video preview capabilities / display
  */
namespace MakeitWorkPress\WP_Custom_Fields\Fields;
use MakeitWorkPress\WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined('ABSPATH') ) {
    die;
}

class Media implements Field {
    
    /**
     * Prepares the variables and renders the field
     * 
     * @param   array $field The array with field attributes data-alpha
     * @return  void
     */     
    public static function render( $field = [] ) {
        
        $config     = self::configurations();
        $add        = isset($field['labels']['add']) ? esc_attr($field['labels']['add']) : $config['labels']['add'];
        $button     = isset($field['labels']['button']) ? esc_attr($field['labels']['button']) : $config['labels']['button'];
        $title      = isset($field['labels']['title']) ? esc_attr($field['labels']['title']) : $config['labels']['title'];
        
        $id         = esc_attr($field['id']);
        $name       = esc_attr($field['name']);        
        $type       = isset($field['subtype']) ? esc_attr($field['subtype']) : 'image';
        $multiple   = isset($field['multiple']) ? esc_attr($field['multiple']) : true;
        $url        = isset($field['url']) && $field['url'] ? true : false;
        $media      = ! empty($field['values']) ? explode(',', rtrim($field['values'], ',')) : []; 
        $value      = esc_attr($field['values']);?>

            <div class="wp-custom-fields-upload-wrapper" data-type="<?php echo $type; ?>'" data-button="<?php echo $button; ?>" data-title="<?php echo $title; ?>" data-multiple="<?php echo $multiple; ?>">
            
                <?php 
                    foreach($media as $medium) {
                        if( empty($medium) ) {
                            continue; 
                        }                    
                ?>
                    <div class="wp-custom-fields-single-media" data-id="<?php echo $medium; ?>">
                        <?php echo wp_get_attachment_image($medium, 'thumbnail', true); ?>
                        <?php if( $url ) { ?>
                            <?php $attachment_url = esc_url( wp_get_attachment_url($medium) ); ?>
                            <div class="wp-custom-fields-media-url">
                                <i class="material-icons">link</i>
                                <input type="text" readonly="readonly" value="<?php echo $attachment_url; ?>" />
                            </div>              
                        <?php } ?>  
                        <a href="#" class="wp-custom-fields-upload-remove"><i class="material-icons">clear</i></a>                  
                    </div>        
                <?php } ?>
            
                <div class="wp-custom-fields-single-media empty">
                    <a href="#" class="wp-custom-fields-upload-add" title="<?php echo $add; ?>">
                        <i class="material-icons">add</i>
                        <?php echo $add; ?>
                    </a>
                </div>
                <input id="<?php echo $id; ?>" name="<?php echo $name; ?>" class="wp-custom-fields-upload-value" type="hidden" value="<?php echo $value; ?>" /> 
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
            'type'      => 'media',
            'defaults'  => '',
            'labels'    => [
                'add'       => __('Add', 'wp-custom-fields'),
                'button'    => __('Insert', 'wp-custom-fields'),
                'title'     => __('Add Media', 'wp-custom-fields')
            ]
        ];
            
        return apply_filters( 'wp_custom_fields_media_config', $configurations );

    }
    
}