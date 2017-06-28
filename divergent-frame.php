<?php
/**
 * Creates the variable values for a new options frame
 * This acts as the main controller for passing data to a template.
 */
namespace Divergent;
use stdClass as stdClass;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) 
    die;

class Divergent_Frame {
    
    // Contains our frame
    private $frame;
    
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
        $this->class            = isset($frame['class']) ? $frame['class'] : '';
        $this->errors           = '';
        $this->id               = $frame['id'];
        $this->resetButton      = '';
        $this->restoreButton    = '';
        $this->saveButton       = '';
        $this->sections         = array();
        $this->settingFields    = '';
        $this->title            = $frame['title'];
        $this->type             = '';

        // Include our scripts and media      
        wp_enqueue_script('jquery-validate');
        wp_enqueue_script('alpha-color-picker');
        wp_enqueue_script('admin-js');
        wp_enqueue_media();           
        
        // Populate Variables
        $this->populateSections();
        
    }
    
    /**
     * Populates the sections and their fields
     */
    private function populateSections() {
    
        if( ! isset($this->frame['sections']) || ! is_array($this->frame['sections']) )
            return;
        
        // Current section
        $transient              = get_transient( 'divergent_current_section_' . $this->frame['id'] );
        $this->currentSection   = ! empty( $transient ) ? $transient : $this->frame['sections'][0]['id'];        
        
        // Loop through our sections
        foreach( $this->frame['sections'] as $key => $section ) {
            
            $this->sections[$key]                  = $section;
            $this->sections[$key]['active']        = $this->currentSection == $section['id'] ? 'active'          : '';
            $this->sections[$key]['description']   = isset( $section['description'] ) ? $section['description'] : '';
            $this->sections[$key]['fields']        = array();
            $this->sections[$key]['icon']          = ! empty( $section['icon'] ) ? $section['icon']  : false;
            
            foreach( $section['fields'] as $field) {
                $this->sections[$key]['fields'][]  = $this->populateField( $field );
            }
                
        }
        
    }
    
    /**
     * Populates the fields. Is executed by $this->populateSections
     *
     * @param array $fields The array from a single field
     */
    private function populateField( Array $field = array() ) {
        
        // We should have a field type
        if( ! isset($field['type']) )
            return $field;
        
        // Populate our variables
        $field                  = $field;
        $field['column']        = isset($field['columns'])              ?  $field['columns'] : 'full';
        $field['form']          = __('We are sorry, the given field class does not exist', 'divergent');
        
        // Make sure our IDs do not contain brackets
        $field['id']            = str_replace('[', '_', $field['id']); 
        $field['id']            = str_replace(']', '', $field['id']); 
        $field['name']          = isset( $field['name'] )               ? $field['name']                    : $field['id'];
        
        $field['placeholder']   = isset( $field['placeholder'] )        ? $field['placeholder']             : '';
        $field['titleTag']      = $field['type'] == 'heading'           ? 'h2'                              : 'h4';
        
        // Check if there is a default value set up, and whether there is a value already stored for the specific field
        $default                = isset( $field['default'] )            ? $field['default'] : '';
        $field['values']        = isset( $this->values[$field['id']] )  ? maybe_unserialize( $this->values[$field['id']] ) : $default; 
        
        // Render our field form, allow custom fields to be filtered.
        $class                  = apply_filters('divergent_field_class', 'Divergent\Fields\Divergent_Field_' . ucfirst( $field['type'] ), $field );
        
        if( class_exists($class) )
            $field['form']      = apply_filters('divergent_field_form', $class::render($field), $field);
        
        return $field;
        
    }
    
    /**
     * Displays the frame
     */
    public function render() {

        // If we have nothing, return the nothing 
        if( empty($this->sections) ) {
            require_once( DIVERGENT_PATH . '/templates/nothing.php' );
            return;
        } 
        
        // Cast the object to the frame variable.
        $frame = $this;
        
        // Render the frame
        require_once( DIVERGENT_PATH . '/templates/frame.php' );
        
    }
    
}