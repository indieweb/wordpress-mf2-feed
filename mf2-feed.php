<?php
/**
 * Plugin Name: MF2 Feed
 * Plugin URI: http://github.com/indieweb/wordpress-mf2-feed/
 * Description: Adds a Microformats2 JSON feed for every entry
 * Version: 2.1.0
 * Author: Matthias Pfefferle
 * Author URI: https://notiz.blog/
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
		add_filter( 'query_vars', array( 'Mf2Feed', 'query_vars' ) );
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
		require_once dirname( __FILE__ ) . '/includes/class-mf2-feed-entry.php';

		if ( $for_comments ) {
			$post = new Mf2_Feed_Entry( get_the_ID() );

			$post = $post->to_mf2();

			$items          = array();
			$items['items'] = $post;
		} else {
			$items = array(
				'items' => array(
					array(
						'type'       => array( 'h-feed' ),
						'properties' => array(
							'name'    => array( get_bloginfo( 'name' ) ),
							'summary' => array( get_bloginfo( 'description' ) ),
							'url'     => array( site_url( '/' ) ),
						),
					),
				),
			);

			while ( have_posts() ) {
				the_post();

				$post = new Mf2_Feed_Entry( get_the_ID() );

				$items['items'][0]['children'][] = current( $post->to_mf2() );
			}
		}

		// filter output
		$json = apply_filters( 'mf2_feed_array', $items );

		header( 'Content-Type: ' . feed_content_type( 'mf2' ) . '; charset=' . get_option( 'blog_charset' ), true );

		$options = 0;
		// JSON_PRETTY_PRINT added in PHP 5.4
		if ( get_query_var( 'pretty' ) ) {
			$options |= JSON_PRETTY_PRINT;
		}

		/*
		 * Options to be passed to json_encode()
		 *
		 * @param int $options The current options flags
		 */
		$options = apply_filters( 'mf2_feed_options', $options );

		$json_str = wp_json_encode( $json, $options );

		echo $json_str;
	}

	/**
	 * adds an UF2 JSON feed
	 *
	 * @param boolean $for_comments true if it is a comment-feed
	 */
	public static function do_feed_jf2( $for_comments ) {
		require_once dirname( __FILE__ ) . '/includes/class-mf2-feed-entry.php';

		if ( $for_comments ) {
			$post  = new Mf2_Feed_Entry( get_the_ID(), $for_comments );
			$items = $post->to_jf2();
		} else {
			$items = array( 'type' => 'feed' );

			while ( have_posts() ) {
				the_post();

				$post                = new Mf2_Feed_Entry( get_the_ID() );
				$items['children'][] = $post->to_jf2();
			}
		}

		// filter output
		$json = apply_filters( 'jf2_feed_array', $items );

		header( 'Content-Type: ' . feed_content_type( 'jf2' ) . '; charset=' . get_option( 'blog_charset' ), true );

		$options = 0;

		// JSON_PRETTY_PRINT added in PHP 5.4
		if ( get_query_var( 'pretty' ) ) {
			$options |= JSON_PRETTY_PRINT;
		}

		/*
		 * Options to be passed to json_encode()
		 *
		 * @param int $options The current options flags
		 */
		$options = apply_filters( 'jf2_feed_options', $options );

		$json_str = wp_json_encode( $json, $options );

		echo $json_str;
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

		return $content_type;
	}

	/**
	 * add 'pretty' as a valid query variables.
	 *
	 * @param array $vars
	 * @return array
	 */
	public static function query_vars( $vars ) {
		$vars[] = 'pretty';
		return $vars;
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
		} elseif ( is_home() ) {
			?>
<link rel="alternate" type="<?php echo esc_attr( feed_content_type( 'mf2' ) ); ?>" href="<?php echo esc_url( get_feed_link( 'mf2' ) ); ?>" />
<link rel="alternate" type="<?php echo esc_attr( feed_content_type( 'jf2' ) ); ?>" href="<?php echo esc_url( get_feed_link( 'jf2' ) ); ?>" />
			<?php
		}
	}
}
