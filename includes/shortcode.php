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
    if ( cp_check_permissions( 'shortcode_user_role' ) ) {
    	cp_admin_menu_page_load();
    } else {
    	if ( is_user_logged_in() ) {
    		echo 'You do not have access to this project.';
    	} else {
    		echo 'You must be logged in to view this page.';
    	}

    }

}

// Checks to see if the current post/page is using
// CollabPress shortcode - if so adds front end css
add_action('the_posts', 'cp_using_shortcode');

function cp_using_shortcode($posts) {

    if ( empty($posts) )
        return $posts;

    $foundsc = false;

    foreach ($posts as $post) {
        if ( stripos($post->post_content, '[collabpress') )
            $foundsc = true;
            break;
        }

    if ($foundsc = true) {
    $css_src = CP_PLUGIN_URL . 'includes/css/front.css';
    $js_src = CP_PLUGIN_URL . 'includes/js/frontend.js';

    wp_register_style('cp_frontend_css', $css_src );
    wp_enqueue_style('cp_frontend_css');

    wp_register_script('cp_frontend_js', $js_src );
	wp_enqueue_script('jquery');
    wp_enqueue_script('cp_frontend_js');
	}

    return $posts;
}
