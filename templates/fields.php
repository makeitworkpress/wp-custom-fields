<?php
/**
 * Displays a set of fields
 */
?>

<?php foreach( $fields as $key => $field ) ?>

    <div class="divergent-option-field <?php echo $field->column; ?> 'field-'<?php echo $field->type; ?>">
        
        <div class="divergent-field-context">
            <?php 
                /**
                 * Echo the title for the field
                 */
                if( isset($field->title) )              
                    echo '<'.$field->titleTag.'>' . $field->title . '</'.$field->titleTag.'>';
            ?>
            
            <?php 
                /**
                 * Echo the description for the field
                 */
                if( isset($field->description) )              
                    echo '<p>' . $field->description . '</p>';
            ?>
            
        </div>
        
        <div class="divergent-field-input">
            <?php echo $field->form; ?>
        </div>
        
    </div>

<?php } ?>