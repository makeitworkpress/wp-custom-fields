<?php
/**
 * Loads the initial configurations for the plugin
 */

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) { 
    die; 
}

/**
 * Registers styles
 */  
$styles[] = array(
    'handle'    => 'fontawesome-css',
    'src'       => 'https://fonts.googleapis.com/icon?family=Material+Icons',
    'deps'      => array(),
    'ver'       => null,
    'media'     => 'all'
);     

$styles[] = array(
    'handle'    => 'admin-css',
    'src'       => DIVERGENT_ASSETS_URL . 'css/admin.min.css',
    'deps'      => array(),
    'ver'       => null,
    'media'     => 'all'
);

$styles[] = array(
    'handle'    => 'wp-color-picker',
    'src'       => '',
    'deps'      => array(),
    'ver'       => '',
    'media'     => ''
);

$styles[] = array(
    'handle'    => 'alpha-color-picker',
    'src'       => DIVERGENT_ASSETS_URL . 'css/vendor/alpha-color-picker.min.css',
    'deps'      => array( 'wp-color-picker' ),
    'ver'       => '',
    'media'     => ''
);    

$styles[] = array(
    'handle'    => 'mirror-css',
    'src'       => DIVERGENT_ASSETS_URL . 'css/vendor/codemirror.min.css',
    'deps'      => array(),
    'ver'       => '',
    'media'     => ''
);     

/**
 * Registers scripts
 */
$scripts[] = array(
    'handle'    => 'alpha-color-picker',
    'src'       => DIVERGENT_ASSETS_URL . 'js/vendor/alpha-color-picker.min.js',
    'deps'      => array( 'jquery', 'wp-color-picker' ),
    'ver'       => '',
    'in_footer' => 'true', 
    'action'    => 'register',
);      

$scripts[] = array(
    'handle'    => 'admin-js',
    'src'       => DIVERGENT_ASSETS_URL . 'js/divergent.js',
    'deps'      => array('jquery', 'alpha-color-picker'),
    'ver'       => null,
    'in_footer' => true,  
    'action'    => 'register'
);  

$scripts[] = array(
    'handle'    => 'google-maps-js',
    'src'       => 'https://maps.googleapis.com/maps/api/js?libraries=places&key=' . GOOGLE_MAPS_KEY,
    'deps'      => null,
    'ver'       => '3',
    'in_footer' => true,  
    'action'    => 'register'
); 

$scripts[] = array(
    'handle'    => 'mirror-js',
    'src'       => DIVERGENT_ASSETS_URL . 'js/vendor/codemirror.min.js',
    'deps'      => null,
    'ver'       => null,
    'in_footer' => true,  
    'action'    => 'register'
);


/**
 * Contains builtin fonts
 */
$fonts = array(
    'websafe' => array(
        'georgia' => array('name' => 'Georgia', 'family' => 'Georgia, serif'),
        'palatino' => array('name' => 'Palatino', 'family' => 'Palatino, serif'),
        'timesnewroman' => array('name' => 'Times New Roman', 'family' => 'Times New Roman, Times, serif'),
        'arial' => array('name' => 'Arial', 'family' => 'Arial, Helvetica, sans-serif'),
        'comicsansms' => array('name' => 'Comic Sans MS', 'family' => 'Comic Sans MS, cursive, sans-serif'),
        'impact' => array('name' => 'Impact', 'family' => 'Impact, Charcoal, sans-serif'),
        'lucidasans' => array('name' => 'Lucida Sans', 'family' => 'Lucida Sans Unicode, Lucida Grande, sans-serif'),
        'tahoma' => array('name' => 'Tahoma', 'family' => 'Tahoma, Geneva, sans-serif'),
        'trebuchet' => array('name' => 'Trebuchet MS', 'family' => 'Trebuchet MS, Helvetica, sans-serif'),
        'verdana' => array('name' => 'Verdana', 'family'  => 'Verdana, Geneva, sans-serif'),
        'couriernew' => array('name' => 'Courier New', 'family' => 'Courier New, Courier, monospace'),
        'lucidaconsole' => array('name' => 'Lucida Console', 'family' => 'Lucida Console, Monaco, monospace'),
    ),
    'google' => array(
        'abel' => array('name' => 'Abel', 'family' => 'Abel, sans-serif', 'styles' => array('normal'), 'weights' => array(400)),
        'alegreya' => array('name' => 'Alegreya', 'family' => 'Alegreya, serif', 'styles' => array('normal', 'italic'), 'weights' => array(400, 700, 900)),
        'alegreyasans' => array('name' => 'Alegreya Sans', 'family'  => 'Alegreya Sans, sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(100, 300, 400, 500, 700, 800, 900)),
        'amaticsc' => array('name' => 'Amatic SC', 'family'  => 'Amatic SC, cursive', 'styles' => array('normal'), 'weights' => array(400, 700)),
        'amiri' => array('name' => 'Amiri', 'family' => 'Amiri, serif', 'styles' => array('normal', 'italic'), 'weights' => array(400, 700)),
        'archivonarrow' => array('name' => 'Archivo Narrow', 'family' => 'Archivo Narrow, sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(400, 700)),
        'arimo' => array('name' => 'Arimo', 'family' => 'Arimo, sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(400, 700)),
        'arvo' => array('name' => 'Arvo', 'family' => 'Arvo, serif', 'styles' => array('normal', 'italic'), 'weights' => array(400, 700)),
        'asap' => array('name' => 'Asap', 'family' => 'Asap, sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(400, 700)),
        'breeserif' => array('name' => 'Bree Serif', 'family' => 'Bree Serif, serif', 'styles' => array('normal'), 'weights' => array(400)),
        'cabin' => array('name' => 'Cabin', 'family' => 'Cabin, sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(400, 500, 600, 700)),
        'cabinsketch' => array('name' => 'Cabin Sketch', 'family' => 'Cabin Sketch, cursive', 'styles' => array('normal'), 'weights' => array(400, 700)),
        'crimsontext' => array('name' => 'Crimson Text', 'family' => 'Crimson Text, serif', 'styles' => array('normal', 'italic'), 'weights' => array(400, 600, 700)),           
        'cuprum' => array('name' => 'Cuprum', 'family' => 'Cuprum, sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(400, 700)),                
        'dosis' => array('name' => 'Dosis', 'family' => 'Dosis, sans-serif', 'styles' => array('normal'), 'weights' => array(200, 300, 400, 500, 600, 700, 800)),                
        'droidsans' => array('name' => 'Droid Sans', 'family' => 'Droid Sans, sans-serif', 'styles' => array('normal'), 'weights' => array(400, 700)),                
        'droidserif' => array('name' => 'Droid Serif', 'family' => 'Droid Serif, serif', 'styles' => array('normal', 'italic'), 'weights' => array(400, 700)),                   
        'exo2' => array('name' => 'Exo 2', 'family' => 'Exo 2, sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(100, 200, 300, 400, 500, 600, 700, 800, 900)),                
        'firasans' => array('name' => 'Fira Sans', 'family' => 'Fira Sans, sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(300, 400, 500, 700)),           
        'gloriahallelujah' => array('name' => 'Gloria Hallelujah', 'family'  => 'Gloria Hallelujah, cursive', 'styles' => array('normal'), 'weights' => array(400)),             
        'hind' => array('name' => 'Hind', 'family' => 'Hind, sans-serif', 'styles' => array('normal'), 'weights' => array(300, 400, 500, 600, 700)),                
        'inconsolata' => array('name' => 'Inconsolata', 'family' => 'Inconsolata, monospace', 'styles' => array('normal'), 'weights' => array(400, 700)),                
        'indieflower' => array('name' => 'Indie Flower', 'family' => 'Indie Flower, cursive', 'styles' => array('normal'), 'weights' => array(400)),                
        'josefinsans' => array('name' => 'Josefin Sans', 'family' => 'Josefin Sans, sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(100, 300, 400, 600, 700)),                
        'josefinslab' => array('name' => 'Josefin Slab', 'family' => 'Josefin Slab, serif', 'styles' => array('normal', 'italic'), 'weights' => array(100, 300, 400, 600, 700)), 
        'karla' => array('name' => 'Karla', 'family' => 'Karla, sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(400, 700)),                
        'lato' => array('name' => 'Lato', 'family' => 'Lato, sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(100, 300, 400, 700, 900)),
        'lobster' => array('name' => 'Lobster', 'family' => 'Lobster, cursive', 'styles' => array('normal'), 'weights' => array(400)),                
        'lora' => array('name' => 'Lora', 'family' => 'Lora, serif', 'styles' => array('normal', 'italic'), 'weights' => array(400, 700)),                
        'merriweather' => array('name' => 'Merriweather', 'family' => 'Merriweather, serif', 'styles' => array('normal', 'italic'), 'weights' => array(300, 400, 700, 900)),     
        'merriweathersans' => array('name' => 'Merriweather Sans', 'family'  => 'Merriweather Sans, sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(300, 400, 700, 900)),
        'montserrat' => array('name' => 'Montserrat', 'family' => 'Montserrat, sans-serif', 'styles' => array('normal'), 'weights' => array(400, 700)),                
        'muli' => array('name' => 'Muli', 'family' => 'Muli, sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(400, 700)),                
        'notosans' => array('name' => 'Noto Sans', 'family' => 'Noto Sans, sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(400, 700)),                     
        'notoserif' => array('name' => 'Noto Serif', 'family' => 'Noto Serif, serif', 'styles' => array('normal', 'italic'), 'weights' => array(400, 700)),                       
        'nunito' => array('name' => 'Nunito', 'family' => 'Nunito,sans-serif', 'styles' => array('normal'), 'weights' => array(300, 400, 700)),                
        'opensans' => array('name' => 'Open Sans', 'family' => 'Open Sans, sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(300, 400, 600, 700, 800)),
        'orbitron' => array('name' => 'Orbitron', 'family' => 'Orbitron, sans-serif', 'styles' => array('normal'), 'weights' => array(400, 500, 700, 900)),                
        'oswald' => array('name' => 'Oswald', 'family' => 'Oswald, sans-serif', 'styles' => array('normal'), 'weights' => array(300, 400, 700)),                
        'pacifico' => array('name' => 'Pacifico', 'family' => 'Pacifico, cursive', 'styles' => array('normal'), 'weights' => array(400)),                             
        'play' => array('name' => 'Play', 'family' => 'Play, sans-serif', 'styles' => array('normal'), 'weights' => array(400, 700)),                
        'playfairdisplay' => array('name' => 'Playfair Display', 'family'  => 'Playfair Display, serif', 'styles' => array('normal', 'italic'), 'weights' => array(400, 700, 900)),                
        'ptsans' => array('name' => 'PT Sans', 'family' => 'PT Sans, sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(400, 700)),                
        'ptsansnarrow' => array('name' => 'PT Sans Narrow', 'family'  => 'PT Sans Narrow, sans-serif', 'styles' => array('normal'), 'weights' => array(400, 700)),               
        'ptserif' => array('name' => 'PT Serif', 'family' => 'PT Serif, serif', 'styles' => array('normal', 'italic'), 'weights' => array(400, 700)),                
        'quicksand' => array('name' => 'Quicksand', 'family' => 'Quicksand, sans-serif', 'styles' => array('normal'), 'weights' => array(400, 700)),                
        'raleway' => array('name' => 'Raleway', 'family' => 'Raleway, sans-serif', 'styles' => array('normal'), 'weights' => array(100, 200, 300, 400, 500, 600, 700, 800, 900)), 
        'roboto' => array('name' => 'Roboto', 'family'  => 'Roboto, sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(100, 300, 400, 500, 700, 900)),       
        'robotocondensed' => array('name' => 'Roboto Condensed', 'family'  => 'Roboto Condensed, sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(300, 400, 700)),
        'robotoslab' => array('name' => 'Roboto Slab', 'family' => 'Roboto Slab, serif', 'styles' => array('normal'), 'weights' => array(100, 300, 400, 700)),              
        'shadowsintolight' => array('name' => 'Shadows Into Light', 'family' => 'Shadows Into Light, cursive', 'styles' => array('normal'), 'weights' => array(400)),             
        'signika' => array('name' => 'Signika', 'family' => 'Signika, sans-serif', 'styles' => array('normal'), 'weights' => array(300, 400, 600, 700)),                
        'sourcecodepro' => array('name' => 'Source Code Pro', 'family' => 'Source Code Pro, monospace', 'styles' => array('normal'), 'weights' => array(200, 300, 400, 500, 600, 700, 900)),
        'sourcesanspro' => array('name' => 'Source Sans Pro', 'family' => 'Source Sans Pro, sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(200, 300, 400, 600, 700, 900)),
        'titilliumweb' => array('name' => 'Titillium Web', 'family' => 'Titillium Web, sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(200, 300, 400, 600, 700)),            
        'ubuntu' => array('name' => 'Ubuntu', 'family' => 'Ubuntu, sans-serif', 'styles' => array('normal', 'italic'), 'weights' => array(300, 400, 500, 700)),
        'vollkorn' => array('name' => 'Vollkorn', 'family' => 'Vollkorn, serif', 'styles' => array('normal', 'italic'), 'weights' => array(400, 700)),                
        'yanonekaffeesatz' => array('name' => 'Yanone Kaffeesatz', 'family' => 'Yanone Kaffeesatz, sans-serif', 'styles' => array('normal'), 'weights' => array(200, 300, 400, 700))
    )
);

/**
 * Retrieve our icons
 */
$iconsFile          = json_decode( file_get_contents(DIVERGENT_ASSETS_URL . 'js/vendor/material-icons.json'), true );

foreach( $iconsFile['icons'] as $key => $icon ) {
    $material[$key]     = str_replace(' ', '_', strtolower($icon['name']));       
}

$icons['material']  = $material;