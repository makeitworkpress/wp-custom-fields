/**
 * Our button module, accepting custom ajax actions
 */
module.exports = {

    // Initializes the dependency module
    init: function(framework) {

        var self = this;

        jQuery(framework).find('.wpcf-dependent-field').each( function(index, item) {
        
            // Values from our dependency field
            var field = jQuery(item).hasClass('wpcf-repeatable-field') ? jQuery(item).find('.wpcf-repeatable-field-input') : jQuery(item).find('.wpcf-field-input'),
                equation = jQuery(field).data('equation'),
                source = jQuery(field).data('source'),
                value = jQuery(field).data('value');

            if( ! equation || ! source || ! value ) {
                return;
            } 
            
            // Target fields
            var selector = jQuery(item).hasClass('wpcf-repeatable-field') ? '.wpcf-repeatable-group' : '.wpcf-fields',
                target = jQuery(item).closest(selector).find('.field-id-' + source),
                input = jQuery(target).find('input'),
                select = jQuery(target).find('select');
        
            // Select fields (only supports single select fields)
            if( select.length > 0 ) {
                jQuery(select).change( function() {
                    self.compare(this, item, equation, value);
                });
            }
            
            // Input fields (only supports simple input fields)
            if( input.length > 0 ) {
                jQuery(input).change( function(event) {
                    self.compare(this, item, equation, value);
                });   
            }
        
        });      

    },

    // Compares values
    compare: function(changedField, dependentField, equation, value) {
 
        var changedFieldValue = changedField.value;

        // Checkboxes
        if( changedField.type == 'checkbox') {
            if( changedField.checked && changedField.dataset.key == value ) {
                changedFieldValue = value; 
            } else if( ! changedField.checked && changedField.dataset.key == value ) {
                changedFieldValue = '';    
            } 
        }
        
        if( equation == '=' ) {
            if( changedFieldValue == value ) {
                jQuery(dependentField).addClass('active');
            } else {
                jQuery(dependentField).removeClass('active');    
            }
        }

        if( equation == '!=' ) {
            if( changedFieldValue != value ) {
                jQuery(dependentField).addClass('active');
            } else {
                jQuery(dependentField).removeClass('active');    
            }
        }

    } 

};