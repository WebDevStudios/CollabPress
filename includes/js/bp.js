jQuery(document).ready(function($){
	$('.hide-on-load .inside').slideToggle(200);

	$('.cp-meta-box h4').click(function(){
		var parent = $(this).parent();
		$(parent).children('.inside').slideToggle(200);
	});
},jQuery);