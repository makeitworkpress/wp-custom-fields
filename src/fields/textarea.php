<?php
 /** 
  * Displays a text input field
  */
namespace MakeitWorkPress\WP_Custom_Fields\Fields;
use MakeitWorkPress\WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) 
    die;

class Textarea implements Field {
    
    /**
     * Prepares the variables and renders the field
     * 
     * @param   array $field The array with field attributes data-alpha
     * @return  void
     */      
    public static function render( $field = array() ) {
        
        $config = self::configurations();
        $cols   = isset($field['cols']) ? intval($field['cols']) : $config['properties']['cols'];
        $rows   = isset($field['rows']) ? intval($field['rows']) : $config['properties']['rows'];

        $id     = esc_attr($field['id']);
        $name   = esc_attr($field['name']);
        $value  = esc_textarea($field['values']); ?>        
        
            <textarea id="<?php echo $id; ?>" name="<?php echo $id; ?>" rows="<?php echo $rows; ?>" cols="<?php echo $cols; ?>"><?php echo $value; ?></textarea>

        <?php    
    }
    
    /**
     * Returns the global configurations for this field
     *
     * @return array $configurations The configurations
     */      
    public static function configurations() {

        $configurations = [
            'type'          => 'textarea',
            'defaults'      => '',
            'properties'    => [
                'cols' => 70,
                'rows' => 7
            ]
        ];
            
        return apply_filters( 'wp_custom_fields_textarea_config', $configurations );
        
    }
    
}