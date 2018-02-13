<?php
 /** 
  * Displays a typography input field
  */
namespace MakeitWorkPress\WP_Custom_Fields\Fields;
use MakeitWorkPress\WP_Custom_Fields\Field as Field;
use MakeitWorkPress\WP_Custom_Fields\Framework as Framework;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) 
    die;

class Typography implements Field {
    
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
        if( apply_filters('wp_custom_fields_select_field_js', true) && ! wp_script_is('select2-js', 'enqueued') )
            wp_enqueue_script('select2-js');         
        
        // Retrieve our configurations
        $configurations = self::configurations();
        
        /**
         * Display the fonts
         */
        $output = '<div class="wp-custom-fields-typography-font-select">';
        $output .= '    <select class="wp-custom-fields-typography-fonts" name="' . $field['name'] . '[font]" id="' . $field['id'] . '_font" >';
        
        foreach( $configurations['properties']['fonts'] as $fontspace => $types ) {
            
            $output .= '        <optgroup label="' . ucfirst($fontspace) . '">';    
            
            foreach( $types as $key => $font ) {          
                $display        = isset($font['example']) ? $font['example'] : WP_CUSTOM_FIELDS_ASSETS_URL . 'img/' . $key . '.png'; // Allows for custom fonts
                $output .= '<option data-display="' . $display . '" value="' . $key . '" ' . selected( isset($field['values']['font']) ? $field['values']['font'] : '', $key, false ) . '>';
                $output .=      $font['name'];
                $output .= '</option>';
            }
            
            $output .= '    </optgroup>';
            
        }
             
        $output .= '    </select><!-- .wp-custom-fields-typography-fonts -->';
        $output .= '</div><!-- .wp-custom-fields-typography-font-select -->';
        
        /**
         * Display dimensions
         */        
        $output .= '<div class="wp-custom-fields-typography-properties wp-custom-fields-field-left">';
        
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
        $output .= '<div class="wp-custom-fields-typography-weight">';
        $output .= '    <i class="material-icons">format_bold</i> ';
        $output .= Select::render( array(
            'id'            => $field['id'] . '_font_weight',
            'name'          => $field['name'] . '[font_weight]', 
            'options'       => $configurations['properties']['weights'],
            'placeholder'   => isset($field['labels']['weights']) ? $field['labels']['weights'] : $configurations['labels']['weights'],
            'values'        => isset($field['values']['font_weight']) ? $field['values']['font_weight'] : ''
        ) );
        $output .= '</div>';
        
        $output .= '</div><!-- .wp-custom-fields-typography-properties -->';
        
        $output .= '<div class="wp-custom-fields-typography-appearance wp-custom-fields-field-left">';
      
        /**
         * Display font characteristics
         */       
        
        // Style Buttons
        foreach($configurations['properties']['styles'] as $key => $style) {
            $output .= '    <ul class="wp-custom-fields-typography-' . $key . ' wp-custom-fields-icon-list">'; 
            
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
        
        $output .= '</div><!-- .wp-custom-fields-typography-appearance -->';
        
        // If this field is responsible for some styling, we can also opt to load all weights
        if( isset($field['selector']) ) {
            
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
            'defaults'  => array(
                'font'  => '',
            ),            
            'labels'        => array(
                'normal'    => __('Load all normal font-weights for this font.', 'wp-custom-fields'),
                'italic'    => __('Load all italic font-weights for this font.', 'wp-custom-fields'),
                'weights'   => __('Font-Weight', 'wp-custom-fields')
            ),
            'properties'    => array(
                'dimensions'    => array(
                    'size'          => __('Font-Size', 'wp-custom-fields'), 
                    'line_spacing'  => __('Line-Height', 'wp-custom-fields'), 
                ),
                'fonts'         => Framework::$fonts,
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
                    100 => __('100 (Thin)', 'wp-custom-fields'), 
                    200 => __('200 (Extra Light)', 'wp-custom-fields'), 
                    300 => __('300 (Light)', 'wp-custom-fields'), 
                    400 => __('400 (Normal)', 'wp-custom-fields'), 
                    500 => __('500 (Medium)', 'wp-custom-fields'), 
                    600 => __('600 (Semi Bold)', 'wp-custom-fields'), 
                    700 => __('700 (Bold)', 'wp-custom-fields'), 
                    800 => __('800 (Extra Bold)', 'wp-custom-fields'), 
                    900 => __('900 (Black)', 'wp-custom-fields') 
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