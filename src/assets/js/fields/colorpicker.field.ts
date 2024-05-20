/**
 * Our colorpicker module - because we included the alpha colorpicker script, this is already included by default
 * @param {HTMLElement} framework The parent framework element
 */
declare var jQuery;

export const ColorpickerField = (framework: HTMLElement) => {
    const colorpickers = framework.querySelectorAll('.wpcf-colorpicker');
    colorpickers.forEach((element: Element) => {
        jQuery(element as any).wpColorPicker({
            palettes: true
        });
    });
};