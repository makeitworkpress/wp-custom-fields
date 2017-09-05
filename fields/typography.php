<?php
 /** 
  * Displays a typography input field
  */
namespace Divergent\Fields;
use Divergent\Divergent as Divergent;
use Divergent\Divergent_Field as Divergent_Field;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) 
    die;

class Typography implements Divergent_Field {
    
    public static function render( $field = array() ) {
        
        $type = isset($field['subtype']) ? $field['subtype'] : 'text';
        
        // Load the select2 script, but only if not yet enqueued
        if( apply_filters('divergent_select_field_js', true) && ! wp_script_is('select2-js', 'enqueued') )
            wp_enqueue_script('select2-js');         
        
        // Retrieve our fonts
        $fonts = Divergent::$fonts;
        
        /**
         * Display the fonts
         */
        $output = '<div class="divergent-typography-font-select">';
        $output .= '    <select class="divergent-typography-fonts" name="' . $field['name'] . '[font]" id="' . $field['id'] . '_font" >';
        
        foreach( $fonts as $fontspace => $types ) {
            
            $output .= '        <optgroup label="' . ucfirst($fontspace) . '">';    
            
            foreach( $types as $key => $font ) {          
                $display        = isset($font['example']) ? $font['example'] : DIVERGENT_ASSETS_URL . 'img/' . $key . '.png'; // Allows for custom fonts
                $selected       = isset($field['values']['font']) && $key == $field['values']['font'] ? ' selected="selected" ' : '';
                
                $output .= '            <option data-display="' . $display . '" value="' . $key . '"' . $selected . '>' . $font['name'] . '</option>';
            }
            
            $output .= '    </optgroup>';
            
        }
             
        $output .= '    </select><!-- .divergent-typography-fonts -->';
        $output .= '</div><!-- .divergent-typography-font-select -->';
        
        /**
         * Display dimensions
         */        
        $output .= '<div class="divergent-typography-properties divergent-field-left">';
        
        // Text Dimensions
        $dimensions = array(
            'size'          => __('Font-Size', 'divergent'), 
            'line_spacing'  => __('Line-Height', 'divergent'), 
        );
        
        foreach($dimensions as $key => $label) {
         
            $output .= Dimension::render( array(
                'step'          => 0.01,
                'icon'          => 'format_' . $key,
                'id'            => $field['id'] . '_' . $key,
                'name'          => $field['name'] . '[' . $key . ']',
                'placeholder'   => $label,
                'values'        => isset($field['values'][$key]) ? $field['values'][$key] : ''
            ) );
            
        } 
        
        // Font-weight
        $output .= '<i class="material-icons">format_bold</i> ';
        $output .= Select::render( array(
            'id'            => $field['id'] . '_font_weight',
            'name'          => $field['name'] . '[font_weight]', 
            'options'       => array( 
                100 => __('100 (Thin)', 'divergent'), 
                200 => __('200 (Extra Light)', 'divergent'), 
                300 => __('300 (Light)', 'divergent'), 
                400 => __('400 (Normal)', 'divergent'), 
                500 => __('500 (Medium)', 'divergent'), 
                600 => __('600 (Semi Bold)', 'divergent'), 
                700 => __('700 (Bold)', 'divergent'), 
                800 => __('800 (Extra Bold)', 'divergent'), 
                900 => __('900 (Black)', 'divergent') 
            ),
            'placeholder'   => __('Font-Weight', 'divergent'),
            'values'        => isset($field['values']['font_weight']) ? $field['values']['font_weight'] : ''
        ) );
        
        $output .= '</div><!-- .divergent-typography-properties -->';
        
        $output .= '<div class="divergent-typography-appearance divergent-field-left">';
      
        /**
         * Display font characteristics
         */
        $styles = array(
            'styles'     => array(
                'italic'        => 'format_italic', 
                'line-through'  => 'format_strikethrough', 
                'underline'     => 'format_underlined', 
                'uppercase'     => 'title'
            ),
            'text-align' => array('left' => 'format_align_left', 'right' => 'format_align_right', 'center' => 'format_align_center', 'justify' => 'format_align_justify'),
        );        
        
        // Style Buttons
        foreach($styles as $key => $style) {
            $output .= '    <ul class="divergent-typography-' . $key . ' divergent-icon-list">'; 
            
            foreach($style as $value => $icon) {
                $checked = is_array($field['values']) && in_array($value, $field['values']) ? ' checked="checked" ' : '';
                
                $name = $key == 'styles' ? $field['name'] . '[' . $value . ']' : $field['name'] . '[' . $key . ']';
                $type = $key == 'styles' ? 'checkbox' : 'radio';
                
                $output .= '    <li class="divergent-icons-icon">';
                $output .= '        <input type="' . $type . '" name="' . $name . '" id="' . $field['id'] . '-' . $value . '" value="' . $value . '"' . $checked . '/>';
                $output .= '        <label for="' . $field['id'] . '-' . $value . '">';
                $output .= '            <i class="material-icons">' . $icon . '</i>';
                $output .= '        </label>';
                $output .= '    </li>';                    
            }
            
            $output .= '    </ul>';
        }
        
        // Font-color
        $output .= Colorpicker::render( array(
            'values' => isset( $field['values']['color'] ) ? $field['values']['color'] : '',
            'name'   => $field['name'] . '[color]',
            'id'     => $field['id'] . '-color'        
        ) );        
        
        $output .= '</div><!-- .divergent-typography-appearance -->';
        
        // If this field is responsible for some styling, we can also opt to load all weights
        if( isset($field['css']) ) {
            
            $normal = isset($field['labels']['normal']) ? $field['labels']['normal'] : __('Load all normal font-weights for this font.', 'divergent');
            $italic = isset($field['labels']['italic']) ? $field['labels']['italic'] : __('Load all italic font-weights for this font.', 'divergent');
            
            $output .= Checkbox::render( array(
                'id'            => $field['id'] . '_load',
                'name'          => $field['name'] . '[load]', 
                'options'       => array( 
                    'normal' => array( 'label' => $normal ),
                    'italic' => array( 'label' => $italic )
                ),
                'values'        => isset($field['values']['load']) ? $field['values']['load'] : array()
            ) );
        }        
        
        return $output;    
    }
    
    public static function configurations() {
        $configurations = array(
            'type' => 'typography'
        );
            
        return $configurations;
    }
    
}