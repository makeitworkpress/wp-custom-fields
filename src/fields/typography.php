<?php
 /** 
  * Displays a typography input field
  */
namespace MakeitWorkPress\WP_Custom_Fields\Fields;
use MakeitWorkPress\WP_Custom_Fields\Field as Field;
use MakeitWorkPress\WP_Custom_Fields\Framework as Framework;

// Bail if accessed directly
if ( ! defined('ABSPATH') ) {
    die;
}

class Typography implements Field {
    
    /**
     * Prepares the variables and renders the field
     * 
     * @param   array $field The array with field attributes
     * @return  void
     */   
    public static function render( $field = [] ) {
        
        // Load the select2 script, but only if not yet enqueued
        if( apply_filters('wp_custom_fields_select_field_js', true) && ! wp_script_is('select2-js', 'enqueued') )
            wp_enqueue_script('select2-js');         
        
        // Retrieve our configurations
        $configurations = self::configurations();
        foreach( ['normal', 'italic', 'select', 'weights'] as $label ) {
            ${$label}   = isset($field['labels']['select']) ? $field['labels'][$label] : $configurations['labels'][$label];
        } ?>       

            <div class="wpcf-typography-font-select">
                <select class="wpcf-typography-fonts" name="<?php echo esc_attr($field['name']); ?>[font]" id="<?php echo esc_attr($field['id']); ?>-font" >

                    <option value=""><?php echo $select; ?></option>
                
                    <?php foreach( $configurations['properties']['fonts'] as $fontspace => $types ) { ?>
                        
                        <optgroup label="<?php echo esc_attr( ucfirst($fontspace) ); ?>">    
                        
                        <?php foreach( $types as $key => $font ) { ?>         
                            <?php $display = isset($font['example']) ? esc_url($font['example']) : WP_CUSTOM_FIELDS_ASSETS_URL . 'img/' . $key . '.png'; // Allows for custom fonts ?> 
                            <option data-display="<?php echo $display; ?>" value="<?php echo esc_attr($key); ?>" <?php selected( isset($field['values']['font']) ? $field['values']['font'] : '', $key); ?>>
                                <?php esc_html_e( $font['name'] ); ?>
                            </option>
                        <?php } ?>
                        
                        </optgroup>
                    
                    <?php } ?>
             
                </select><!-- .wpcf-typography-fonts -->
            </div><!-- .wpcf-typography-font-select -->
        
            <div class="wpcf-typography-properties wpcf-field-left">
        
                <?php 
                    foreach( $configurations['properties']['dimensions'] as $key => $label ) {
                
                        Dimension::render( [
                            'step'          => 0.01,
                            'icon'          => 'format_' . $key,
                            'id'            => $field['id'] . '_' . $key,
                            'name'          => $field['name'] . '[' . $key . ']',
                            'placeholder'   => $label,
                            'values'        => isset($field['values'][$key]) ? $field['values'][$key] : ''
                        ] );
                    
                    } 
                ?> 
        
        
                <div class="wpcf-typography-weight">
                    <i class="material-icons">format_bold</i>
                        <?php
                            Select::render( [
                                'id'            => $field['id'] . '_font_weight',
                                'name'          => $field['name'] . '[font_weight]', 
                                'options'       => $configurations['properties']['weights'],
                                'placeholder'   => isset($field['labels']['weights']) ? $field['labels']['weights'] : $configurations['labels']['weights'],
                                'values'        => isset($field['values']['font_weight']) ? $field['values']['font_weight'] : ''
                            ] );
                        ?>
                </div>
        
            </div><!-- .wpcf-typography-properties -->
        
            <div class="wpcf-typography-appearance wpcf-field-left">
      
                <?php                        
                    /**
                     * Display font characteristics
                     */       
                    
                    // Style Buttons
                    foreach($configurations['properties']['styles'] as $key => $style) { 
                ?>
                    <ul class="wpcf-typography-'<?php echo esc_attr($key); ?> wpcf-icon-list"> 
            
                        <?php 
                            foreach( $style as $value => $icon ) {
                                $checked = is_array($field['values']) && in_array($value, $field['values']) ? ' checked="checked" ' : '';   
                                $name = $key == 'styles' ? $field['name'] . '[' . $value . ']' : $field['name'] . '[' . $key . ']';
                                $type = $key == 'styles' ? 'checkbox' : 'radio';         
                        ?>
                            
                            <li>
                                <input type="<?php echo esc_attr($type); ?>" name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($field['id'] . '-' . $value); ?>" value="<?php echo esc_attr($value); ?>" <?php echo $checked; ?> />
                                <label for="<?php echo esc_attr($field['id'] . '-' . $value); ?>">
                                <i class="material-icons"><?php esc_html_e($icon); ?></i>
                                </label>
                            </li>

                        <?php } ?>
            
                    </ul>
                <?php  }
                    Colorpicker::render( [
                        'values' => isset( $field['values']['color'] ) ? $field['values']['color'] : '',
                        'name'   => $field['name'] . '[color]',
                        'id'     => $field['id'] . '-color'        
                    ] );  
                ?>      
        
            </div><!-- .wpcf-typography-appearance -->
        
        <?php             
            if( isset($field['selector']) ) {
                
                Checkbox::render([
                    'id'            => $field['id'] . '_load',
                    'name'          => $field['name'] . '[load]', 
                    'options'       =>[ 
                        'normal' => ['label' => isset($field['labels']['normal']) ? $field['labels']['normal'] : $configurations['labels']['normal']],
                        'italic' => ['label' => isset($field['labels']['italic']) ? $field['labels']['italic'] : $configurations['labels']['italic']]
                    ],
                    'values'        => isset($field['values']['load']) ? $field['values']['load'] : []
                ] );
                
            }

    }
    
    /**
     * Returns the global configurations for this field
     *
     * @return array $configurations The configurations
     */      
    public static function configurations() {

        $configurations = [
            'type'          => 'typography',
            // The default values
            'defaults'  => [
                'font'  => '',
            ],  
            // The default labels for the typography field          
            'labels'        => [
                'normal'    => __('Load all normal font-weights for this font.', 'wp-custom-fields'),
                'italic'    => __('Load all italic font-weights for this font.', 'wp-custom-fields'),
                'select'    => __('Select a font', 'wp-custom-fields'),
                'weights'   => __('Font-Weight', 'wp-custom-fields')
            ],
            // The default properties for the typography field
            'properties'    => [
                'dimensions'    => [
                    'size'          => __('Font-Size', 'wp-custom-fields'), 
                    'line_spacing'  => __('Line-Height', 'wp-custom-fields'), 
                ],
                'fonts'         => Framework::$fonts,
                'styles'        => [
                    'styles'    => [
                        'italic'        => 'format_italic', 
                        'line_through'  => 'format_strikethrough', 
                        'underline'     => 'format_underlined', 
                        'uppercase'     => 'title'
                    ],
                    'text_align' => [
                        'left' => 'format_align_left', 
                        'right' => 'format_align_right', 
                        'center' => 'format_align_center', 
                        'justify' => 'format_align_justify'
                    ],
                ],
                'weights'       => [ 
                    100 => __('100 (Thin)', 'wp-custom-fields'), 
                    200 => __('200 (Extra Light)', 'wp-custom-fields'), 
                    300 => __('300 (Light)', 'wp-custom-fields'), 
                    400 => __('400 (Normal)', 'wp-custom-fields'), 
                    500 => __('500 (Medium)', 'wp-custom-fields'), 
                    600 => __('600 (Semi Bold)', 'wp-custom-fields'), 
                    700 => __('700 (Bold)', 'wp-custom-fields'), 
                    800 => __('800 (Extra Bold)', 'wp-custom-fields'), 
                    900 => __('900 (Black)', 'wp-custom-fields') 
                ]
            ],
            // Setting keys, which are used within the customizer setup to create settings
            'settings' => [
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
            ]
        ];
            
        return apply_filters( 'wp_custom_fields_typography_config', $configurations );
        
    }
    
}