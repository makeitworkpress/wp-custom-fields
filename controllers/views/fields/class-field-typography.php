<?php
 /** 
  * Displays a typography input field
  */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) { 
    die; 
}

class Divergent_Field_Typography implements Divergent_Field {
    
    public static function render($field = array()) {
        
        $type = isset($field['subtype']) ? $field['subtype'] : 'text';
        
        $fonts = Divergent_Fields::get('fonts');
        
        // Display the fonts
        $output = '<div class="divergent-typography-fonts divergent-field-left">';
        
        // Select fount display
        if( isset($field['values']['font']) ) {
        
            foreach($fonts as $fontspace => $types) {
                foreach($types as $key => $font) {
                    if($font['name'] == $field['values']['font']) {
                        $font_display = isset($font['example']) ? $font['example'] : DIVERGENT_ASSETS_URL . 'img/' . $key . '.png';
                        $weights = isset($font['weights']) ? ' data-weights="' . implode(',', $font['weights']) . '"' : '';
                        $styles = isset($font['styles']) ? ' data-styles="' . implode(',', $font['styles']) . '"' : ''; 

                        $output .= '<p class="divergent-typography-title"><strong>' . __('Selected Font:', 'divergent') . '</strong></p>';    
                        $output .= '<div class="divergent-typography-set">';                    
                        $output .= '    <div class="divergent-typography-font selected"' . $weights . $styles . '>';           
                        $output .= '        <img src="' . $font_display . '" />';              
                        $output .= '    </div>';                   
                        $output .= '</div>';                   
                    }
                }
            }
            
        }
        
        // Loop through the sets
        foreach($fonts as $fontspace => $types) {
            $output .= '    <p class="divergent-typography-title"><strong>' . ucfirst($fontspace) . ':</strong></p>';    
            $output .= '    <ul class="divergent-typography-set">';
            
            foreach($types as $key => $font) {
                            
                $font_display = isset($font['example']) ? $font['example'] : DIVERGENT_ASSETS_URL . 'img/' . $key . '.png';
                $weights = isset($font['weights']) ? ' data-weights="' . implode(',', $font['weights']) . '"' : '';
                $styles = isset($font['styles']) ? ' data-styles="' . implode(',', $font['styles']) . '"' : '';
                $selected = isset($field['values']['font']) && $font['name'] == $field['values']['font'] ? ' checked="checked" ' : '';
                
                $output .= '    <li class="divergent-typography-font"' . $weights . $styles . '>';
                $output .= '        <input type="radio" name="' . $field['name'] . '[font]" id="' . $field['id'] . '-' . $key . '" value="' . $font['name'] . '"' . $selected . '/>';               
                $output .= '        <label for="' . $field['id'] . '-' . $key . '">';
                $output .= '            <img src="' . $font_display . '" />';
                $output .= '        </label>';                
                $output .= '    </li>';
            }
            
            $output .= '    </ul>';
        }
             
        $output .= '</div><!-- .divergent-typography-fonts -->';
        
        $output .= '<div class="divergent-typography-styles divergent-field-left">';
        
        // Text Dimensions
        $dimensions = array('size' => __('Font-Size', 'divergent'), 'line_spacing' => __('Line-Height', 'divergent'));
        
        foreach($dimensions as $key => $label) {
            $icon = 'format_' . $key;
            $value = isset($field['values'][$key]) ? $field['values'][$key] : '';
            $output .= Divergent_Fields::dimension_field($field['id'] . '-' . $key, $field['name'] . '[' . $key . ']', $value, '', $label, $icon);
        }        
        
        // Display font characteristics
        $styles = array(
            'styles'     => array(
                'bold'          => 'format_bold', 
                'italic'        => 'format_italic', 
                'strikethrough' => 'format_strikethrough', 
                'underlined'    => 'format_underlined', 
                'uppercase'     => 'title'
            ),
            'alignments' => array('left' => 'format_align_left', 'right' => 'format_align_right', 'center' => 'format_align_center', 'justify' => 'format_align_justify'),
        );        
        
        // Style Buttons
        foreach($styles as $key => $style) {
            $output .= '    <ul class="divergent-typography-' . $key . ' divergent-icon-list">'; 
            
            foreach($style as $value => $icon) {
                $checked = is_array($field['values']) && in_array($value, $field['values']) ? ' checked="checked" ' : '';
                
                $output .= '    <li class="divergent-icons-icon">';
                $output .= '        <input type="checkbox" name="' . $field['name'] . '[' . $value . ']" id="' . $field['id'] . '-' . $value . '" value="' . $value . '"' . $checked . '/>';               
                $output .= '        <label for="' . $field['id'] . '-' . $value . '">';
                $output .= '            <i class="material-icons">' . $icon . '</i>';
                $output .= '        </label>';
                $output .= '    </li>';                    
            }
            
            $output .= '    </ul>';
        }
        
        $output .= '</div><!-- .divergent-typography-styles -->';
        
        return $output;    
    }
    
    public static function configurations() {
        $configurations = array(
            'type' => 'typography'
        );
            
        return $configurations;
    }
    
}