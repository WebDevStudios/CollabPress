jQuery(document).ready(function() {
	var thePostID;
	jQuery('#upload_image_button').live('click', function() {
		thePostID = jQuery(this).parent().children('.cp-featured-id').filter(':first').val();
		formfield = jQuery('#upload_image').attr('name');
		inputfield = jQuery('#upload_image_button').attr('name');
		tb_show('', 'media-upload.php?type=file&post_id='+thePostID+'&TB_iframe=true');
	return false;
	});
});