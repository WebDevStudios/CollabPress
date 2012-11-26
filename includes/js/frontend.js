jQuery(function() {	
	// Date Picker
	jQuery("#datepicker").datepicker({dateFormat: 'm/d/yy'});
	
	jQuery(".open").click(function() {
	var toggleDiv = jQuery(this).attr('rel'); // get id of form to open
		jQuery(toggleDiv).show();
		 return false;
	});
	jQuery(".close").click(function() {
	var toggleDiv = jQuery(this).attr('rel');  // get id of form to close
		jQuery(toggleDiv).hide();
		return false;
	});

});