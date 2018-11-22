/**
 * Our colorpicker module - because we included the alpha colorpicker script, this is already included by default
 */
module.exports.init = function(framework) {
 
    jQuery(framework).find('.wp-custom-fields-code-editor-value').each(function (index, node) {

        window.wcfCodeMirror[node.id] = CodeMirror.fromTextArea(node, {
                mode: node.dataset.mode,
                lineNumbers: true
        });

    });
    
};