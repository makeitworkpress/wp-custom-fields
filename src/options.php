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
    public $option_page;

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

        $this->option_page  = $group;

        $allowed            = ['menu', 'submenu', 'dashboard', 'posts', 'media', 'links', 'pages', 'comments', 'theme', 'users', 'management', 'options'];
        $this->location     = isset( $this->option_page['location'] ) && in_array( $this->option_page['location'], $allowed ) ? $this->option_page['location'] : 'menu';
        
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

        $this->register_hooks();

    }
    
    /**
     * Register WordPress Hooks
     */
    protected function register_hooks() {

        if( isset($this->option_page['context']) && $this->option_page['context'] == 'network' ) {
            $this->option_page['action'] = isset($this->option_page['action']) ? $this->option_page['action'] : 'edit.php?action=wpcf_update';
            add_action( 'network_admin_menu', [$this, 'add_page'] );
            add_action( 'network_admin_edit_wpcf_update', [$this, 'save_network'] );
        } else {
            add_action( 'admin_init', [$this, 'add_settings'] );
            add_action( 'admin_menu', [$this, 'add_page'] );
        }

    }
    
    /**
     * Controls the display of the options page
     */
    public function add_page() {
  
        // Check if a proper ID is set and add a menu page
        if( ! isset($this->option_page['id']) || ! $this->option_page['id'] ) {
            return new WP_Error( 'wrong', __( 'Your options configurations require an id.', 'wpcf' ) );
        } 
          
        $add_page   = 'add_' . $this->location . '_page';
        
        switch( $this->location ) {
            case 'menu':
                add_menu_page(
                    $this->option_page['title'], 
                    $this->option_page['menu_title'], 
                    $this->option_page['capability'], 
                    $this->option_page['id'], 
                    [$this, 'render_page'], 
                    $this->option_page['menu_icon'],
                    $this->option_page['menu_position']                
                );
                break;
            case 'submenu':
                add_submenu_page( 
                    $this->option_page['slug'], 
                    $this->option_page['title'], 
                    $this->option_page['menu_title'], 
                    $this->option_page['capability'], 
                    $this->option_page['id'], 
                    [$this, 'render_page'] 
                );                
                break;
            default:
                $add_page( 
                    $this->option_page['title'], 
                    $this->option_page['menu_title'], 
                    $this->option_page['capability'], 
                    $this->option_page['id'], 
                    [$this, 'render_page'] 
                );                 
        }

    }    
    
    /**
     * Adds the settings using the settings api
     */
    public function add_settings() {
    
        // Check if a proper ID is set
        if( ! isset($this->option_page['id']) || empty($this->option_page['id']) )
            return;

        // Register the setting so it can be retrieved under a single option name. Sanitization is done on field level and executed by the sanitize method.
        register_setting( $this->option_page['id'] . '_group', $this->option_page['id'], ['sanitize_callback' => [$this, 'sanitize']] );

        foreach( $this->option_page['sections'] as $section ) {

            if( ! isset($section['id']) || empty($section['id']) )
                continue;                

            // Add the settings sections. We use a custom function for displaying the sections
            add_settings_section( $section['id'], $section['title'], [$this, 'renderSection'], $this->option_page['id'] );

            // Add the settings per field
            foreach($section['fields'] as $field) {

                if( ! isset($field['id']) )
                    continue;

                add_settings_field( $field['id'], isset($field['title']) ? $field['title'] : '', [$this, 'renderField'], $this->option_page['id'], $section['id'] );

            }                        

        }
        
    }
    
    
    /**
     * Renders the option page
     * 
     * @parram array $args The arguments passed to this callback.
     */
    public function render_page( $args ) {

        // Again, check the access for users
        if( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'Sorry, you are not allowed to access this page.', 'wpcf' ), 403 );
            return;
        }

        // Limiting access for network pages
        if ( isset($this->option_page['context']) && $this->option_page['context'] == 'network' && ! current_user_can( 'manage_network_options' ) ) {
            wp_die( __( 'Sorry, you are not allowed to access this page.', 'wpcf' ), 403 );
            return;
        }        
                        
        $page_ID                = $this->option_page['id'];
        $values                 = isset($this->option_page['context']) && $this->option_page['context'] == 'network' ? get_site_option( $page_ID ) : get_option( $page_ID );
        
        $frame                  = new Frame( $this->option_page, $values );
        $frame->action          = isset($this->option_page['action']) ? esc_attr( $this->option_page['action'] ) : 'options.php';
        $frame->type            = 'options';

        // Network pages handle errors differently
        if ( isset($this->option_page['context']) && $this->option_page['context'] == 'network' ) {
            if( isset($_GET['wpcf-action']) ) {
                Validate::add_error_message($this->option_page, sanitize_key($_GET['wpcf-action']));
            }
        }

        // Errors - they are already implemented automatically at option screens.
        $screen                 = get_current_screen();
        if( $screen->parent_base != 'options-general' ) {
            ob_start();
            settings_errors( $page_ID ); 
            $frame->errors          = ob_get_clean();
        }

        /**
         * Buttons
         */
        $labels = [
            'save'      => isset($this->option_page['labels']['save']) ? $this->option_page['labels']['save'] : __( 'Save Settings', 'wpcf' ),
            'reset'     => isset($this->option_page['labels']['reset']) ? $this->option_page['labels']['reset'] : __( 'Reset Settings', 'wpcf' ),
            'restore'   => isset($this->option_page['labels']['restore']) ? $this->option_page['labels']['restore'] : __( 'Restore Settings', 'wpcf' )
        ];

        // Save Button
        ob_start();
        submit_button( $labels['save'], 'primary button-hero wp-custom-fields-save', $page_ID . '_save', false );
        $frame->save_button      = ob_get_clean();

        // Reset Button
        ob_start();
        submit_button( $labels['reset'], 'delete button-hero wp-custom-fields-reset', $page_ID . '_reset', false );
        $frame->reset_button     = ob_get_clean();

        // Restore Button
        ob_start();
        submit_button( $labels['restore'], 'delete button-hero wp-custom-fields-reset-section', $page_ID . '_restore', false );
        $frame->restore_button   = ob_get_clean();

        // Setting Fields
        ob_start();
        settings_fields( $page_ID . '_group' );
        $frame->settings_fields  = ob_get_clean();   

        // Render our options page;
        $frame->render();
        
        return; 
 
    }  
    
    /**
     * Handles the saving of options within network admin pages
     * 
     * Could also be converted to a generic save function if we incorporate update_option
     */
    public function save_network() {

        // Checks the security nonce
        check_admin_referer( $this->option_page['id'] . '_group-options');

        // Checks if the user has the correct capabilities
        if( ! current_user_can( 'manage_network_options' ) ) {
            wp_die( __( 'Sorry, you are not allowed to save data.' ), 403 );
            return;
        }

        // Sanitizes the $_POST global variable and saves the site option
        $values = $this->sanitize(); 
        update_site_option( $this->option_page['id'], $values);

        // Determines our slug to display custom error messages for network pages
        if( isset($_POST[$this->option_page['id'] . '_restore']) ) {
            $action = 'restore';
        } elseif( isset($_POST[$this->option_page['id'] . '_reset']) ) {
            $action = 'reset';
        } elseif( isset($_POST['import_submit']) ) {
            $action = 'import';
        } else {
            $action = 'update';
        }

        // Redirects back to the given page
        $page = 'admin.php';
        wp_redirect( add_query_arg( 'wpcf-action', $action, network_admin_url( $page . '?page=' . $this->option_page['id'] ) ) );
        
        // Exits
        exit();        

    }
    
    /**
     * Function for sanitizing the saved data. Hooks upon sanitizing the option directly.
     */
    public function sanitize() {
        
        $value = Validate::format( $this->option_page, $_POST, 'options' );
        
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