module.exports.tabs = function() {
    
    jQuery(".divergent-tabs a").click(function (e) {
        
        e.preventDefault();
        
        var activeTab = jQuery(this).attr("href"),
            section = activeTab.replace('#', '');
        
        // Change our active section
        jQuery('input[name="divergentSection"]').val(section);
		
        // Remove current active classes
        jQuery(".divergent-tabs a").removeClass("active");
        jQuery(".divergent-section").removeClass("active");
        
        // Add active class to our new things
        jQuery(this).addClass("active");      
        jQuery(activeTab).addClass("active");

	});
    
}