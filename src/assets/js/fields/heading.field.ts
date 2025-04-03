/**
 * Our heading module, supporting collapsible sections within the customizer
 */
export const HeadingField = () => {

    const collapsibleElements = document.querySelectorAll('.wpcf-heading-collapsible');
    collapsibleElements.forEach((element: any) => {
        const collapsibleSections = element.dataset.sections;

    
        if (!collapsibleSections) {
            return;
        }
    
        const sectionsArray = collapsibleSections.split(',');
    
        sectionsArray.forEach((section: string) => {
            document.querySelector(`li[id$="${section}"]`)?.classList.add('hidden');
            document.querySelector(`.wpcf-field.field-id-${section}`)?.classList.add('hidden');
        });
    
        element.addEventListener('click', () => {
            element.classList.toggle('active');
    
            sectionsArray.forEach((section: string) => {
                document.querySelector(`li[id$="${section}"]`)?.classList.toggle('hidden');
                document.querySelector(`.wpcf-field.field-id-${section}`)?.classList.toggle('hidden');
            });
        });
    });
    
}