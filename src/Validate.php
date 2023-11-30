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
     * Displays an settings error message depending on the context, using the add_settings_error functionality
     * Use the get_settings_errors and settings_errors function to display given errors
     * 
     * @param array  $frame_options  The options for the frame to format
     * @param bool   $type           The type of error to add
     */
    public static function add_error_message( array $frame_options = [], string $type = 'update' ): void {
        
        // An setting ID is required (the id of the option page)
        if( ! $frame_options['id'] ) {
            return;
        }

        $messages = [
            'reset'     => isset($frame_options['messages']['reset']) ? $frame_options['messages']['reset'] : __('All settings are reset.', 'wpcf'),    
            'restore'   => isset($frame_options['messages']['restore']) ? $frame_options['messages']['restore'] : __('Settings restored for this section.', 'wpcf'),    
            'update'    => isset($frame_options['messages']['update']) ? $frame_options['messages']['update'] : __('Settings saved!', 'wpcf'),    
            'import'    => isset($frame_options['messages']['import']) ? $frame_options['messages']['import'] : __('Settings Imported!', 'wpcf')  
        ];

        switch( $type ) {
            case 'reset':
                add_settings_error( $frame_options['id'], 'wp-custom-fields-notification', $messages['reset'], 'info' );
                break;
            case 'restore':
                add_settings_error( $frame_options['id'], 'wp-custom-fields-notification', $messages['restore'], 'info' );
                break;  
            case 'update':
                add_settings_error( $frame_options['id'], 'wp-custom-fields-notification', $messages['update'], 'success' );
                break;                               
            case 'import':  
                add_settings_error( $frame_options['id'], 'wp-custom-fields-notification', $messages['import'], 'info' );
                break;  
        }

    }

    /**
     * Invokes the internal static method for generating an error message for network option pages
     * 
     * @param array  $frame_options  The options for the frame to format
     * @param bool   $type           The type of error to add
     */    
    public function add_network_error_message( array $frame_options = [], string $type = 'update' ): void {
        self::add_error_message($frame_options, $type);
    }
    
    /**
     * Formats the output by sanitizing and validating
     *
     * @param array $frame_options  The options for the frame to format
     * @param array $input          The $_POST $input generated
     * @param string $type           The type to format for, either options, user, post or term
     * 
     * @return array $output The validated and sanitized fields
     */
    public function format( array $frame_options, array $input, string $type = '' ): array {

        // Validate our users before formating any data
        if( ! is_user_logged_in() ) {
            return [];
        }

        if( $type == 'options' && ! current_user_can('manage_options') ) {
            return [];
        } 
        
        if( $type == 'user' && ! current_user_can('edit_users') ) {
            return [];  
        }
        
        if( $type == 'post' && (! current_user_can('edit_posts') || ! current_user_can('edit_pages')) ) {
            return [];
        } 
        
        if( $type == 'term' && (! current_user_can('edit_posts') || ! current_user_can('edit_pages')) ) {
            return [];
        }         
        
        // Checks in which tab we are
        $currentTab = strip_tags( $input['wp_custom_fields_section_' . $frame_options['id']] );
        
        // Sets the transient for the current section
        set_transient('wp_custom_fields_current_section_' . $frame_options['id'], $currentTab, 10); 
        
        /**
         * Restore the fields for the current section
         */
        if( isset($input[$frame_options['id'] . '_restore']) || isset($input[$frame_options['id'] . '_restore_bottom']) ) {
                                          
            foreach( $frame_options['sections'] as $section ) { 
                
                // Fields are just saved if the are not restored
                if( $section['id'] !== $currentTab ) {
                    
                    foreach($section['fields'] as $field) {
                        $output[$field['id']] = self::sanitize_fields( $input[$field['id']], $field );
                    } 
                    
                    continue;
                    
                }
                  
                foreach($section['fields'] as $field) {               
                    
                    $default = isset($field['default']) ? $field['default'] : '';
                    $output[$field['id']] = $default;
                    
                }               
                
            }
            
            // Add a notification for option pages
            if( $type === 'options' ) {
                self::add_error_message( $frame_options, 'restore' );
            }
            
            return $output;
            
        }
        
        /**
         * Restore all options
         */
        if( isset($input[$frame_options['id'] . '_reset']) ) {
            
            foreach($frame_options['sections'] as $section) {
                
                foreach($section['fields'] as $field) {                
                    
                    $default                = isset($field['default']) ? $field['default'] : '';
                    $output[$field['id']]   = $default;
                    
                }
                
            }
            
            if( $type === 'options' ) {
                self::add_error_message( $frame_options, 'reset' );
            }
            
            return $output;
        
        }   
        
        /**
         * Import data
         */
        if( isset($output['import_submit']) ) {
            
            $output = unserialize( base64_decode($input['import_value']) );
            
            if( $type === 'options' ) {
                self::add_error_message( $frame_options, 'import' );
            }
            
            return $output;

        }
        
        /**
         * Default formatting of data
         */
        foreach( $frame_options['sections'] as $section ) {

            foreach( $section['fields'] as $key => $field ) { 

                if( ! isset($input[$field['id']]) ) {
                    $input[$field['id']] = '';
                }
                
                $output[$field['id']] = self::sanitize_fields($input[$field['id']], $field);

            }
            
        }
        
        // Add default settings errors for option page (the update notification)
        if( $type === 'options' ) {
            self::add_error_message( $frame_options, 'update' );
        }
        
        return $output;
        
    }
    
    /**
     * Sanitizes our fields and looks if we have a repeatable field
     *
     * @param mixed    $input The input data for the field
     * @param array    $field The field array    
     * 
     * @param mixed    The sanitized output for all inputs and matching fields 
     */
    private static function sanitize_fields( $input, array $field ) {

        
        if( $field['type'] == 'repeatable' ) {
            
            $return = [];
            
            /**
             * Since our repeatable fields are draggable, keys may get sorted in JS. 
             * Altering the keys on the front-end breaks the value binding to fields, hence we reset the keys here based on the array order. 
             * 
             * Thus, for repeatable fields an array will be saved, where the keys always will match the field order.
             */
            $key = 0;
            foreach( $input as $group_values ) {
                foreach( $field['fields'] as $subfield ) {
                    $group_field_input = isset($group_values[$subfield['id']]) ? $group_values[$subfield['id']] : null;
                    $return[$key][$subfield['id']] = self::sanitize_field( $group_field_input, $subfield );
                }
                $key++;
            }
        } else {
            $return = self::sanitize_field( $input, $field );
        }
        
        return $return;
    }
    
    /**
     * Sanitizes input
     *
     * @param   mixed   $input  The input data for the field
     * @param   array   $field  The field array
     * @return  mixed   The sanized field
     */
    private static function sanitize_field( $input, array $field ) {

        if( ! isset($input) ) {
            return;
        }        
            
        $field_type     = $field['type'];
        $field_subtype  = isset($field['subtype']) ? $field['subtype'] : '';
        $field_value    = $input;
        
        // Switch for various field types
        switch($field_type) {
                
            // Background field
            case 'background':
                
                $texts = ['attachment', 'color', 'position', 'upload', 'repeat', 'size'];
                
                foreach( $texts as $text ) {
                    $return_value[$text] = isset($field_value[$text]) ? sanitize_text_field( $field_value[$text] ) : '';
                }
                break;
            
            // Border Field 
            case 'border':
                
                if( isset($field['borders']) && $field['borders'] == 'all' ) {
                    
                    $sides = ['top', 'right', 'bottom', 'left'];
                    
                    foreach($sides as $side) {
                        $return_value[$side]['color']           = isset($field_value[$side]['color']) ? sanitize_text_field( $field_value[$side]['color'] ) : ''; 
                        $return_value[$side]['style']           = isset($field_value[$side]['style']) ? sanitize_key( $field_value[$side]['style'] ) : '';                            
                        $return_value[$side]['width']['amount'] = isset($field_value[$side]['width']['amount']) ? intval( $field_value[$side]['width']['amount'] ) : '';
                        $return_value[$side]['width']['unit']   = isset($field_value[$side]['width']['unit']) ? sanitize_text_field( $field_value[$side]['width']['unit'] ): '';                       
                    }
                    
                } else {
                    $return_value['color']           = isset($field_value['color']) ? sanitize_text_field( $field_value['color'] ) : ''; 
                    $return_value['style']           = isset($field_value['style']) ? sanitize_key( $field_value['style'] ) : '';     
                    $return_value['width']['amount'] = isset($field_value['width']['amount']) ? intval( $field_value['width']['amount'] ) : '';
                    $return_value['width']['unit']   = isset($field_value['width']['unit']) ? sanitize_text_field( $field_value['width']['unit'] ) : '';
                }
                
                break;
                
            // Boxshadow field
            case 'boxshadow':
                
                $ints = ['x', 'y', 'blur', 'spread'];
                
                foreach($ints as $int) {
                    $return_value[$int] = isset($field_value[$int]) ? intval( $field_value[$int] ) : '';    
                }
                $return_value['color']  = isset($field_value['color']) ? sanitize_text_field( $field_value['color'] ) : '';
                $return_value['type']   = isset($field_value['type']) ? sanitize_text_field( $field_value['type'] ) : '';
                break;
                
            // Checkboxes
            case 'checkbox':
                
                if( isset($field['single']) && $field['single'] == true && is_array($field['options']) && count($field['options']) == 1 ) {
                    $return_value = isset($field_value) && $field_value == 'on' ? true : false;       
                } elseif( is_array($field['options']) ) {
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
                
                if( isset($field['borders']) && $field['borders'] == 'all' ) {
                    $sides = ['top', 'right', 'bottom', 'left'];
                    
                    foreach($sides as $side) {
                        $return_value[$side]['amount'] = isset($field_value[$side]['amount']) ? intval( $field_value[$side]['amount'] ) : '';
                        $return_value[$side]['unit']   = isset($field_value[$side]['unit']) ? sanitize_text_field( $field_value[$side]['unit'] ) : '';
                    }
                } else {
                    $return_value['amount'] = isset($field_value['amount']) ? intval( $field_value['amount'] ) : '';
                    $return_value['unit']   = isset($field_value['unit']) ? sanitize_text_field( $field_value['unit'] ) : '';
                }

                break;
                
            // Editor field    
            case 'editor':
            case 'html':
            case 'textarea':
                    
                global $allowedposttags;
                $return_value = wp_kses( $field_value, $allowedposttags );
                
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
                $return_value['lat']            = isset($field_value['lat']) ? floatval( $field_value['lat'] ) : '';
                $return_value['lng']            = isset($field_value['lng']) ? floatval( $field_value['lng'] ) : '';
                $return_value['number']         = isset($field_value['number']) ? sanitize_key( $field_value['number'] ) : '';
                $return_value['street']         = isset($field_value['street']) ? sanitize_text_field( $field_value['street'] ) : '';
                $return_value['city']           = isset($field_value['city']) ? sanitize_text_field( $field_value['city'] ) : '';
                $return_value['postal_code']    = isset($field_value['postal_code']) ? sanitize_text_field( $field_value['postal_code'] ) : '';
                $return_value['state']          = isset($field_value['state']) ? sanitize_text_field( $field_value['state'] ) : '';
                $return_value['country']        = isset($field_value['country']) ? sanitize_text_field( $field_value['country'] ) : '';
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

                $return_value                       = [];

                // Font-family
                $return_value['font']               = sanitize_text_field( $field_value['font'] );
                
                // Sizes
                $sizes                              = ['size', 'line_spacing'];
                foreach($sizes as $size) {
                    $return_value[$size]['amount']  = isset($field_value[$size]['amount']) && is_numeric( $field_value[$size]['amount'] ) ? intval( $field_value[$size]['amount'] ) : '';
                    $return_value[$size]['unit']    = isset($field_value[$size]['unit']) ? sanitize_text_field( $field_value[$size]['unit'] ) : '';                  
                }

                // Font-weight
                $return_value['font_weight']        = is_numeric( $field_value['font_weight'] ) ? intval( $field_value['font_weight'] ) : '';
                $return_value['color']              = isset($field_value['color']) ? sanitize_text_field( $field_value['color'] ) : '';
                $return_value['load']['normal']     = isset($field_value['load']['normal']) && $field_value['load']['normal'] == 'on' ? true : false;
                $return_value['load']['italic']     = isset($field_value['load']['italic']) && $field_value['load']['italic'] == 'on' ? true : false;
                
                // Styles
                $styles                             = ['italic', 'line_through', 'underline', 'uppercase', 'text_align'];
                foreach( $styles as $style ) {
                    $return_value[$style]           = isset( $field_value[$style] ) ? sanitize_key( $field_value[$style] ) : '';
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
     * @param   string $type        The field type;
     * @return  string $function    The built-in WordPress sanitization function
     */
    public static function sanitize_customizer_field( string $type = '' ): string { 
        
        switch( $type ) {
            case 'hidden':
            case 'tel':
            case 'search':
            case 'time':
            case 'date':
            case 'datetime':
            case 'week':
            case '[repeat]':
            case '[attachment]':
            case '[size]':
            case '[position]':
            case '[unit]':
            case '[line_spacing][unit]': 
            case '[size][unit]': 
            case 'number':
            case 'range':
            case '[amount]':      
            case '[line_spacing][amount]':         
            case '[size][amount]':         
                $sanitize = 'sanitize_text_field';
                break; 
            case 'html':                
            case 'text':                
            case 'textarea':
                $sanitize = 'wp_kses_data';
                break;
            case 'email':
                $sanitize = 'sanitize_email';
                break;
            case 'code-editor': 
                $sanitize = 'sanitize_textarea_field';
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
    public function validate_configurations( array $options = [], array $required = [] ) {

        foreach( $required as $requirement ) {
            if( ! isset($options[$requirement]) ) {
                return new WP_Error( 
                    'wrong', 
                    sprintf( __('The configurations for one of your custom options are missing a required attribute: %s.', 'wpcf'), $requirement )
                );    
            }
        }

        return true;

    }
   
}