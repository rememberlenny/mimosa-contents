<?php
/*-----------------------------------------------------------------------------------*/
/* Start WooThemes Functions - Please refrain from editing this section */
/*-----------------------------------------------------------------------------------*/

// Set path to WooFramework and theme specific functions
$functions_path = get_template_directory() . '/functions/';
$includes_path = get_template_directory() . '/includes/';

// Don't load alt stylesheet from WooFramework
if ( ! function_exists( 'woo_output_alt_stylesheet' ) ) {
	function woo_output_alt_stylesheet () {}
}

// Define the theme-specific key to be sent to PressTrends.
define( 'WOO_PRESSTRENDS_THEMEKEY', 'tnla49pj66y028vef95h2oqhkir0tf3jr' );

// WooFramework
require_once ( $functions_path . 'admin-init.php' );			// Framework Init

if ( get_option( 'woo_woo_tumblog_switch' ) == 'true' ) {
	//Enable Tumblog Functionality and theme is upgraded
	update_option( 'woo_needs_tumblog_upgrade', 'false' );
	update_option( 'tumblog_woo_tumblog_upgraded', 'true' );
	update_option( 'tumblog_woo_tumblog_upgraded_posts_done', 'true' );
	require_once ( $functions_path . 'admin-tumblog-quickpress.php' );	// Tumblog Dashboard Functionality
}

/*-----------------------------------------------------------------------------------*/
/* Load the theme-specific files, with support for overriding via a child theme.
/*-----------------------------------------------------------------------------------*/

$includes = array(
				'includes/theme-options.php', 			// Options panel settings and custom settings
				'includes/theme-functions.php', 		// Custom theme functions
				'includes/theme-actions.php', 			// Theme actions & user defined hooks
				'includes/theme-comments.php', 			// Custom comments/pingback loop
				'includes/theme-js.php', 				// Load JavaScript via wp_enqueue_script
				'includes/sidebar-init.php', 			// Initialize widgetized areas
				'includes/theme-widgets.php'			// Theme widgets
				);

// Theme-Specific
$includes[] = 'includes/theme-advanced.php';			// Advanced Theme Functions
$includes[] = 'includes/theme-shortcodes.php';	 		// Custom theme shortcodes
// Modules
$includes[] = 'includes/woo-layout/woo-layout.php';
$includes[] = 'includes/woo-meta/woo-meta.php';
$includes[] = 'includes/woo-hooks/woo-hooks.php';

// Allow child themes/plugins to add widgets to be loaded.
$includes = apply_filters( 'woo_includes', $includes );
			
foreach ( $includes as $i ) {
	locate_template( $i, true );
}

// Load WooCommerce functions, if applicable.
if ( is_woocommerce_activated() ) {
	locate_template( 'includes/theme-woocommerce.php', true );
}

// WooTumblog Loader
if ( get_option( 'woo_woo_tumblog_switch' ) == 'true' ) {
define( 'WOOTUMBLOG_ACTIVE', true ); // Define a constant for use in our theme's templating engine.
require_once ( $includes_path . 'tumblog/theme-tumblog.php' );		// Tumblog Output Functions
// Test for Post Formats
if ( get_option( 'woo_tumblog_content_method' ) == 'post_format' ) {
	require_once( $includes_path . 'tumblog/wootumblog_postformat.class.php' );
} else {
	require_once ($includes_path . 'tumblog/theme-custom-post-types.php' );	// Custom Post Types and Taxonomies
}

// Test for Post Formats
if ( get_option( 'woo_tumblog_content_method' ) == 'post_format' ) {
    global $woo_tumblog_post_format; 
    $woo_tumblog_post_format = new WooTumblogPostFormat(); 
    if ( $woo_tumblog_post_format->woo_tumblog_upgrade_existing_taxonomy_posts_to_post_formats()) {
    	update_option( 'woo_tumblog_post_formats_upgraded', 'true' );
    }
}
}

if ( ! is_admin() ) {
// Output stylesheet and custom.css after Canvas custom styling
remove_action( 'wp_head', 'woothemes_wp_head' );
add_action( 'woo_head', 'woothemes_wp_head' );
if ( get_option( 'woo_woo_tumblog_switch' ) == 'true' && get_option( 'woo_custom_rss' ) == 'true' ) {
	add_filter( 'the_excerpt_rss', 'woo_custom_tumblog_rss_output' );
	add_filter( 'the_content_rss', 'woo_custom_tumblog_rss_output' );
}
}

/*-----------------------------------------------------------------------------------*/
/* You can add custom functions below */
/*-----------------------------------------------------------------------------------*/










/*-----------------------------------------------------------------------------------*/
/* Don't add any code below here or the sky will fall down */
/*-----------------------------------------------------------------------------------*/
?>