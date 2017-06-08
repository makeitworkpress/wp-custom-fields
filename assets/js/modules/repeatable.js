/**
 * Our repeatable fields module
 */
var fields = require('./../fields');

module.exports.repeatable = function(framework) {
    
    /**
     * Repeatable Groups 
     *
     * @todo Add function to reset fields to default value
     */
    jQuery('.divergent-repeatable-add').on('click', function (e) {
        e.preventDefault();
        var length = jQuery(this).closest('.divergent-repeatable-container').find('.divergent-repeatable-group').length,
            group = jQuery(this).closest('.divergent-repeatable-container').find('.divergent-repeatable-group').last(),
            newGroup = group.clone(true, true),
            newGroupNumber = newGroup.find('h4 span');
        
        // The title should follow the numbering
        newGroupNumber.text(length);
        
        // Clone the current group and replace the current keys by new ones
        newGroup.html(function (i, oldGroup) {
            return oldGroup.replace(/\[\d\]/g, '[' + length + ']').replace(/\-\d\-/g, '-' + length + '-');
        });
        
        // Redraw the fields within the group
        fields.fields(newGroup);
                
        // Finally, insert the newGroup after the current group
        group.after(newGroup);
        
    });
    
    jQuery('.divergent-repeatable-remove').on('click', function (e) {
        e.preventDefault();
        var length = jQuery(this).closest('.divergent-repeatable-container').find('.divergent-repeatable-group').length,
            group = jQuery(this).closest('.divergent-repeatable-container').find('.divergent-repeatable-group').last();
        
        // Keep the first group
        if (length > 1) {
            group.remove();
        }
    });
    
    jQuery('body').on('click', '.divergent-repeatable-toggle', function (e) {
        e.preventDefault();
        
        console.log('YUP');
        
        if( jQuery(this).find('i').text() === 'arrow_drop_down' ) {
            jQuery(this).find('i').text('arrow_drop_up');        
        } else if( jQuery(this).find('i').text() === 'arrow_drop_up' ) {
            jQuery(this).find('i').text('arrow_drop_down');    
        }
        jQuery(this).closest('.divergent-repeatable-group').find('.divergent-repeatable-fields').slideToggle('closed');
    });
    
}