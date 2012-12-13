<?php
/*
Plugin Name: CollabPress
Plugin URI: http://collabpress.org/
Description: A Project Management Plugin for WordPress
Version: 1.2.3
Author: WebDevStudios.com
Author URI: http://webdevstudios.com/
License: GPLv2
*/

/*  Copyright 2011  WebDevStudios  (email : contact@webdevstudios.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// CollabPress Define(s)
define( 'CP_VERSION', '1.2.3' );
define( 'CP_BASENAME', plugin_basename(__FILE__) );
define( 'CP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CP_PLUGIN_URL', plugins_url( $path = 'collabpress/' ) );
define( 'CP_RSS_URL', 'http://collabpress.org/feed' );

// Before CollabPress
do_action( 'cp_before_collabpress' );

//front-end querystring support
$cp_qs_add = cp_frontend_querystrings();

$cp_dashboard = ( is_admin() ) ? 'admin.php?page=collabpress-dashboard' : '?' .$cp_qs_add. 'cp=front';
define( 'CP_DASHBOARD', $cp_dashboard );

// CollabPress Core
require_once( 'includes/cp-core.php' );

function cp_frontend_querystrings() {

	// grab any query strings that exist
	$cp_all_querystrings = ( $_SERVER["QUERY_STRING"] ) ? $_SERVER["QUERY_STRING"] : '';
	$cp_querystrings = explode( '&', $cp_all_querystrings );

	//set pattern to strip out
	$pattern = "/^cp|project|task-list|task|view/";
	$cp_qs_add = '';

	foreach ( $cp_querystrings as $cp_querystring ) {

	    if ( !preg_match( $pattern, $cp_querystring ) ) {
		$cp_qs_add .= $cp_querystring .'&';
	    }

	}

	if ( $cp_qs_add != '&' ) :
	    return $cp_qs_add;
	endif;

}