module.exports.init = function() {
    
    // Click handler for our tabs
    jQuery(".wpcf-tabs a").click(function (e) {
        
        e.preventDefault();
        
        var activeTab = jQuery(this).attr("href"),
            section = activeTab.replace('#', ''),
            frame = jQuery(this).closest('.wpcf-framework').attr('id');
        
        // Change our active section
        jQuery('#wp_custom_fields_section_' + frame).val(section);
		
        // Remove current active classes
        jQuery(this).closest('.wpcf-framework').find(".wpcf-tabs a").removeClass("active");
        jQuery(this).closest('.wpcf-framework').find(".wpcf-section").removeClass("active");
        
        // Add active class to our new things
        jQuery(this).addClass("active");      
        jQuery(activeTab).addClass("active");

    });
 
}