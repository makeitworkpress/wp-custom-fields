<?php
/**
 * Converts setting values with a style attribute to inline styling on the frontend
 */
namespace Classes\Divergent;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) 
    die;

class Divergent_Styling {
    
    /**
     * Contains our fields that have CSS selectors
     */
    private $fields;
    
    /**
     * Constructor
     *
     * @param array $frames The array with frames
     */
    public function __construct( Array $frames = array() ) {
        $this->frames = $frames;  
        
        // Examine for CSS related functions
        $this->examine();
        
        // We should have fields with styles
        if( ! isset($this->fields) )
            return;       
        
        // Examines our custom fonts and enqueues them
        $this->customFonts();
        
        // Create our output
        $this->output();
        
    }
    
    /**
     * Examines the frames for CSS related suggestions
     */
    private function examine() {

        foreach( $this->frames as $frame => $fieldGroups ) {
            
            foreach( $fieldGroups as $group ) {
                
                if( ! isset($group['id']) )
                    continue;
                
                foreach( $group['sections'] as $section ) {
                    
                    if( ! isset($section['id']) )
                        continue;                    
                
                    // Loop through our fields and see if some have a CSS target defined
                    foreach( $section['fields'] as $field ) {

                        if( ! isset($field['css']) )
                            continue;

                        // Save the id per group so we can retrieve the values later.
                        $cssFields[] = array(
                            'css'   => $field['css'],
                            'id'    => $field['id'],
                            'type'  => $field['type']
                        );

                    }
                    
                }
                
                // Now, retrieve our values from the database, but only if we have values
                if( ! isset($cssFields) )
                    return;

                // Retrieve options
                if( $frame == 'options' )
                    $optionValues   = get_option($group['id']);

                // Retrieve meta values. For now, only supports posts.
                if( $frame == 'meta' && is_singular() ) {

                    global $post;

                    $metaValues     = get_metadata( $group['type'], $post->ID, $group['id'], true );

                }

                // Retrieve customizer values
                if( $frame == 'customizer' )
                    $customizerValues = get_option( isset($group['option']) ? $group['option'] : 'theme_mod' )[$group['id']];
                

                /**
                 * Loop again through our fields and see if we have values. 
                 * Please note that meta values with the same ID overwrite option values.
                 */
                foreach( $cssFields as $field ) {

                    if( isset( $optionValues[$field['id']] ) && $optionValues[$field['id']] )
                        $field['values'] = $optionValues[$field['id']];
                    
                    if( isset( $customizerValues[$field['id']] ) && $customizerValues[$field['id']] )
                        $field['values'] = $customizerValues[$field['id']];                     

                    if( isset( $metaValues[$field['id']] ) && $metaValues[$field['id']] )
                        $field['values'] = $metaValues[$field['id']];
                    
                    // Because we loop through all our frames looking for values, we might add the same field twice. 
                    if( isset($this->fields[$field['id']]) )
                        continue;

                    // Now, if the field has values, we add it to the array.
                    if( isset($field['values']) && $field['values'] )
                        $this->fields[$field['id']] = $field;

                }                 
                    
            }
            
        }
 
    } 
    
    /**
     * Retrieve values if we have css fields for them.
     */
    private function output() {
             
        $style = '';
        
        // Loop through our fields that have CSS attributes and values
        foreach( $this->fields as $field ) {
            $style.= isset($field['css']['class']) ? $field['css']['class'] : $field['css'] . '{' . $this->formatField( $field ) . '}';    
        }
        
        $style = $style ? '<style type="text/css">' . $style . '</style>' : '';
        
        echo $style;
        
    }
    
    /**
     * Formats the css based upon a fields type values
     *
     * @param array    $field   The field type, including its values
     */
    private function formatField( $field ) {
        
        // Default values;
        $properties = array();
        $style      = '';
        $value      = array(); 
        
        // Switch types
        switch( $field['type'] ) {
                
            // Background field
            case 'background':
                
                foreach( $field['values'] as $key => $content  ) {
                    if( ! $field['values'][$key] || $key == 'upload' )
                        continue;
                    
                    $properties[]                   = 'background-' . $key;
                    $value['background-' . $key]    = $content;
                }
                
                if( $field['values']['upload'] ) {
                    
                    // Only uses the first one as media
                    $media  = explode( ',', $field['values']['upload'] );
                    $src    = wp_get_attachment_image_url( $media[0], isset($field['css']['size']) ? $field['css']['size'] : 'full' );
                
                    $properties[]               = 'background-image';
                    $value['background-image']  = 'url("' . $src . '")';
                    
                }
                
                break;
                
            // Boxshadow field    
            case 'boxshadow':
                
                $shadow             = $field['values'];
                $properties[]       = 'boxshadow';
                $value['boxshadow'] = $shadow['x'] . 'px ' . $shadow['y'] . 'px ' . $shadow['blur'] . 'px ' . $shadow['spread'] . 'px ' . $shadow['type'];
                
                break; 
                
            // Border field 
            case 'border':
                
                if( isset($field['borders']) ) {
                    
                    foreach( $field['values'] as $key => $values ) {
                        $properties[]   = $key;
                        $value[$key]    = $values['width']['amount'].$values['width']['unit'] . ' ' . $values['style'] . ' ' . $values['color'];
                    }
                    
                } else {
                    $properties[]       = 'border';
                    $value['border']    = $field['values']['width']['amount'].$field['values']['width']['unit'] . ' ' . $field['values']['style'] . ' ' . $field['values']['color'];
                }
                
                break;
                
            // Dimensions field
            case 'dimensions':
                
                $properties[]       = 'padding';
                
                if( isset($field['borders']) ) {
                    
                    $value['padding'] = '';
                    
                    foreach( $field['values'] as $key => $values ) {
                        $value['padding'] .= $values['amount'].$values['unit'] . ' ';
                    }
                    
                    $value['padding'] = rtrim($value['padding']);
                    
                } else {
                    $value['padding']    = $field['values']['amount'].$field['values']['unit'];
                }                
                break;
                
            // Color picker field (including customizer)    
            case 'colorpicker':
                $properties[]    = 'color';
                $value['color']  = $field['values'];
                break;
            
            // Media field (customizer)
            case 'media':
                
                // Only uses the first one as media
                $media  = explode( ',', $field['values'] );
                $src    = wp_get_attachment_image_url( $media[0], isset($field['css']['size']) ? $field['css']['size'] : 'full' );
                
                $properties[]               = 'background-image';
                $value['background-image']  = 'url("' . $src . '")';                
                break;
           
            // Upload field (customizer)
            case 'upload':
                $properties[]               = 'background-image';
                $value['background-image']  = 'url("' . $src . '")';
                break;
                
            // Typographic field
            case 'typography':
                
                $properties[]           = 'font-family';
                $value['font-family']   = $field['values']['font'];
                
                // Add additional properties
                if( $field['values']['size'] ) {
                    $properties[]           = 'font-family';
                    $value['font-family']   = $field['values']['size']['amount'] . $field['values']['size']['unit'];
                }
                
                if( $field['values']['line_spacing'] ) {
                    $properties[]           = 'line-height';
                    $value['line-height']   = $field['values']['line_spacing']['amount'] . $field['values']['line_spacing']['unit'];
                }
                
                if( $field['values']['font_weight'] ) {
                    $properties[]           = 'font-weight';
                    $value['font-weight']   = $field['values']['font_weight'];
                }
                
                // Text styles
                $styles = array(
                    'italic'        => 'font-style', 
                    'line-through'  => 'text-decoration', 
                    'underline'     => 'text-decoration', 
                    'uppercase'     => 'text-transform', 
                    'text-align'    => 'text-align'
                );
                
                foreach( $styles as $key => $property ) {
                    
                    if( ! isset($field['values'][$key]) )
                        continue;
                    
                    $properties[]       = $property;
                    $value[$property]   = $field['values'][$key];
                    
                }
                
                break;
               
        }
        
        // If we have a custom property for the CSS
        $properties  = isset($field['css']['properties']) ? $field['css']['properties'] : $properties;
        
        // Only add the style if we have values for it
        if( $value && $properties ) {
            
            $properties = array_unique($properties);
            
            foreach( $properties as $property ) {
                $content = isset($field['css']['properties']) ? implode('', $value) : $value[$property];
                $style  .= $property . ':' . $content . ';';
            }
            
        }
        
        return apply_filters('divergent_css', $style, $field);
        
    }
    
    /**
     * Determine our custom fonts
     */
    private function customFonts() {
     
    }
    
}