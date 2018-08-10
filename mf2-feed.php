<?php
/**
 * Plugin Name: UF2 Feed
 * Plugin URI: http://github.com/indieweb/wordpress-mf2-feed/
 * Description: Adds a Microformats2 JSON feed for every plugin
 * Version: 1.0.0
 * Author: Matthias Pfefferle
 * Author URI: https://notiz.blog/
 */

add_action( 'init', array( 'Mf2Feed', 'init' ) );
register_activation_hook( __FILE__, array( 'Mf2Feed', 'flush_rewrite_rules' ) );
register_deactivation_hook( __FILE__, array( 'Mf2Feed', 'flush_rewrite_rules' ) );

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
		// add 'json' as feed
		add_action( 'do_feed_mf2', array( 'Mf2Feed', 'do_feed_mf2' ), 10, 1 );
		add_feed( 'mf2', array( 'Mf2Feed', 'do_feed_mf2' ) );

		add_action( 'do_feed_jf2', array( 'Mf2Feed', 'do_feed_jf2' ), 10, 1 );
		add_feed( 'jf2', array( 'Mf2Feed', 'do_feed_jf2' ) );

		add_action( 'wp_head', array( 'Mf2Feed', 'add_html_header' ), 5 );
		add_filter( 'query_vars', array( 'Mf2Feed', 'query_vars' ) );
		add_filter( 'feed_content_type', array( 'Mf2Feed', 'feed_content_type' ), 10, 2 );
	}

	/**
	 * adds an MF2 JSON feed
	 *
	 * @param boolean $for_comments true if it is a comment-feed
	 */
	public static function do_feed_mf2( $for_comments ) {
		if ( ! $for_comments ) {
			return;
		}

		require_once dirname( __FILE__ ) . '/includes/class-mf2-feed-entry.php';

		$post = new Mf2_Feed_Entry( get_the_ID(), $for_comments );

		$post = $post->to_mf2();

		$items            = array();
		$items['items'][] = $post;

		// filter output
		$json = apply_filters( 'mf2_feed_array', $items );

		header( 'Content-Type: ' . feed_content_type( 'mf2' ) . '; charset=' . get_option( 'blog_charset' ), true );

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
			$options = apply_filters( 'mf2_feed_options', $options );

			$json_str = json_encode( $json, $options );
		}

		echo $json_str;
	}

	/**
	 * adds an UF2 JSON feed
	 *
	 * @param boolean $for_comments true if it is a comment-feed
	 */
	public static function do_feed_jf2( $for_comments ) {
		if ( ! $for_comments ) {
			return;
		}

		require_once dirname( __FILE__ ) . '/includes/class-mf2-feed-entry.php';

		$post = new Mf2_Feed_Entry( get_the_ID(), $for_comments );

		$post = $post->to_jf2();

		// filter output
		$json = apply_filters( 'jf2_feed_array', $post );

		header( 'Content-Type: ' . feed_content_type( 'jf2' ) . '; charset=' . get_option( 'blog_charset' ), true );

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
			$options = apply_filters( 'jf2_feed_options', $options );

			$json_str = json_encode( $json, $options );
		}

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

	/**
	 * Echos autodiscovery links
	 */
	public static function add_html_header() {
		if ( is_singular() ) {
		?>
<link rel="alternate" type="<?php echo esc_attr( feed_content_type( 'mf2' ) ); ?>" href="<?php echo esc_url( get_post_comments_feed_link( null, 'mf2' ) ); ?>" />
<link rel="alternate" type="<?php echo esc_attr( feed_content_type( 'jf2' ) ); ?>" href="<?php echo esc_url( get_post_comments_feed_link( null, 'jf2' ) ); ?>" />
		<?php
		}
	}
}
