<?php

defined( 'ABSPATH' ) || exit;

/**
 * Mf2_Feed class
 *
 * @author Matthias Pfefferle
 */
class Mf2_Feed {
	/**
	 * init function
	 */
	public static function init() {
		self::setup_feeds();
		// add 'json' as feed
		add_action( 'do_feed_mf2', array( __CLASS__, 'do_feed_mf2' ), 10, 1 );
		add_action( 'do_feed_jf2', array( __CLASS__, 'do_feed_jf2' ), 10, 1 );

		add_action( 'wp_head', array( __CLASS__, 'add_html_header' ), 5 );
		add_filter( 'feed_content_type', array( __CLASS__, 'feed_content_type' ), 10, 2 );

		add_filter( 'template_include', array( __CLASS__, 'render_json_template' ), 100 );
	}

	public static function activate() {
		self::setup_feeds();
		flush_rewrite_rules();
	}


	public static function setup_feeds() {
		add_feed( 'mf2', array( __CLASS__, 'do_feed_mf2' ) );
		add_feed( 'jf2', array( __CLASS__, 'do_feed_jf2' ) );
	}

	/**
	 * adds an MF2 JSON feed
	 *
	 * @param boolean $for_comments true if it is a comment-feed
	 */
	public static function do_feed_mf2( $for_comments ) {
		if ( $for_comments ) {
			load_template( MF2_FEED_PLUGIN_DIR . 'includes/feed-mf2-comments.php' );
		} else {
			load_template( MF2_FEED_PLUGIN_DIR . 'includes/feed-mf2.php' );
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
			load_template( MF2_FEED_PLUGIN_DIR . 'includes/feed-jf2-comments.php' );
		} else {
			load_template( MF2_FEED_PLUGIN_DIR . 'includes/feed-jf2.php' );
		}
	}

	/**
	 * Return a MF2/JF2 JSON version of an author, post or page.
	 *
	 * @param  string $template The path to the template object.
	 *
	 * @return string The new path to the JSON template.
	 */
	public static function render_json_template( $template ) {
		if ( ! is_singular() ) {
			return $template;
		}

		global $wp_query;

		if ( ! isset( $_SERVER['HTTP_ACCEPT'] ) ) {
			return $template;
		}

		$accept_header = $_SERVER['HTTP_ACCEPT'];

		if (
			stristr( $accept_header, 'application/mf2+json' )
		) {
			return MF2_FEED_PLUGIN_DIR . 'includes/feed-mf2-comments.php';
		} elseif (
			stristr( $accept_header, 'application/jf2+json' )
		) {
			return MF2_FEED_PLUGIN_DIR . 'includes/feed-jf2-comments.php';
		}

		// Accept header as an array.
		$accept = explode( ',', trim( $accept_header ) );

		if (
			in_array( 'application/mf2+json', $accept, true )
		) {
			return MF2_FEED_PLUGIN_DIR . 'includes/feed-mf2-comments.php';
		} elseif (
			in_array( 'application/jf2+json', $accept, true )
		) {
			return MF2_FEED_PLUGIN_DIR . 'includes/feed-jf2-comments.php';
		}

		return $template;
	}

	/**
	 * adds "mf2" content-type
	 *
	 * @param string $content_type the default content-type
	 * @param string $type the feed-type
	 * @return string the as1 content-type
	 */
	public static function feed_content_type( $content_type, $type ) {
		switch ( $type ) {
			case 'mf2':
				return apply_filters( 'mf2_feed_content_type', 'application/mf2+json' );
				break;
			case 'jf2':
				return apply_filters( 'jf2_feed_content_type', 'application/jf2+json' );
				break;
			case 'jf2feed':
				return apply_filters( 'jf2_feed_content_type', 'application/jf2feed+json' );
				break;
			default:
				return $content_type;
				break;
		}
	}

	/**
	 * Echos autodiscovery links
	 */
	public static function add_html_header() {
		if ( is_singular() ) {
			$links = array(
				array( 'mf2', get_post_comments_feed_link( null, 'mf2' ) ),
				array( 'jf2', get_post_comments_feed_link( null, 'jf2' ) ),
			);
		} else {
			$links = array(
				array( 'mf2', get_feed_link( 'mf2' ) ),
				array( 'jf2feed', get_feed_link( 'jf2' ) ),
			);
		}

		foreach ( $links as list( $type, $href ) ) {
			printf(
				'<link rel="alternate" type="%1$s" href="%2$s" />' . PHP_EOL,
				esc_attr( feed_content_type( $type ) ),
				esc_url( $href )
			);
		}
	}
}
