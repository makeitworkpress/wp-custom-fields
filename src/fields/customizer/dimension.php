<?php
 /** 
  * Displays a custom dimension field for the customizer
  */
namespace MakeitWorkPress\WP_Custom_Fields\Fields\Customizer;
use WP_Customize_Control as WP_Customize_Control;
use MakeitWorkPress\WP_Custom_Fields\Fields\Dimension as DimensionField;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) 
    die; 

// The customizer class should exist
if ( ! class_exists( 'WP_Customize_Control' ) )
	return NULL;

class Dimension extends WP_Customize_Control {
	
    /**
	 * Render the control's content.
	 * Allows the content to be overridden without having to rewrite the wrapper.
	 *
	 * @return  void
	 */
	public function render_content() {
    
        $dimensions     = DimensionField::configurations();
        
		?>

		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
            
            <?php if( $this->description ) { ?>
                <span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
            <?php } ?>
            
		</label>           

        <div class="wpcf-dimensions-input">
            <i class="material-icons">vertical_align_center</i>
            <input <?php $this->link( 'amount' ); ?> value="<?php echo esc_attr( $this->value('amount') ); ?>" type="number" step="0.01" />
            <select <?php $this->link( 'unit' ); ?>>
                <?php foreach($dimensions['properties']['units'] as $measure) { ?>
                    <option value="<?php echo $measure; ?>" <?php selected( $this->value( 'unit' ), $measure ); ?>>
                        <?php echo $measure; ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <?php
	}
    
}