<?php 
/** 
 * This class is responsible for controlling the display of the theme options page
 * 
 * @author Michiel
 * @since 1.0.0
 */
namespace WP_Custom_Fields;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) )
    die;

class Options { 
    
    /**
     * Use our validation functions
     */
    use Validate;    
     
    /**
     * Contains the option values for each of the option pages
     * @access public
     */
    public $optionPage;
        
    /**
     * Constructor
     *
     * @param array $group The array with settings, sections and fields
     */    
    public function __construct( $group = array() ) {
        $this->optionPage = $group;
        $this->registerHooks();
    }
    
    /**
     * Register WordPress Hooks
     */
    protected function registerHooks() {
        add_action( 'admin_init', array($this, 'addSettings') );
        add_action( 'admin_menu', array($this, 'optionsPage') );
    }
    
    
    /**
     * Controls the display of the options page
     */
    public function optionsPage() {
   
        // Check if a proper ID is set and add a menu page
        if( ! isset($this->optionPage['id']) || empty( $this->optionPage['id'] ) )
            return new WP_Error( 'wrong', __( 'Your options configurations are an ID.', 'wp-custom-fields' ) );

        $allowed    = array( 'menu', 'submenu', 'dashboard', 'posts', 'media', 'links', 'pages', 'comments', 'theme', 'users', 'management', 'options' );
        $location   = isset( $this->optionPage['location'] ) && in_array( $this->optionPage['location'], $allowed ) ? $this->optionPage['location'] : 'menu';
        $addPage    = 'add_' . $location . '_page';
        $pageArgs   = $this->optionPage;
        
        switch( $location ) {
            case 'menu':
                add_menu_page(
                    $this->optionPage['title'], 
                    $this->optionPage['menu_title'], 
                    $this->optionPage['capability'], 
                    $this->optionPage['id'], 
                    array( $this, 'renderPage' ), 
                    $this->optionPage['menu_icon'],
                    $this->optionPage['menu_position']                
                );
                break;
            case 'submenu':
                add_submenu_page( 
                    $this->optionPage['slug'], 
                    $this->optionPage['title'], 
                    $this->optionPage['menu_title'], 
                    $this->optionPage['capability'], 
                    $this->optionPage['id'], 
                    array( $this, 'renderPage' ) 
                );                
                break;
            default:
                $addPage( 
                    $this->optionPage['title'], 
                    $this->optionPage['menu_title'], 
                    $this->optionPage['capability'], 
                    $this->optionPage['id'], 
                    array( $this, 'renderPage' ) 
                );                 
        }

    }    
    
    /**
     * Adds the settings using the settings api
     */
    public function addSettings() {
    
        // Check if a proper ID is set
        if( ! isset($this->optionPage['id']) || empty($this->optionPage['id']) )
            return;

        // Register the setting so it can be retrieved under a single option name. Sanitization is done on field level and executed by the sanitize method.
        register_setting( $this->optionPage['id'] . '_group', $this->optionPage['id'], array($this, 'save') );  

        foreach( $this->optionPage['sections'] as $section ) {

            if( ! isset($section['id']) || empty($section['id']) )
                continue;                

            // Add the settings sections. We use a custom function for displaying the sections
            add_settings_section( $section['id'], $section['title'], array($this, 'renderSection'), $this->optionPage['id'] );

            // Add the settings per field
            foreach($section['fields'] as $field) {

                if( ! isset($field['id']) )
                    continue;

                add_settings_field( $field['id'], isset($field['title']) ? $field['title'] : '', array($this, 'renderField'), $this->optionPage['id'], $section['id'] );

            }                        

        }
        
    }
    
    
    /**
     * Renders the option page
     * 
     * @parram array $args The arguments passed to this callback.
     */
    public function renderPage( $args ) {
                        
        $pageID                 = $this->optionPage['id'];
        $values                 = get_option( $pageID );
        
        $frame                  = new Frame( $this->optionPage, $values );
        $frame->type            = 'Options';

        // Errors
        ob_start();
        settings_errors(); 
        $frame->errors          = ob_get_clean();

        // Save Button
        ob_start();
        submit_button( __( 'Save Settings', 'wp-custom-fields' ), 'primary wp-custom-fields-save', $pageID . '_save', false );
        $frame->saveButton      = ob_get_clean();

        // Reset Button
        ob_start();
        submit_button( __( 'Reset Settings', 'wp-custom-fields' ), 'delete wp-custom-fields-reset', $pageID . '_reset', false );
        $frame->resetButton     = ob_get_clean();

        // Restore Button
        ob_start();
        submit_button( __( 'Restore Section', 'wp-custom-fields' ), 'delete wp-custom-fields-reset-section', $pageID . '_restore', false );
        $frame->restoreButton   = ob_get_clean();

        // Setting Fields
        ob_start();
        settings_fields( $pageID . '_group' );
        $frame->settingsFields  = ob_get_clean();              

        // Render our options page;
        $frame->render();
        
        return; 
 
    }   
    
    /**
     * Function for sanitizing saved data
     *
     * @param $output The output from the saved form. 
     */
    public function save( $output ) {
        
        $output = Validate::format( $this->optionPage, $_POST, 'Options' );
        
        return $output;
    }
    
    /**
     * Renders a section, as possible callback for do_settings_sections
     */
    public function renderSection() {}
    
    /**
     * Renders a field, as possible callback for do_settings_fields
     */
    public function renderField() {}     
    
}