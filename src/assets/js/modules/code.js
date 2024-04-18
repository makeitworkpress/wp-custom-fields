/**
 * Our colorpicker module - because we included the alpha colorpicker script, this is already included by default
 */
module.exports.init = function(framework) {
 
    jQuery(framework).find('.wpcf-code-editor-value').each(function (index, node) {

        window.wcfCodeMirror[node.id] = wp.codeEditor.initialize(node, {
                mode: node.dataset.mode,
                lineNumbers: true
        });

    });
    
};