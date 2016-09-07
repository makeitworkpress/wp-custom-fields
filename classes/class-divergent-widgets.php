<?php
/**
 * Registers widgets
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) { 
    die; 
} 

class Divergent_Sidebars extends Divergent {
    
    /**
     * Holds the sidebars
     */
    public $sidebars;
    
    /**
     * Holds the sidebars
     */
    public $widgets;    
    
    /**
     * Constructor. Autoloads custom widgets and registers sidebars
     *
     * @param array $params The array with widgets  to register
     */
    protected function initialize(Array $params) {
        add_action('divergent_widgets_initialize', $this);
        $this->sidebars = $params;   
    }
    
    /**
     * Registers action hooks
     */    
    protected function register_hooks() {
        if( ! empty($this->sidebars) ) {
            $this->actions[] = array('widgets_init', 'add_widgets');
        }
    }
    
    /**
     * Adds sidebars based upon theme configurations
     */
    public function add_widgets() {
        foreach($this->sidebars as $widget) {
            
            // Do not continue if the widget file does not exist or the widget class is empty
            if( ! file_exists($widget['location']) || ! $widget['class']) )
                continue;
            
            require_once($widget['location']);
            
            if( class_exists($widget['location']) )
            register_widget();    
        }
    }
    
}