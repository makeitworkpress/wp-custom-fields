<?php
/**
 * Registers sidebars
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
     * @param array $params The array with sidebars to register
     */
    protected function initialize(Array $params) {
        add_action('divergent_sidebars_initialize', $this);
        $this->sidebars = $params;   
    }
    
    /**
     * Registers action hooks
     */
    protected function register_hooks() {
        if( ! empty($this->sidebars) ) {
            $this->actions[] = array('widgets_init', 'add_sidebars');
        }
    }
    
    /**
     * Adds sidebars based upon theme configurations
     */
    public function add_sidebars() {
        foreach($this->sidebars as $sidebar) {
            register_sidebar(
                array( 
                    'name'          => $sidebar['name'],
                    'id'            => $sidebar['id'],
                    'description'   => $sidebar['description'],
                    'class'         => $sidebar['class'],
                    'before_widget' => '<section id="%1$s" class="widget %2$s">',
                    'after_widget'  => '</section>',
                    'before_title'  => '<h3 class="widget-title">',
                    'after_title'   => '</h3>'                   
                )
            );    
        }
    }
    
}