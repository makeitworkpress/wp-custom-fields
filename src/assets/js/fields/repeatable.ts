/**
 * Our repeatable fields module
 * @todo Rewrite this in a more efficient manner.
 * @param {HTMLElement} framework The parent framework element
 */
import { fields } from '../modules/fields';
import { datepicker } from './datepicker';

declare var jquery;

export const repeatable = (framework: HTMLElement) => {

    /**
     * Groups are sortable
     */
    jQuery('.wpcf-repeatable-groups').sortable({
        placeholder: 'wpcf-highlight',
        update: function( event, ui ) { 
            jQuery(this).find('.wpcf-repeatable-group').each( function(index, node) {
                // jQuery(node).html( function(n, node) {
                //     return node.replace(/\[\d+\]/g, '[' + index + ']').replace(/\_\d+\_/g, '_' + index + '_');
                // });
            });
        }
    });
    
    /**
     * Repeatable Groups 
     */
    jQuery('.wpcf-repeatable-add').on('click', function (e) {
        e.preventDefault();
        var codeNodes   = [],
            length      = jQuery(this).closest('.wpcf-repeatable-container').find('.wpcf-repeatable-group').length,
            group       = jQuery(this).closest('.wpcf-repeatable-container').find('.wpcf-repeatable-group').last();
            
        // Destroy our select2 instances, if it is defined of course
        if( typeof jQuery.fn.select2 !== 'undefined' && jQuery.fn.select2 ) {
            jQuery(group).find('.wpcf-select-advanced').select2('destroy');
        }     

        // Destroy current codemirror instances
        jQuery(framework).find('.wpcf-code-editor-value').each(function (index, node) {

            if( typeof(window.wcfCodeMirror[node.id]) !== 'undefined' ) {
                window.wcfCodeMirror[node.id].toTextArea(node);

                codeNodes.push(node);
            }

        });
        
        // Destroy our datepicker instances before re-adding
        var datepickers = jQuery(group).find('.wpcf-datepicker');

        if( datepickers.length > 0 ) {
            jQuery(datepickers).each( function() {
                if( typeof this._flatpickr !== 'undefined' ) {
                    this._flatpickr.destroy();    
                }
            });
        }
       

        // Build our newgroup
        var newGroup = group.clone(true, true);
        
        // Clone the current group and replace the current keys by new ones. The length is always one bigger as the current array, so it matches the key for the new group.
        newGroup.html(function (i, oldGroup) {
            return oldGroup.replace(/\[\d+\]/g, '[' + length + ']').replace(/\_\d+\_/g, '_' + length + '_');
        });             

        // Empty inputs in our  new group
        newGroup.find('input').val('');
        newGroup.find('textarea').val('');
        newGroup.find('option').attr('selected', false); 
        newGroup.find('.wpcf-single-media').not('.empty').remove(); // Removes the media from the cloned group            
                
        // Finally, insert the newGroup after the current group
        group.after(newGroup);         

        // Redraw all the fields within the group
        fields.init(newGroup);  

        // Reinit old datepicker groups
        if( datepickers.length > 0 ) {
            datepicker.init(group);
        }
        
        // Reinitialize old codemirror groups
        codeNodes.forEach( function(node) {
            if( typeof(window.wcfCodeMirror[node.id]) !== 'undefined' ) {
                window.wcfCodeMirror[node.id] = wp.codeEditor.initialize(node, {mode: node.dataset.mode, lineNumbers: true});
            }
        });
        
    });
    
    // Remove the latest group
    jQuery('.wpcf-repeatable-remove-latest').on('click', function (e) {
        e.preventDefault();
        var groupLength = jQuery(this).closest('.wpcf-repeatable-container').find('.wpcf-repeatable-group').length,
            group = jQuery(this).closest('.wpcf-repeatable-container').find('.wpcf-repeatable-group').last();
        
        // Keep the first group
        if( groupLength < 2 ) {
            return;
        }
        
        group.fadeOut();
        setTimeout( function() {
            group.remove();
        }, 500);

    });

    /**
     * Remove the current group
     */
    jQuery(document).on('click', '.wpcf-repeatable-remove-group', function(e) {

        e.preventDefault();
        var groupLength = jQuery(this).closest('.wpcf-repeatable-container').find('.wpcf-repeatable-group').length,
            group = jQuery(this).closest('.wpcf-repeatable-group');
            groupContainer = jQuery(this).closest('.wpcf-repeatable-container');        
        
        // Only remove if not the first group
        if( groupLength < 2 ) {
            return;
        }

        // Fade-out and remove after a certain timeout
        group.fadeOut();

        setTimeout( function() {
            group.remove();
        }, 500);

    });    
    
    // Open or close a group
    jQuery('body').on('click', '.wpcf-repeatable-toggle', function (e) {
        e.preventDefault();
        
        if( jQuery(this).find('i').text() === 'arrow_drop_down' ) {
            jQuery(this).find('i').text('arrow_drop_up');        
        } else if( jQuery(this).find('i').text() === 'arrow_drop_up' ) {
            jQuery(this).find('i').text('arrow_drop_down');    
        }
        jQuery(this).closest('.wpcf-repeatable-group').find('.wpcf-repeatable-fields').slideToggle('closed');
    });
    
};