/**
 * Our repeatable fields module
 */
var fields = require('./../fields');

module.exports.init = function(framework) {
    
    /**
     * Repeatable Groups 
     *
     * @todo Add function to reset fields to default value
     */
    jQuery('.wp-custom-fields-repeatable-add').on('click', function (e) {
        e.preventDefault();
        var codeNodes   = [],
            length      = jQuery(this).closest('.wp-custom-fields-repeatable-container').find('.wp-custom-fields-repeatable-group').length,
            group       = jQuery(this).closest('.wp-custom-fields-repeatable-container').find('.wp-custom-fields-repeatable-group').last();
            
        // Destroy our select2 instances
        jQuery('.wp-custom-fields-select').select2('destroy');

        // Destroy current codemirror instances
        jQuery(framework).find('.wp-custom-fields-code-editor-value').each(function (index, node) {

            if( typeof(window.wcfCodeMirror[node.id]) !== 'undefined' ) {
                window.wcfCodeMirror[node.id].toTextArea(node);

                codeNodes.push(node);
            }

        });       

        // Build our newgroup
        var newGroup = group.clone(true, true);
        
        // Clone the current group and replace the current keys by new ones
        newGroup.html(function (i, oldGroup) {
            return oldGroup.replace(/\[\d\]/g, '[' + length + ']').replace(/\_\d\_/g, '_' + length + '_');
        }); 

        // Empty inputs in our  new group
        newGroup.find('input').val('');
        newGroup.find('textarea').val('');
        newGroup.find('option').attr('selected', false);        
                
        // Finally, insert the newGroup after the current group
        group.after(newGroup);

        // Redraw the fields within the group
        fields.init(newGroup);  
        
        // Reinitialize old codemirror groups
        codeNodes.forEach( function(node) {
            if( typeof(window.wcfCodeMirror[node.id]) !== 'undefined' ) {
                window.wcfCodeMirror[node.id] = CodeMirror.fromTextArea(node, {ode: node.dataset.mode, lineNumbers: true});
            }
        });
        
    });
    
    // Remove the container
    jQuery('.wp-custom-fields-repeatable-remove').on('click', function (e) {
        e.preventDefault();
        var length = jQuery(this).closest('.wp-custom-fields-repeatable-container').find('.wp-custom-fields-repeatable-group').length,
            group = jQuery(this).closest('.wp-custom-fields-repeatable-container').find('.wp-custom-fields-repeatable-group').last();
        
        // Keep the first group
        if (length > 1) {
            group.remove();
        }
    });
    
    // Open or close a group
    jQuery('body').on('click', '.wp-custom-fields-repeatable-toggle', function (e) {
        e.preventDefault();
        
        if( jQuery(this).find('i').text() === 'arrow_drop_down' ) {
            jQuery(this).find('i').text('arrow_drop_up');        
        } else if( jQuery(this).find('i').text() === 'arrow_drop_up' ) {
            jQuery(this).find('i').text('arrow_drop_down');    
        }
        jQuery(this).closest('.wp-custom-fields-repeatable-group').find('.wp-custom-fields-repeatable-fields').slideToggle('closed');
    });
    
}