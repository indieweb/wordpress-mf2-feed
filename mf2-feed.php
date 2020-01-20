<?php
/**
 * Plugin Name: MF2 Feed
 * Plugin URI: http://github.com/indieweb/wordpress-mf2-feed/
 * Description: Adds a Microformats2 JSON feed for every entry
 * Version: 3.0.0
 * Author: WordPress Outreach Club
 * Author URI: https://indieweb.org/WordPress_Outreach_Club
 * License: MIT
 * License URI: http://opensource.org/licenses/MIT
 * Text Domain: mf2-feed
 * Domain Path: /languages
 */

add_action( 'init', array( 'Mf2Feed', 'init' ) );

// flush rewrite rules
register_activation_hook( __FILE__, array( 'Mf2Feed', 'activate' ) );
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );

/**
 * Mf2Feed class
 *
 * @author Matthias Pfefferle
 */
class Mf2Feed {
	/**
	 * init function
	 */
	public static function init() {
		self::setup_feeds();
		// add 'json' as feed
		add_action( 'do_feed_mf2', array( 'Mf2Feed', 'do_feed_mf2' ), 10, 1 );
		add_action( 'do_feed_jf2', array( 'Mf2Feed', 'do_feed_jf2' ), 10, 1 );

		add_action( 'wp_head', array( 'Mf2Feed', 'add_html_header' ), 5 );
		add_filter( 'feed_content_type', array( 'Mf2Feed', 'feed_content_type' ), 10, 2 );
	}

	public static function activate() {
		self::setup_feeds();
		flush_rewrite_rules();
	}


	public static function setup_feeds() {
		add_feed( 'mf2', array( 'Mf2Feed', 'do_feed_mf2' ) );
		add_feed( 'jf2', array( 'Mf2Feed', 'do_feed_jf2' ) );
	}

	/**
	 * adds an MF2 JSON feed
	 *
	 * @param boolean $for_comments true if it is a comment-feed
	 */
	public static function do_feed_mf2( $for_comments ) {
		if ( $for_comments ) {
			load_template( dirname( __FILE__ ) . '/includes/feed-mf2-comments.php' );
		} else {
			load_template( dirname( __FILE__ ) . '/includes/feed-mf2.php' );
		}
	}

	/**
	 * Prepares JSON for output
	 *
	 * @param array $json Associative array
	 * @return string $json_str JSON encoded string
	 */
	public static function encode_json( $json, $feed = 'mf2' ) {
		$options = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES;
		/*
		 * Options to be passed to json_encode()
		 *
		 * @param int $options The current options flags
		 */
		$options = apply_filters( '{$feed}_feed_options', $options ); // phpcs:ignore

		return wp_json_encode( $json, $options );
	}

	/**
	 * adds an UF2 JSON feed
	 *
	 * @param boolean $for_comments true if it is a comment-feed
	 */
	public static function do_feed_jf2( $for_comments ) {
		if ( $for_comments ) {
			load_template( dirname( __FILE__ ) . '/includes/feed-jf2-comments.php' );
		} else {
			load_template( dirname( __FILE__ ) . '/includes/feed-jf2.php' );
		}
	}

	/**
	 * adds "mf2" content-type
	 *
	 * @param string $content_type the default content-type
	 * @param string $type the feed-type
	 * @return string the as1 content-type
	 */
	public static function feed_content_type( $content_type, $type ) {
		if ( 'mf2' === $type || 'mf2' === $type ) {
			return apply_filters( 'mf2_feed_content_type', 'application/mf2+json' );
		}

		if ( 'jf2' === $type || 'jf2' === $type ) {
			return apply_filters( 'jf2_feed_content_type', 'application/jf2+json' );
		}
		if ( 'jf2feed' === $type || 'jf2feed' === $type ) {
			return apply_filters( 'jf2_feed_content_type', 'application/jf2feed+json' );
		}

		return $content_type;
	}

	/**
	 * Echos autodiscovery links
	 */
	public static function add_html_header() {
		if ( is_singular() ) {
			?>
<link rel="alternate" type="<?php echo esc_attr( feed_content_type( 'mf2' ) ); ?>" href="<?php echo esc_url( get_post_comments_feed_link( null, 'mf2' ) ); ?>" />
<link rel="alternate" type="<?php echo esc_attr( feed_content_type( 'jf2' ) ); ?>" href="<?php echo esc_url( get_post_comments_feed_link( null, 'jf2' ) ); ?>" />
			<?php
		} else {
			?>
<link rel="alternate" type="<?php echo esc_attr( feed_content_type( 'mf2' ) ); ?>" href="<?php echo esc_url( get_feed_link( 'mf2' ) ); ?>" />
<link rel="alternate" type="<?php echo esc_attr( feed_content_type( 'jf2feed' ) ); ?>" href="<?php echo esc_url( get_feed_link( 'jf2' ) ); ?>" />
			<?php
		}
	}
}

// Backcompat for function introduced in WordPress 5.3
if ( ! function_exists( 'get_self_link' ) ) {
	function get_self_link() {
		$host = @parse_url( home_url() );
		return set_url_scheme( 'http://' . $host['host'] . wp_unslash( $_SERVER['REQUEST_URI'] ) );
	}
}

