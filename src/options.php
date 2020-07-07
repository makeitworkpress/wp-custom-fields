<?php 
/** 
 * This class is responsible for controlling the display of the theme options page
 * 
 * @author Michiel
 * @since 1.0.0
 */
namespace MakeitWorkPress\WP_Custom_Fields;

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
     * Examines if we have validated
     * @access public
     */
    public $validated = false;    
        
    /**
     * Constructor
     *
     * @param array $group The array with settings, sections and fields
     * @return WP_Error|void An WP Error if we encounter a configuration error, otherwise nothing
     */    
    public function __construct( $group = [] ) {

        // This can only be executed in admin context
        if( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        // Limiting access for network pages
        if ( isset($group['context']) && $group['context'] == 'network' && ! current_user_can( 'manage_network_options' ) ) {
            return;
        }

        $this->optionPage   = $group;

        $allowed            = ['menu', 'submenu', 'dashboard', 'posts', 'media', 'links', 'pages', 'comments', 'theme', 'users', 'management', 'options'];
        $this->location     = isset( $this->optionPage['location'] ) && in_array( $this->optionPage['location'], $allowed ) ? $this->optionPage['location'] : 'menu';
        
        // Validate our configurations and return if we don't
        switch( $this->location ) {
            case 'menu':
                $this->validated = Validate::configurations( $group, ['title', 'menu_title', 'capability', 'id', 'menu_icon', 'menu_position'] );
                break;
            case 'submenu':
                $this->validated = Validate::configurations( $group, ['title', 'menu_title', 'capability', 'id', 'slug'] );                         
                break;
            default:
                $this->validated = Validate::configurations( $group, ['title', 'menu_title', 'capability', 'id'] );       
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

        if( isset($this->optionPage['context']) && $this->optionPage['context'] == 'network' ) {
            $this->optionPage['action'] = isset($this->optionPage['action']) ? $this->optionPage['action'] : 'edit.php?action=wpcf_update';
            add_action( 'network_admin_menu', [$this, 'addPage'] );
            add_action( 'network_admin_edit_wpcf_update', [$this, 'saveNetwork'] );
        } else {
            add_action( 'admin_init', [$this, 'addSettings'] );
            add_action( 'admin_menu', [$this, 'addPage'] );
        }

    }
    
    /**
     * Controls the display of the options page
     */
    public function addPage() {
  
        // Check if a proper ID is set and add a menu page
        if( ! isset($this->optionPage['id']) || ! $this->optionPage['id'] ) {
            return new WP_Error( 'wrong', __( 'Your options configurations require an id.', 'wp-custom-fields' ) );
        } 
          
        $addPage    = 'add_' . $this->location . '_page';
        
        switch( $this->location ) {
            case 'menu':
                add_menu_page(
                    $this->optionPage['title'], 
                    $this->optionPage['menu_title'], 
                    $this->optionPage['capability'], 
                    $this->optionPage['id'], 
                    [$this, 'renderPage'], 
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
                    [$this, 'renderPage'] 
                );                
                break;
            default:
                $addPage( 
                    $this->optionPage['title'], 
                    $this->optionPage['menu_title'], 
                    $this->optionPage['capability'], 
                    $this->optionPage['id'], 
                    [$this, 'renderPage'] 
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
        register_setting( $this->optionPage['id'] . '_group', $this->optionPage['id'], ['sanitize_callback' => [$this, 'sanitize']] );

        foreach( $this->optionPage['sections'] as $section ) {

            if( ! isset($section['id']) || empty($section['id']) )
                continue;                

            // Add the settings sections. We use a custom function for displaying the sections
            add_settings_section( $section['id'], $section['title'], [$this, 'renderSection'], $this->optionPage['id'] );

            // Add the settings per field
            foreach($section['fields'] as $field) {

                if( ! isset($field['id']) )
                    continue;

                add_settings_field( $field['id'], isset($field['title']) ? $field['title'] : '', [$this, 'renderField'], $this->optionPage['id'], $section['id'] );

            }                        

        }
        
    }
    
    
    /**
     * Renders the option page
     * 
     * @parram array $args The arguments passed to this callback.
     */
    public function renderPage( $args ) {

        // Again, check the access for users
        if( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'Sorry, you are not allowed to access this page.' ), 403 );
            return;
        }

        // Limiting access for network pages
        if ( isset($this->optionPage['context']) && $this->optionPage['context'] == 'network' && ! current_user_can( 'manage_network_options' ) ) {
            wp_die( __( 'Sorry, you are not allowed to access this page.' ), 403 );
            return;
        }        
                        
        $pageID                 = $this->optionPage['id'];
        $values                 = isset($this->optionPage['context']) && $this->optionPage['context'] == 'network' ? get_site_option( $pageID ) : get_option( $pageID );
        
        $frame                  = new Frame( $this->optionPage, $values );
        $frame->action          = isset($this->optionPage['action']) ? esc_attr( $this->optionPage['action'] ) : 'options.php';
        $frame->type            = 'options';

        // Network pages handle errors differently
        if ( isset($this->optionPage['context']) && $this->optionPage['context'] == 'network' ) {
            if( isset($_GET['wpcf-action']) ) {
                Validate::addErrorMessage($pageID, sanitize_key($_GET['wpcf-action']));
            }
        }

        // Errors - they are already implemented automatically at option screens.
        $screen                 = get_current_screen();
        if( $screen->parent_base != 'options-general' ) {
            ob_start();
            settings_errors( $pageID ); 
            $frame->errors          = ob_get_clean();
        }

        // Save Button
        ob_start();
        submit_button( __( 'Save Settings', 'wp-custom-fields' ), 'primary button-hero wp-custom-fields-save', $pageID . '_save', false );
        $frame->saveButton      = ob_get_clean();

        // Reset Button
        ob_start();
        submit_button( __( 'Reset Settings', 'wp-custom-fields' ), 'delete button-hero wp-custom-fields-reset', $pageID . '_reset', false );
        $frame->resetButton     = ob_get_clean();

        // Restore Button
        ob_start();
        submit_button( __( 'Restore Section', 'wp-custom-fields' ), 'delete button-hero wp-custom-fields-reset-section', $pageID . '_restore', false );
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
     * Handles the saving of options within network admin pages
     * 
     * Could also be converted to a generic save function if we incorporate update_option
     */
    public function saveNetwork() {

        // Checks the security nonce
        check_admin_referer( $this->optionPage['id'] . '_group-options');

        // Checks if the user has the correct capabilities
        if( ! current_user_can( 'manage_network_options' ) ) {
            wp_die( __( 'Sorry, you are not allowed to save data.' ), 403 );
            return;
        }

        // Sanitizes the $_POST global variable and saves the site option
        $values = $this->sanitize(); 
        update_site_option( $this->optionPage['id'], $values);

        // Determines our slug to display custom error messages for network pages
        if( isset($_POST[$this->optionPage['id'] . '_restore']) ) {
            $action = 'restore';
        } elseif( isset($_POST[$this->optionPage['id'] . '_reset']) ) {
            $action = 'reset';
        } elseif( isset($_POST['import_submit']) ) {
            $action = 'import';
        } else {
            $action = 'update';
        }

        // Redirects back to the given page
        $page = 'admin.php';
        wp_redirect( add_query_arg( 'wpcf-action', $action, network_admin_url( $page . '?page=' . $this->optionPage['id'] ) ) );
        
        // Exits
        exit();        

    }
    
    /**
     * Function for sanitizing the saved data. Hooks upon sanitizing the option directly.
     */
    public function sanitize() {
        
        $value = Validate::format( $this->optionPage, $_POST, 'options' );
        
        return $value;

    }
    
    /**
     * Renders a section, as possible callback for do_settings_sections. Not used at the moment as everything is rendered in a single frame.
     */
    public function renderSection() {}
    
    /**
     * Renders a field, as possible callback for do_settings_fields. Not used at the moment as everything is rendered in a single frame.
     */
    public function renderField() {}

    
}