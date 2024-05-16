/**
 * Our repeatable fields module
 * @todo Rewrite this in a more efficient manner.
 * @param {HTMLElement} framework The parent framework element
 */
import { fields } from '../modules/fields';
import { datepicker } from './datepicker';

declare var jQuery, wp;

export const repeatable = (framework: HTMLElement) => {

    /**
     * Groups are sortable
     */
    jQuery('.wpcf-repeatable-groups').sortable({
        placeholder: 'wpcf-highlight',
        update: function( event, ui ) { 
            // This updating does not matter anymore
            // jQuery(this).find('.wpcf-repeatable-group').each( function(index, node) {
                // jQuery(node).html( function(n, node) {
                //     return node.replace(/\[\d+\]/g, '[' + index + ']').replace(/\_\d+\_/g, '_' + index + '_');
                // });
            // });
        }
    });
    
    /**
     * Repeatable Groups 
     */
    document.querySelectorAll('.wpcf-repeatable-add').forEach((button) => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const codeNodes: HTMLElement[] = [];
            const length = (button.closest('.wpcf-repeatable-container') as HTMLElement).querySelectorAll('.wpcf-repeatable-group').length;
            const group = (button.closest('.wpcf-repeatable-container') as HTMLElement).querySelector('.wpcf-repeatable-group:last-child') as HTMLElement;
    
            // Destroy our select2 instances, if defined
            const selectAdvancedFields = group.querySelectorAll('.wpcf-select-advanced');
            selectAdvancedFields.forEach((field: any) => {
                if (typeof field.select2 !== 'undefined' && field.select2) {
                    field.select2.destroy();
                }
            });
    
            // Destroy current codemirror instances
            (document.querySelectorAll('.wpcf-code-editor-value') as NodeListOf<HTMLElement>).forEach((node: HTMLElement) => {
                if ((window as any).wcfCodeMirror[node.id]) {
                    (window as any).wcfCodeMirror[node.id].toTextArea(node);
                    codeNodes.push(node);
                }
            });
    
            // Destroy our datepicker instances before re-adding
            const datepickers = group.querySelectorAll('.wpcf-datepicker') as NodeListOf<HTMLElement>;
            datepickers.forEach((datepickerInstance: any) => {
                if (datepickerInstance._flatpickr) {
                    datepickerInstance._flatpickr.destroy();
                }
            });
    
            // Build our new group
            const newGroup = group.cloneNode(true) as HTMLElement;
    
            // Replace current keys with new ones in the new group
            newGroup.innerHTML = newGroup.innerHTML.replace(/\[\d+\]/g, `[${length}]`).replace(/\_\d+\_/g, `_${length}_`);
    
            // Empty inputs in our new group
            (newGroup.querySelectorAll('input, textarea') as NodeListOf<HTMLInputElement>).forEach((input: HTMLInputElement) => input.value = '');
            newGroup.querySelectorAll('option').forEach((option: HTMLOptionElement) => option.selected = false);
            (newGroup.querySelectorAll('.wpcf-single-media:not(.empty)') as NodeListOf<HTMLElement>).forEach((media: HTMLElement) => media.remove());
    
            // Insert the newGroup after the current group
            group.after(newGroup);
    
            // Redraw all the fields within the group
            fields(newGroup, true);
    
            // Reinit old datepicker groups
            datepickers.forEach((element: HTMLElement) => {
                datepicker(group);
            });
    
            // Reinitialize old codemirror groups
            codeNodes.forEach((node: HTMLElement) => {
                const settings = JSON.parse( node.dataset.settings as string );
                (window as any).wcfCodeMirror[node.id] = wp.codeEditor.initialize(node, settings);
            });
        });
    });
    
    // Remove the latest group
    document.querySelectorAll('.wpcf-repeatable-remove-latest').forEach((button) => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const groupLength = (button.closest('.wpcf-repeatable-container') as HTMLElement).querySelectorAll('.wpcf-repeatable-group').length;
            const group = (button.closest('.wpcf-repeatable-container') as HTMLElement).querySelector('.wpcf-repeatable-group:last-child') as HTMLElement;
    
            // Keep the first group
            if (groupLength < 2) return;
    
            group.style.display = 'none';
            setTimeout(() => group.remove(), 500);
        });
    });
    
    // Remove the current group
    document.addEventListener('click', (e) => {
        const target = e.target as HTMLElement;
        if (target.classList.contains('wpcf-repeatable-remove-group')) {
            e.preventDefault();
            const groupLength = (target.closest('.wpcf-repeatable-container') as HTMLElement).querySelectorAll('.wpcf-repeatable-group').length;
            const group = target.closest('.wpcf-repeatable-group') as HTMLElement;
    
            // Only remove if not the first group
            if (groupLength < 2) return;
    
            group.style.display = 'none';
            setTimeout(() => group.remove(), 500);
        }
    });
    
    // Open or close a group
    document.querySelectorAll('.wpcf-repeatable-toggle').forEach((button) => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const icon = button.querySelector('i') as HTMLElement;
            const group = (button.closest('.wpcf-repeatable-group') as HTMLElement).querySelector('.wpcf-repeatable-fields') as HTMLElement;
    
            if (icon.textContent === 'arrow_drop_down') {
                icon.textContent = 'arrow_drop_up';
            } else if (icon.textContent === 'arrow_drop_up') {
                icon.textContent = 'arrow_drop_down';
            }
            group.style.display = group.style.display === 'none' ? 'block' : 'none';
        });
    });
    
};