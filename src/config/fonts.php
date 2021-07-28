<?php
/**
 * Contains builtin fonts
 * We're going to automate this someday with somekind of API so this can be dynamically updated.
 * Just need to find a way to automatically generate previews for these fonts
 */

// Bail if accessed directly
if ( ! defined('ABSPATH') ) {
    die; 
}

$fonts = [
    'websafe' => [
        'georgia'       => ['name' => 'Georgia', 'family' => 'Georgia, serif'],
        'palatino'      => ['name' => 'Palatino', 'family' => 'Palatino, serif'],
        'timesnewroman' => ['name' => 'Times New Roman', 'family' => 'Times New Roman, Times, serif'],
        'arial'         => ['name' => 'Arial', 'family' => 'Arial, Helvetica, sans-serif'],
        'comicsansms'   => ['name' => 'Comic Sans MS', 'family' => 'Comic Sans MS, cursive, sans-serif'],
        'impact'        => ['name' => 'Impact', 'family' => 'Impact, Charcoal, sans-serif'],
        'lucidasans'    => ['name' => 'Lucida Sans', 'family' => 'Lucida Sans Unicode, Lucida Grande, sans-serif'],
        'tahoma'        => ['name' => 'Tahoma', 'family' => 'Tahoma, Geneva, sans-serif'],
        'trebuchet'     => ['name' => 'Trebuchet MS', 'family' => 'Trebuchet MS, Helvetica, sans-serif'],
        'verdana'       => ['name' => 'Verdana', 'family'  => 'Verdana, Geneva, sans-serif'],
        'couriernew'    => ['name' => 'Courier New', 'family' => 'Courier New, Courier, monospace'],
        'lucidaconsole' => ['name' => 'Lucida Console', 'family' => 'Lucida Console, Monaco, monospace'],
    ],
    'google' => [
        'abel'              => ['name' => 'Abel', 'family' => 'Abel, sans-serif', 'styles' => ['normal'], 'weights' => [400]],
        'alegreya'          => ['name' => 'Alegreya', 'family' => 'Alegreya, serif', 'styles' => ['normal', 'italic'], 'weights' => [400, 700, 900]],
        'alegreyasans'      => ['name' => 'Alegreya Sans', 'family'  => 'Alegreya Sans, sans-serif', 'styles' => ['normal', 'italic'], 'weights' => [100, 300, 400, 500, 700, 800, 900]],
        'amaticsc'          => ['name' => 'Amatic SC', 'family'  => 'Amatic SC, cursive', 'styles' => ['normal'], 'weights' => [400, 700]],
        'amiri'             => ['name' => 'Amiri', 'family' => 'Amiri, serif', 'styles' => ['normal', 'italic'], 'weights' => [400, 700]],
        'archivonarrow'     => ['name' => 'Archivo Narrow', 'family' => 'Archivo Narrow, sans-serif', 'styles' => ['normal', 'italic'], 'weights' => [400, 700]],
        'arimo'             => ['name' => 'Arimo', 'family' => 'Arimo, sans-serif', 'styles' => ['normal', 'italic'], 'weights' => [400, 700]],
        'arvo'              => ['name' => 'Arvo', 'family' => 'Arvo, serif', 'styles' => ['normal', 'italic'], 'weights' => [400, 700]],
        'asap'              => ['name' => 'Asap', 'family' => 'Asap, sans-serif', 'styles' => ['normal', 'italic'], 'weights' => [400, 700]],
        'breeserif'         => ['name' => 'Bree Serif', 'family' => 'Bree Serif, serif', 'styles' => ['normal'], 'weights' => [400]],
        'cabin'             => ['name' => 'Cabin', 'family' => 'Cabin, sans-serif', 'styles' => ['normal', 'italic'], 'weights' => [400, 500, 600, 700]],
        'cabinsketch'       => ['name' => 'Cabin Sketch', 'family' => 'Cabin Sketch, cursive', 'styles' => ['normal'], 'weights' => [400, 700]],
        'crimsontext'       => ['name' => 'Crimson Text', 'family' => 'Crimson Text, serif', 'styles' => ['normal', 'italic'], 'weights' => [400, 600, 700]],           
        'cuprum'            => ['name' => 'Cuprum', 'family' => 'Cuprum, sans-serif', 'styles' => ['normal', 'italic'], 'weights' => [400, 700]],                
        'dosis'             => ['name' => 'Dosis', 'family' => 'Dosis, sans-serif', 'styles' => ['normal'], 'weights' => [200, 300, 400, 500, 600, 700, 800]],                
        'droidsans'         => ['name' => 'Droid Sans', 'family' => 'Droid Sans, sans-serif', 'styles' => ['normal'], 'weights' => [400, 700]],                
        'droidserif'        => ['name' => 'Droid Serif', 'family' => 'Droid Serif, serif', 'styles' => ['normal', 'italic'], 'weights' => [400, 700]],                   
        'exo2'              => ['name' => 'Exo 2', 'family' => 'Exo 2, sans-serif', 'styles' => ['normal', 'italic'], 'weights' => [100, 200, 300, 400, 500, 600, 700, 800, 900]],                
        'firasans'          => ['name' => 'Fira Sans', 'family' => 'Fira Sans, sans-serif', 'styles' => ['normal', 'italic'], 'weights' => [300, 400, 500, 700]],           
        'gloriahallelujah'  => ['name' => 'Gloria Hallelujah', 'family'  => 'Gloria Hallelujah, cursive', 'styles' => ['normal'], 'weights' => [400]],             
        'hind'              => ['name' => 'Hind', 'family' => 'Hind, sans-serif', 'styles' => ['normal'], 'weights' => [300, 400, 500, 600, 700]],                
        'inconsolata'       => ['name' => 'Inconsolata', 'family' => 'Inconsolata, monospace', 'styles' => ['normal'], 'weights' => [400, 700]],                
        'indieflower'       => ['name' => 'Indie Flower', 'family' => 'Indie Flower, cursive', 'styles' => ['normal'], 'weights' => [400]],                
        'josefinsans'       => ['name' => 'Josefin Sans', 'family' => 'Josefin Sans, sans-serif', 'styles' => ['normal', 'italic'], 'weights' => [100, 300, 400, 600, 700]],                
        'josefinslab'       => ['name' => 'Josefin Slab', 'family' => 'Josefin Slab, serif', 'styles' => ['normal', 'italic'], 'weights' => [100, 300, 400, 600, 700]], 
        'karla'             => ['name' => 'Karla', 'family' => 'Karla, sans-serif', 'styles' => ['normal', 'italic'], 'weights' => [400, 700]],                
        'lato'              => ['name' => 'Lato', 'family' => 'Lato, sans-serif', 'styles' => ['normal', 'italic'], 'weights' => [100, 300, 400, 700, 900]],
        'lobster'           => ['name' => 'Lobster', 'family' => 'Lobster, cursive', 'styles' => ['normal'], 'weights' => [400]],                
        'lora'              => ['name' => 'Lora', 'family' => 'Lora, serif', 'styles' => ['normal', 'italic'], 'weights' => [400, 700]],                
        'merriweather'      => ['name' => 'Merriweather', 'family' => 'Merriweather, serif', 'styles' => ['normal', 'italic'], 'weights' => [300, 400, 700, 900]],     
        'merriweathersans'  => ['name' => 'Merriweather Sans', 'family'  => 'Merriweather Sans, sans-serif', 'styles' => ['normal', 'italic'], 'weights' => [300, 400, 700, 900]],
        'montserrat'        => ['name' => 'Montserrat', 'family' => 'Montserrat, sans-serif', 'styles' => ['normal', 'italic'], 'weights' => [100, 200, 300, 400, 500, 600, 700, 800, 900]],                
        'muli'              => ['name' => 'Muli', 'family' => 'Muli, sans-serif', 'styles' => ['normal', 'italic'], 'weights' => [400, 700]],                
        'notosans'          => ['name' => 'Noto Sans', 'family' => 'Noto Sans, sans-serif', 'styles' => ['normal', 'italic'], 'weights' => [400, 700]],                     
        'notoserif'         => ['name' => 'Noto Serif', 'family' => 'Noto Serif, serif', 'styles' => ['normal', 'italic'], 'weights' => [400, 700]],                       
        'nunito'            => ['name' => 'Nunito', 'family' => 'Nunito,sans-serif', 'styles' => ['normal'], 'weights' => [300, 400, 700]],                
        'opensans'          => ['name' => 'Open Sans', 'family' => 'Open Sans, sans-serif', 'styles' => ['normal', 'italic'], 'weights' => [300, 400, 600, 700, 800]],
        'orbitron'          => ['name' => 'Orbitron', 'family' => 'Orbitron, sans-serif', 'styles' => ['normal'], 'weights' => [400, 500, 700, 900]],                
        'oswald'            => ['name' => 'Oswald', 'family' => 'Oswald, sans-serif', 'styles' => ['normal'], 'weights' => [300, 400, 700]],                
        'pacifico'          => ['name' => 'Pacifico', 'family' => 'Pacifico, cursive', 'styles' => ['normal'], 'weights' => [400]],                             
        'play'              => ['name' => 'Play', 'family' => 'Play, sans-serif', 'styles' => ['normal'], 'weights' => [400, 700]],                
        'playfairdisplay'   => ['name' => 'Playfair Display', 'family'  => 'Playfair Display, serif', 'styles' => ['normal', 'italic'], 'weights' => [400, 700, 900]],                
        'ptsans'            => ['name' => 'PT Sans', 'family' => 'PT Sans, sans-serif', 'styles' => ['normal', 'italic'], 'weights' => [400, 700]],                
        'ptsansnarrow'      => ['name' => 'PT Sans Narrow', 'family'  => 'PT Sans Narrow, sans-serif', 'styles' => ['normal'], 'weights' => [400, 700]],               
        'ptserif'           => ['name' => 'PT Serif', 'family' => 'PT Serif, serif', 'styles' => ['normal', 'italic'], 'weights' => [400, 700]],                
        'quicksand'         => ['name' => 'Quicksand', 'family' => 'Quicksand, sans-serif', 'styles' => ['normal'], 'weights' => [400, 700]],                
        'raleway'           => ['name' => 'Raleway', 'family' => 'Raleway, sans-serif', 'styles' => ['normal'], 'weights' => [100, 200, 300, 400, 500, 600, 700, 800, 900]], 
        'roboto'            => ['name' => 'Roboto', 'family'  => 'Roboto, sans-serif', 'styles' => ['normal', 'italic'], 'weights' => [100, 300, 400, 500, 700, 900]],       
        'robotocondensed'   => ['name' => 'Roboto Condensed', 'family'  => 'Roboto Condensed, sans-serif', 'styles' => ['normal', 'italic'], 'weights' => [300, 400, 700]],
        'robotoslab'        => ['name' => 'Roboto Slab', 'family' => 'Roboto Slab, serif', 'styles' => ['normal'], 'weights' => [100, 300, 400, 700]],              
        'shadowsintolight'  => ['name' => 'Shadows Into Light', 'family' => 'Shadows Into Light, cursive', 'styles' => ['normal'], 'weights' => [400]],             
        'signika'           => ['name' => 'Signika', 'family' => 'Signika, sans-serif', 'styles' => ['normal'], 'weights' => [300, 400, 600, 700]],                
        'sourcecodepro'     => ['name' => 'Source Code Pro', 'family' => 'Source Code Pro, monospace', 'styles' => ['normal'], 'weights' => [200, 300, 400, 500, 600, 700, 900]],
        'sourcesanspro'     => ['name' => 'Source Sans Pro', 'family' => 'Source Sans Pro, sans-serif', 'styles' => ['normal', 'italic'], 'weights' => [200, 300, 400, 600, 700, 900]],
        'titilliumweb'      => ['name' => 'Titillium Web', 'family' => 'Titillium Web, sans-serif', 'styles' => ['normal', 'italic'], 'weights' => [200, 300, 400, 600, 700]],            
        'ubuntu'            => ['name' => 'Ubuntu', 'family' => 'Ubuntu, sans-serif', 'styles' => ['normal', 'italic'], 'weights' => [300, 400, 500, 700]],
        'vollkorn'          => ['name' => 'Vollkorn', 'family' => 'Vollkorn, serif', 'styles' => ['normal', 'italic'], 'weights' => [400, 700]],                
        'yanonekaffeesatz'  => ['name' => 'Yanone Kaffeesatz', 'family' => 'Yanone Kaffeesatz, sans-serif', 'styles' => ['normal'], 'weights' => [200, 300, 400, 700]]
    ]
];