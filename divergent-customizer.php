<?php 
/** 
 * This class is responsible for controlling the display of the theme options page
 * 
 * @author Michiel
 * @since 1.0.0
 */
namespace Divergent;
use WP_Customize_Color_Control as WP_Customize_Color_Control;
use WP_Customize_Cropped_Image_Control as WP_Customize_Cropped_Image_Control;
use WP_Customize_Media_Control as WP_Customize_Media_Control;
use WP_Customize_Upload_Control as WP_Customize_Upload_Control;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class Divergent_Customizer extends Divergent_Abstract {    
        
    /**
     * Constructor
     */    
    protected function initialize() {
        $this->panel = $this->params;    
    }
    
    /**
     * Register WordPress Hooks
     */
    protected function registerHooks() {
        
        $this->actions = array(
            array('customize_register', 'addSettings', 20, 1),
        );       
    }
    
    /**
     * Adds the settings using the settings api
     * Built in types: input (text, hidden, number, range, url, tel, email, search, time, date, datetime, week), checkbox, textarea, radio, select, dropdown-pages, range
     *
     * @param $wp_customize The WP Customize Object
     */
    public function addSettings( $wp_customize ) {
        
        // Check
        $panel = $this->panel;
        
        if( ! isset($panel['id']) )
            return; 

        $panelArgs = array(
            'title'         => $panel['title']                
        );

        if( isset($panel['description']) )
            $panelArgs[ 'description'] = $panel['description'];            

        // Add our panel
        $wp_customize->add_panel( $panel['id'], $panelArgs );

        foreach( $panel['sections'] as $section ) {

            // Check
            if( ! isset($section['id']) || ! isset($section['title']) )
                continue;

            $sectionArgs = array(
                'description'   => isset($section['description']) ? $section['description'] : '', 
                'panel'         => $panel['id'], 
                'title'         => $section['title']                    
            );
            
            if( isset($section['active_callback']) )
                $sectionArgs[ 'active_callback']    = $section['active_callback'];            

            // Add our section
            $wp_customize->add_section( $section['id'], $sectionArgs );

            foreach( $section['fields'] as $field ) {             

                // Check required fields
                if( ! isset($field['id']) || ! isset($field['default']) || ! isset($field['type']) || ! isset($field['title']) )
                    continue; 

                $settingArgs = array(
                    'default' => $field['default'],
                    'type'    => isset( $panel['option'] ) ? $panel['option'] : 'theme_mod'
                );
                
                // Transport our updates with partial refresh
                if( isset($field['transport']) )
                    $settingArgs['transport']             = $field['transport'];                

                if( isset($field['sanitize']) )
                    $settingArgs['sanitize_callback']     = $field['sanitize'];

                if( isset($field['sanitize_js']) )
                    $settingArgs['sanitize_js_callback']  = $field['sanitize_js'];                    

                // Add our settings
                $wp_customize->add_setting( $panel['id'] . '[' . $field['id'] . ']', $settingArgs );

                // Define our arguments for the controls
                $controlArgs                = array();
                $controlArgs['section']     = $section['id'];
                $controlArgs['label']       = $field['title'];
                $controlArgs['settings']    = $panel['id'] . '[' . $field['id'] . ']'; // This is required for custom classes
                
                // Define our additional control arguments
                $controls = array('choices', 'description', 'height', 'input_attrs', 'mime_type', 'settings', 'type', 'width');
                
                foreach( $controls as $type ) {
                    if( isset($field[$type]) ) 
                        $controlArgs[$type] = $field[$type];
                }
                
                // Custom types
                switch( $field['type'] ) {
                    case 'colorpicker':
                        unset($controlArgs['type']); // Having a defined type breaks the color picker somehow
                        $wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, $panel['id'] . '[' . $field['id'] . ']', $controlArgs) ); 
                        break;                    
                    case 'image':
                        $wp_customize->add_control( new WP_Customize_Cropped_Image_Control($wp_customize, $panel['id'] . '[' . $field['id'] . ']', $controlArgs) ); 
                        break;                    
                    case 'media':
                        $wp_customize->add_control( new WP_Customize_Media_Control($wp_customize, $panel['id'] . '[' . $field['id'] . ']', $controlArgs) );
                        break;                    
                    case 'upload':
                        $wp_customize->add_control( new WP_Customize_Upload_Control($wp_customize, $panel['id'] . '[' . $field['id'] . ']', $controlArgs) );
                        break;                    
                    case 'custom':
                        $wp_customize->add_control( new $field['custom']($wp_customize, $panel['id'] . '[' . $field['id'] . ']', $controlArgs) ); 
                        break;
                    default:
                        $wp_customize->add_control( $panel['id'] . '[' . $field['id'] . ']', $controlArgs );
                }

                // Register our partials
                if( isset($field['partial']) )
                    $wp_customize->selective_refresh->add_partial( $panel['id'] . '[' . $field['id'] . ']', $field['partial'] );

            }

        }
        
    }
    
    
    /**
     * Default fallback for validation
     * 
     * @parram array $validity  The The validity of the setting
     * @parram array $value     The value passed
     */
    public function validateCustomizerField( $validity, $value ) {
        
    }
    
    /**
     * Default fallback for sanitization
     * 
     * @parram array $value     The value passed
     */
    public function sanitizeCustomizerField( $value ) {

    }
    
}