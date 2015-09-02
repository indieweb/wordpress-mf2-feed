<?php
/*
Plugin Name: UF2 Feed
Plugin URI: http://github.com/pfefferle/wordpress-uf2-feed/
Description: Adds a Microformats2 JSON feed for every plugin
Version: 1.0.0
Author: Matthias Pfefferle
Author URI: http://notizblog.org/
*/

// check if php version is >= 5.3
// version is required by the mf2 parser
function uf2_feed_activation() {
	if ( version_compare( phpversion(), 5.3, '<' ) ) {
		die( 'The minimum PHP version required for this plugin is 5.3' );
	}
}
register_activation_hook( __FILE__, 'uf2_feed_activation' );

if ( ! class_exists( 'Mf2\Parser' ) ) {
	require_once 'Mf2/Parser.php';
}

use Mf2\Parser;

add_action( 'init', array( 'Uf2Feed', 'init' ) );
register_activation_hook( __FILE__, array( 'Uf2Feed', 'flush_rewrite_rules' ) );
register_deactivation_hook( __FILE__, array( 'Uf2Feed', 'flush_rewrite_rules' ) );

/**
 * Uf2Feed class
 *
 * @author Matthias Pfefferle
 */
class Uf2Feed {
	/**
	 * init function
	 */
	public static function init() {
		// add 'json' as feed
		add_action( 'do_feed_uf2', array( 'Uf2Feed', 'do_feed_uf2' ), 10, 1 );
		// add 'json' as feed
		add_action( 'do_feed_mf2', array( 'Uf2Feed', 'do_feed_uf2' ), 10, 1 );
		// add the as1 feed
		add_feed( 'uf2', array( 'Uf2Feed', 'do_feed_uf2' ) );
		// add the as2 feed
		add_feed( 'mf2', array( 'Uf2Feed', 'do_feed_uf2' ) );

		add_filter( 'query_vars', array( 'Uf2Feed', 'query_vars' ) );
		add_filter( 'feed_content_type', array( 'Uf2Feed', 'feed_content_type' ), 10, 2 );
	}

	/**
	 * adds an UF2 JSON feed
	 *
	 * @param boolean $for_comments true if it is a comment-feed
	 */
	public static function do_feed_uf2( $for_comments ) {
		global $wp;

		// get query vars as array
		$params = $wp->query_vars;

		// remove feed param
		if ( isset( $params['feed'] ) ) {
			unset( $params['feed'] );
		}

		$current_url = add_query_arg( $params, site_url() );

		// filter feed URL (add url params for example)
		$current_url = apply_filters( 'uf2_feed_url', $current_url );

		// get HTML content
		$response = wp_remote_retrieve_body( wp_remote_get( $current_url, array( 'timeout' => 100 ) ) );

		// parse source html
		$parser = new Parser( $response, $current_url );

		// also support uf1?
		$parseUf1 = apply_filters( 'uf2_feed_support_uf1', false );

		$mf_array = $parser->parse( $parseUf1 );

		// filter output
		$json = apply_filters( 'uf2_feed_array', $mf_array );

		header( 'Content-Type: ' . feed_content_type( 'uf2' ) . '; charset=' . get_option( 'blog_charset' ), true );

		if ( version_compare( phpversion(), '5.3.0', '<' ) ) {
			// json_encode() options added in PHP 5.3
			$json_str = json_encode( $json );
		} else {
			$options = 0;
			// JSON_PRETTY_PRINT added in PHP 5.4
			if ( get_query_var( 'pretty' ) && version_compare( phpversion(), '5.4.0', '>=' ) ) {
				$options |= JSON_PRETTY_PRINT;
			}

			/*
			 * Options to be passed to json_encode()
			 *
			 * @param int $options The current options flags
			 */
			$options = apply_filters( 'uf2_feed_options', $options );
			$json_str = json_encode( $json, $options );
		}

		echo $json_str;
	}

	/**
	 * adds "uf2" content-type
	 *
	 * @param string $content_type the default content-type
	 * @param string $type the feed-type
	 * @return string the as1 content-type
	 */
	public static function feed_content_type( $content_type, $type ) {
		if ( 'uf2' == $type || 'mf2' == $type ) {
			return apply_filters( 'uf2_feed_content_type', 'application/json' );
		}

		return $content_type;
	}

	/**
	 * add 'feed' and 'pretty' as a valid query variables.
	 *
	 * @param array $vars
	 * @return array
	 */
	public static function query_vars( $vars ) {
		$vars[] = 'feed';
		$vars[] = 'pretty';

		return $vars;
	}

	/**
	 * reset rewrite rules
	 */
	public static function flush_rewrite_rules() {
		global $wp_rewrite;

		$wp_rewrite->flush_rules();
	}
}
