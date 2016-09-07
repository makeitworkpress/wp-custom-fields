<?php 
/** 
 * This class is responsible for enqueueing all scripts and styles,
 * and also all script or style related settings. 
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) { 
    die; 
} 

class Divergent_Enqueue extends Divergent {
    
    /**
     * Holds all scripts
     */
    public $scripts;
    
    /**
     * Holds all styles
     */
    public $styles;    
        
    /**
     * Constructs enqueue, expects enqueue options array
     */
    protected function initialize(Array $params) {
        
        add_action('divergent_enqueue_initialize', $this);  
        
        $this->scripts = $params['scripts'];
        $this->styles = $params['styles'];

    }
    
    /** 
     * Registers all hooks
     */
    protected function register_hooks() {
         
        $this->actions = array(
            array('wp_enqueue_scripts', 'frontend_enqueue'),
            array('admin_enqueue_scripts', 'admin_enqueue')
        );
        
    }
    
    /**
     * Examines an array of scripts or styles and performs the right action
     *
     * @param string $type Whether to enqueue scripts or styles
     * @param string $context Whether to enqueue in front or backend
     */
    private function add_assets($type = '', $context = '') {
        
        $assets = $type == 'script' ? $this->scripts : $this->styles; 
        
        if( empty($assets) ) 
            return;
        
        foreach($assets as $asset) {
            
            // If there is no context defined, continue
            if($asset['context'] == $context  || $asset['context'] == 'both' ) {

                // Determine which function to execute, either register, dequeue or enqueue. For safety, hardcoded function names
                if(isset($asset['action']) && $asset['action'] == 'register') {
                    $action = 'register';
                } elseif(isset($asset['action']) && $asset['action'] == 'dequeue') {
                    $action = 'dequeue';
                } else {
                    $action = 'enqueue';
                }
                $last = $type == 'script' ?  $asset['in_footer'] : $asset['media'];

                $enqueue_function = 'wp_' . $action .  '_' . $type;
                $enqueue_function($asset['handle'], $asset['src'], $asset['deps'], $asset['ver'], $last);
                
            }

        }        
    }
            
    /**
     * Enqueues scripts and styles within the frontend context
     */
    public function frontend_enqueue() {
        $this->add_assets('script', 'front');
        $this->add_assets('style', 'front');           
    }
    
    /**
     * Enqueues styles in the admin
     */
    public function admin_enqueue() {
        
        $this->add_assets('script', 'admin');
        $this->add_assets('style', 'admin'); 
    }
    
    /**
     * Enqueues stylesheets in a non-blocking way
     */
    public function defer_css() {
        
    }
    
    /**
     * Makes sure that enqueued scripts are deferred
     */
    public function defer_js() {
        
    }
    
}