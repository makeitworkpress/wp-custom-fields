<?php
/**
 * Displays the HTML for the general framework
 */
?>

<?php if( $frame->type == 'Options' ) { ?>
    <div class="wrap">
        <form method="post" action="options.php" enctype="multipart/form-data"> 
<?php } ?>

            <div class="wp-custom-fields-framework <?php echo $frame->class; ?>" id="<?php echo $frame->id; ?>">

                <header class="wp-custom-fields-header">                  
                                                        
                    <div class="wp-custom-fields-notifications">

                        <?php if( $frame->type == 'Options' ) { ?>
                            <h1><?php echo $frame->title; ?></h1>
                        <?php } ?>
                    
                        <?php if( $frame->restoreButton || $frame->saveButton ) { ?>
                            <div class="wp-custom-fields-buttons">
                                <?php 
                                    // Displays any errors
                                    echo $frame->errors; 
                                    echo $frame->restoreButton;
                                    echo $frame->saveButton; 
                                ?>
                            </div>
                        <?php } ?>
                    
                    </div>
                    
                    <ul class="wp-custom-fields-tabs">
                    
                        <?php 
                            foreach( $frame->sections as $key => $section ) { 
                                
                                if( $section['tabs'] == false ) {
                                    continue;
                                }
                        ?>
                            <li>
                                
                                <a class="wp-custom-fields-tab <?php echo $section['active']; ?>" href="#<?php echo $section['id']; ?>">
                                    
                                    <?php if( $section['icon'] ) { ?>
                                        <i class="wp-custom-fields-icon material-icons"><?php echo $section['icon'] ; ?></i>
                                    <?php } ?>
                                    
                                    <?php echo $section['title']; ?>
                                    
                                </a>
                                
                            </li>
                        <?php 
                            } 
                        ?>
                        
                    </ul>        
                        
                </header>
                
                <div class="wp-custom-fields-sections">
                    
                    <?php foreach( $frame->sections as $key => $section ) { ?>
                    
                        <section id="<?php echo $section['id']; ?>" class="wp-custom-fields-section <?php echo $section['active']; ?>">
                            
                            <h3 class="wp-custom-fields-section-title"><?php echo $section['title']; ?></h3>
                            
                            <?php if( $section['description'] ) { ?>
                                <p class="wp-custom-fields-section-description"><?php echo $section['description']; ?></p>
                            <?php } ?>
                            
                            <div class="wp-custom-fields-fields grid flex">

                                <?php foreach( $section['fields'] as $key => $field ) { ?>

                                    <div class="wp-custom-fields-option-field <?php echo $field['column']; ?> field-<?php echo $field['type']; ?>">

                                        <?php do_action('wcf_before_field', $field); ?>
                                        
                                        <?php if( $field['title'] ) { ?>
                                            <div class="wp-custom-fields-field-title">
                                                <?php           
                                                    echo '<'.$field['titleTag'].'>' . $field['title'] . '</' . $field['titleTag'] .'>';
                                                ?>
                                            </div>
                                        <?php } ?>

                                        <?php if( $field['form'] ) { ?>
                                            <div class="wp-custom-fields-field-input">
                                                <?php echo $field['form']; ?>
                                            </div>
                                        <?php } ?>
                                        
                                        <?php if( $field['description'] ) { ?>
                                            <div class="wp-custom-fields-field-description">          
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
                
                <footer class="wp-custom-fields-buttons">
                    <?php 
                        echo $frame->resetButton;
                        echo $frame->restoreButton;
                        echo $frame->saveButton; 
                    ?>                
                </footer>

            </div>
            
            <?php 
                /**
                 * Echo settings fields, such as those that are rendered by the options page or the nonce fields for meta box pages
                 */
                echo $frame->settingsFields; 
            ?>
            
            <input type="hidden" name="wp_custom_fields_section_<?php echo $frame->id; ?>" id="wp_custom_fields_section_<?php echo $frame->id; ?>" value="<?php echo $frame->currentSection; ?>" />

<?php if( $frame->type == 'Options' ) { ?>
        </form>
    </div>
<?php } ?>