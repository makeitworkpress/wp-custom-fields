<?php
/**
 * Container class that contains helper functions for validating and saving user input.
 *
 * @author Michiel
 * @package WP_Custom_Fields
 * @since 1.0.0
 */
namespace MakeitWorkPress\WP_Custom_Fields;
use WP_Error as WP_Error;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

trait Validate {
    
    /**
     * Formats the output by sanitizing and validating
     *
     * @param array $frame   The frame to format
     * @param array $input   The $_Post $input generated
     * @param array $type    The type to format for
     */
    public static function format( $frame, $input, $type = '' ) {

        // Validate our users before formating any data
        if( ! is_user_logged_in() ) {
            return;
        }

        if( $type == 'options' && ! current_user_can('manage_options') ) {
            return;
        } 
        
        if( $type == 'user' && ! current_user_can('edit_users') ) {
            return;  
        }
        
        if( $type == 'post' && (! current_user_can('edit_posts') || ! current_user_can('edit_pages')) ) {
            return;
        } 
        
        if( $type == 'term' && (! current_user_can('edit_posts') || ! current_user_can('edit_pages')) ) {
            return;
        }         
        
        // Checks in which tab we are
        $currentTab = strip_tags( $input['wp_custom_fields_section_' . $frame['id']] );
        
        // Sets the transient for the current section
        set_transient('wp_custom_fields_current_section_' . $frame['id'], $currentTab, 10); 
        
        /**
         * Restore the fields for a current section
         */
        if( isset($input[$frame['id'] . '_restore']) ) {
                                          
            foreach( $frame['sections'] as $section ) { 
                
                // Fields are just saved if the are not restored
                if( $section['id'] !== $currentTab ) {
                    
                    foreach($section['fields'] as $field) {
                        $output[$field['id']] = self::sanitizeFields( $input[$field['id']], $field );
                    } 
                    
                    continue;
                    
                }
                  
                foreach($section['fields'] as $field) {               
                    
                    $default = isset($field['default']) ? $field['default'] : '';
                    $output[$field['id']] = $default;
                    
                }               
                
            }
            
            // Add a notification for option pages
            if( $type == 'options' ) {
                add_settings_error( $frame['id'], 'wp-custom-fields-notification', __('Settings restored for this section.', 'wp-custom-fields'), 'update' );
            }
            
            return $output;
            
        }
        
        /**
         * Restore the complete section
         */
        if( isset($input['wp_custom_fields_options_reset']) ) {
            
            foreach($frame['sections'] as $section) {
                
                foreach($section['fields'] as $field) {                
                    
                    $default                = isset($field['default']) ? $field['default'] : '';
                    $output[$field['id']]   = $default;
                    
                }
                
            }
            
            if( $type == 'options' ) {
                add_settings_error( $frame['id'], 'wp-custom-fields-notification', __('All settings are restored.', 'wp-custom-fields'), 'update' );
            }
            
            return $output;
        
        }   
        
        /**
         * Import data
         */
        if( isset($output['import_submit']) ) {
            
            $output = unserialize( base64_decode($input['import_value']) );
            
            if( $type == 'options' ) {
                add_settings_error( $frame['id'], 'wp-custom-fields-notification', __('Settings Imported!', 'wp-custom-fields'), 'update' );
            }
            
            return $output;

        }
        
        /**
         * Default formatting of data
         */
        foreach( $frame['sections'] as $section ) {

            foreach( $section['fields'] as $key => $field ) { 

                if( ! isset($input[$field['id']]) ) {
                    $input[$field['id']] = '';
                }
                
                $output[$field['id']] = self::sanitizeFields($input[$field['id']], $field);

            }
            
        }
        
        if( $type == 'options' ) {
            add_settings_error( $frame['id'], 'wp-custom-fields-notification', __('Settings saved!', 'wp-custom-fields'), 'update' );
        }
        
        return $output;
        
    }
    
    /**
     * Sanitizes our fields and looks if we have a repeatable field
     *
     * @param array     $input The input data for the field
     * @param string    $field The field array     
     */
    private static function sanitizeFields( $input, $field ) {
        
        if( $field['type'] == 'repeatable' ) {
            
            foreach( $input as $key => $groupValues ) {
            
                foreach( $field['fields'] as $subfield ) {
                    $return[$key][$subfield['id']] = self::sanitizeField( $groupValues[$subfield['id']], $subfield );
                }
                
            }
        } else {
            $return = self::sanitizeField( $input, $field );
        }
        
        return $return;
    }
    
    /**
     * Sanitizes input
     *
     * @param array     $input The input data for the field
     * @param string    $field The field array
     *
     * @todo Improve sanitization for borders and special types
     */
    private static function sanitizeField( $input, $field ) {
            
        $field_type     = $field['type'];
        $field_subtype  = isset($field['subtype']) ? $field['subtype'] : '';
        $field_value    = $input;
        
        // Switch for various field types
        switch($field_type) {
                
            // Background field
            case 'background':
                
                $texts = ['attachment', 'color', 'position', 'upload', 'repeat', 'size'];
                
                foreach( $texts as $text ) {
                    $return_value[$text] = sanitize_text_field( $field_value[$text] );    
                }
                break;
            
            // Border Field 
            case 'border':
                
                if( isset($field['borders']) && $field['borders'] == 'all' ) {
                    
                    $sides = ['top', 'right', 'bottom', 'left'];
                    
                    foreach($sides as $side) {
                        $return_value[$side]['color']           = sanitize_text_field( $field_value[$side]['color'] ); 
                        $return_value[$side]['style']           = sanitize_key( $field_value[$side]['style'] );                            
                        $return_value[$side]['width']['amount'] = intval( $field_value[$side]['width']['amount'] );
                        $return_value[$side]['width']['unit']   = sanitize_text_field( $field_value[$side]['width']['unit'] );                       
                    }
                    
                } else {
                    $return_value['color']           = sanitize_text_field( $field_value['color'] ); 
                    $return_value['style']           = sanitize_key( $field_value['style'] );     
                    $return_value['width']['amount'] = intval( $field_value['width']['amount'] );
                    $return_value['width']['unit']   = sanitize_text_field( $field_value['width']['unit'] );                                      
                }
                
                break;
                
            // Boxshadow field
            case 'boxshadow':
                
                $ints = ['x', 'y', 'blur', 'spread'];
                
                foreach($ints as $int) {
                    $return_value[$int] = isset($field_value[$int]) ? intval( $field_value[$int] ) : '';    
                }
                $return_value['color']  = sanitize_text_field( $field_value['color'] );
                $return_value['type']   = sanitize_text_field( $field_value['type'] );
                break;
                
            // Checkboxes
            case 'checkbox':
                
                if( isset($field['single']) && $field['single'] == true && count($field['options']) == 1 ) {
                    $return_value = isset($field_value) && $field_value == 'on' ? true : false;       
                } else {
                    foreach( $field['options'] as $key => $option ) {
                        $return_value[$key] = isset($field_value[$key]) && $field_value[$key] == 'on' ? true : false;
                    }
                }
                break;
                 
            // Code field    
            case 'code':
                $return_value = htmlentities(stripslashes($field_value));
                break; 
                
            // Colorpicker     
            case 'colorpicker':
                $return_value = sanitize_text_field( $field_value ); 
                break;
                
            // Border Values
            case 'dimensions':
            case 'dimension':
                
                if( isset($field['border']) && $field['border'] == 'all' ) {
                    $sides = ['top', 'right', 'bottom', 'left'];
                    
                    foreach($sides as $side) {
                        $return_value[$side]['amount'] = intval( $field_value[$side]['amount'] );
                        $return_value[$side]['unit']   = sanitize_text_field( $field_value[$side]['unit'] );                       
                    }
                } else {
                    $return_value['amount'] = intval( $field_value['amount'] );
                    $return_value['unit']   = sanitize_text_field( $field_value['unit'] );                    
                }

                break;
                
            // Editor field    
            case 'editor':
            case 'textarea':
                    
                global $allowedposttags;
                $return_value = wp_kses( $field_value, $allowedposttags );
                
                break;  
                
            // Editor field    
            case 'gallery':
                $return_value = $return_value;
                break; 
                 
            // Editor field    
            case 'icons':
                
                // Multiple iceons
                if( is_array($field_value) ) {
                    foreach($field_value as $value) {
                        $return_value[] = sanitize_key( $value );
                    }
                }
                
                $return_value = sanitize_key( $field_value );
                break;
                
            // Editor field    
            case 'location':
                $return_value['lat']            = floatval( $field_value['lat'] );
                $return_value['lng']            = floatval( $field_value['lng'] );
                $return_value['number']         = sanitize_key( $field_value['number'] );
                $return_value['street']         = sanitize_text_field( $field_value['number'] );
                $return_value['city']           = sanitize_text_field( $field_value['number'] );
                $return_value['postal_code']    = sanitize_key( $field_value['number'] );
                break; 
              
            // Media field
            case 'media':
                $return_value = sanitize_text_field( $field_value );
                break;
                
            // Media field
            case 'radio':
                $return_value = sanitize_key( $field_value );
                break;                
                
            // Select field    
            case 'select':
                
                // Multiple iceons
                if( is_array($field_value) ) {
                    $return_value       = [];
                    foreach($field_value as $value) {
                        $return_value[] = sanitize_text_field($value);
                    }
                } else {                  
                    $return_value = sanitize_text_field( $field_value );
                }
                break; 
                
            // Slider
            case 'slider':
                $return_value = floatval( $field_value );  
                break;
                
            // Default textual input
            case 'text':
                switch($field_subtype) {
                    case 'url':
                        $return_value = esc_url_raw( $field_value ); 
                        break;
                    case 'email':
                        $return_value = sanitize_email( $field_value ); 
                        break;
                    case 'number':
                        $return_value = floatval( $field_value );               
                        break;
                    default:
                        $return_value = sanitize_text_field( $field_value );    
                }
                break;
            
            // Typographic field
            case 'typography':

                
                // Font-family
                $return_value['font']               = sanitize_text_field( $field_value['font'] );
                
                // Sizes
                $sizes                              = ['size', 'line_spacing'];
                foreach($sizes as $size) {
                    $return_value[$size]['amount']  = is_numeric( $field_value[$size]['amount'] ) ? intval( $field_value[$size]['amount'] ) : '';
                    $return_value[$size]['unit']    = sanitize_text_field( $field_value[$size]['unit'] );                  
                }

                // Font-weight
                $return_value['font_weight']        = is_numeric( $field_value['font_weight'] ) ? intval( $field_value['font_weight'] ) : '';
                $return_value['color']              = sanitize_text_field( $field_value['color'] );
                $return_value['load']['normal']     = isset($field_value['load']['normal']) && $field_value['load']['normal'] == 'on' ? true : false;
                $return_value['load']['italic']     = isset($field_value['load']['italic']) && $field_value['load']['italic'] == 'on' ? true : false;
                
                // Styles
                $styles                             = ['italic', 'line_through', 'underline', 'uppercase', 'text_align'];
                foreach( $styles as $style ) {
                    $return_value[$style]           = sanitize_key( $field_value[$style] );     
                }
                
                break; 
                
            // Default sanitization            
            default:
                $return_value = sanitize_text_field($field_value);
        }
        
        return apply_filters( 'wp_custom_fields_sanitized_value', $return_value, $field_value, $field );
        
    }

    /**
     * Returns the correct sanitization for any customizer fields
     * 
     * @param   array $field        The field type;
     * @return string $function     The built-in WordPress sanitization function
     */
    public static function sanitizeCustomizerField( $type = '' ) { 
        
        switch( $type ) {
            case 'hidden':
            case 'tel':
            case 'search':
            case 'time':
            case 'date':
            case 'datetime':
            case 'week':
            case '[unit]': // Used for the dimensions field, as it has multidimensional settings  
            case '[line_spacing][unit]': 
            case '[size][unit]': 
            case 'number':
            case 'range':
            case '[amount]': // Used for the dimensions field, as it has multidimensional settings            
            case '[line_spacing][amount]':         
            case '[size][amount]':         
                $sanitize = 'sanitize_text_field';
                break; 
            case 'text':                
            case 'textarea':
                $sanitize = 'wp_filter_kses';
                break;
            case 'email':
                $sanitize = 'sanitize_email';
                break;
            case 'cropped-image':
            case 'image':
            case 'upload':                
            case 'url':
                $sanitize = 'esc_url_raw';
                break;  
            case 'colorpicker':
            case '[color]':
                $sanitize = 'sanitize_hex_color';
                break;
            case 'dropdown-pages':               
            case 'media':
            case '[font_weight]':                                                     
            case 'select':
            case 'radio':
            case '[font]':
            case '[text_align]':
                $sanitize = 'sanitize_key';
                break;
            // case 'checkbox':                
            // case '[italic]': 
            // case '[load][italic]':
            // case '[load][normal]':
            // case '[underline]':
            // case '[uppercase]':                                        
            //     $sanitize = 'sanitize_key'; // boolval may give problems in some instances, a sanitized key also evals to true.                       
            default:
                $sanitize = 'sanitize_text_field';
        }

        return $sanitize;
    
    }

    /**
     * Examines the correctness of our configurations
     * 
     * @param array $options    The array with configurations
     * @param array $required   The array with required configuration keys
     * @return WP_Error|true    True if we pass the test, a WP_Error if we fail
     */
    public static function configurations( $options = [], $required = [] ) {

        foreach( $required as $requirement ) {
            if( ! isset($options[$requirement]) ) {
                return new WP_Error( 
                    'wrong', 
                    sprintf( __('The configurations for one of your custom options are missing a required attribute: %s.', 'wp-custom-fields'), $requirement )
                );    
            }
        }

        return true;

    }
   
}