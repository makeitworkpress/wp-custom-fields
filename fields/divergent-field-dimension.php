<?php
 /** 
  * Displays a single dimension field
  */
namespace Divergent\Fields;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die; 

class Divergent_Field_Dimension implements Divergent_Field {
    
    public static function render( $field = array() ) {
        
        $amount         = isset($field['values']['amount']) ? $field['values']['amount'] : '';
        $measure        = isset($field['values']['unit']) ? $field['values']['unit'] : '';        
        $step           = isset($field['step']) ? floatval($field['step']) : 1;
        $placeholder    = ! empty($field['placeholder']) ? ' placeholder="' . $field['placeholder'] . '"' : '';
        
        $measurements   =  array('px', 'em', '%', 'rem', 'vh', 'vw');
        
        $output         = '<div class="divergent-dimensions-input">';
        $output        .= ! empty( $field['label'] )    ? '    <label for="' . $field['id'] . '">' . $field['label'] . '</label>'   : '';    
        $output        .= ! empty( $field['icon'] )     ? '     <i class="material-icons">' . $field['icon'] . '</i>'               : '';    
        $output        .= '    <input id="' . $field['id'] . '" type="number" name="' . $field['name']  . '[amount]" value="' . $amount . '"' . $placeholder . ' step="' . $step . '">';
        $output        .= '    <select name="' . $field['name'] . '[unit]">';
        
        foreach( $measurements as $measurement ) {
            $selected   = $measurement == $measure ? 'selected="selected"' : ''; 
            $output    .= '        <option value="' . $measurement . '"' . $selected . '>' . $measurement . '</option>';
        }
        
        $output        .= '    </select>';
        $output        .= '</div>';
        
        return $output;
        
    }
    
    public static function configurations() {
        $configurations = array(
            'type' => 'dimension'
        );
            
        return $configurations;
    }    
    
}