<?php

//create [collabpress $id ] shortcode
//calls cp_sc_projects( $id)
//$id = project ID (option) : if not set all projects listed
add_shortcode( 'collabpress', 'cp_project_short' );

function cp_project_short( $atts ) {
    extract( shortcode_atts( array(
        "id"    =>  null
    ), $atts ) );

    //verify user has permission to view shortcode
	if ( is_user_logged_in() ) {
        if ( cp_check_permissions( 'shortcode_user_role' ) ) {
            cp_admin_menu_page_load();
        }
        else {
            _e( 'You do not have access to this project.', 'collabpress' );
        }
	} else {
		_e( 'You must be logged in to view this page.', 'collabpress' );
	}
}