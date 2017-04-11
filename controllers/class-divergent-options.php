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

namespace Controllers;
use stdClass as stdClass;

class Divergent_Options extends Divergent_Abstract {    
    
    /**
     * Contains the current set of option fields within a loop
     */
    protected $currentPage; 
    
    /**
     * Contains the current set of option values within a loop
     */
    protected $currentPageValues; 
    
    /**
     * Contains the option values for each of the option pages
     */
    protected $optionPages;
        
    /**
     * Constructor
     */    
    protected function initialize() {
        $this->optionPages = $this->params;
    }
    
    /**
     * Register WordPress Hooks
     */
    protected function registerHooks() {
        $this->actions = array(
            array('admin_init', 'addSettings'),
            array('admin_menu', 'optionsPage'),
        );
        
        add_action('admin_enqueue_scripts', function () {
            wp_enqueue_media();    
        });          
    }
    
    
    /**
     * Controls the display of the options page
     */
    public function optionsPage() {
        
        foreach( $this->optionPages as $page ) {
            
            // Check if a proper ID is set and add a menu page
            if( ! isset($page['id']) || empty( $page['id'] ) )
                continue;
            
            // Add a menu page
            add_menu_page( 
                $page['title'], 
                $page['menu_title'], 
                $page['capability'], 
                $page['id'], 
                array( $this, 'renderPage' ), 
                $page['menu_icon'],
                $page['menu_position']
            ); 

        }
    }    
    
    /**
     * Adds the settings using the settings api
     */
    public function addSettings() {

        foreach( $this->optionPages as $page ) {
            
            // Check if a proper ID is set
            if( ! isset($page['id']) || empty($page['id']) )
                continue;
  
            // Our current values
            $this->currentPageValues    = get_option( $page['id'] );
            $this->currentPage          = $page;

            // Register the setting so it can be retrieved under a single option name. Sanitization is done on field level and executed by the sanitize method.
            register_setting($page['id'] . '_group', $page['id'], array($this, 'sanitize') );  

            foreach($page['sections'] as $section) {
                
                if( ! isset($section['id']) || empty($section['id']) )
                    continue;                

                // Add the settings sections. We use a custom function for displaying the sections
                add_settings_section( $section['id'], $section['title'], array($this, 'renderSection'), $page['id'] );

                // Add the settings per field
                foreach($section['fields'] as $field) {

                    if( ! isset($field['id']) )
                        continue;

                    $fieldArgs['attributes']                 = $field;
                    $fieldArgs['attributes']['values']       = isset($this->currentPageValues[$field['id']]) ? $this->currentPageValues[$field['id']] : '';
                    $fieldArgs['attributes']['page_id']      = $page['id']; // Stores the option field ID so it can be used for saving properly using the settings API  
                    $fieldArgs['attributes']['section_id']   = $section['id'];

                    add_settings_field( $field['id'], isset($field['title']) ? $field['title'] : '', array($this, 'renderField'), $page['id'], $section['id'], $fieldArgs );

                }                        
            
            }
            
        }
        
    }
    
    
    /**
     * Renders the option page
     * 
     * @parram array $args The arguments passed to this callback.
     */
    public function renderPage( $args ) {
                        
        $sections   = $this->currentPage['sections'];
        $pageID     = $this->currentPage['id'];
        
        $frame      = new stdClass();
        
        // If we have sections, load our shizzle.
        if( ! empty($sections) ) {
            
            $transient              = get_transient('divergent_current_section_' . $pageID);
            $frame->currentSection  = ! empty( $transient ) ? $transient : $sections[0]['id'];
            
            // Errors
            ob_start();
            settings_errors(); 
            $frame->errors          = ob_get_clean();
            
            $frame->id              = $pageID;
            
            // Save Button
            ob_start();
            submit_button( __( 'Save Settings', 'divergent' ), 'primary divergent-save', $pageID . '_save', false );
            $frame->saveButton      = ob_get_clean();
            
            // Reset Button
            ob_start();
            submit_button( __( 'Reset Settings', 'divergent' ), 'delete divergent-reset', $pageID . '_reset', false );
            $frame->resetButton     = ob_get_clean();
            
            // Restore Button
            ob_start();
            submit_button( __( 'Restore Section', 'divergent' ), 'delete divergent-reset-section', $pageID . '_reset_section', false )
            $frame->restoreButton   = ob_get_clean();
            
            // Sections
            foreach( $sections as $key => $section ) {
                $frame->sections[$key]              = $frame->currentSection == $section['id'] ? 'active' : '';
                $frame->sections[$key]['active']    = ! empty( $section['icon'] ) ? $section['icon'] : false;
                $frame->sections[$key]['icon']      = ! empty( $section['icon'] ) ? $section['icon'] : false;
            }
             
            // Setting Fields
            ob_start();
            settings_fields( $pageID . '_group' );
            $frame->settingFields   = ob_get_clean();              
            
            $frame->title           = $this->currentPage['title'];

            do_settings_section();
                
            foreach( $sections as $key => $section ) {
                
                $active = $current_section == $section['id'] ? ' active' : '';
                
                $output .= $this->render_section($pageID, $section, $active);
                                        
            } 

        
        } else {
            $output .= '<div class="error"><p>' . __('You have not added sections for your option page (properly). Please review your options configurations.', 'divergent') . '</p></div>';
        } 
            
        $output .= '</form>';
        $output .= '</div>';    
        
        echo $output;    
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
                          
        // The current tab we are in
        $current = isset($_POST['divergent-current-section']) ? strip_tags($_POST['divergent-current-section']) : $this->currentPage['sections'][0]['id'];
        set_transient('divergent_current_section_' . $this->currentPage['id'], $current, 10);
                
        // Restore Section: Restore the fields only for the current section, after submitting restore section
        if( isset($_POST['divergent_options_reset_section']) ) {
                                          
            foreach($this->currentPage['sections'] as $section) { 
                
                if( $section['id'] === $current ) {
                    
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
            
            add_settings_error( $this->currentPage['id'], 'divergent-notification', __('Settings restored for this section.', 'divergent'), 'update' ); 
        }
        
        // Restore All: If the reset button was used, empty all of the output or resture them to the default values
        if( isset($_POST['divergent_options_reset']) ) {
            foreach($this->currentPage['sections'] as $section) {
                foreach($section['fields'] as $field) { 
                    $default = isset($field['default']) ? $field['default'] : '';
                    $output[$field['id']] = $default;
                }
            }
            add_settings_error( $this->currentPage['id'], 'divergent-notification', __('All settings are restored.', 'divergent'), 'update' ); 
        
        // If some data is imported
        } elseif(isset($_POST['import_submit'])) {
            
            $output = unserialize(base64_decode($_POST['import_value']));
            add_settings_error( $this->currentPage['id'], 'divergent-notification', __('Settings Imported!', 'divergent'), 'update' ); 
        
        // Otherwise, just save the values
        } else {
            
            // Foreach registered field with sanitation enabled, sanitize the output.
            foreach($this->currentPage['sections'] as $section) {

                foreach($section['fields'] as $field) {
                    $output[$field['id']] = Divergent_Validate::sanitize($output[$field['id']], $field);
                }
            }
            
            add_settings_error( $this->currentPage['id'], 'divergent-notification', __('Settings saved!', 'divergent'), 'update' ); 
        }
        
        return $output;
        
    } 
    
}