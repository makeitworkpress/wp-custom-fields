<?php
 /** 
  * Displays a custom typography field
  */
namespace MakeitWorkPress\WP_Custom_Fields\Fields\Customizer;
use WP_Customize_Control as WP_Customize_Control;
use MakeitWorkPress\WP_Custom_Fields\Fields\Typography as TypographyField;
use MakeitWorkPress\WP_Custom_Fields\Fields\Dimension as DimensionField;

// Bail if accessed directly
if ( ! defined( 'ABSPATH' ) ) 
    die; 

// The customizer class should exist
if ( ! class_exists( 'WP_Customize_Control' ) )
	return NULL;

class Typography extends WP_Customize_Control {
	
    /**
	 * Render the control's content.
	 * Allows the content to be overridden without having to rewrite the wrapper.
	 *
	 * @return  void
	 */
	public function render_content(): void {
    
        $configurations = TypographyField::configurations();
        $dimensions     = DimensionField::configurations();
        
		?>

		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
            
            <?php if( $this->description ) { ?>
                <span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
            <?php } ?>
            
		</label>           
            
        <div class="wpcf-typography-font-select">
            <select class="wpcf-typography-fonts" <?php $this->link('font'); ?> >

                <option value=""><?php echo $configurations['labels']['select']; ?></option>

                <?php foreach( $configurations['properties']['fonts'] as $fontspace => $types ) { ?>

                    <optgroup label="<?php echo esc_attr(ucfirst($fontspace)); ?>">    

                    <?php foreach( $types as $key => $font ) { ?>         
                        <?php $display = isset($font['example']) ? $font['example'] : WP_CUSTOM_FIELDS_ASSETS_URL . 'img/' . $key . '.png'; ?>
                        <option data-display="<?php echo esc_attr($display); ?>" value="<?php echo esc_attr($key); ?>" <?php selected($this->value('font'), $key); ?>>
                            <?php echo $font['name']; ?>
                        </option>
                    <?php } ?>

                    </optgroup>

                <?php } ?>

            </select>
        </div>

        <div class="wpcf-typography-properties">
            
            <div class="wpcf-typography-weight">
                <i class="material-icons">format_bold</i>
                <select <?php $this->link( 'font_weight' ); ?>>
                    <option value=""><?php echo $configurations['labels']['weights']; ?></option>
                    <?php foreach( $configurations['properties']['weights'] as $weight => $label ) { ?>
                        <option value="<?php echo $weight; ?>" <?php selected( $this->value( 'font_weight' ), $weight ); ?>>
                            <?php echo $label; ?>
                    </option>
                    <?php } ?>
                </select>
            </div>           
            
            <?php foreach( $configurations['properties']['dimensions'] as $key => $label ) { ?>
                <div class="wpcf-dimensions-input">
                    <i class="material-icons"><?php echo 'format_' . $key; ?></i>
                    <input <?php $this->link( $key . 'amount' ); ?> value="<?php echo esc_attr($this->value($key . 'amount')); ?>" type="number" step="0.01" placeholder="<?php echo $label; ?>" />
                    <select <?php $this->link( $key . 'unit' ); ?>>
                        <?php foreach($dimensions['properties']['units'] as $measure) { ?>
                            <option value="<?php echo $measure; ?>" <?php selected( $this->value( $key . 'unit' ), $measure ); ?>>
                                <?php echo $measure; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            <?php } ?>

        </div>

        <div class="wpcf-typography-appearance">
            <?php foreach( $configurations['properties']['styles'] as $key => $style ) { ?>
                <ul class="wpcf-typography-<?php echo $key; ?> wpcf-icon-list"> 

                <?php foreach( $style as $value => $icon ) { ?>
                    <?php
                        $name = $key == 'styles' ? $value : $key;
                        $type = $key == 'styles' ? 'checkbox' : 'radio';
                    ?>
                    <li>
                        <input name="<?php echo $name; ?>" id="<?php echo $this->id . $value; ?>" <?php $this->link( $name ); ?> type="<?php echo $type; ?>"  value="<?php echo $value; ?>" <?php checked( $this->value($name) ); ?>/>
                        <label for="<?php echo $this->id . $value; ?>"><i class="material-icons"><?php echo $icon; ?></i></label>
                    </li>
                <?php } ?>

                </ul>
            <?php } ?>                
        </div>

        <?php foreach( $configurations['labels'] as $key => $label ) { ?>
            <?php 
                if( in_array($key, ['weights', 'select']) )
                    continue;
            ?>
            <label>
                <input <?php $this->link( 'load' . $key ); ?> type="checkbox" value="<?php echo $key; ?>" <?php checked( $this->value('load' . $key) ); ?>/>
                <?php echo $label; ?>
            </label>
		<?php }
	}
    
}