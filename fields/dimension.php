<?php
 /** 
  * Displays a single dimension field
  */
namespace Divergent\Fields;
use Divergent\Divergent_Field as Divergent_Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die; 

class Dimension implements Divergent_Field {
    
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
        
        $measurements   = $configurations['properties']['units'];
        
        $output         = '<div class="divergent-dimensions-input">';
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
            'properties'    => array(
                'units' => array('px', 'em', '%', 'rem', 'vh', 'vw')
            )
        );
            
        return $configurations;
    }    
    
}