<?php
 /** 
  * Displays a text input field
  */
namespace MakeitWorkPress\WP_Custom_Fields\Fields;
use MakeitWorkPress\WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) 
    die;

class Checkbox implements Field {

    /**
     * Prepares the variables and renders the field
     * 
     * @param   array $field The array with field attributes
     * @return  void
     */       
    public static function render( $field = [] ) {
        
        $options    = isset($field['options']) && is_array($field['options']) ? $field['options'] : [];
        $single     = isset($field['single']) && count($options) == 1 ? true : false;

        // Accepts an optional .buttonset style, for a set of styled buttons or .switcher/.switcher .switcher-disable style for a switch display
        $style      = isset($field['style']) ? esc_attr($field['style']) : ''; ?> 

            <ul class="wpcf-field-checkbox-wrapper <?php echo $style; ?>">

                <?php 
                    foreach($options as $key => $option) { 

                        // Single checkboxes
                        if( $single ) {
                            $id     = esc_attr($field['id']);
                            $name   = esc_attr($field['name']);
                            $value  = isset($field['values']) && ! is_array($field['values']) ? $field['values'] : false;
                        // Multiple checkboxes
                        } else {
                            $id     = esc_attr($field['id']  . '_' . $key);
                            $name   = esc_attr($field['name'] . '[' . $key . ']');  
                            $value  = isset($field['values'][$key]) ? $field['values'][$key] : '';
                        }

                        $label  = isset($option['label']) ? esc_html($option['label']) : '';
                        $icon   = isset($option['icon']) ? '<i class="material-icons">' . esc_html($option['icon'])  . '</i>' : ''; ?> 
                        <li class="wpcf-field-checkbox-input">
                            <input type="checkbox" id="<?php echo $id; ?>" name="<?php echo $name; ?>" <?php checked($value, true); ?> data-key="<?php echo esc_attr($key); ?>" />
                            <?php if( ! empty($label) ) { ?>
                                <label for="<?php echo $id; ?>"><?php echo $icon . $label; ?></label>  
                            <?php } ?>
                        </li>
                    <?php         
                ?>
                <?php 
                    } 
                ?>
        
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
            'type'      => 'checkbox',
            'defaults'  => []
        ];
            
        return apply_filters( 'wp_custom_fields_checkbox_config', $configurations );
    }
    
}