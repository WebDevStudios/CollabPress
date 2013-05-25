<?php

/**
 * PHP Contstant Declarations
 *
 * @package CollabPress
 * @since 1.4
 */

define( 'COLLABPRESS_VERSION', '1.4-dev' );

if ( defined( 'CP_BASENAME' ) )
	define( 'COLLABPRESS_BASENAME', CP_BASENAME );

if ( ! defined( 'COLLABPRESS_BASENAME' ) )
	define( 'COLLABPRESS_BASENAME', plugin_basename(__FILE__) );

if ( defined( 'CP_PLUGIN_DIR' ) ) {
	if ( false !== strpos( CP_PLUGIN_DIR, 'collabpress' ) ) {
		define( 'COLLABPRESS_PLUGIN_DIR', CP_PLUGIN_DIR );
	}
}

if ( ! defined( 'COLLABPRESS_PLUGIN_DIR' ) )
	define( 'COLLABPRESS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

if ( defined( 'CP_PLUGIN_URL' ) )
	define( 'COLLABPRESS_PLUGIN_URL', CP_PLUGIN_URL );

if ( ! defined( 'COLLABPRESS_PLUGIN_URL' ) )
	define( 'COLLABPRESS_PLUGIN_URL', plugins_url( substr( COLLABPRESS_BASENAME, 0, strpos( COLLABPRESS_BASENAME, '/' ) ) ) . '/' );

define( 'COLLABPRESS_RSS_URL', 'http://collabpress.org/feed' );