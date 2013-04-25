<?php global $cp; ?>
<div class="collabpress">
	<div class="project-links" style="float: right;">
		<?php cp_project_links(); ?>
	</div>
	<?php echo cp_project_title(); ?>
		<h3><?php _e( 'Users', 'collabpress' ); ?></h3>
		<div class="users">
			<?php foreach ( cp_get_project_users() as $user ) {
				echo get_avatar( $user->ID );
			} ?>
		</div>
		<?php if ( cp_check_permissions( 'settings_user_role' ) ) { ?>
		<a href="#inline_content" class="modify-users-link"><?php _e( 'Modify users', 'collabpress' ); ?></a>
		<?php } ?>
</div>
<div style='display:none'>
	<div id='inline_content' style='padding:10px; background:#fff;'>
	<h2><?php _e( 'Modify Users in Project', 'collabpress' ); ?></h2>
	<input type="hidden" id="modify_project_users_nonce" value="<?php echo wp_create_nonce( 'modify-project-users' ); ?>">
	<input type="hidden" id="cp-project-id" value="<?php echo cp_get_project_id() ?>">
		<p>
			<input type="button" name="CheckAll" class="check-all-users-button" value="<?php _e( 'Check All', 'collabpress' ); ?>" />
			<input type="button" name="UnCheckAll" class="uncheck-all-users-button" value="<?php _e( 'Uncheck All', 'collabpress' ); ?>" />
		</p>
		<?php
		//check if user is subscriber
		if ( !current_user_can( 'manage_options' ) ) {
			//if not admin, assign project to logged in user
			echo '<input type="checkbox" name="cp_project_users[]" value="'.$current_user->ID .'" checked="checked" />&nbsp;' .$current_user->user_login .'<br />';
		}else{
			// @todo This fails on huge userbases
			$wp_user_search = new WP_User_Query( array( 'fields' => 'all' ) );
			$wp_users = $wp_user_search->get_results();

			foreach ( $wp_users as $wp_user ) {
				echo '<input type="checkbox" name="cp_project_users[]" class="cp_project_user" value="'.$wp_user->ID .'" ' . checked( true, cp_user_is_in_project( cp_get_project_id(), $wp_user->ID ), false ) . ' />&nbsp;' .$wp_user->user_login .'<br />';
			}
		}
		?>
		<p class="submit">
			<input class="button-primary" type="submit" name="cp-add-project" value="<?php _e( 'Submit', 'collabpress' ); ?>"/>
			<span class="spinner" style="float: left"></span></p>
	</div>
</div>
<script>
(function($) {
	$(document).ready(function() {
		$('.modify-users-link').colorbox(
			{
				inline: true,
				width: '50%',
				onLoad: function() {
				}
			}
		);
	});
	$('.submit').click(function() {
		var data = {
			project_id: $('#cp-project-id').val(),
			users: [],
			collabpress_ajax_request_origin: '<?php echo ( is_admin() ? 'admin' : 'frontend' ); ?>',
		};
		$('.cp_project_user').each(function( i, el ){
			if ( $(el).is(':checked') ) {
				data.users.push( $(el).val() );
			}
		});
		$('#inline_content .spinner').show();
		$.post(
			ajaxurl,
			{
				action: 'cp_modify_project_users',
				data: data,
				nonce: jQuery('#modify_project_users_nonce').val()
			}, function( response ) {
				$('#inline_content .spinner').hide();
				window.location = response.data.redirect;
			}
		);
	});
	$('.check-all-users-button').click( function() {
		$('.cp_project_user').each( function() {
			jQuery(this).prop('checked', 'checked');
		});
	});
	$('.uncheck-all-users-button').click( function() {
		$('.cp_project_user').each( function() {
			jQuery(this).prop('checked', '');
		});
	});
})(jQuery);
</script>