<?php
/**
 * This is the container class for the plugin, containing all necessary configuration
 *
 * @package Divergent
 * @author Michiel Tramper 
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) { 
    die; 
} 

class Divergent extends Divergent_Abstract {
    
    /**
     * Make the helper class methods available to the framework
     */
    use Divergent_Helpers;
      
    /**
     * These properties hold all configurations, such as scripts, added widgets, optimalizations, optionfields and the current framework object.
     */
    protected $divergent; 
    
    /**
     * Holds the display for each type of input field
     */
    protected $field_views; 
    
    /**
     * Initializes the plugin 
     *
     * @param mixed $params The parameters passed to this object
     */
    protected function initialize(Array $params) {
        
        do_action('divergent_initialize', $this);  

    }
    
    /**
     * Adds functions to WordPress hooks - is automatically performed at a new instance
     */
    protected function register_hooks() { 
        
        /**
         * Executes the class on late after_setup_theme,
         * so plugins, child themes and themes can hook into filters on earlier occasions 
         */             
        $this->actions[] = array('after_setup_theme', 'setup', 20);

    }
    
    /**
     * Set-ups the filters that allow external configurations to drip
     * Set-ups up all divergent modules 
     * Set-ups all back-end option screens, providing they are requested by the configurations
     */
    final public function setup() {
        
        $this->add_configurations();
        $this->setup_modules();
        
        if( is_admin() ) {
            
            $this->setup_framework();
        }        
    }
    
    /**
     * Adds necessary filters for adjusting configurations
     */
    final private function add_configurations() {
                
        // Set filters for data types and thus add additional data
        $data_types = array('postmeta', 'taxmeta', 'option_pages', 'sidebars', 'post_types', 'taxonomies', 'widgets', 'scripts', 'styles');
        foreach($data_types as $type) {
            $values = isset($this->divergent[$type]) ? $this->divergent[$type] : array();
            $this->divergent[$type] = apply_filters('divergent_' . $type, $values);
        }
        
    }

    /**
     * Loads additional modules if settings are present
     */
    final private function setup_modules() {    
        
        // Provide a mechanism for enqueueing scripts
        if( ! empty($this->divergent['scripts']) || ! empty($this->divergent['styles']) ) {
            require_once( DIVERGENT_INCLUDES_PATH . 'class-divergent-enqueue.php');
            $this->enqueue = Divergent_Enqueue::instance(array('scripts' => $this->divergent['scripts'], 'styles' => $this->divergent['styles'])); 
        }
        
        // Initialize customposts
        if( ! empty($this->divergent['post_types']) || ! empty($this->divergent['taxonomies']) ) {
            require_once( DIVERGENT_INCLUDES_PATH . 'class-divergent-posts.php');
            $this->customposts = Divergent_Customposts::instance( array('post_types' => $this->divergent['post_types'], 'taxonomies' => $this->divergent['taxonomies']) );
        }
        
        // Initialize custom sidebars
        if( ! empty($this->divergent['sidebars']) ) {
            require_once( DIVERGENT_INCLUDES_PATH . 'class-divergent-sidebars.php');
            $this->sidebars = Divergent_Sidebars::instance($this->divergent['sidebars']);  
        }
        
        if( ! empty($this->divergent['widgets']) ) {
            require_once( DIVERGENT_INCLUDES_PATH . 'class-divergent-widgets.php');
            $this->optimize = Divergent_Optimize::instance($this->divergent['widgets']);  
        }        
                  
    }
    
    /**
     * Set-up the option pages for the framework
     */
    final public function setup_framework() {
        
        global $pagenow;
        
        // Adds options page
        if( ! empty($this->divergent['option_pages']) ) { 
            
            $this->require_files('options-page');
            $this->autoload_fields();
            $this->option_pages = Divergent_Options_Page::instance($this->divergent['option_pages']); // Create an option page 

        }
        
        // Adds metaboxes
        if( ! empty($this->divergent['postmeta']) && ($pagenow == 'post.php' || $pagenow == 'post-new.php') ) {
            
            $this->require_files('postmeta');
            $this->autoload_fields(); 
            $this->postmeta = Divergent_Postmeta::instance($this->divergent['postmeta']); 

        }  
        
        // Adds taxonomy metaboxes
        if( ! empty($this->divergent['taxmeta']) && ($pagenow == 'edit-tags.php' ) ) {
            
            $this->require_files('taxmeta');
            $this->autoload_fields(); 
            $this->taxmeta = Divergent_Taxmeta::instance($this->divergent['taxmeta']); 

        }  
        
        // Adds user metaboxes
        if( ! empty($this->divergent['usermeta']) && ($pagenow == 'user-new.php' || $pagenow == 'profile.php' || $pagenow == 'user-edit.php'  ) ) {
            
            $this->require_files('usermeta');
            $this->autoload_fields(); 
            $this->usermeta = Divergent_Usermeta::instance($this->divergent['usermeta']); 

        }        

    } 
    
    /**
     * Loads the basic set of required scripts for a certains settings page
     *
     * @param string $content The context to load the files for
     */
    protected function require_files($context = '') {

        require_once( DIVERGENT_INCLUDES_PATH . 'controllers/class-divergent-validate.php');
        require_once( DIVERGENT_INCLUDES_PATH . 'controllers/class-divergent-' . $context . '.php');
        
        // Metaboxes share the same view
        if($context == 'postmeta' || $context == 'usermeta' || $context == 'taxmeta')
            $context = 'metaboxes';
            
        require_once( DIVERGENT_INCLUDES_PATH . 'views/class-divergent-views-' . $context . '.php');
        require_once( DIVERGENT_INCLUDES_PATH . 'views/class-divergent-fields.php');
        require_once( DIVERGENT_INCLUDES_PATH . 'views/interface-divergent-fields.php');        
        
    }
                   
    /**
     * Automatically loads all view fields to be displayed into an array
     * These fields represent a certain field type, such as an input or editor field
     */
    protected function autoload_fields() { 
        
        // Load all input fields
        $fields = array(
            'fields'  => DIVERGENT_INCLUDES_PATH . 'views/fields/',
        );  
        
        // Filter the fields so extensions can add other folders
        $fields = apply_filters('divergent_fields', $fields);
        
        // Store all modules in accessible data
        $this->field_views = $this->filelist($fields); 
        
        // Load the fields
        $this->load($this->field_views, false);

    }                    
    
    /**
     * Retrieves certain configurations
     *
     * @param string $type The kind of configurations to get
     * @return array $configurations The array of configurations for the respective type
     */
    public function get($type) {
        return $this->divergent[$type];
    }     
        
    /**
     * Allows to adds certain data, such as data for fields. 
     * If hooked late on after_setup_theme but before init, this will add fields.
     *
     * @param string $type The type to which you want to add values
     * @param array $values The respective values;
     */
    public function set($type, $values) {
        $this->divergent[$type] = $values;    
    }    
       
}