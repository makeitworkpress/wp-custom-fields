<?php
 /** 
  * Displays a single dimension field
  */
namespace MakeitWorkPress\WP_Custom_Fields\Fields;
use MakeitWorkPress\WP_Custom_Fields\Field as Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die; 

class Dimension implements Field {
    
    /**
     * Renders the Dimension Field
     *
     * @param   array   $field  The array with field parameters
     *
     * @return  string  $output The output generated
     */
    public static function render( $field = array() ) {
        
        $configurations = self::configurations();
        
        $amount         = isset($field['values']['amount']) ? $field['values']['amount'] : '';
        $measure        = isset($field['values']['unit']) ? $field['values']['unit'] : '';        
        $step           = isset($field['step']) ? floatval($field['step']) : 1;
        $placeholder    = ! empty($field['placeholder']) ? ' placeholder="' . $field['placeholder'] . '"' : '';        
        $measurements   = isset($field['units']) && is_array($field['units']) ? $field['units'] : $configurations['properties']['units'];
        
        $output         = '<div class="wp-custom-fields-dimensions-input">';
        $output        .= ! empty( $field['label'] )    ? '    <label for="' . $field['id'] . '">' . $field['label'] . '</label>'   : '';    
        $output        .= ! empty( $field['icon'] )     ? '     <i class="material-icons">' . $field['icon'] . '</i>'               : '';    
        $output        .= '    <input id="' . $field['id'] . '" type="number" name="' . $field['name']  . '[amount]" value="' . $amount . '"' . $placeholder . ' step="' . $step . '">';
        $output        .= '    <select name="' . $field['name'] . '[unit]">';
        
        foreach( $measurements as $measurement ) {
            $output    .= '        <option value="' . $measurement . '" ' . selected($measurement, $measure, false) . '>' . $measurement . '</option>';
        }
        
        $output        .= '    </select>';
        $output        .= '</div>';
        
        return $output;
        
    }
    
    /**
     * Returns the global configurations for this field
     *
     * @return array $configurations The configurations
     */    
    public static function configurations() {
        $configurations = array(
            'type'          => 'dimension',
            'defaults'      => array(),
            'properties'    => array(
                'units' => array('', 'px', 'em', '%', 'rem', 'vh', 'vw')
            ),
            'settings'  => array(
                '[amount]',
                '[unit]'
            )
        );
            
        return $configurations;
    }    
    
}