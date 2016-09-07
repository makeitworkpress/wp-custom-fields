<?php
/**
 * Defines that classes can only be loaded once.
 *
 * @author Michiel
 * @since 1.0.0
 * @todo Add function for adding data
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) { 
    die; 
} 

abstract class Divergent_Abstract {
    
    /**
     * Determines whether a class has already been instanciated.
     * @access protected
     */
    protected static $instance = null;
    
    /**
     * Holds the filters registered in a class
     */
    protected $filters;
    
    /**
     * Holds the actions registered in a class
     */
    protected $actions;    
    
    /** 
     * Constructor. This allows the boot class to be only initialized once.
     */
    private function __construct() {}    
        
    /**
     * Gets the single instance. Applies Singleton Pattern
     *
     * @param array $params Obtional parameters which can be passed to the class
     */
    public static function instance(Array $params = array()) {
        
        $c = get_called_class();
        if ( !isset( self::$instance[$c] ) ) {
            self::$instance[$c] = new $c();
            self::$instance[$c]->initialize($params);
            self::$instance[$c]->register_hooks();
            self::$instance[$c]->add_hooks();
        }

        return self::$instance[$c];
    }
    
    /**
     * Adds registered hooks
     */
    private function add_hooks() {
        
        // Filters
        if( isset($this->filters) && is_array($this->filters) )  {
            foreach($this->filters as $filter) {
                $priority = isset($filter[2]) ? $filter[2] : ''; 
                $arguments = isset($filter[3]) ? $filter[3] : '';    
                    
                add_filter($filter[0], array($this, $filter[01]), $priority, $arguments);    
            }    
        }
        
        // Actions
        if( isset($this->actions) && is_array($this->actions) )  {
            foreach($this->actions as $action) {
                $priority = isset($action[2]) ? $action[2] : ''; 
                $arguments = isset($action[3]) ? $action[3] : ''; 
                    
                add_action($action[0], array($this, $action[1]), $priority, $arguments);    
            }    
        }         
    }
    
    /**
     * Determines the use of an initialize function
     *
     * @param array $params Optional parameters which can be passed to the class     
     */
    abstract protected function initialize(Array $params);
    
    /**
     * Holds the function for registering custom action and filter hooks.
     * Child classes should use register their hooks in the protected $filters and $actions property in the following format
     *
     * $var = array(
     *      array('string filter_or_action_name', 'string method_or_function', 'int priority', 'int number_of_arguments')
     * )
     */
    abstract protected function register_hooks();    

}