<?php
/** 
 * Displays an option page
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) { 
    die; 
} 

class Divergent_Views_Options_Page {
    
    /**
     * Holds all defined fields
     */
    protected $fields;
    
    /**
     * Holds the current option field in a certain controller loop
     */
    public $current_options_page;
    
    /**
     * Constructor
     *
     * @param array $fields The sections and fields
     */
    public function __construct( $fields = array() ) {
        $this->fields = $fields;
    }    
    
    /**
     * Renders the option page
     * 
     * @parram array $args The arguments passed to this callback.
     */
    public function render_page( $args ) {
                        
        $current_options_page = $this->current_options_page;
        $sections = $current_options_page['sections'];
        $page_id = $current_options_page['id'];
        
        $output = '<div class="wrap">';
        $output .= '<form method="post" action="options.php" enctype="multipart/form-data" id="divergent-options-page">';
        
        // Use output buffering, as settings fields echo's on default
        ob_start();
        settings_fields( $page_id . '_group' );
        $output .= ob_get_clean();          
        
        if( ! empty($sections) ) {
            
            $current_section = ! empty( get_transient('divergent_current_section_' . $page_id) ) ? get_transient('divergent_current_section_' . $page_id) : $sections[0]['id'];
                    
            // Stores the current section. This is the first available section by default, but is altered by Javascript when navigating through the tabs.
            $output .= '<input type="hidden" name="divergent-current-section" id="divergent-current-section_' . $page_id . '" value="' . $current_section . '" />';             
     
            $output .= '<div class="divergent-options-page divergent-framework">';
            
            // The options page header
            $output .= '<header class="divergent-header">';  
            $output .= '    <h2>' . $current_options_page['title'] . '</h2>';
            ob_start();
            settings_errors(); 
            $output .= ob_get_clean();
            
            // Add submit buttons
            ob_start();
            submit_button( __( 'Save Settings', 'divergent' ), 'primary divergent-save', $page_id . '_save', false );
            submit_button( __( 'Restore Section', 'divergent' ), 'delete divergent-reset-section', $page_id . '_reset_section', false );
            $output .= ob_get_clean();
            
             $output .= '    <ul class="divergent-tabs">';

            foreach( $sections as $key => $section ) {

                $active = $current_section == $section['id'] ? ' active' : '';

                $icon = ( ! empty( $section['icon'] ) ) ? '<i class="divergent-icon '. $section['icon'] .'"></i>' : '';

                $output .= '       <li><a href="#'. $section['id'] .'" class="divergent-tab' . $active . '" data-section="'. $section['id'] .'">'. $icon . $section['title'] .'</a></li>';           
            }

            $output .= '    </ul>';           
            
            $output .= '</header>';
            
            $output .= '<div class="divergent-sections">';
                
            foreach( $sections as $key => $section ) {
                
                $active = $current_section == $section['id'] ? ' active' : '';
                
                $output .= $this->render_section($page_id, $section, $active);
                                        
            } 
            
            $output .= '</div>'; 
        
            // The options page footer
            $output .= '<footer>';
                ob_start();
                submit_button( __( 'Save Settings', 'divergent' ), 'primary divergent-save', $page_id . '_save', false );
                submit_button( __( 'Restore Section', 'divergent' ), 'delete divergent-reset-section', $page_id . '_reset_section', false );
                submit_button( __( 'Reset Settings', 'divergent' ), 'delete divergent-reset', $page_id . '_reset', false );
                $output .= ob_get_clean();        
            $output .= '</footer>';

            $output .= '</div>';
        
        } else {
            $output .= '<div class="error"><p>' . __('You have not added sections for your option page (properly). Please review your options configurations.', 'divergent') . '</p></div>';
        } 
            
        $output .= '</form>';
        $output .= '</div>';    
        
        echo $output;    
    }
    
    /**
     * Renders a section
     * The do_settings_fields automatically loads the render_field callback.
     *
     * @param string $page_id The id of the page the section belongs to
     * @param array  $section The section arguments
     * @param string $active The class determining when a section is active
     */
    private function render_section( $page_id = '', $section = array(), $active = '' ) {
        
        $output = '<section id="' . $section['id'] . '" class="divergent-section' . $active . '">';
        $output .= '   <h3 class="divergent-section-title">' . $section['title'] . '</h4>';
        
        // Loop through each field
        if( isset($section['fields']) ) {            
            ob_start();
            do_settings_fields( $page_id, $section['id'] ); 
            $output .= ob_get_clean(); 
        }
        
        $output .= '</section>';
        
        return $output;
    }
    
    /**
     * Renders an option field. 
     * This is the callback added through add_settings_field in includes/class-divergent-options-page.php
     * The callback is activated if do_settings_fields is called for the sections a specific field is added
     * 
     * @param array $args The arguments as passed through the fallback in includes/class-divergent-options-page.php
     */
    public function render_field( $args ) {
        
        $args['attributes']['id'] = $args['attributes']['page_id'] . '[' . $args['attributes']['id'] . ']';
            
        $output = Divergent_Fields::render($args['attributes']);
        
        echo $output;    
    }      
}