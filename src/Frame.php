<?php
/**
 * Creates the variable values for a new options frame
 * This acts as the main controller for passing data to a template.
 */
namespace MakeitWorkPress\WP_Custom_Fields;
use MakeitWorkPress\WP_Custom_Fields\Framework as Framework;
use stdClass as stdClass;

// Bail if accessed directly
if ( ! defined('ABSPATH') ) {
    die;
}

class Frame {
    
    /**
     * Contains the custom HTML selector class for the frame
     * 
     * @var string
     * @access public
     */
    public $class;

    /**
     * Contains the current active section
     * 
     * @var string
     * @access private
     */
    private $current_section;    

    /**
     * Contains our frame with all configured option fields
     * 
     * @var array
     * @access private
     */
    private $frame;


    /**
     * Contains the id for the given option frame
     * 
     * @var string
     * @access public
     */
    public $id;

    /**
     * Contains the properties that are option page specific
     * 
     * @var array
     * @access public
     */
    public $option_props;    
    
    /**
     * Contains the various sections for the option page
     * 
     * @var array
     * @access public
     */
    public $sections;

    /**
     * Contain the nonce for saving option pages or verifying custom post fields
     * 
     * @var string
     * @access public
     */
    public $settings_fields;
    
    /**
     * Contains the title for our frame
     * 
     * @var string
     * @access public
     */
    public $title;

    /**
     * Contains the type of frame
     * 
     * @var string
     * @access public
     */
    public $type;    
    
     /**
     * Contains our vsaved values for the fields
     * 
     * @var array
     * @access private
     */
    private $values;

    /**
     * Set our values
     *
     * @param array $frames The array with option frames, such as option pages or metaboxes
     * @param array $values The array with values for the option fields
     */
    public function __construct( Array $frame, $values = '' ) {
        
        // Our frame and values
        $this->frame    = $frame;  
        $this->values   = $values;
        
        // Default public variables
        $this->class                    = isset($frame['class']) ? esc_attr($frame['class']) : '';
        $this->current_section          = '';
        $this->id                       = esc_attr($frame['id']);
        $this->sections                 = [];
        $this->title                    = esc_html($frame['title']);
        $this->type                     = '';

        // Default option page settings
        $this->option_props             = [
            'action'                => 'options.php',
            'errors'                => '',
            'reset_button'          => '',
            'restore_button'        => '',
            'restore_button_bottom' => '',
            'save_button'           => '',
            'save_button_bottom'    => '',
            'setting_fields'        => ''
        ];

        // Include our scripts and media
        wp_enqueue_media();  

        if( ! wp_script_is('alpha-color-picker') ) {
            wp_enqueue_script('alpha-color-picker');
        }
        
        if( ! wp_script_is('wp-custom-fields-js') ) {
            wp_enqueue_script('wp-custom-fields-js');  
        }       
        
        // Populate Variables
        $this->populate_sections();
        
    }
    
    /**
     * Populates the sections and their fields
     */
    private function populate_sections(): void {
    
        if( ! isset($this->frame['sections']) || ! is_array($this->frame['sections']) )
            return;
        
        // Current section
        $transient              = get_transient( 'wp_custom_fields_current_section_' . $this->frame['id'] );
        $this->current_section  = ! empty( $transient ) ? $transient : array_values($this->frame['sections'])[0]['id'];   
        
        // Loop through our sections
        foreach( $this->frame['sections'] as $key => $section ) {
            
            if( ! isset($section['id']) )
                continue;
            
            $this->sections[$key]                   = $section;
            $this->sections[$key]['active']         = $this->current_section == $section['id'] ? 'active'          : '';
            $this->sections[$key]['description']    = isset( $section['description'] ) ? esc_textarea($section['description']) : '';
            $this->sections[$key]['fields']         = [];
            $this->sections[$key]['icon']           = ! empty( $section['icon'] ) ? esc_html($section['icon'])  : false;
            $this->sections[$key]['id']             = esc_attr($section['id']);
            $this->sections[$key]['tabs']           = isset( $section['tabs'] ) && $section['tabs'] == false ? false : true;
            $this->sections[$key]['title']          = isset( $section['title'] ) ? esc_html($section['title'])  : __( 'Titleless Section', 'wpcf' );
            $this->sections[$key]['display_title']  = isset( $section['display_title'] ) ? $section['display_title'] : true;

            if( ! isset($section['fields']) || ! is_array($section['fields']) ) {
                continue;
            }
            
            foreach( $section['fields'] as $field ) {

                // Fields without an id are not added
                if( ! isset($field['id']) )
                    continue;

                $this->sections[$key]['fields'][]  = $this->populate_field( $field );

            }
                
        }
        
    }
    
    /**
     * Populates the fields. Is executed by $this->populate_sections
     *
     * @param array $fields The array from a single field
     * @return array The populated field
     */
    private function populate_field( array $field = [] ): array {
        
        // Populate our variables
        $field                  = $field;
        $field['classes']       = isset( $field['columns'] )            ?  'wpcf-' . esc_attr($field['columns']) : 'wpcf-full';
        $field['description']   = isset( $field['description'] )        ?  esc_textarea($field['description'])      : '';
        $field['dependency']    = isset( $field['dependency'] ) && $field['dependency'] ?  $field['dependency'] : [];
        $field['form']          = '<div class="error notice"><p>' . sprintf( __('The given field class does not exist for the field with id: %s', 'wpcf'), $field['id']) . '</p></div>';
        $field['type']          = isset($field['type']) ? esc_attr($field['type']) : 'unknown';

        // Make sure our IDs do not contain brackets
        $field['id']            = str_replace( '[', '_', esc_attr($field['id']) ); 
        $field['id']            = str_replace( ']', '', esc_attr($field['id']) ); 
        $field['name']          = isset( $field['name'] )               ? esc_attr($field['name'])          : $field['id'];
        
        $field['placeholder']   = isset( $field['placeholder'] )        ? esc_attr($field['placeholder'])   : '';
        $field['title']         = isset( $field['title'] )              ? esc_html($field['title'])         : '';
        $field['titleTag']      = isset( $field['type'] ) && $field['type'] == 'heading'           ? 'h3'                              : 'h4';
        $field['titleClass']    = isset( $field['type'] ) && isset( $field['collapse'] ) && $field['type'] == 'heading' ? ' wpcf-heading-collapsible' : '';
        $field['titleSections'] = isset( $field['type'] ) && isset( $field['collapse'] ) && $field['type'] == 'heading' ? implode(',', $field['collapse']) : '';

        // Set-up additional classes and settings
        if( $field['dependency'] ) {
            $field['classes']  .= ' wpcf-dependent-field' . Framework::return_dependency_class($field['dependency'], $this->sections, $this->values);
        }

        if( isset($field['class']) ) {
            $field['classes']   .= ' ' . esc_attr($field['class']);
        }

        $field['classes']       .= ' field-' . $field['type'] . ' field-id-' . $field['id'];

        // We should have a field type
        if( $field['type'] == 'unknown' ) {
            $field['form']      = '<div class="error notice"><p>' . sprintf( __('The type is not defined for the field with id: %s', 'wpcf'), $field['id']) . '</p></div>';
            return $field;
        }

        $field['type']          = esc_attr($field['type']);
        
        // The class
        $class                  = apply_filters( 'wp_custom_fields_field_class', 'MakeitWorkPress\WP_Custom_Fields\Fields\\' . ucfirst( $field['type'] ), $field );
        
        // Render our field form, allow custom fields to be filtered.   
        if( class_exists($class) ) {

            $configurations     = $class::configurations();
        
            // Check if there is a default value set up, and whether there is a value already stored for the specific field
            $default            = isset( $field['default'] )            ? $field['default'] : $configurations['defaults'];
            $field['values']    = isset( $this->values[$field['id']] )  ? maybe_unserialize( $this->values[$field['id']] ) : $default; 

            // Get the buffered string output from our rendered templates
            ob_start();
                $class::render($field);
            $form = ob_get_clean();

            $field['form']      = apply_filters( 'wp_custom_fields_field_form', $form, $field );

        }
        
        return $field;
        
    }
    
    /**
     * Displays the frame
     */
    public function render(): void {

        // If we have nothing, return the nothing 
        if( empty($this->sections) ) {
            require( WP_CUSTOM_FIELDS_PATH . '/templates/nothing.php' );
            return;
        } 
        
        // Cast the object to the frame variable.
        $frame = $this;
        
        // Render the frame
        require( WP_CUSTOM_FIELDS_PATH . '/templates/frame.php' );
        
    }
    
}