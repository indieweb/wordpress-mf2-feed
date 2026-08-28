<?php
/**
 * Plugin Name: MF2 Feed
 * Plugin URI: http://github.com/indieweb/wordpress-mf2-feed/
 * Description: Adds a Microformats2 JSON feed for every entry
 * Version: 3.2.0
 * Author: IndieWeb WordPress Outreach Club
 * Author URI: https://indieweb.org/WordPress_Outreach_Club
 * License: MIT
 * License URI: http://opensource.org/licenses/MIT
 * Requires at least: 5.3
 * Requires PHP: 7.4
 * Text Domain: mf2-feed
 * Domain Path: /languages
 */

\define( 'MF2_FEED_VERSION', '3.2.0' );

\define( 'MF2_FEED_PLUGIN_DIR', \plugin_dir_path( __FILE__ ) );
\define( 'MF2_FEED_PLUGIN_BASENAME', \plugin_basename( __FILE__ ) );
\define( 'MF2_FEED_PLUGIN_FILE', __FILE__ );
\define( 'MF2_FEED_PLUGIN_URL', \plugin_dir_url( __FILE__ ) );

require_once MF2_FEED_PLUGIN_DIR . 'includes/class-mf2-feed.php';
require_once MF2_FEED_PLUGIN_DIR . 'includes/class-mf2-feed-entry.php';

// Backcompat for the old class name.
\class_alias( 'Mf2_Feed', 'Mf2Feed' );

add_action( 'init', array( 'Mf2_Feed', 'init' ) );

// flush rewrite rules
register_activation_hook( __FILE__, array( 'Mf2_Feed', 'activate' ) );
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
