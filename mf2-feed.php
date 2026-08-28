<?php
/**
 * Plugin Name: MF2 Feed
 * Plugin URI: http://github.com/indieweb/wordpress-mf2-feed/
 * Description: Adds a Microformats2 JSON feed for every entry
 * Version: 3.1.1
 * Author: IndieWeb WordPress Outreach Club
 * Author URI: https://indieweb.org/WordPress_Outreach_Club
 * License: MIT
 * License URI: http://opensource.org/licenses/MIT
 * Text Domain: mf2-feed
 * Domain Path: /languages
 */

\define( 'MF2_FEED_VERSION', '3.1.1' );

\define( 'MF2_FEED_PLUGIN_DIR', \plugin_dir_path( __FILE__ ) );
\define( 'MF2_FEED_PLUGIN_BASENAME', \plugin_basename( __FILE__ ) );
\define( 'MF2_FEED_PLUGIN_FILE', \plugin_dir_path( __FILE__ ) . '/' . \basename( __FILE__ ) );
\define( 'MF2_FEED_PLUGIN_URL', \plugin_dir_url( __FILE__ ) );

require_once MF2_FEED_PLUGIN_DIR . 'includes/class-mf2-feed.php';

add_action( 'init', array( 'Mf2_Feed', 'init' ) );

// flush rewrite rules
register_activation_hook( __FILE__, array( 'Mf2_Feed', 'activate' ) );
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );

// Backcompat for function introduced in WordPress 5.3
if ( ! function_exists( 'get_self_link' ) ) {
	function get_self_link() {
		$host = @parse_url( home_url() );
		return set_url_scheme( 'http://' . $host['host'] . wp_unslash( $_SERVER['REQUEST_URI'] ) );
	}
}

