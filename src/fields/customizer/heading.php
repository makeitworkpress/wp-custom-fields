<?php
 /** 
  * Displays a heading customizer field
  */
namespace MakeitWorkPress\WP_Custom_Fields\Fields\Customizer;
use WP_Customize_Control as WP_Customize_Control;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) 
    die; 

// The customizer class should exist
if ( ! class_exists( 'WP_Customize_Control' ) )
	return NULL;

class Heading extends WP_Customize_Control {
	
    /**
	 * Render the control's content.
	 * Allows the content to be overriden without having to rewrite the wrapper.
	 *
	 * @return  void
	 */
	public function render_content() {

		$sections = implode(',', $this->choices); ?>

			<label class="wpcf-heading<?php if($sections) { ?> wpcf-heading-collapsible<?php }?>" <?php if($sections) { ?> data-sections="<?php esc_attr_e($sections); ?>" <?php } ?>>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php if( $this->description ) { ?>
					<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
				<?php } ?>			
			</label>

		<?php
	}
    
}