<?php
/**
 * Converts setting values with a style attribute to inline styling on the frontend
 *
 * @todo Too much responsibility. Split up classes according to their responsibilities in the WP Customizer and normal front-end.
 * @todo Might merge $value and $properties into one variable in the formatField method
 */
namespace MakeitWorkPress\WP_Custom_Fields;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) 
    die;

class Styling extends Base {
    
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
        
        // This can only be executed outside the admin context
        if( is_admin() ) {
            return;
        }        

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
        
        $this->frames = Framework::instance()->get('all');
        
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

                        // A selector should be defined. A selector supports selector, size (for the background thumbnail size), max-width (for the max-width media query), property
                        if( ! isset($field['selector']) )
                            continue;

                        // Save the id per group so we can retrieve the values later.
                        $cssFields[] = array(
                            'selector'  => $field['selector'],
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
            if( $field['type'] == 'typography' && ! isset($this->fonts) ) {
                $this->fonts = Framework::$fonts;  
            }          
            
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
            if( isset($field['selector']['html']) || isset($field['selector']['attr']) ) {
                continue;
            }
            
            // Some fields do not have values. We skip those
            if( ! isset($field['values']) || ! $field['values'] ) {
                continue;
            }

            // Only add the style if we have values for it 
            foreach( $field['properties'] as $property => $value ) {

                // Skip properties without value
                if( ! $value )
                    continue;

                $properties  .= $property . ':' . $value . ';';

            }
        
            // And save our styles with a filter, so developers can filter the style output if they want.
            $properties = apply_filters( 'wp_custom_fields_css_properties', $properties, $field ); 
            
            // If we don't have properties, go to the following field
            if( ! $properties ) {
                continue;
            }

            // If we have a media query for a maximum width
            if( isset($field['selector']['max-width'])  || isset($field['selector']['min-width']) ) {
                $value      = isset($field['selector']['max-width']) ? $field['selector']['max-width'] : $field['selector']['min-width'];
                $width      = isset($field['selector']['max-width']) ? 'max-width:' : 'min-width:';
                $style     .= '@media screen and (' . $width  . ' ' . $value . ') {';
            }               
            
            // Add our styling accordingly.
            $selector   = isset( $field['selector']['selector'] ) ? $field['selector']['selector'] : $field['selector'];            
            $style     .= $selector . '{' . $properties . '}';

            // Close our media query
            if( isset($field['selector']['max-width']) || isset($field['selector']['min-width']) ) {
                $style     .= '}';
            }             
            
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
        $uniques            = array();
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
                        $src    = wp_get_attachment_image_url( $media[0], isset($field['selector']['size']) ? $field['selector']['size'] : 'full' );

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
                
            // Dimensions field. Defaults to padding, but may be overwritten by custom properties within a field settings.
            case 'dimension':
            case 'dimensions':
                
                $properties['padding'] = '';
                
                if( ! $field['values'] ) {
                    $properties['padding'] = '';
                } else {
                
                    if( isset($field['borders']) ) {

                        foreach( $field['values'] as $key => $values ) {
                            if( isset($values['amount']) && $values['amount'] && isset($values['unit']) && $values['unit'] ) {
                                $properties['padding'] .= $values['amount'] . $values['unit'] . ' ';
                            }
                        }

                        $properties['padding'] = rtrim($properties['padding']);

                    } elseif( isset($field['values']['amount']) && $field['values']['amount'] && isset($field['values']['unit']) && $field['values']['unit'] ) {
                        $properties['padding'] = $field['values']['amount'] . $field['values']['unit'];
                    }  
                    
                }
                
                break;
            
            // Media field (customizer)
            case 'media':
                
                // Only uses the first one as media
                $media  = explode( ',', $field['values'] );
                $src    = wp_get_attachment_image_url( $media[0], isset($field['selector']['size']) ? $field['selector']['size'] : 'full' );
                
                $properties['background-image'] = 'url("' . $src . '")';               
                break;
           
            // Upload or image field (customizer)          
            case 'cropped-image':   
            case 'image':
            case 'upload':    
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
                            
                            $properties['font-family'] = isset($field['values']['font']) && $family == $field['values']['font'] ? $font['family'] : '';
                            
                        }
                       
                    }

                    // Add additional properties
                    if( isset($field['values']['size']) && $field['values']['size']['amount'] && isset($field['values']['size']['unit']) ) {
                        $properties['font-size']     = $field['values']['size']['amount'] . $field['values']['size']['unit'];
                    }

                    if( isset($field['values']['line_spacing']) && $field['values']['line_spacing']['amount'] && isset($field['values']['line_spacing']['unit']) ) {
                        $properties['line-height']   = $field['values']['line_spacing']['amount'] . $field['values']['line_spacing']['unit'];
                    }

                    if( isset($field['values']['font_weight']) && $field['values']['font_weight'] ) {
                        $properties['font-weight']   = $field['values']['font_weight'];
                    }

                    if( isset($field['values']['color']) && $field['values']['color'] ) {
                        $properties['color']         = $field['values']['color'];
                    }                

                    // Text styles
                    $styles = array(
                        'italic'        => 'font-style', 
                        'line_through'  => 'text-decoration', 
                        'underline'     => 'text-decoration', 
                        'uppercase'     => 'text-transform', 
                        'text_align'    => 'text-align'
                    );

                    foreach( $styles as $key => $property ) {
                        
                        if( ! isset($field['values'][$key]) )
                            continue;                        

                        if( ! $field['values'][$key] )
                            continue;

                        $properties[$property] = $key == 'text_align' ? $field['values'][$key] : str_replace( '_', '-', $key );

                    }
                    
                }
                
                break;
               
        }
        
        /**
         * If we have a custom string or array property for the CSS, we us it with the values from the properties
         * This thus only works with some of the fields which have single properties (box-shadow, dimensions, colorpicker, media, image, upload) and not all fields
         */
        if( isset($field['selector']['property']) && count($properties) == 1 ) {
            $values     = implode('', $properties);
            $properties = array();

            if( is_array($field['selector']['property']) ) {
                foreach( $field['selector']['property'] as $property ) {
                    $properties[$property] = $values;
                }
            } else {
                $properties[$field['selector']['property']] = $values;
            }
        }
        
        // Only unique properties. Similar properties are overwritten by the last one.
        foreach($properties as $property => $value) {
            $uniques[$property] = $value;
        }     

        // Save the final properties to the fields array. This is then later processed to output css.
        $this->fields[$field['id']]['properties'] = $uniques;
        
    }
    
    /**
     * Determine the enqueueing of our custom fonts
     */
    public function customFonts() {
        
        // We should have fields with styles
        if( ! isset($this->fields) )
            return;
        
        $styles     = array();
        $weights    = array();
        
        // Build our styles.
        foreach( $this->fields as $field ) {
            
            // Only typographic fields are supported
            if( $field['type'] != 'typography') {
                continue;
            }
            
            foreach( $this->fonts as $key => $set ) {
                
                if( ! isset($field['values']['font']) || ! isset($set[$field['values']['font']]) )
                    continue;
                
                // Google fonts. Supports multiple fonts.
                if( $key == 'google' ) {

                    // Font weights, grouped per font so we can support multiple fonts settings with the same fonts, but different weights.
                    if( isset($field['values']['font_weight']) && $field['values']['font_weight'] && in_array($field['values']['font_weight'], $set[$field['values']['font']]['weights']) ) {
                        $italic = isset($field['values']['italic']) && $field['values']['italic'] && in_array('italic', $set[$field['values']['font']]['styles']) ? 'i' : '';
                        $weights[$field['values']['font']][] = $field['values']['font_weight'] . $italic; 
                    } else {
                        $weights[$field['values']['font']][] = 400;    
                    }
                    
                    /**
                     * Include all font weights overwrites previous weights
                     */
                    $italics = array();
                    $normals = array();
                    
                    // Normal fonts
                    if( isset($field['values']['load']['normal']) && $field['values']['load']['normal'] ) {
                        $normals = $set[$field['values']['font']]['weights'];
                        
                        // Weights are merged because we might have another italic weight.
                        $weights[$field['values']['font']] = array_merge( $weights[$field['values']['font']], $normals ); 
                    }  

                    // Italic fonts, if available
                    if( isset($field['values']['load']['italic']) && $field['values']['load']['italic'] && in_array('italic', $set[$field['values']['font']]['styles']) ) { 
                        foreach( $set[$field['values']['font']]['weights'] as $weight ) {
                            $italics[] = $weight . 'i'; 
                        }
                        
                        // Weights are merged because we might have another non-italic weight.
                        $weights[$field['values']['font']] = array_merge( $weights[$field['values']['font']], $italics ); 
                    }
                    
                    // Merge if we have them both
                    if( $normals && $italics ) {
                        $weights[$field['values']['font']] = array_merge( $normals, $italics );       
                    }
                    
                    // Our definite url. Updated if we have more loops....
                    $variants = implode(',', array_unique($weights[$field['values']['font']]) );
                    $styles[$field['values']['font']] = $set[$field['values']['font']]['name'] . ':' . $variants;
                 
                // Custom urls    
                } elseif( isset($set[$field['values']['font']]['url']) ) {    
                    $styles[$field['values']['font']] = $set[$field['values']['font']]['url'];
                }
            }
            
        }
        
        // Enqueue the styles.
        if( $styles ) {
            wp_enqueue_style( 'wp-custom-fields-fonts', 'https://fonts.googleapis.com/css?family=' . implode('|', $styles) );
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
                
                // The messages should be transported
                if( ! $field['transport'] ) {
                    continue;
                }
                
                $bind       = '';
                
                // The field should have properties, unless we have a content or attribute field
                if( $field['properties'] || isset($field['selector']['html']) || isset($field['selector']['attr']) ) {
                
                    $selector   = isset( $field['selector']['selector'] ) ? $field['selector']['selector'] : $field['selector'];
                    
                    // The output might differ per field type
                    if( isset($field['selector']['html']) ) {
                        $target = 'html(newValue)';    
                    } elseif( isset( $field['selector']['attr']) ) {
                        $target = 'attr("' . $field['selector']['attr'] . '", newValue)';
                    } elseif($field['properties']) {
                        $target = 'css("' . array_keys($field['properties'])[0] . '", newValue)';
                    }

                    $bind  .= 'value.bind( function( newValue ) {
                        $("' . $selector . '").' . $target . ';
                    } );';
                    
                    $script .= 'wp.customize( "' . $field['group'] . '[' . $field['id'] . ']' . '", function( value ) {
                        ' . $bind . '
                    } );';   

                }

            }

            // Our bindings script
            if( $script ) {
                echo '<script type="text/javascript" id="wp-custom-fields-customizer-js">( function( $ ) {' . $script . '} )( jQuery );</script>';
            }
            
        }, 100);
     
    }    
    
}