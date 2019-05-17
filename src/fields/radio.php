<?php
 /** 
  * Displays a text input field
  */
namespace MakeitWorkPress\WP_Custom_Fields\Fields;
use MakeitWorkPress\WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined('ABSPATH') ) {
    die;
}

class Radio implements Field {
    
    /**
     * Prepares the variables and renders the field
     * 
     * @param   array $field The array with field attributes data-alpha
     * @return  void
     */    
    public static function render( $field = [] ) {
        
        $id         = esc_attr($field['id']);
        $name       = esc_attr($field['name']);        
        $options    = isset($field['options']) ? $field['options'] : [];
        // Accepts an optional .buttonset style, for a set of styled buttons or .switcher style for a switch display
        $style      = isset($field['style']) ? esc_attr($field['style']) : ''; ?>
        
            <ul class="wp-custom-fields-field-radio-wrapper <?php echo $style; ?>">
        
                <?php foreach( $options as $key => $option ) { ?>

                    <li>
                
                        <?php $label  = isset($option['label']) ? esc_html($option['label']) : ''; ?>
                        <?php $icon   = isset($option['icon']) ? '<i class="material-icons">' . esc_html($option['icon']) . '</i> ' : ''; ?>
                    
                        <input type="radio" id="<?php echo esc_attr($id . $key); ?>" name="<?php echo $name; ?>" value="<?php echo esc_attr($key); ?>" <?php checked($field['values'], $key); ?> />
                    
                        <?php if( ! empty($label) ) { ?>
                            <label for="<?php echo esc_attr($id . $key); ?>"><?php echo $icon . $label; ?></label>
                        <?php } ?>

                    </li>

                <?php } ?>
        
            </ul>
        
        <?php
          
    }
    
    /**
     * Returns the global configurations for this field
     *
     * @return array $configurations The configurations
     */        
    public static function configurations() {
        
        $configurations = [
            'type'      => 'radio',
            'defaults'  => ''
        ];
            
        return apply_filters( 'wp_custom_fields_radio_config', $configurations );

    }
    
}