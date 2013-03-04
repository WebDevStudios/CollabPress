<?php
//verify user has permission to view help
if ( cp_check_permissions( 'settings_user_role' ) ) {
?>
    <div id="collabpress-wrap" class="wrap">
	<?php echo cp_screen_icon( 'collabpress' ); ?>
        <h2><?php _e( 'CollabPress Help', 'collabpress' ); ?></h2>
        
		<h3><?php _e( 'Shortcode Legend', 'collabpress' ); ?></h3>
        <p><strong>[collabpress]</strong> - <?php _e( 'Add full CollabPress support to the front-end of WordPress.  View/edit/delete all projects, task-lists, tasks, and comments.', 'collabpress' ); ?></p>
        <p><strong><a href="admin.php?page=collabpress-settings"><?php _e('Set user role', 'collabpress'); ?></a></strong> <?php _e('for front-end [collabpress] shortcode support', 'collabpress'); ?></p>
        
		<h3><?php _e( 'Support and Info', 'collabpress' ); ?></h3>
        <p><?php _e('Visit the', 'collabpress'); ?> <a href="http://wordpress.org/support/plugin/collabpress" target="_blank"><?php _e('CollabPress Support Forum', 'collabpress'); ?></a> &middot; <a href="http://twitter.com/collabpress" target="_blank"><?php _e( 'CollabPress on Twitter', 'collabpress' ); ?></a></p>
        
		<h3><?php _e( 'Get Involed', 'collabpress' ); ?></h3>
		<p><a href="https://github.com/WebDevStudios/CollabPress" target="_blank">Github</a> &middot; <a href="http://make.collabpress.org/" target="_blank">Make Blog</a></p>
		
		<h3><?php _e( 'FAQ', 'collabpress' ); ?></h3>
		<p><strong><?php _e( 'Q: Why isn&apos;t CollabPress working with BuddyPress?', 'collabpress' ); ?></strong></p>
		<p><?php _e( 'A: CollabPress requires BuddyPress v1.5 or higher to work.  You can download a copy on the <a href="http://buddypress.org/blog/" target="_blank">BuddyPress site</a>', 'collabpress' ); ?>.</p>
		<p><strong><?php _e( 'Q: How can I get involved with CollabPress development?', 'collabpress' ); ?></strong></p>
		<p><?php _e( 'A: Visit CollabPress on <a href="https://github.com/WebDevStudios/CollabPress" target="_blank">Github</a> to track active development.  You can also get involved on the <a href="http://make.collabpress.org/" target="_blank">Make Blog</a>.', 'collabpress' ); ?>.</p>
		
		
		<h3><?php _e( 'Donate', 'collabpress' ); ?></h3>
        <p><?php _e('Please donate to the development of CollabPress:', 'collabpress'); ?>
            <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
            <input type="hidden" name="cmd" value="_s-xclick">
            <input type="hidden" name="hosted_button_id" value="JHLW4KL7ZUZPY">
            <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="<?php _e('PayPal - The safer, easier way to pay online!', 'collabpress'); ?>">
            <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
            </form>
        </p>
		
<?php
    echo '<hr />';
    cp_footer();
}
?>