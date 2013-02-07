jQuery(function() {
	
	// Hover Class
	jQuery('.cp-activity-row ').live('mouseover mouseout', function(event) {
		if (event.type == 'mouseover') {
			jQuery(this).addClass('hover');
		} else {
			jQuery(this).removeClass('hover');
		}
	});
	
	// Date Picker
	jQuery("#datepicker").datepicker({dateFormat: 'm/d/yy'});
	
	jQuery("a.cp_grouped_elements").fancybox();

});
