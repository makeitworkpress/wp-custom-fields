<?php 
/** 
 * This class is responsible for controlling the display of the theme options page
 * 
 * @author Michiel
 * @since 1.0.0
 */
namespace MakeitWorkPress\WP_Custom_Fields;
use WP_Error as WP_Error;
use WP_Customize_Color_Control as WP_Customize_Color_Control;
use WP_Customize_Cropped_Image_Control as WP_Customize_Cropped_Image_Control;
use WP_Customize_Image_Control as WP_Customize_Image_Control;
use WP_Customize_Media_Control as WP_Customize_Media_Control;
use WP_Customize_Upload_Control as WP_Customize_Upload_Control;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class Customizer {
      
    /**
     * Contains the option values for each of the panels
     * @access public
     */
    public $panel; 
    
    /**
     * Examines if we have validated
     * @access public
     */
    public $validated = false;     
        
    /**
     * Constructor
     *
     * @param array $group      The array with settings, sections and fields 
     * @return WP_Error|void    Returns a WP_Error if something is wrong in the configurations, otherwise nothing    
     */    
    public function __construct( $group = array() ) {
        $this->panel = $group;

        // Validate for errors
        if( ! isset($group['id']) || ! isset($group['sections']) ) {
            $this->validated = new WP_Error( 'wrong', __( 'Your customizer configurations are missing sections or an id.', 'wp-custom-fields' ) ); 
        }
    
        // Prohibited names
        if( in_array($group['id'], array('widget_', 'sidebars_widgets', 'nav_menu', 'nav_menu_item')) ) {
            $this->validated = new WP_Error( 'wrong', __( 'It is forbidden to use widget_, sidebars_widget, nav_menu or nav_menu_item for customizer ids.', 'wp-custom-fields' ) );
        }

        if( is_wp_error($this->validated) ) {
            return;
        }

        $this->registerHooks();

    }
    
    /**
     * Register WordPress Hooks
     */
    protected function registerHooks() {
        add_action( 'customize_register', array($this, 'addSettings') );
        add_action( 'admin_enqueue_scripts', array($this, 'enqueue') );             
    }
    
    /**
     * Enqueue custom scripts used in the customizer
     */
    public function enqueue() {
        
        // Load the select2 script, but only if not yet enqueued
        if( apply_filters('wp_custom_fields_select_field_js', true) && ! wp_script_is('select2-js', 'enqueued') )
            wp_enqueue_script('select2-js');        
        
        if( ! wp_script_is('wp-custom-fields-js') )
            wp_enqueue_script('wp-custom-fields-js');
        
    }
    
    /**
     * Adds the settings using the settings api
     * Built in types: input (text, hidden, number, range, url, tel, email, search, time, date, datetime, week), 
     * checkbox, textarea, radio, select, dropdown-pages, range
     *
     * @param object $wp_customize The WP Customize Object
     *
     * @return void
     */
    public function addSettings( $wp_customize ) {
        
        // Check
        $panel = $this->panel;

        /**
         * Add our panel
         * This is optional, because it is not advertised to use panels by default
         * according to the customizer codex
         */
        if( isset($panel['panel']) && $panel['panel'] ) {
            
            $panelArgs = array(
                'title'         => $panel['title']                
            );

            if( isset($panel['description']) )
                $panelArgs[ 'description'] = $panel['description'];              

            $wp_customize->add_panel( $panel['id'], $panelArgs );
        
        
        }

        /**
         * Add new sections or add settings to existing (core) settings
         */
        foreach( $panel['sections'] as $section ) {

            // Check
            if( ! isset($section['id']) || ! isset($section['title']) )
                continue;

            $sectionArgs = array(
                'description'   => isset($section['description']) ? $section['description'] : '',  
                'title'         => $section['title']                    
            );
            
            // If we have panels enabled, we add the section to this panel
            if( isset($panel['panel']) && $panel['panel'] )
                $sectionArgs['panel'] = $panel['id'];
            
            // Accepts string parameters such as 'is_page' or 'is_single' to hide sections conditionally
            if( isset($section['active_callback']) )
                $sectionArgs[ 'active_callback']    = $section['active_callback'];            

            // Add our section, but not necessarely if it is a core section
            if( ! in_array($section['id'], array('themes', 'title_tagline', 'colors', 'header_image', 'background_image', 'static_front_page')) )
                $wp_customize->add_section( $section['id'], $sectionArgs );

            foreach( $section['fields'] as $field ) {             

                // Check required fields
                if( ! isset($field['id']) || ! isset($field['type']) || ! isset($field['title']) )
                    continue; 

                $settingArgs = array(
                    'default' => isset($field['default']) ? $field['default'] : '',
                    'type'    => isset( $panel['option'] ) ? $panel['option'] : 'theme_mod'
                );
                
                // Transport our updates with partial refresh
                if( isset($field['transport']) )
                    $settingArgs['transport']             = $field['transport'];                

                if( isset($field['sanitize']) )
                    $settingArgs['sanitize_callback']     = $field['sanitize'];

                if( isset($field['sanitize_js']) )
                    $settingArgs['sanitize_js_callback']  = $field['sanitize_js'];                    

                /**
                 * Add our settings. Elaborate controls have multiple settings.
                 */
                switch( $field['type'] ) {
                    case 'dimension':
                    case 'typography':

                        if( $field['type'] == 'typography') {
                            $configurations = Fields\Typography::configurations();
                        }

                        if( $field['type'] == 'dimension') {
                            $configurations = Fields\Dimension::configurations();
                        }

                        // Add all custom settings
                        foreach( $configurations['settings'] as $setting ) {  
                            $wp_customize->add_setting($panel['id'] . '[' . $field['id'] . ']' . $setting, $settingArgs );    
                        }
                        
                        break;                      
                    default:
                        $wp_customize->add_setting( $panel['id'] . '[' . $field['id'] . ']', $settingArgs );
                }

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
                
                /**
                 * Custom Control types
                 */
                switch( $field['type'] ) {
                    case 'colorpicker':
                        unset($controlArgs['type']); // Having a defined type breaks the color picker somehow
                        $wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, $panel['id'] . '[' . $field['id'] . ']', $controlArgs) ); 
                        break;                    
                    case 'cropped-image':
                        $wp_customize->add_control( new WP_Customize_Cropped_Image_Control($wp_customize, $panel['id'] . '[' . $field['id'] . ']', $controlArgs) ); 
                        break;
                    case 'image':
                        $wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, $panel['id'] . '[' . $field['id'] . ']', $controlArgs) ); 
                        break;                    
                    case 'media':
                        $wp_customize->add_control( new WP_Customize_Media_Control($wp_customize, $panel['id'] . '[' . $field['id'] . ']', $controlArgs) );
                        break;                    
                    case 'upload':
                        $wp_customize->add_control( new WP_Customize_Upload_Control($wp_customize, $panel['id'] . '[' . $field['id'] . ']', $controlArgs) );
                        break;
                    case 'textarea':
                        $wp_customize->add_control( new Fields\Customizer\TextArea($wp_customize, $panel['id'] . '[' . $field['id'] . ']', $controlArgs) );
                        break;
                    case 'dimension':                          
                    case 'typography':
                        
                        $controlArgs['settings']    = array();
                        
                        foreach( $configurations['settings'] as $key => $setting ) {
                            $link = str_replace( array('[', ']'), '', $setting );
                            $controlArgs['settings'][$link] = $panel['id'] . '[' . $field['id'] . ']' . $setting;
                        }
                        
                        if( $field['type'] == 'typography' ) {
                            $wp_customize->add_control( new Fields\Customizer\Typography($wp_customize, $panel['id'] . '[' . $field['id'] . ']', $controlArgs) );
                        }

                        if( $field['type'] == 'dimension' ) {
                            $wp_customize->add_control( new Fields\Customizer\Dimension($wp_customize, $panel['id'] . '[' . $field['id'] . ']', $controlArgs) );
                        }

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