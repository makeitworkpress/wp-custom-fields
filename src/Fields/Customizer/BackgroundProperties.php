<?php
 /** 
  * Displays a field with common background properties
  */
namespace MakeitWorkPress\WP_Custom_Fields\Fields\Customizer;
use WP_Customize_Control as WP_Customize_Control;
use MakeitWorkPress\WP_Custom_Fields\Fields\Background as BackgroundField;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) 
    die; 

// The customizer class should exist
if ( ! class_exists( 'WP_Customize_Control' ) )
	return NULL;

class BackgroundProperties extends WP_Customize_Control {
	
    /**
	 * Render the control's content.
	 * Allows the content to be overridden without having to rewrite the wrapper.
	 *
	 * @return  void
	 */
	public function render_content(): void {
    
        $configurations     = BackgroundField::configurations(); ?>

        <?php if( $this->label || $this->description ) { ?> 
            <label>
                <?php if( $this->label ) { ?> 
                    <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                <?php } ?>
                
                <?php if( $this->description ) { ?>
                    <span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
                <?php } ?>
                
            </label>  
            
        <?php } ?>

        <div class="wpcf-background-properties-input">
            <?php foreach( $configurations['properties'] as $property => $values ) { ?>
                <label><?php echo $values['placeholder']; ?></label>
                <select <?php $this->link( $property ); ?>>
                    <option value=""><?php echo $configurations['labels']['placeholder']; ?></option>
                    <?php foreach($values['options'] as $option => $label ) { ?>
                        <option value="<?php echo $option; ?>" <?php selected( $this->value( $property ), $option ); ?>>
                            <?php echo $label; ?>
                        </option>
                    <?php } ?>
                </select>
            <?php } ?>
        </div>

    <?php }
    
}