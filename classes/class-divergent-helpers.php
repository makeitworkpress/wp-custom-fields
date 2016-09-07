<?php
/**
 * Contains various static helper functions
 *
 * @package Divergent
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) { 
    die; 
} 

trait Divergent_Helpers {
    
    /**
     * Loads dependencies from an array
     *
     * @param array $dependencies The array of dependencies to load, using normal and admin key to define their context
     */
    public function load_dependencies( $dependencies = array() ) {
        
        // Load normal dependencies
        if( isset($dependencies['normal']) ) {
            foreach($dependencies['normal'] as $dependency) {
                require_once($dependency); 
            }
        }
        
        // Load admin dependencies
        if( isset($dependencies['admin']) ) {
            if( is_admin() ) {
                foreach($dependencies['admin'] as $dependency) {
                    require_once($dependency); 
                }   
            } 
        }
        
    }
    
    /**
     * Autoloads php files from certain folders
     *
     * @param array $folders Array of folders
     * @param boolean $glob whether to automatically load from folders, or load files from provided paths
     */
    public function load( $folders = array(), $glob = true) {
        
        foreach ($folders as $folder) {
            
            if($glob) { 
                // Folders are automatically scanned for php files
                foreach( glob($folder . '*.php') as $file) {
                    require_once( $file ); 
                }
            } else {
                // The filepaths & names should have been provided in the folders array
                foreach($folder as $file) {
                    require_once( $file ); 
                }                
            }
        }        
    }
        
    /**
     * Automatically lists specified filenames into an array
     *
     * @param array $folders The array of folders to search through;
     * @return array $filelist The list with specific files, grouped to their folder and keyed with their unique name;
     */
    public function filelist( $folders = array() ) {
        
        $filelist = array();
        
        foreach ($folders as $key => $folder) {
            
            foreach( glob($folder . '*.php') as $file) {
                
                // Store the unique class name as a key, so we can use it for reference later
                $file_info = pathinfo($file);
                $file_unique_name = substr($file_info['filename'], 12);
                
                $filelist[$key][$file_unique_name] = $file;

            }
        }
        
        return $filelist;
    }
    
}