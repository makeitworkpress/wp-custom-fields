export const TabsLayout = function() {
    const tabs = document.querySelectorAll('.wpcf-tabs a');

    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();

            const activeTab = this.getAttribute('href');
            const section = activeTab.replace('#', '');
            const frame = this.closest('.wpcf-framework').id;

            // Change our active section
            const customFieldsSection = document.querySelector(`#wp_custom_fields_section_${frame}`) as HTMLInputElement;
            if (customFieldsSection) {
                customFieldsSection.value = section;
            }

            // Remove current active classes
            const framework = this.closest('.wpcf-framework');
            if (framework) {
                framework.querySelectorAll('.wpcf-tabs a').forEach(tab => tab.classList.remove('active'));
                framework.querySelectorAll('.wpcf-section').forEach(section => section.classList.remove('active'));
            }

            // Add active class to our new things
            this.classList.add('active');
            const newActiveTab = document.querySelector(activeTab);
            if (newActiveTab) {
                newActiveTab.classList.add('active');
            }
        });
    });
};