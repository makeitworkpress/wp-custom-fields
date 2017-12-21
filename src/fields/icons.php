<?php
 /** 
  * Displays a text input field
  */
namespace MakeitWorkPress\WP_Custom_Fields\Fields;
use MakeitWorkPress\WP_Custom_Fields\Field as Field;
use MakeitWorkPress\WP_Custom_Fields\Framework as Framework;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class Icons implements Field {
    
    public static function render($field = array()) {
        
        $configurations = self::configurations();
        $iconsets       = $configurations['properties']['icons'];
        $type           = isset($field['multiple']) && $field['multiple'] == true ? 'checkbox' : 'radio';
        
        $output = '<div class="wp-custom-fields-icons">';

        foreach($iconsets as $set => $icons) {
            $output .= '    <p class="wp-custom-fields-icons-title">' . $set . '</p>';
            $output .= '    <ul class="wp-custom-fields-icon-list">';
            
            // Loop through icons of a set
            foreach( $icons as $icon ) {
                
                if( $set == 'fontawesome' ) {
                    $display_icon = '<i class="fa ' . $icon . '"></i>';
                }
                
                if( $set == 'material' ) {
                    $display_icon = '<i class="material-icons">' . $icon . '</i>';
                }                
                
                $display_icon = apply_filters('wp_custom_fields_displayed_icon', $display_icon, $set);
                
                $name = $type == 'checkbox' ? '[' . $icon . ']' : '';
                
                // Get the values for a set
                if($type == 'checkbox' && is_array($field['values'])) {
                    $selected = in_array($icon, $field['values']) ? ' checked="checked" ' : '';
                } else {
                    $selected = $icon == $field['values'] ? ' checked="checked" ' : '';
                }                
                
                $output .= '    <li>';
                $output .= '        <input type="' . $type . '" name="' . $field['name'] . $name . '" id="' . $field['id'] . '-' . $icon . '" value="' . $icon . '"' . $selected . '/>';               
                $output .= '        <label for="' . $field['id'] . '-' . $icon . '">';
                $output .= $display_icon;
                $output .= '        </label>';
                $output .= '    </li>';
            }
            $output .= '    </ul>';
        }
        
        $output .= '</div>';
        
        return $output;    
    }
    
    public static function configurations() {
        $configurations = array(
            'type'          => 'icons',
            'defaults'      => '',
            'properties'    => array(
                'icons' => Framework::$icons
            )
        );
            
        return $configurations;
    }
    
}