<?php
/**
 * Creates the variable values for a new options frame
 */
namespace Divergent;
use stdClass as stdClass;

class Divergent_Frame {
    
    // Contains our frame
    private $frame;
    
    /**
     * Set our values
     *
     * @param array $frames The array with option frames, such as option pages or metaboxes
     * @param array $values The array with values for the option fields
     */
    public function __construct( Array $frame, Array $values ) {
        
        // Our frame and values
        $this->frame    = $frame;  
        $this->values   = $values;  
        
        // Default public variables
        $this->class            = '';
        $this->errors           = '';
        $this->id               = $frame['id'];
        $this->resetButton      = '';
        $this->restoreButton    = '';
        $this->saveButton       = '';
        $this->sections         = array();
        $this->settingFields    = '';
        $this->title            = $frame['title'];
        $this->type             = '';
        
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
        $this->currentSection  = ! empty( $transient ) ? $transient : $this->frame['sections'][0]['id'];        
        
        // Loop through our sections
        foreach( $this->frame['sections'] as $key => $section ) {
            
            $frame->sections[$key]                  = $section;
            $frame->sections[$key]['active']        = $frame->currentSection == $section['id'] ? 'active' : '';
            $frame->sections[$key]['icon']          = ! empty( $section['icon'] ) ? $section['icon'] : false;
            
            foreach( $section['fields'] as $key => $field) {
                $frame->sections[$key]['fields'][]  = $this->populateField( $field );
            }
                
        }
        
    }
    
    /**
     * Populates the fields. Is executed by $this->populateSections
     *
     * @param array $fields The array with a single field
     */
    private function populateField( Array $field = array() ) {
        
        // We should have a field type
        if( isset($field['type']) )
            return $field;
        
        // Populate our variables
        $field                  = $field;
        $field['column']        = isset($field['columns'])          ? ' column ' . $field['columns']    : '';
        $field['form']          = __('We are sorry, the given field class does not exist', 'divergent');
        
        // Make sure our IDs do not contain brackets
        $field['id']            = str_replace('[', '_', $field['id']); 
        $field['id']            = str_replace(']', '', $field['id']); 
        $field['name']          = isset( $field['name'] )           ? $field['name']                    : $field['id'];
        
        $field['placeholder']   = isset( $field['placeholder'] )    ? $field['placeholder']             : '';
        $field['titleTag']      = $field['type'] == 'heading' ? 'h2' : 'h4';
        
        // Check if there is a default value set up, and whether there is a value already stored for the specific field
        $default                = isset( $field['default'] )        ? $field['default'] : '';
        $field['values']        = isset( $field['values'] )         ? maybe_unserialize( $this->values[$field['id']] ) : $default; 
        
        // Render our field form
        $class                  = 'Fields\Divergent_Field_' . ucfirst( $field['type'] );
        
        if( class_exists('Divergent_Field_' . ucfirst( $field['type'] ) ) )
            $field['form']      = $class::render($field);
        
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
        
        
        // Include our media      
        add_action('admin_enqueue_scripts', function () {
            wp_enqueue_media();    
        });
        
        // Cast the object to the frame variable.
        $frame = $this;
        
        // Render the frame
        require_once( DIVERGENT_PATH . '/templates/frame.php' );
        
    }
    
}