<?php
/**
 * Converts setting values with a style attribute to inline styling on the frontend
 *
 * @todo Too much responsibility. Split up classes according to their responsibilities in the WP Customizer and normal front-end.
 * @todo Might merge $value and $properties into one variable in the formatField method
 */
namespace Divergent;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) 
    die;

class Divergent_Styling extends Divergent_Abstract {
    
    /**
     * Contains our fields that have CSS selectors
     */
    private $fields;
    
    /**
     * Contains our option frames
     */
    private $frames;   
    
    /**
     * Contains our custom fonts
     */
    private $fonts;        
    
    /**
     * Constructor
     */
    public function initialize() {}
    
    /**
     * Adds functions to WordPress hooks - is automatically performed at a new instance
     */
    protected function registerHooks() {           
        $this->actions = array(
            array( 'wp_head', 'examine' ),
            array( 'wp_head', 'properties' ),
            array( 'wp_head', 'output', 20 ),
            array( 'wp_enqueue_scripts', 'customFonts' ),
            array( 'customize_preview_init', 'customizerJS' )
        );
    }
    
    /**
     * Examines the frames for CSS related suggestions
     */
    public function examine() {
        
        $this->frames = Divergent::instance()->get('all');
        
        // We don't have any frames
        if( ! $this->frames )
            return;

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
                            'css'       => $field['css'],
                            'group'     => $group['id'],
                            'id'        => $field['id'],
                            'transport' => isset($field['transport']) ? true : false,
                            'type'      => $field['type']
                        );

                    }
                    
                }
                
                // Now, retrieve our values from the database, but only if we have values for the given frame
                if( ! isset($cssFields) )
                    continue;

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
                    $customizerValues = isset($group['option']) ? get_option($group['option']) : get_theme_mod($group['id']);

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

                    $this->fields[$field['id']] = $field;

                }                 
                    
            }
            
        }
 
    } 

    /**
     * Format our fields with the right properties
     */   
    public function properties() {
        
        // We should have fields with styles
        if( ! isset($this->fields) )
            return;
        
        foreach( $this->fields as $key => $field ) {
            
            // Load our fonts if we have a fonts field
            if( $field['type'] == 'typography' && ! isset($this->fonts) )
                $this->fonts = Divergent::$fonts;            
            
            $this->formatField($field);
        }
        
    }
    
    /**
     * Retrieve values if we have css fields for them.
     */
    public function output() {
        
        // We should have fields with styles
        if( ! isset($this->fields) )
            return;            
             
        $style      = '';
        
        // Loop through our fields that have CSS attributes and values
        foreach( $this->fields as $key => $field ) {
            
            $properties = '';
            
            // Some fields are used in the customizer to update content. Those are skipped here.
            if( isset($field['css']['content']) )
                continue;
            
            // Some fields do not have values. We skip those
            if( ! isset($field['values']) || ! $field['values'] )
                continue;

            // Only add the style if we have values for it 
            foreach( $field['properties'] as $property => $value ) {

                // Skip properties without value
                if( ! $value )
                    continue;

                $properties  .= $property . ':' . $value . ';';

            }
        
            // And save our styles with a filter, so developers can filter the style output if they want.
            $properties = apply_filters('divergent_css_properties', $properties, $field); 
            
            // If we don't have properties, go to the following field
            if( ! $properties )
                continue;
            
            // Add our styling accordingly.
            $selector   = isset( $field['css']['selector'] ) ? $field['css']['selector'] : $field['css'];            
            $style     .= $selector . '{' . $properties . '}';
            
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
        $properties         = array();
        $field['values']    = isset($field['values']) && $field['values'] ? $field['values'] : '';
        
        // Switch types
        switch( $field['type'] ) {
                
            // Background field
            case 'background':
                
                if( $field['values'] ) {
                
                    foreach( $field['values'] as $key => $content  ) {
                        if( ! isset($field['values'][$key]) || ! $field['values'][$key] || $key == 'upload' )
                            continue;

                        $properties['background-' . $key] = $content;
                    }

                    if( isset($field['values']['upload']) && $field['values']['upload'] ) {

                        // Only uses the first one as media
                        $media  = explode( ',', $field['values']['upload'] );
                        $src    = wp_get_attachment_image_url( $media[0], isset($field['css']['size']) ? $field['css']['size'] : 'full' );

                        $properties['background-image'] = 'url("' . $src . '")';

                    }
                    
                } else {
                    $properties['background'] = $field['values'];
                }
                
                break;
                
            // Boxshadow field    
            case 'boxshadow':
                
                $shadow                  = $field['values'];
                $properties['boxshadow'] = $shadow ? $shadow['x'] . 'px ' . $shadow['y'] . 'px ' . $shadow['blur'] . 'px ' . $shadow['spread'] . 'px ' . $shadow['type'] : '';
                
                break; 
                
            // Border field 
            case 'border':
                
                if( ! $field['values'] ) {
                    $properties['border'] = '';
                } else {
                
                    if( isset($field['borders']) ) {

                        foreach( $field['values'] as $key => $values ) {
                            $properties[$key] = $values['width']['amount'] . $values['width']['unit'] . ' ' . $values['style'] . ' ' . $values['color'];
                        }

                    } else {
                        $properties['border'] = $field['values']['width']['amount'] . $field['values']['width']['unit'] . ' ' . $field['values']['style'] . ' ' . $field['values']['color'];
                    }
                    
                }
                
                break;
                    
            // Color picker field (including customizer)    
            case 'colorpicker':
                
                $properties['color'] = $field['values'];
                break;               
                
            // Dimensions field
            case 'dimensions':
                
                $properties['padding'] = '';
                
                if( ! $field['values'] ) {
                    $properties['border'] = '';
                } else {
                
                    if( isset($field['borders']) ) {

                        foreach( $field['values'] as $key => $values ) {
                            $properties['padding'] .= $values['amount'].$values['unit'] . ' ';
                        }

                        $properties['padding'] = rtrim($properties['padding']);

                    } else {
                        $properties['padding'] = $field['values']['amount'].$field['values']['unit'];
                    }  
                    
                }
                
                break;
            
            // Media field (customizer)
            case 'media':
                
                // Only uses the first one as media
                $media  = explode( ',', $field['values'] );
                $src    = wp_get_attachment_image_url( $media[0], isset($field['css']['size']) ? $field['css']['size'] : 'full' );
                
                $properties['background-image'] = 'url("' . $src . '")';               
                break;
           
            // Upload or image field (customizer)
            case 'upload':              
            case 'image':   
                $properties['background-image'] = 'url("' . $field['values'] . '")';
                break;            
                
            // Typographic field
            case 'typography':
                
                if( ! $field['values'] ) {
                    $properties['font-family'] = '';
                } else {
                
                    foreach( $this->fonts as $fonts ) {
                        
                        foreach( $fonts as $family => $font ) {
                            
                            // Family found!
                            if( isset($properties['font-family']) && $properties['font-family'] )
                                break;
                            
                            $properties['font-family'] = $family == $field['values']['font'] ? $font['family'] : '';
                            
                        }
                       
                    }

                    // Add additional properties
                    if( $field['values']['size'] && $field['values']['size']['amount'] ) {
                        $properties['font-size']     = $field['values']['size']['amount'] . $field['values']['size']['unit'];
                    }

                    if( $field['values']['line_spacing'] && $field['values']['line_spacing']['amount'] ) {
                        $properties['line-height']   = $field['values']['line_spacing']['amount'] . $field['values']['line_spacing']['unit'];
                    }

                    if( $field['values']['font_weight'] ) {
                        $properties['font-weight']   = $field['values']['font_weight'];
                    }

                    if( $field['values']['color'] ) {
                        $properties['color']         = $field['values']['color'];
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

                        if( ! $field['values'][$key] )
                            continue;

                        $properties[$property] = $field['values'][$key];

                    }
                    
                }
                
                break;
               
        }
        
        /**
         * If we have a custom single property for the CSS, we us it with the values from the properties
         * This thus only works with some of the fields which have single properties (box-shadow, colorpicker, media, image, upload) and not all fields
         */
        if( isset($field['css']['property']) ) {
            $values     = implode('', $properties);
            $properties = array();
            $properties[$field['css']['property']] = $values;
        }
        
        // Only unique properties
        $properties = array_unique($properties);        

        // Save the final properties to the fields array
        $this->fields[$field['id']]['properties'] = $properties;
        
    }
    
    /**
     * Determine the enqueueing of our custom fonts
     */
    public function customFonts() {
        
        // We should have fields with styles
        if( ! isset($this->fields) )
            return;
        
        $styles = array();
        $weights = array();
        
        // Build our styles.
        foreach( $this->fields as $field ) {
            
            if( $field['type'] != 'typography')
                continue;
            
            foreach( $this->fonts as $key => $set ) {
                
                if( ! isset($set[$field['values']['font']]) )
                    continue;
                
                // Google fonts. Supports multiple fonts.
                if( $key == 'google' ) {
                    
                    // Font weights
                    if( $field['values']['font_weight'] && in_array($field['values']['font_weight'], $set[$field['values']['font']]['weights']) ) {
                        $italic = $field['values']['italic'] && in_array('italic', $set[$field['values']['font']]['styles']) ? 'i' : '';
                        $weights[$field['values']['font']][] = $field['values']['font_weight'] . $italic;
                    } else {
                        $weights[$field['values']['font']][] = 400;    
                    }
                    
                    // Our definite url
                    $variants = implode(',', $weights[$field['values']['font']]);
                    $styles[$field['values']['font']] = 'https://fonts.googleapis.com/css?family=' . $set[$field['values']['font']]['name'] . ':' . $variants;
                 
                // Custom urls    
                } elseif( isset($set['url']) ) {    
                    $styles[$field['values']['font']] = $set['url'];
                }
            }
            
        }
        
        // Enqueue the styles. @todo We might merge google fonts into one request using the pipe character.
        foreach($styles as $key => $url) {
            wp_enqueue_style($key, $url);
        }
     
    }
    
    /**
     * Custom Javascript for Transported fields. For now, only works for fields with single values. But is quite awesome nevertheless. 
     */
    public function customizerJS() {
        
        // Are we in the customizer preview?
        if( ! is_customize_preview() )
            return; 
        
        add_action('wp_footer', function() {
            
            // We should have fields
            if( ! isset($this->fields) )
                return;
        
            $script = '';

            // Format our fields
            foreach( $this->fields as $field ) {
                
                if( ! $field['transport'] )
                    continue;
                
                // Reset our target for each field
                $targetProperty = false;
                
                // Get the first field property
                foreach( $field['properties'] as $property => $value ) {
                    
                    if( $targetProperty )
                        break;
                    
                    $targetProperty = $property;     
                }

                $selector  = isset( $field['css']['selector'] ) ? $field['css']['selector'] : $field['css'];
                $target    = isset( $field['css']['content'] ) ? 'html(newValue)' : 'css("' . $targetProperty . '", newValue)';

                $script .= 'wp.customize( "' . $field['group'] . '[' . $field['id'] . ']' . '", function( value ) {
                      value.bind( function( newValue ) {
                         $("' . $selector . '").' . $target . ';
                      } );
                } );';

            }

            // Our bindings script
            if( $script )
                echo '<script type="text/javascript">( function( $ ) {' . $script . '} )( jQuery );</script>';
            
        }, 100);
     
    }    
    
}