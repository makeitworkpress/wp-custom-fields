/**
 * Our icon field, allowing search or icons
 * @param {HTMLElement} framework The parent framework element
 */
export const IconsField = (framework: HTMLElement) => {

    const searchFields = framework.querySelectorAll('.wpcf-icons-search') as NodeListOf<HTMLInputElement>;
    const iconNodes: { [key: string]: NodeListOf<HTMLLIElement> } = {};
    
    searchFields.forEach((searchField: HTMLInputElement) => {
        searchField.addEventListener('input', (event: Event) => {
            const fieldId = (searchField.closest('.wpcf-field') as HTMLElement).dataset.id;

            if (!fieldId ) {
                return;
            }
    
            if (!iconNodes[fieldId]) {
                iconNodes[fieldId] = document.querySelectorAll(`[data-id="${fieldId}"] .wpcf-icon-list li`);
            }
    
            iconNodes[fieldId].forEach((icon: HTMLLIElement) => {
                // Reset visibility
                if (!searchField.value) {
                    icon.classList.remove('hidden');
                    return;
                }
    
                // Hide non matching icons
                if (icon.dataset.icon && icon.dataset.icon.includes(searchField.value)) {
                    icon.classList.remove('hidden');
                } else {
                    icon.classList.add('hidden');
                }
            });
        });
    });
    
}