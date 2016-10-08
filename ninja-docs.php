<?php
/*
Plugin Name: Ninja Docs
Plugin URI: http://wpninjas.com
Description: Documentation plugin for the WP Ninjas
Version: 0.1
Author: James Laws
Author URI: http://wpninjas.com
*/

/**
 * Define constants
 **/
define( "NINJA_DOCS_DIR", WP_PLUGIN_DIR."/".basename( dirname( __FILE__ ) ) );
define( "NINJA_DOCS_URL", plugins_url()."/".basename( dirname( __FILE__ ) ) );
define( "NINJA_DOCS_VERSION", '1.0' );

/**
 * Load plugin textdomain
 **/
load_plugin_textdomain('ninja-docs', false, NINJA_DOCS_DIR . 'languages' );

function ninja_docs_load_files() {
    require_once( NINJA_DOCS_DIR . '/post-types/docs.php' );
    require_once( NINJA_DOCS_DIR . '/display/doc-archive.php' );
    require_once( NINJA_DOCS_DIR . '/inc/search.php' );
    require_once( NINJA_DOCS_DIR . '/inc/nf-docs-action.php' );
    require_once( NINJA_DOCS_DIR . '/inc/edit-suggestions.php' );
}
add_action( 'plugins_loaded', 'ninja_docs_load_files' );
