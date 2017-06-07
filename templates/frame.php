<?php
/**
 * Displays the HTML for the general framework
 */
?>

<?php if( $frame->type == 'Options' ) { ?>
    <div class="wrap">
        <form method="post" action="options.php" enctype="multipart/form-data"> 
<?php } ?>

            <div class="divergent-framework <?php echo $frame->class; ?>">

                <header class="divergent-header">

                    <?php if( $frame->type == 'Options' ) { ?>
                        <h2><?php echo $frame->title; ?></h2>
                    <?php } ?>
                    
                    <div class="divergent-buttons">
                        <?php 
                            // Displays any errors
                            echo $frame->errors; 
                            echo $frame->restoreButton;
                            echo $frame->saveButton; 
                        ?>
                    </div>
                    
                    <ul class="divergent-tabs">
                    
                        <?php foreach( $frame->sections as $key => $section ) { ?>
                            <li>
                                
                                <a class="divergent-tab <?php echo $section['active']; ?>" href="#<?php echo $section['id']; ?>">
                                    
                                    <?php if( $section['icon'] ) { ?>
                                        <i class="divergent-icon material-icons"><?php echo $section['icon'] ; ?></i>
                                    <?php } ?>
                                    
                                    <?php echo $section['title']; ?>
                                    
                                </a>
                                
                            </li>
                        <?php } ?>
                        
                    </ul>        
                        
                </header>
                
                <div class="divergent-sections">
                    
                    <?php foreach( $frame->sections as $key => $section ) { ?>
                    
                        <section id="<?php echo $section['id']; ?>" class="divergent-section <?php echo $section['active']; ?>">
                            
                            <h3 class="divergent-section-title"><?php echo $section['title']; ?></h3>
                            
                            <?php if( $section['description'] ) { ?>
                                <p class="divergent-section-description"><?php echo $section['description']; ?></p>
                            <?php } ?>
                            
                            <div class="divergent-fields grid flex">

                                <?php foreach( $section['fields'] as $key => $field ) { ?>

                                    <div class="divergent-option-field <?php echo $field['column']; ?> field-<?php echo $field['type']; ?>">

                                        <div class="divergent-field-context">
                                            <?php 
                                                /**
                                                 * Echo the title for the field
                                                 */
                                                if( isset($field['title']) )              
                                                    echo '<'.$field['titleTag'].'>' . $field['title'] . '</' . $field['titleTag'] .'>';
                                            ?>

                                            <?php 
                                                /**
                                                 * Echo the description for the field
                                                 */
                                                if( isset($field['description']) )              
                                                    echo '<p>' . $field['description'] . '</p>';
                                            ?>

                                        </div>

                                        <div class="divergent-field-input">
                                            <?php echo $field['form']; ?>
                                        </div>

                                    </div>

                                <?php } ?>
                                
                            </div>
                        
                        </section>
                    
                    <?php } ?>
                    
                </div> 
                
                <footer class="divergent-buttons">
                    <?php 
                        echo $frame->resetButton;
                        echo $frame->restoreButton;
                        echo $frame->saveButton; 
                    ?>                
                </footer>

            </div>
            
            <?php 
                /**
                 * Echo settings fields, such as those that are rendered by the options page or the nonce fields for meta boxe pages
                 */
                echo $frame->settingsFields; 
            ?>
            
            <input type="hidden" name="divergentSection" id="divergentSection_<?php echo $frame->id; ?>" value="<?php echo $frame->currentSection; ?>" />

<?php if( $frame->type == 'Options' ) { ?>
        </form>
    </div>
<?php } ?>