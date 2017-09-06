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
    
    /**
     * Renders the Dimension Field
     *
     * @param   array   $field  The array with field parameters
     *
     * @return  string  $output The output generated
     */    
    public static function render( $field = array() ) {
        
        $type = isset($field['subtype']) ? $field['subtype'] : 'text';
        
        // Load the select2 script, but only if not yet enqueued
        if( apply_filters('divergent_select_field_js', true) && ! wp_script_is('select2-js', 'enqueued') )
            wp_enqueue_script('select2-js');         
        
        // Retrieve our configurations
        $configurations = self::configurations();
        
        /**
         * Display the fonts
         */
        $output = '<div class="divergent-typography-font-select">';
        $output .= '    <select class="divergent-typography-fonts" name="' . $field['name'] . '[font]" id="' . $field['id'] . '_font" >';
        
        foreach( $configurations['properties']['fonts'] as $fontspace => $types ) {
            
            $output .= '        <optgroup label="' . ucfirst($fontspace) . '">';    
            
            foreach( $types as $key => $font ) {          
                $display        = isset($font['example']) ? $font['example'] : DIVERGENT_ASSETS_URL . 'img/' . $key . '.png'; // Allows for custom fonts
                $output .= '<option data-display="' . $display . '" value="' . $key . '" ' . selected( $field['values']['font'], $key, false ) . '>';
                $output .=      $font['name'];
                $output .= '</option>';
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
        foreach($configurations['properties']['dimensions'] as $key => $label) {
         
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
        $output .= '<div class="divergent-typography-weight">';
        $output .= '    <i class="material-icons">format_bold</i> ';
        $output .= Select::render( array(
            'id'            => $field['id'] . '_font_weight',
            'name'          => $field['name'] . '[font_weight]', 
            'options'       => $configurations['properties']['weights'],
            'placeholder'   => isset($field['labels']['weights']) ? $field['labels']['weights'] : $configurations['labels']['weights'],
            'values'        => isset($field['values']['font_weight']) ? $field['values']['font_weight'] : ''
        ) );
        $output .= '</div>';
        
        $output .= '</div><!-- .divergent-typography-properties -->';
        
        $output .= '<div class="divergent-typography-appearance divergent-field-left">';
      
        /**
         * Display font characteristics
         */       
        
        // Style Buttons
        foreach($configurations['properties']['styles'] as $key => $style) {
            $output .= '    <ul class="divergent-typography-' . $key . ' divergent-icon-list">'; 
            
            foreach($style as $value => $icon) {
                $checked = is_array($field['values']) && in_array($value, $field['values']) ? ' checked="checked" ' : '';
                
                $name = $key == 'styles' ? $field['name'] . '[' . $value . ']' : $field['name'] . '[' . $key . ']';
                $type = $key == 'styles' ? 'checkbox' : 'radio';
                
                $output .= '    <li>';
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
            
            $normal = isset($field['labels']['normal']) ? $field['labels']['normal'] : $configurations['labels']['normal'];
            $italic = isset($field['labels']['italic']) ? $field['labels']['italic'] : $configurations['labels']['italic'];
            
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
    
    /**
     * Returns the global configurations for this field
     *
     * @return array $configurations The configurations
     */      
    public static function configurations() {
        $configurations = array(
            'type'          => 'typography',
            'labels'        => array(
                'normal'    => __('Load all normal font-weights for this font.', 'divergent'),
                'italic'    => __('Load all italic font-weights for this font.', 'divergent'),
                'weights'   => __('Font-Weight', 'divergent')
            ),
            'properties'    => array(
                'dimensions'    => array(
                    'size'          => __('Font-Size', 'divergent'), 
                    'line_spacing'  => __('Line-Height', 'divergent'), 
                ),
                'fonts'         => Divergent::$fonts,
                'styles'        => array(
                    'styles'    => array(
                        'italic'        => 'format_italic', 
                        'line_through'  => 'format_strikethrough', 
                        'underline'     => 'format_underlined', 
                        'uppercase'     => 'title'
                    ),
                    'text_align' => array(
                        'left' => 'format_align_left', 
                        'right' => 'format_align_right', 
                        'center' => 'format_align_center', 
                        'justify' => 'format_align_justify'
                    ),
                ),
                'weights'       => array( 
                    100 => __('100 (Thin)', 'divergent'), 
                    200 => __('200 (Extra Light)', 'divergent'), 
                    300 => __('300 (Light)', 'divergent'), 
                    400 => __('400 (Normal)', 'divergent'), 
                    500 => __('500 (Medium)', 'divergent'), 
                    600 => __('600 (Semi Bold)', 'divergent'), 
                    700 => __('700 (Bold)', 'divergent'), 
                    800 => __('800 (Extra Bold)', 'divergent'), 
                    900 => __('900 (Black)', 'divergent') 
                )
            ),
            'settings' => array(
                '[color]', 
                '[font]', 
                '[font_weight]', 
                '[italic]', 
                '[line_spacing][amount]',
                '[line_spacing][unit]',
                '[line_through]',
                '[load][italic]',
                '[load][normal]',
                '[size][amount]', 
                '[size][unit]', 
                '[text_align]', 
                '[underline]', 
                '[uppercase]'
            )
        );
            
        return $configurations;
    }
    
}