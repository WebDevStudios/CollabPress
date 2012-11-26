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
	
	
	jQuery(".open").click(function() {
	var toggleDiv = jQuery(this).attr('rel'); // get id of form to open
		jQuery(toggleDiv).show();
		 return false;
	});
	jQuery(".close").click(function() {
	var toggleDiv = jQuery(this).attr('rel'); // get id of form to close
		jQuery(toggleDiv).hide();
		return false;
	});

});
