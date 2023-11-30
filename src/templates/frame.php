<?php
/**
 * Displays the HTML for the general framework
 */
?>

<?php if( $frame->type == 'options' ) { ?>
    <div class="wrap">
        <form method="POST" action="<?php echo $frame->option_props['action']; ?>" enctype="multipart/form-data"> 
<?php } ?>

<div class="wpcf-framework <?php if( $frame->type == 'options' ) { ?>wpcf-options-page <?php } echo $frame->class; ?>" id="<?php echo $frame->id; ?>">

                <header class="wpcf-header">                  

                    <?php if( $frame->type != 'post' ) { ?>                                    
                    
                        <div class="wpcf-notifications">

                            <?php if( $frame->type == 'options' ) { ?>
                                <h1><?php echo $frame->title; ?></h1>
                            <?php } ?>

                            <?php if( $frame->type == 'user' || $frame->type == 'term' ) { ?>
                                <h2><?php echo $frame->title; ?></h2>
                            <?php } ?>                        
                        
                            <?php if( $frame->option_props['restore_button'] || $frame->option_props['save_button'] ) { ?>
                                <div class="wpcf-buttons">
                                    <?php 
                                        // Displays any errors
                                        echo $frame->option_props['errors']; 
                                        echo $frame->option_props['restore_button'];
                                        echo $frame->option_props['save_button']; 
                                    ?>
                                </div>
                            <?php } ?>
                        
                        </div>

                    <?php } ?>
                    
                    <ul class="wpcf-tabs">
                    
                        <?php 
                            foreach( $frame->sections as $key => $section ) { 
                                
                                if( $section['tabs'] == false ) {
                                    continue;
                                }
                        ?>
                            <li>
                                
                                <a class="wpcf-tab <?php echo $section['active']; ?>" href="#<?php echo $section['id']; ?>">
                                    
                                    <?php if( $section['icon'] ) { ?>
                                        <i class="wpcf-icon material-icons"><?php echo $section['icon'] ; ?></i>
                                    <?php } ?>
                                    
                                    <?php echo $section['title']; ?>
                                    
                                </a>
                                
                            </li>
                        <?php 
                            } 
                        ?>
                        
                    </ul>        
                        
                </header>
                
                <div class="wpcf-sections">
                    
                    <?php foreach( $frame->sections as $key => $section ) { ?>
                    
                        <section id="<?php echo $section['id']; ?>" class="wpcf-section <?php echo $section['active']; ?>">
                            
                            <?php if( $section['display_title']) { ?>
                                <h3 class="wpcf-section-title"><?php echo $section['title']; ?></h3>
                            <?php } ?>    
                            
                            <?php if( $section['description'] ) { ?>
                                <p class="wpcf-section-description"><?php echo $section['description']; ?></p>
                            <?php } ?>
                            
                            <div class="wpcf-fields grid flex">

                                <?php foreach( $section['fields'] as $key => $field ) { ?>

                                    <div class="wpcf-field <?php echo $field['classes']; ?>" data-id="<?php echo $field['id']; ?>">

                                        <?php do_action('wcf_before_field', $field); ?>
                                        
                                        <?php if( $field['title'] ) { ?>
                                            <div class="wpcf-field-title<?php echo $field['titleClass']; ?>"<?php if($field['titleSections']) { ?> data-sections="<?php echo esc_attr($field['titleSections']); ?>" <?php } ?>>
                                                <?php           
                                                    echo '<' . $field['titleTag'] . '>' . $field['title'] . '</' . $field['titleTag'] .'>';
                                                ?>
                                            </div>
                                        <?php } ?>

                                        <?php if( $field['form'] ) { ?>
                                            <div class="wpcf-field-input" <?php foreach($field['dependency'] as $k => $v) { ?> data-<?php echo $k; ?>="<?php echo $v; ?>" <?php } ?>>
                                                <?php echo $field['form']; ?>
                                            </div>
                                        <?php } ?>
                                        
                                        <?php if( $field['description'] ) { ?>
                                            <div class="wpcf-field-description">          
                                                <p><?php echo $field['description']; ?></p>
                                            </div> 
                                        <?php } ?>

                                        <?php do_action('wcf_after_field', $field); ?>

                                    </div>

                                <?php } ?>
                                
                            </div>
                        
                        </section>
                    
                    <?php } ?>
                    
                </div> 
                
                <?php if( $frame->option_props['reset_button'] || $frame->option_props['restore_button_bottom'] || $frame->option_props['save_button_bottom'] ) { ?>
                    <footer class="wpcf-buttons">
                        <?php 
                            echo $frame->option_props['reset_button'];
                            echo $frame->option_props['restore_button_bottom'];
                            echo $frame->option_props['save_button_bottom']; 
                        ?>                
                    </footer>
                <?php } ?>

            </div>
            
            <?php 
                /**
                 * Echo settings fields, such as those that are rendered by the options page or the nonce fields for meta box pages
                 */
                echo $frame->settings_fields; 
            ?>
            
            <input type="hidden" name="wp_custom_fields_section_<?php echo $frame->id; ?>" id="wp_custom_fields_section_<?php echo $frame->id; ?>" value="<?php echo $frame->current_section; ?>" />

            <?php if( $frame->type == 'options' ) { ?>

            <?php } ?>

<?php if( $frame->type == 'options' ) { ?>
        </form>
    </div>
<?php } ?>