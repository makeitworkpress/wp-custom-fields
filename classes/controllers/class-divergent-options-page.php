<?php 
/** 
 * This class is responsible for controlling the display of the theme options page
 * 
 * @author Michiel
 * @since 1.0.0
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) { 
    die; 
} 

class Divergent_Options_Page extends Divergent {    
    
    /**
     * Contains the current set of option fields within a loop
     */
    protected $current_options_page; 
    
    /**
     * Contains the current set of option values within a loop
     */
    protected $current_options_values; 
    
    /**
     * Contains the option values for each of the option pages
     */
    protected $option_fields;      
            
    /**
     * Contains the rendered options object
     */
    protected $options_page_view; 
        
    /**
     * Constructor
     *
     * @param mixed $params The parameters passed to this object
     */    
    protected function initialize(Array $params) {
               
        do_action('divergent_option_page_initialize', $this);
        
        $this->option_fields = $params; 
        $this->options_page_view = new Divergent_Views_Options_Page($this->option_fields);
        
        do_action('after_divergent_option_page_initialize', $this);
    }
    
    /**
     * Wordpress Hooks
     */
    protected function register_hooks() {
        $this->actions = array(
            array('admin_init', 'add_settings'),
            array('admin_menu', 'options_page'),
        );
        
        add_action('admin_enqueue_scripts', function () {
            wp_enqueue_media();    
        });          
    }
    
    /**
     * Adds the settings using the settings api
     */
    public function add_settings() {

        foreach( $this->option_fields as $options_page ) {
            
            // Check if a proper ID is set
            if( isset($options_page['id']) && ! empty($options_page['id']) ) {
                
                $this->current_option_values = get_option($options_page['id']);

                $this->current_options_page = $options_page;
                
                // Register the setting so it can be retrieved under a single option name. Sanitization is done on field level
                register_setting($options_page['id'] . '_group', $options_page['id'], array($this, 'sanitize') );  
                
                foreach($options_page['sections'] as $section) {
                    
                    // Add the settings sections. We use a custom function for displaying the sections
                    if( isset($section['id']) ) {
                        add_settings_section( $section['id'], $section['title'], '', $options_page['id'] );
                    
                        // Add the settings per field
                        foreach($section['fields'] as $field) {

                            if( isset($field['id']) ) {

                                $field_args['attributes']                 = $field;
                                $field_args['attributes']['values']       = isset($this->current_option_values[$field['id']]) ? $this->current_option_values[$field['id']] : '';
                                $field_args['attributes']['page_id']      = $options_page['id']; // Stores the option field ID so it can be used for saving properly using the settings API  
                                $field_args['attributes']['section_id']   = $section['id'];

                                add_settings_field( $field['id'], '',  array($this->options_page_view, 'render_field'), $options_page['id'], $section['id'], $field_args);
                            }
                        }                        
                        
                    }
                }
            }
        }
    }
    
    /**
     * Controls the display of the options page
     */
    public function options_page() {
        
        foreach( $this->option_fields as $options_page ) {
            
            // Set the current field to the option field we are looping through so we can access them through the viewer
            $this->options_page_view->current_options_page = $options_page;
            
            // Check if a proper ID is set and add a menu page
            if( isset($options_page['id']) ) { 
                add_menu_page( 
                    $options_page['title'], 
                    $options_page['menu_title'], 
                    $options_page['capability'], 
                    $options_page['id'], 
                    array( $this->options_page_view, 'render_page'), 
                    $options_page['menu_icon'],
                    $options_page['menu_position']
                ); 
            }
        }
    }
    
    /**
     * Function for sanitizing saved data
     *
     * @param $input The input from the saved form. 
     * @return $output The saved fields output, sanitized.
     *
     * @todo Optimize restoration and saving of options which are already equal in the database.
     */
    public function sanitize( $output ) {
                
        // The current page we are saving for
        $current_options_page = $this->current_options_page;
        $page_id = $current_options_page['id'];
          
        // The current tab we are in
        $current_section = isset($_POST['divergent-current-section']) ? strip_tags(stripslashes($_POST['divergent-current-section']) ) : $current_options_page['sections'][0]['id'];
        set_transient('divergent_current_section_' . $page_id, $current_section, 10);
                
        // Restore Section: Restore the fields only for the current section, after submitting restore section
        if( isset($_POST['divergent_options_reset_section']) ) {
                                          
            foreach($current_options_page['sections'] as $section) { 
                
                if($section['id'] === $current_section) {
                    foreach($section['fields'] as $field) {
                        $default = isset($field['default']) ? $field['default'] : '';
                        $output[$field['id']] = $default;
                    }
                    
                // Otherfields are just saved
                } else {
                    foreach($section['fields'] as $field) {
                        $output[$field['id']] = Divergent_Validate::sanitize($output[$field['id']], $field);
                    }
                }
            }
            
            add_settings_error( $current_options_page['id'], 'divergent-notification', __('Settings restored for this section.', DIVERGENT_LANGUAGE), 'update' ); 
        }
        
        // Restore All: If the reset button was used, empty all of the output or resture them to the default values
        if( isset($_POST['divergent_options_reset']) ) {
            foreach($current_options_page['sections'] as $section) {
                foreach($section['fields'] as $field) { 
                    $default = isset($field['default']) ? $field['default'] : '';
                    $output[$field['id']] = $default;
                }
            }
            add_settings_error( $current_options_page['id'], 'divergent-notification', __('All settings are restored.', DIVERGENT_LANGUAGE), 'update' ); 
        
        // If some data is imported
        } elseif(isset($_POST['import_submit'])) {
            $output = unserialize(base64_decode($_POST['import_value']));
            add_settings_error( $current_options_page['id'], 'divergent-notification', __('Settings Imported!', DIVERGENT_LANGUAGE), 'update' ); 
        
        // Otherwise, just save the values
        } else {
            // Foreach registered field with sanitation enabled, sanitize the output.
            foreach($current_options_page['sections'] as $section) {

                foreach($section['fields'] as $field) {
                    $output[$field['id']] = Divergent_Validate::sanitize($output[$field['id']], $field);
                }
            }
            
            add_settings_error( $current_options_page['id'], 'divergent-notification', __('Settings saved!', DIVERGENT_LANGUAGE), 'update' ); 
        }
        
        
        return $output;
    }
}