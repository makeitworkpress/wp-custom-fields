<?php 
/** 
 * This class is responsible for controlling the display of the theme options page
 * 
 * @author Michiel
 * @since 1.0.0
 */
namespace Classes\Divergent;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class Divergent_Customize extends Divergent_Abstract {    
        
    /**
     * Constructor
     */    
    protected function initialize() {
        $this->panels = $this->params;
    }
    
    /**
     * Register WordPress Hooks
     */
    protected function registerHooks() {
        
        $this->actions = array(
            array('customize_register', 'addSettings'),
        );       
    }
    
    /**
     * Adds the settings using the settings api
     * Built in types: input (text, hidden, number, range, url, tel, email, search, time, date, datetime, week), checkbox, textarea, radio, select, dropdown-pages, range
     *
     * @param $wp_customize The WP Customize Object
     */
    public function addSettings( $wp_customize ) {

        foreach( $this->panels as $panel ) {
            
            // Check
            if( ! isset($panel['id']) )
                continue; 
            
            $panelArgs = array(
                'priority'      => 160,
                'title'         => $panel['title']                
            );
            
            if( isset($panel['description']) )
                $panelArgs[ 'description'] = $panel['description'];            
            
            // Add our panel
            $wp_customize->add_panel( $panel['id'], $panelArgs );
            
            foreach( $panel['sections'] as $section ) {
                
                // Check
                if( ! isset($section['id']) )
                    continue;
                
                $sectionArgs = array(
                    'description'   => $section['description'], 
                    'priority'      => 160, 
                    'title'         => $section['title']                    
                );
                
                if( isset($section['description']) )
                    $sectionArgs[ 'description'] = $section['description'];
                
                // Add our section
                $wp_customize->add_section( $section['id'], $sectionArgs );
                
                foreach( $section['fields'] as $field ) {
                    
                    // Check
                    if( ! isset($field['id']) || ! isset($field['default']) || ! isset($field['type']) )
                        continue; 
                    
                    $settingArgs = array(
                        'default' => $field['default']
                        'type'    => isset( $panel['option'] ) ? $panel['option'] : 'theme_mod'
                    );
                    
                    if( isset($field['sanitize']) )
                        $settingArgs['sanitize_callback']     = $field['sanitize'];
                    
                    if( isset($field['sanitize_js']) )
                        $settingArgs['sanitize_js_callback']  = $field['sanitize_js'];                    
                    
                    // Add our settings
                    $wp_customize->add_setting( $panel['id'] . '[' . $field['id'] . ']', $settingArgs );
                    
                    
                    $controlArgs['section'] = $section['id'];
                    
                    foreach( $field as $attribute => $value ) {
                        $controlArgs[$attribute] = $value;       
                    }                     
                    
                    // Add several types controls. Might DRY this up someday.
                    if( $field['type'] == 'media' ) {
                        $wp_customize->add_control( new WP_Customize_Media_Control($wp_customize, $panel['id'] . '[' . $field['id'] . ']', $controlArgs) );    
                    } elseif( $field['type'] == 'colorpicker' ) {
                        $wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, $panel['id'] . '[' . $field['id'] . ']', $controlArgs) );    
                    } elseif( $field['type'] == 'image' ) {
                        $wp_customize->add_control( new WP_Customize_Cropped_Image_Control($wp_customize, $panel['id'] . '[' . $field['id'] . ']', $controlArgs) );    
                    } elseif( $field['type'] == 'upload' ) {
                        $wp_customize->add_control( new WP_Customize_Upload_Control($wp_customize, $panel['id'] . '[' . $field['id'] . ']', $controlArgs) );    
                    } elseif( $field['type'] == 'custom' && isset($field['custom']) && class_exists($field['custom']) ) {
                        $wp_customize->add_control( new $field['custom']($wp_customize, $panel['id'] . '[' . $field['id'] . ']', $controlArgs) );    
                    } else {
                        $wp_customize->add_control( $panel['id'] . '[' . $field['id'] . ']', $controlArgs );
                    }
                    
                    // Register our partials
                    if( isset($field['partial']) )
                        $wp_customize->selective_refresh->add_partial( $panel['id'] . '[' . $field['id'] . ']', $field['partial'] );
                    
                }
                    
            }
            
        }
        
    }
    
    
    /**
     * Default fallback for validation
     * 
     * @parram array $validity  The The validity of the setting
     * @parram array $value     The value passed
     */
    public function validate(  $validity, $value ) {
        
    }
    
    /**
     * Default fallback for sanitization
     * 
     * @parram array $value     The value passed
     */
    public function validate(  $value ) {

    }
    
}