/**
 * Our code mirror code module, using the native code mirror functionalities from Wordpress
 * @param {HTMLElement} framework The parent framework element
 */
declare var wp;

export const CodeField = (framework) => {

    if (typeof (wp as any).codeEditor === 'undefined') {
        return;
    }

    framework.querySelectorAll('.wpcf-code-editor-value').forEach((node: HTMLElement) => {
        const settings = JSON.parse(node.dataset.settings || '{}');
        (window as any).wpcfCodeMirror[node.id] = (wp as any).codeEditor.initialize(node, settings);
    });
    
};