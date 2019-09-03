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
     * Contains an option frame, whether it is a customizer, options page or meta box frame
     */
    private $frame;   
    
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

        $this->actions = [
            ['updated_option', 'saveFields', 20, 1],
            ['save_post', 'saveFields', 20, 1],
            ['customize_save_after', 'saveFields', 20, 1],
            ['wp_head', 'retrieveFields'],                  // Retrieve the fields
            ['wp_head', 'outputCSS', 20],                   // Render the output
            ['wp_enqueue_scripts', 'customFonts'],          // Enqueue fonts, if necessary
            ['customize_preview_init', 'customizerJS']      // Enqueue JS for customizer, if necessary
        ];

    }

    /**
     * Retrieves all frames, converts them into fields with css properties and saves this array within the database
     * @param mixed     $key    A certain key, id or hooked argument, based upon the hook
     * @param string    $hook   A hook string, to manually trigger a certain save action from outside the hooks
     */
    public function saveFields( $key, $hook = '' ) {

        // If we're saving fields, look what hook we're in
        $hook   = $hook ? $hook : current_filter();

        /**
         * For the option hook, we only want to save our css fields if we're targeting the ID of an option page added by WPCF.
         * Because this hook is widely employed, we want to add this safety measure
         */
        if( $hook == 'updated_option' ) {

            $update = false;

            // Retrieve all our options frames, so we can filter for the given hook
            $optionFrames = Framework::instance()->get('options');

            foreach( $optionFrames as $page ) {
                
                if( isset($page['id']) && $key == $page['id'] ) {
                    $update = true;
                }

            }

            // Check if we may update our option
            if( ! $update ) {
                return;   
            }            

        }

        // Generates the fields with the correct css properties from all configurations
        $this->setFields( $hook, $key );

        // Do we have fields any fields at all?
        if( ! isset($this->fields) ) {
            return;
        }  

        // Save the fields depending on their context
        switch( $hook ) {
            case 'updated_option':
                // Check our user capabilities
                if( ! current_user_can('manage_options', $key) ) {
                    return;
                }              

                update_option('wpcf_options_css_fields', $this->fields, 'no');            
                break;
            case 'customize_save_after':
                // Check our user capabilities before doing something
                if( ! current_user_can('manage_options', $key) ) {
                    return;
                }              

                update_option('wpcf_customizer_css_fields', $this->fields, 'no');            
                break;
            case 'save_post':
                // Check our user capabilities
                if( ! current_user_can('edit_posts', $key) || ! current_user_can('edit_pages', $key) ) {
                    return;
                }

                update_post_meta( $key, 'wpcf_singular_css_fields', $this->fields );            
                break;
        }

    }
    
    /**
     * Examines the frames for CSS related suggestions and sets the correct fields
     * @param string    $hook   The hook by which the parent function was triggered. Accepts 'customize_save_after', 'updated_option' or 'save_post'
     * @param mixed     $key    Possible post id, option key or WP Customizer object
     */
    private function setFields( $hook = '', $id = '' ) {

        // A hook is mandatory
        if( ! $hook ) {
            return;
        }
        
        // Retrieve the array of groups with fields
        switch( $hook ) {
            case 'customize_save_after':
                $this->frame = Framework::instance()->get('customizer');
                break;
            case 'updated_option':
                $this->frame = Framework::instance()->get('options');
                break; 
            case 'save_post':
                $this->frame = Framework::instance()->get('meta');
                break;                               
        }
        
        // We don't have any frames or properly formatted frames
        if( ! isset($this->frame) || ! $this->frame || ! is_array($this->frame) ) {
            return;
        }

        foreach( $this->frame as $group ) {
                
            // Each group should have any id
            if( ! isset($group['id']) ) {
                continue;
            }
            
            foreach( $group['sections'] as $section ) {
                
                if( ! isset($section['id']) )
                    continue;  
            
                // Loop through our fields and see if some have a CSS target defined
                foreach( $section['fields'] as $key => $field ) {

                    // A selector should be defined. A selector supports selector, size (for the background thumbnail size), max-width (for the max-width media query), property
                    if( ! isset($field['selector']) ) {
                        continue;
                    }

                    // Save the id per group so we can retrieve the values later.
                    $cssFields[$field['id']] = [
                        'selector'  => $field['selector'],
                        'type'      => $field['type']
                    ];

                    if( $hook == 'customize_save_after' ) {
                        $cssFields[$field['id']]['group']       = $group['id'];
                        $cssFields[$field['id']]['transport']   = isset($field['transport']) ? true : false;
                    }

                }
                
            }
            
            /**
             * Now, retrieve our values from the database, but ony if we have fieldswith a selector specified
             */
            if( ! isset($cssFields) ) {
                continue;
            }

            // Retrieve the actual values of our fields
            switch( $hook ) {
                case 'customize_save_after':
                    $values     = isset($group['option']) ? get_option($group['option']) : get_theme_mod($group['id']);
                    break;
                case 'updated_option':
                    $values     = get_option($group['id']);
                    break; 
                case 'save_post':

                    // Single metaboxes
                    if( isset($group['single']) && $group['single'] && is_array($group['sections']) ) {
                        
                        $values = [];
                        foreach( $group['sections'] as $section ) {
                            
                            if( ! isset($section['fields']) || ! is_array($section['fields']) ) {
                                continue;    
                            }

                            foreach( $section['fields'] as $field ) {
                                
                                if( ! isset($field['id']) || ! isset($field['selector']) ) {
                                    continue;    
                                }

                                $values[$field['id']] = get_metadata( $group['type'], $id, $field['id'], true );

                            }
                                
                        } 

                    // Default metaboxes
                    } else {
                        $values     = get_metadata( $group['type'], $id, $group['id'], true );
                    }

                    break; 
                default:
                    $values = [];   
            } 
            
           

            /**
             * Loop again through our fields and see if we have values. 
             * If similar field ids exist in the same frame, only the first one can be used.
             */
            foreach( $cssFields as $fieldID => $field ) {

                if( isset( $values[$fieldID] ) && $values[$fieldID] ) {
                    $field['values'] = $values[$fieldID];
                }
                
                // Because we loop through all our frames looking for values, we might add the same field twice. 
                if( isset($this->fields[$fieldID]) ) {
                    continue;
                }

                $this->fields[$fieldID] = $field;

            }                 
            
        }

        // By now, we should have fields with styles
        if( ! isset($this->fields) || ! $this->fields ) {
            return;
        }
        
        // Formats the fields, an also strips unnecessary keys (at the given point)
        foreach( $this->fields as $fieldID => $field ) {     
            
            $this->formatField($field, $fieldID);

        }        
 
    }


   /**
     * Formats the css based upon a fields type values
     *
     * @param array    $field   The field type, including its values
     * @param string   $fieldID The string for the field id
     */
    private function formatField( $field, $fieldID ) {
        
        // Default values;
        $uniques            = [];
        $properties         = [];
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
                    $styles = [
                        'italic'        => 'font-style', 
                        'line_through'  => 'text-decoration', 
                        'underline'     => 'text-decoration', 
                        'uppercase'     => 'text-transform', 
                        'text_align'    => 'text-align'
                    ];

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
            $properties = [];

            if( is_array($field['selector']['property']) ) {
                foreach( $field['selector']['property'] as $property ) {
                    $properties[$property] = $values;
                }
            } else {
                $properties[$field['selector']['property']] = $values;
            }
        }
        
        // Only unique properties. Similar properties are overwritten by the last one for the given selector.
        foreach($properties as $property => $value) {
            $uniques[$property] = $value;
        }     

        // Our values have been transferred to properties, and are no longer needed
        unset($this->fields[$fieldID]['values']);

        // Save the final properties to the fields array. This is then later processed to output css.
        $this->fields[$fieldID]['properties'] = $uniques;
        
    }    

    /**
     * Retrieves our css fields and properties from the database.
     * Also used in later functions (customFonts and customizerJS) to load specific assets.
     */
    public function retrieveFields() {

        /**
         * Load our customizer fields manually if we're previewing. 
         * This enables us to output JS binding functions even before these fields have been added
         * to the database, and also render this output before these fields have been saved.
         * 
         * Otherwise, we just load the values from the database
         */
        if( is_customize_preview() ) {
            $this->setFields('customize_save_after');
        } else {
            $customizerValues   = maybe_unserialize( get_option('wpcf_customizer_css_fields') );
            $this->fields       = is_array($customizerValues) ? $customizerValues : [];
        }

        /**
         * Loads our option page fields
         */
        $optionValues   = maybe_unserialize( get_option('wpcf_options_css_fields') );
        $optionFields   = is_array($optionValues) ? $optionValues: [];
        $this->fields   = $this->fields + $optionFields;       

        /**
         * Loads the fields for singular templates
         */
        if( is_singular() ) {
            global $post;
            $metaValues     = get_post_meta( $post->ID, 'wpcf_singular_css_fields', true );
            $metaFields     = is_array($metaValues) ? $metaValues : [];
            $this->fields   = $this->fields + $metaFields;
        }

        // No fields? Do nothing!
        if( ! $this->fields ) {
            return;            
        }   

    }    
    
    /**
     * Retrieve values if we have css fields for them.
     */
    public function outputCSS() {

        // We should have fields with styles
        if( ! isset($this->fields) || ! $this->fields ) {
            return;            
        }
             
        $style      = '';
        
        // Loop through our fields that have CSS attributes and values
        foreach( $this->fields as $key => $field ) {
            
            $properties = '';            
            
            // Some fields are used in the customizer to update content. Those are skipped here.
            if( isset($field['selector']['html']) || isset($field['selector']['attr']) ) {
                continue;
            }

            // Only add the style if we have values for it 
            foreach( $field['properties'] as $property => $value ) {

                // Skip properties without value
                if( ! $value ) {
                    continue;
                }

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
     * Determine the enqueueing of our custom fonts
     */
    public function customFonts() {
        
        // We should have fields with styles
        if( ! isset($this->fields) || ! $this->fields ) {
            return;            
        }
        
        $styles     = [];
        $weights    = [];
        
        // Build our styles.
        foreach( $this->fields as $field ) {
            
            // Only typographic fields are supported
            if( $field['type'] != 'typography') {
                continue;
            }

            // Retrieve the fonts from our framework
            if( ! isset($this->fonts) ) {
                $this->fonts = Framework::$fonts;  
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
                    $italics = [];
                    $normals = [];
                    
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
        if( ! is_customize_preview() ) {
            return; 
        }
        
        add_action('wp_footer', function() {
            
            // We should have fields
            if( ! isset($this->fields) || ! $this->fields ) {
                return;
            }
        
            $script = '';

            // Format our fields
            foreach( $this->fields as $id => $field ) {
                
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
                    
                    $script .= 'wp.customize( "' . $field['group'] . '[' . $id . ']' . '", function( value ) {
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