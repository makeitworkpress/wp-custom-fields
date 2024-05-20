/**
 * Initializes our datepicker using the flatpickr library
 * @param {HTMLElement} framework The parent framework element
 */
declare var flatpickr;

export const DatepickerField = (framework: HTMLElement) => {

    if (typeof flatpickr !== 'function') {
        return;
    }

    interface FlatpickrConfig {
        altFormat?: string;
        altInput?: boolean;
        dateFormat?: string;
        time_24hr?: boolean;
        wrap?: boolean;
        [key: string]: any; // Additional properties
    }
    
    const config: FlatpickrConfig = {
        altFormat: 'F j, Y',
        altInput: true,
        dateFormat: 'U',
        time_24hr: true,
        wrap: true
    };
    
    const datePicker = framework.querySelectorAll('.wpcf-datepicker');
    
    (datePicker as NodeListOf<HTMLInputElement>).forEach((element: HTMLInputElement) => {
        const customProperties: string[] = ['enable-time', 'alt-format', 'date-format', 'locale', 'max-date', 'min-date', 'mode', 'no-calendar', 'week-numbers'];
        
        customProperties.forEach((attribute: string) => {
            const propertyValue = element.dataset[attribute];
            
            if (propertyValue) {
                const propertyName = attribute.replace(/-([a-z])/g, (match, letter) => letter.toUpperCase());
                config[propertyName] = propertyValue;
            }
        });
    
        flatpickr(element, config);
    });

};