<?php
 /** 
  * Displays a border field
  */
namespace Divergent\Fields;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class Divergent_Field_Border implements Divergent_Field {
    
    public static function render($field = array()) {
        
        // Basic Variables
        $output = '';
        $border = isset($field['borders']) ? $field['borders'] : '';
        $type['options'] = array(
            'solid'     => __('Solid', 'divergent'), 
            'dotted'    => __('Dotted', 'divergent'),  
            'dashed'    => __('Dashed', 'divergent'),  
            'double'    => __('Double', 'divergent'),  
            'groove'    => __('Groove', 'divergent'),  
            'ridge'     => __('Ridge', 'divergent'), 
            'inset'     => __('Inset', 'divergent'),  
            'outset'    => __('Outset', 'divergent'), 
        );
        $type['placeholder'] = __('Border Style', 'divergent');
        
        // Control each side of the box
        if( $border == 'all' ) {
            
            $sides = array(
                'top'       => __('Top', 'divergent'), 
                'right'     => __('Right', 'divergent'), 
                'bottom'    => __('Bottom', 'divergent'), 
                'left'      => __('Left', 'divergent')
            );
            
            foreach($sides as $key => $side) {

                $output             .= '<div class="divergent-single-border">';

                
                // Dimensions
                $output             .= ' <div class="divergent-field-left">';
                $output             .= Divergent_Field_Dimension::render( array(
                    'icon'           => 'border_' . $key,
                    'id'             => $field['id'] . '-' . $key . '-width',
                    'name'           => $field['name'] . '[' . $key . '][width]',
                    'placeholder'    => $side,
                    'values'         => isset($field['values'][$key]['width']) ? $field['values'][$key]['width'] : array()               
                ) );
                $output                 .= ' </div>';

                
                // Border Type
                $type['id']             = $field['id']  . '-' . $key. '-style';
                $type['name']           = $field['name'] . '[' . $key . '][style]';
                $type['values']         = isset($field['values'][$key]['style']) ? $field['values'][$key]['style'] : '';
                $type['placeholder']    = __('Border Style', 'divergent');
                
                $output                .= ' <div class="divergent-field-left">';
                $output                .= Divergent_Field_Select::render($type);
                $output                .= ' </div>';
                
                
                // Colorpicker
                $colorpicker['values']  = isset($field['values'][$key]['color']) ? $field['values'][$key]['color'] : '';
                $colorpicker['id']      = $field['id'] . '-' . $key . '-color';
                $colorpicker['name']    = $field['name']. '[' . $key . '][color]';
                
                $output                .= ' <div class="divergent-field-left">';
                $output                .= Divergent_Field_Colorpicker::render($colorpicker);                
                $output                .= ' </div>';
                
                
                $output                .= '</div><!-- .divergent-single-border -->';
                
            }
            
        // One control
        } else {
          
            // Dimensions       
            $dimension['icon']          = 'border_outer'; 
            $dimension['id']            = $field['id'] . '-width'; 
            $dimension['name']          = $field['name'] . '[width]'; 
            $dimension['placeholder']   = __('Border Width', 'divergent'); 
            $dimension['values']        = isset($field['values']['width']) ? $field['values']['width'] : array();            
            
            $output                    .= '<div class="divergent-field-left">';
            $output                    .= Divergent_Field_Dimension::render( $dimension );
            $output                    .= '</div>';
            
            
            // Border Type
            $type['id']                 = $field['id']  . '-style'; 
            $type['name']               = $field['name'] . '[style]'; 
            $type['values']             = isset($field['values']['style']) ? $field['values']['style'] : ''; 
            $type['placeholder']        = __('Border Style', 'divergent');       
            
            $output                    .= '<div class="divergent-field-left">';
            $output                    .= Divergent_Field_Select::render($type);   
            $output                    .= '</div>';
            

            // Colorpicker
            $colorpicker['values']      = isset($field['values']['color']) ? $field['values']['color'] : '';
            $colorpicker['id']          = $field['id'] . '-color';
            $colorpicker['name']        = $field['name'] . '[color]';              
            
            $output                    .= '<div class="divergent-field-left">';
            $output                    .= Divergent_Field_Colorpicker::render($colorpicker);
            $output                    .= '</div>';                 

        }
        
        return $output;    
    }
    
    public static function configurations() {
        $configurations = array(
            'type' => 'border'
        );
            
        return $configurations;
    }
    
}