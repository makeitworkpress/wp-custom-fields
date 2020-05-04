<?php
/** 
 * Displays a collapsable section
 */
namespace MakeitWorkPress\WP_Custom_Fields\Fields\Customizer;
use WP_Customize_Control as WP_Customize_Control;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) 
    die; 

// The customizer class should exist
if ( ! class_exists( 'WP_Customize_Control' ) )
	return NULL;

class Section extends WP_Customize_Control {
	
    /**
	 * Render the control's content.
	 *
	 * @return  void
	 */
	protected function render() {

		$id    = str_replace( ['[', ']'], ['-', ''], $this->id );   

        if( in_array('open', $this->choices) ) { ?>
            <li id="customize-control-<?php esc_attr_e($id); ?>" class="customize-control customize-control-start-<?php esc_attr_e($this->type); ?>">
                <ul>
        <?php } ?>
                    <li class="customize-control customize-control-<?php esc_attr_e($this->type); ?>">
                        <label>
                            <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                            <?php if( $this->description ) { ?>
                                <span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
                            <?php } ?>			
                        </label>  
                    </li>            
        <?php if( in_array('close', $this->choices) ) { ?>    
                </ul>
            </li>
        <?php } ?> 

	<?php }
    
}