<?php
/** 
 * This class determines the displays of a specific field
 *
 * @author Michiel
 * @package Divergent
 * @since 1.0.0
 *
 * @todo implement options and functions for multilanguage options and retrieving values from any multilangual database.
 * @todo implement option to automatically add rows and detect previous and upcoming fields
 * @todo find a way to sanitize input on this side, here or already earlier
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) { 
    die(); 
} 

class Divergent_Fields {
    
    /**
     * Contains additional properties for the field,
     * such as icons, fonts and whether a field is allowed;
     */
    private static $properties;
    
    /**
     * Gets properties from a certain value
     *
     * @param string $type The type of properties to retrieve;
     */
    public static function get($type) {
    
        // Sets builtin icons
        if($type == 'icons') {
            self::builtin_icons();    
        }
        
        // Sets builtin fonts
        if($type == 'fonts') {
            self::builtin_fonts();     
        }
        
        return self::$properties[$type];
    }
    
    /**
     * Sets properties from a certain value
     * Hook on early after_setup_theme to add custom properties
     */
    public static function set($type, $value) {
        self::$properties[$type] = $value;       
    }
    
    /**
     * Determines which field to render
     * 
     * @param array $field The parameters for the specific field as set up in the configurations and later variables, such as saved values
     * @return string $output The rendered field;
     */
    public static function render($field = array()) {
        
        $output = '';
        
        // Field type should be allowed
        if( isset(self::$properties['disallowed']) && is_array(self::$properties['disallowed']) && in_array($field['type'], self::$properties['disallowed']) ) {
            return $output;
        }
        
        // Rewrite ID's so they can be used by JavaScript
        $field['name'] = ! isset($field['name']) ? $field['id'] : $field['name'];
        $field['id'] = str_replace('[', '_', $field['id']);
        $field['id'] = str_replace(']', '', $field['id']); 
        
        // Setup columns and rows
        $columns = isset($field['columns']) ? ' column ' . $field['columns'] : '';
        $row = isset($field['row']) ? $field['row'] : ''; 
        
        // Prepare placholders
        $field['placeholder'] = isset($field['placeholder']) ? $field['placeholder'] : '';
        
        // Get all declared classes
        $classes = get_declared_classes();

        // Check if there is a default value set up, and whether there is a value already stored for the specific field
        $default = isset($field['default']) ? $field['default'] : '';
        $field['values'] = isset($field['values']) ? $field['values'] : $default; 
        
        // Unserialize serialized values
        if(is_serialized($field['values'])) {
            $field['values'] = unserialize($field['values']);
        }         
        
        // Start rendering the HTLM output
        if($row == 'open') {
            $output .= '<div class="row">';
        }
        
        $output .= '<div class="divergent-option-field' . $columns . ' field-' . $field['type'] . '">';
        $output .= '    <div class="divergent-field-context">';
        
        if( isset($field['title']) ) {
            
            $tag = $field['type'] == 'heading' ? 'h2' : 'h4';                
            $output .= '        <'.$tag.'>' . $field['title'] . '</'.$tag.'>';   
        }
        
        if( isset($field['description']) ) {
            $output .= '        <p class="description">' . $field['description'] . '</p>';  
        }
        
        $output .= '    </div>';
        
        $output .= '    <div class="divergent-field-input">';
        
        foreach( $classes as $class ) {
            
            // Check of the loaded classes are an implementation of the Divergent_Fields interface and render them.
            if( in_array("Divergent_Field", class_implements($class) ) ) {
                
                // Grab configurations from the fields
                $field_configurations = $class::configurations();

                // Render the field if the type matches the configurations
                if( $field_configurations['type'] == $field['type'] ) {
                    $output .= $class::render($field);
                }   
            }
        
        }
        
        $output .= '    </div>';
        
        $output .= '</div>';
        
        if($row == 'close') {
            $output .= '</div><!-- .row -->';
        } 
                   
        return $output;
    }
    
    /**
     * Renders an input in combination with a select element, including measures
     * 
     * @param string $id The id for this element
     * @param string $name The name for this element
     * @param string $value The current value for this element
     * @param string $label The label for this element
     * @param string $placeholder The label for this element
     * @param string $icon The icon for this element
     */
    public static function dimension_field($id = '', $name = '', $value = '', $label = '', $placeholder = '', $icon = '') {
        
        $id         = ! empty($id) ? $id : '';
        $name       = ! empty($name) ? $name : '';
        $amount     = isset($value['amount']) ? $value['amount'] : '';
        $measure    = isset($value['unit']) ? $value['unit'] : '';
        $placeholder = ! empty($placeholder) ? ' placeholder="' . $placeholder . '"' : '';
        $label      = ! empty($label) ? '<label for="' . $id . '">' . $label . '</label>' : '';
        $icon       = ! empty($icon) ? '<i class="material-icons">' . $icon . '</i>' : '';
        
        $measurements =  array('px', 'em', '%', 'rem', 'vh', 'vw');
        
        $output = '<div class="divergent-dimensions-input">';
        $output .=      $label;    
        $output .=      $icon;    
        $output .= '    <input id="' . $id . '" type="number" name="' . $name . '[amount]" value="' . $amount . '"' . $placeholder . '>';
        $output .= '    <select name="' . $name . '[unit]">';
        
        foreach($measurements as $measurement) {
            $selected = $measurement == $measure ? 'selected="selected"' : ''; 
            $output .= '        <option value="' . $measurement . '"' . $selected . '>' . $measurement . '</option>';
        }
        
        $output .= '    </select>';
        $output .= '</div>';
        
        return $output;
    }
    
    /**
     * Retrieves an array of builtin fonts
     */
    private static function builtin_fonts() {   
        self::$properties['fonts'] = array(
            'websafe' => array(
                'georgia' => array('name' => 'Georgia', 'family' => '"Times New Roman", Times, serif'),
                'palatino' => array('name' => 'Palatino', 'family' => '"Times New Roman", Times, serif'),
                'timesnewroman' => array('name' => 'Times New Roman', 'family' => '"Times New Roman", Times, serif'),
                'arial' => array('name' => 'Arial', 'family' => 'Arial, Helvetica, sans-serif'),
                'comicsansms' => array('name' => 'Comic Sans MS', 'family' => '"Comic Sans MS", cursive, sans-serif'),
                'impact' => array('name' => 'Impact', 'family' => 'Impact, Charcoal, sans-serif'),
                'lucidasans' => array('name' => 'Lucida Sans', 'family' => '"Lucida Sans Unicode", "Lucida Grande", sans-serif'),
                'tahoma' => array('name' => 'Tahoma', 'family' => 'Tahoma, Geneva, sans-serif'),
                'trebuchet' => array('name' => 'Trebuchet MS', 'family' => '"Trebuchet MS", Helvetica, sans-serif'),
                'verdana' => array('name' => 'Verdana', 'family'  => 'Verdana, Geneva, sans-serif'),
                'couriernew' => array('name' => 'Courier New', 'family' => '"Courier New", Courier, monospace'),
                'lucidaconsole' => array('name' => 'Lucida Console', 'family' => '"Lucida Console", Monaco, monospace'),
            ),
            'google' => array(
                'abel' => array('name' => 'Abel', 'family' => '"Abel", sans-serif', 'styles' => array('normal'), 'weights' => array(400)),
                'alegreya' => array('name' => 'Alegreya', 'family' => '"Alegreya", serif', 'styles' => array('normal', 'italic'), 'weights' => array(400, 700, 900)),
                'alegreyasans' => array('name' => 'Alegreya Sans', 'family'  => '"Alegreya Sans", sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(100, 300, 400, 500, 700, 800, 900)),
                'amaticsc' => array('name' => 'Amatic SC', 'family'  => '"Amatic SC", cursive', 'styles' => array('normal'), 'weights' => array(400, 700)),
                'amiri' => array('name' => 'Amiri', 'family' => '"Amiri", serif', 'styles' => array('normal', 'italic'), 'weights' => array(400, 700)),
                'archivonarrow' => array('name' => 'Archivo Narrow', 'family' => '"Archivo Narrow", sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(400, 700)),
                'arimo' => array('name' => 'Arimo', 'family' => '"Arimo", sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(400, 700)),
                'arvo' => array('name' => 'Arvo', 'family' => '"Arvo", serif', 'styles' => array('normal', 'italic'), 'weights' => array(400, 700)),
                'asap' => array('name' => 'Asap', 'family' => '"Asap", sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(400, 700)),
                'breeserif' => array('name' => 'Bree Serif', 'family' => '"Bree Serif, serif', 'styles' => array('normal'), 'weights' => array(400)),
                'cabin' => array('name' => 'Cabin', 'family' => '"Cabin", sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(400, 500, 600, 700)),
                'cabinsketch' => array('name' => 'Cabin Sketch', 'family' => '"Cabin Sketch", cursive', 'styles' => array('normal'), 'weights' => array(400, 700)),
                'crimsontext' => array('name' => 'Crimson Text', 'family' => '"Crimson Text", serif', 'styles' => array('normal', 'italic'), 'weights' => array(400, 600, 700)),                
                'cuprum' => array('name' => 'Cuprum', 'family' => '"Cuprum", sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(400, 700)),                
                'dosis' => array('name' => 'Dosis', 'family' => '"Dosis", sans-serif', 'styles' => array('normal'), 'weights' => array(200, 300, 400, 500, 600, 700, 800)),                
                'droidsans' => array('name' => 'Droid Sans', 'family' => '"Droid Sans", sans-serif', 'styles' => array('normal'), 'weights' => array(400, 700)),                
                'droidserif' => array('name' => 'Droid Serif', 'family' => '"Droid Serif", serif', 'styles' => array('normal', 'italic'), 'weights' => array(400, 700)),                            
                'exo2' => array('name' => 'Exo 2', 'family' => '"Exo 2", sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(100, 200, 300, 400, 500, 600, 700, 800, 900)),                
                'firasans' => array('name' => 'Fira Sans', 'family' => '"Fira Sans", sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(300, 400, 500, 700)),                         
                'gloriahallelujah' => array('name' => 'Gloria Hallelujah', 'family'  => '"Gloria Hallelujah", cursive', 'styles' => array('normal'), 'weights' => array(400)),                
                'hind' => array('name' => 'Hind', 'family' => '"Hind", sans-serif', 'styles' => array('normal'), 'weights' => array(300, 400, 500, 600, 700)),                
                'inconsolata' => array('name' => 'Inconsolata', 'family' => '"Inconsolata", monospace', 'styles' => array('normal'), 'weights' => array(400, 700)),                
                'indieflower' => array('name' => 'Indie Flower', 'family' => '"Indie Flower", cursive', 'styles' => array('normal'), 'weights' => array(400)),                
                'josefinsans' => array('name' => 'Josefin Sans', 'family' => '"Josefin Sans", sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(100, 300, 400, 600, 700)),                
                'josefinslab' => array('name' => 'Josefin Slab', 'family' => '"Josefin Slab", serif', 'styles' => array('normal', 'italic'), 'weights' => array(100, 300, 400, 600, 700)),            
                'karla' => array('name' => 'Karla', 'family' => '"Karla", sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(400, 700)),                
                'lato' => array('name' => 'Lato', 'family' => '"Lato", sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(100, 300, 400, 700, 900)),
                'lobster' => array('name' => 'Lobster', 'family' => '"Lobster", cursive', 'styles' => array('normal'), 'weights' => array(400)),                
                'lora' => array('name' => 'Lora', 'family' => '"Lora", serif', 'styles' => array('normal', 'italic'), 'weights' => array(400, 700)),                
                'merriweather' => array('name' => 'Merriweather', 'family' => '"Merriweather", serif', 'styles' => array('normal', 'italic'), 'weights' => array(300, 400, 700, 900)),                
                'merriweathersans' => array('name' => 'Merriweather Sans', 'family'  => '"Merriweather Sans", sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(300, 400, 700, 900)),
                'montserrat' => array('name' => 'Montserrat', 'family' => '"Montserrat", sans-serif', 'styles' => array('normal'), 'weights' => array(400, 700)),                
                'muli' => array('name' => 'Muli', 'family' => '"Muli", sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(400, 700)),                
                'notosans' => array('name' => 'Noto Sans', 'family' => '"Noto Sans", sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(400, 700)),                             
                'notoserif' => array('name' => 'Noto Serif', 'family' => '"Noto Serif", serif', 'styles' => array('normal', 'italic'), 'weights' => array(400, 700)),                             
                'nunito' => array('name' => 'Nunito', 'family' => '"Nunito",sans-serif', 'styles' => array('normal'), 'weights' => array(300, 400, 700)),                
                'opensans' => array('name' => 'Open Sans', 'family' => '"Open Sans", sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(300, 400, 600, 700, 800)),
                'orbitron' => array('name' => 'Orbitron', 'family' => '"Orbitron", sans-serif', 'styles' => array('normal'), 'weights' => array(400, 500, 700, 900)),                
                'oswald' => array('name' => 'Oswald', 'family' => '"Oswald", sans-serif', 'styles' => array('normal'), 'weights' => array(300, 400, 700)),                
                'pacifico' => array('name' => 'Pacifico', 'family' => '"Pacifico", cursive', 'styles' => array('normal'), 'weights' => array(400)),                             
                'play' => array('name' => 'Play', 'family' => '"Play", sans-serif', 'styles' => array('normal'), 'weights' => array(400, 700)),                
                'playfairdisplay' => array('name' => 'Playfair Display', 'family'  => '"Playfair Display", serif', 'styles' => array('normal', 'italic'), 'weights' => array(400, 700, 900)),                
                'ptsans' => array('name' => 'PT Sans', 'family' => '"PT Sans", sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(400, 700)),                
                'ptsansnarrow' => array('name' => 'PT Sans Narrow', 'family'  => '"PT Sans Narrow", sans-serif', 'styles' => array('normal'), 'weights' => array(400, 700)),                
                'ptserif' => array('name' => 'PT Serif', 'family' => '"PT Serif", serif', 'styles' => array('normal', 'italic'), 'weights' => array(400, 700)),                
                'quicksand' => array('name' => 'Quicksand', 'family' => '"Quicksand", sans-serif', 'styles' => array('normal'), 'weights' => array(400, 700)),                
                'raleway' => array('name' => 'Raleway', 'family' => '"Raleway", sans-serif', 'styles' => array('normal'), 'weights' => array(100, 200, 300, 400, 500, 600, 700, 800, 900)),                
                'roboto' => array('name' => 'Roboto', 'family'  => '"Roboto", sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(100, 300, 400, 500, 700, 900)),                
                'robotocondensed' => array('name' => 'Roboto Condensed', 'family'  => '"Roboto Condensed", sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(300, 400, 700)),
                'robotoslab' => array('name' => 'Roboto Slab', 'family' => '"Roboto Slab", serif', 'styles' => array('normal'), 'weights' => array(100, 300, 400, 700)),              
                'shadowsintolight' => array('name' => 'Shadows Into Light', 'family' => '"Shadows Into Light", cursive', 'styles' => array('normal'), 'weights' => array(400)),                
                'signika' => array('name' => 'Signika', 'family' => '"Signika", sans-serif', 'styles' => array('normal'), 'weights' => array(300, 400, 600, 700)),                
                'sourcecodepro' => array('name' => 'Source Code Pro', 'family' => '"Source Code Pro", monospace', 'styles' => array('normal'), 'weights' => array(200, 300, 400, 500, 600, 700, 900)),
                'sourcesanspro' => array('name' => 'Source Sans Pro', 'family' => '"Source Sans Pro", sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(200, 300, 400, 600, 700, 900)),
                'titilliumweb' => array('name' => 'Titillium Web', 'family' => '"Titillium Web", sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(200, 300, 400, 600, 700)),            
                'ubuntu' => array('name' => 'Ubuntu', 'family' => '"Ubuntu", sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(300, 400, 500, 700)),
                'vollkorn' => array('name' => 'Vollkorn', 'family' => '"Vollkorn", serif', 'styles' => array('normal', 'italic'), 'weights' => array(400, 700)),                
                'yanonekaffeesatz' => array('name' => 'Yanone Kaffeesatz', 'family' => '"Yanone Kaffeesatz", sans-serif', 'styles' => array('normal'), 'weights' => array(200, 300, 400, 700))
            )
        );
    }
    
    /**
     * Returns an array of material icons
     */
    private static function builtin_icons() {
                
        $icons    = json_decode(file_get_contents(DIVERGENT_ASSETS_URL . 'js/vendor/material-icons.js'), true);
        $material = array();
        
        foreach($icons['icons'] as $key => $icon ) {
            $material = str_replace(' ', '_', strtolower($icon['name']));       
        }
        
        self::$properties['icons']['material'] = $material;     
    }
                   
}