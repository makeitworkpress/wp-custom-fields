module.exports.init = function() {
    
    jQuery(".wp-custom-fields-tabs a").click(function (e) {
        
        e.preventDefault();
        
        var activeTab = jQuery(this).attr("href"),
            section = activeTab.replace('#', '');
        
        // Change our active section
        jQuery('input[name="divergentSection"]').val(section);
		
        // Remove current active classes
        jQuery(this).closest('.wp-custom-fields-framework').find(".wp-custom-fields-tabs a").removeClass("active");
        jQuery(this).closest('.wp-custom-fields-framework').find(".wp-custom-fields-section").removeClass("active");
        
        // Add active class to our new things
        jQuery(this).addClass("active");      
        jQuery(activeTab).addClass("active");

	});
    
}