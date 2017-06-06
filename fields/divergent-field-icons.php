<?php
 /** 
  * Displays a text input field
  */
namespace Divergent\Fields;
use Divergent\Divergent as Divergent;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class Divergent_Field_Icons implements Divergent_Field {
    
    public static function render($field = array()) {
        
        $iconsets   = Divergent::$icons;
        $iconsets   = apply_filters('divergent_icons', $iconsets);
        $type       = isset($field['multiple']) && $field['multiple'] == true ? 'checkbox' : 'radio';
        
        $output = '<div class="divergent-icons">';

        foreach($iconsets as $set => $icons) {
            $output .= '    <p class="divergent-icons-title">' . $set . '</p>';
            $output .= '    <ul class="divergent-icons-set">';
            
            // Loop through icons of a set
            foreach($icons as $icon) {
                
                if($set == 'fontawesome') {
                    $display_icon = '<i class="fa ' . $icon . '"></i>';
                }
                
                if($set == 'material') {
                    $display_icon = '<i class="material-icons">' . $icon . '</i>';
                }                
                
                $display_icon = apply_filters('divergent_displayed_icon', $display_icon, $set);
                
                $name = $type == 'checkbox' ? '[' . $icon . ']' : '';
                
                // Get the values for a set
                if($type == 'checkbox' && is_array($field['values'])) {
                    $selected = in_array($icon, $field['values']) ? ' checked="checked" ' : '';
                } else {
                    $selected = $icon == $field['values'] ? ' checked="checked" ' : '';
                }                
                
                $output .= '    <li class="divergent-icons-icon">';
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
            'type' => 'icons',
        );
            
        return $configurations;
    }
    
}