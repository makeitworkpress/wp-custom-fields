/**
 * Helper function to create the dependent field functionality
 * 
 * @param {HTMLElement} framework The framework root element
 */
declare var jQuery;

export const DependencyHelper = (framework: HTMLElement) => {

    framework.querySelectorAll('.wpcf-dependent-field').forEach((item: Element) => {
        // Values from our dependency field
        const field = item.classList.contains('wpcf-repeatable-field') ? item.querySelector('.wpcf-repeatable-field-input') : item.querySelector('.wpcf-field-input');
        const equation = field?.getAttribute('data-equation');
        const source = field?.getAttribute('data-source');
        const value = field?.getAttribute('data-value');

        
        if (!equation || !source || !value) {
            return;
        }

        // Target fields
        const selector = item.classList.contains('wpcf-repeatable-field') ? '.wpcf-repeatable-group' : '.wpcf-fields';
        const target = item.closest(selector)?.querySelector(`.field-id-${source}`);
        const input = target?.querySelector('input');
        const select = target?.querySelector('select');


        // Select fields (only supports single select fields)
        if (select) {
            jQuery(select).on('change', function() {
                compare(this, item, equation, value);
            });
        }

        // Input fields (only supports simple input fields)
        if (input) {
            jQuery(input).on('change', function() {
                compare(this, item, equation, value);
            });
        }
    });

    /**
     * Compare values
     * @param {HTMLInputElement | HTMLSelectElement } changedField The field that changed is value
     * @param {Element} dependentField The field that depents on the changed field
     * @param {string | null} equation The equation to compare
     * @param {string | null} value The value to compare for 
     */
    function compare(changedField: HTMLInputElement | HTMLSelectElement, dependentField: Element, equation: string | null, value: string | null) {
        let changedFieldValue = changedField.value;

        // Checkboxes
        if (changedField.type === 'checkbox') {
            changedField = changedField as HTMLInputElement;

            if (changedField.checked && changedField.dataset.key === value) {
                changedFieldValue = value;
            } else if (!changedField.checked && changedField.dataset.key === value) {
                changedFieldValue = '';
            }
        }

        if (equation === '=') {
            if (changedFieldValue === value) {
                dependentField.classList.add('active');
            } else {
                dependentField.classList.remove('active');
            }
        }

        if (equation === '!=') {
            if (changedFieldValue !== value) {
                dependentField.classList.add('active');
            } else {
                dependentField.classList.remove('active');
            }
        }
    }
};