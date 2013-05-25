<?php

/**
 * PHP Contstant Declarations
 *
 * @package CollabPress
 * @since 1.3.2
 */

define( 'CP_VERSION', '1.3.1.1' );

if ( ! defined( 'CP_BASENAME' ) ) {
	define( 'CP_BASENAME', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'CP_PLUGIN_DIR' ) ) {
	define( 'CP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'CP_PLUGIN_URL' ) ) {
	define( 'CP_PLUGIN_URL', plugins_url( substr( CP_BASENAME, 0, strpos( CP_BASENAME, '/' ) ) ) . '/' );
}

define( 'CP_RSS_URL', 'http://collabpress.org/feed' );
