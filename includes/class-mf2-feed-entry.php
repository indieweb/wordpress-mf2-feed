<?php
/**
 * MF2 Feed Entry Class
 *
 * Assists in generating microformats2 properties from a post
 *
 * Based on the work of @dshankse: https://github.com/dshanske/indieweb-post-kinds/blob/master/includes/class-mf2-post.php
 */

defined( 'ABSPATH' ) || exit;

class Mf2_Feed_Entry {
	public $_id;
	public $type;
	public $name;
	public $url;
	public $uid;
	public $author = array();
	public $published;
	public $updated;
	public $content = array();
	public $summary;
	public $category = array();
	public $featured;
	public $comment = array();

	/**
	 * Builds an entry from a post.
	 *
	 * @param int|WP_Post $post          Post ID or object.
	 * @param bool        $with_comments Whether to include the comments.
	 *
	 * @return Mf2_Feed_Entry|null The entry, or null if the post does not exist.
	 */
	public static function from_post( $post, $with_comments = false ) {
		$post = get_post( $post );

		if ( ! $post instanceof WP_Post ) {
			return null;
		}

		return new self( $post, $with_comments );
	}

	/**
	 * @param WP_Post $post          A valid post object, see `from_post()`.
	 * @param bool    $with_comments Whether to include the comments.
	 */
	public function __construct( WP_Post $post, $with_comments = false ) {
		$this->_id  = $post->ID;
		$this->type = 'entry';
		$this->name = get_the_title( $post );
		// Eliminate IDs as names
		if ( (string) $this->_id === (string) $this->name ) {
			$this->name = null;
		}
		$this->published = get_post_time( DATE_W3C, false, $post );
		$this->updated   = get_post_modified_time( DATE_W3C, false, $post );
		$content         = get_the_content( null, false, $post );
		if ( ! empty( $content ) ) {
			$this->content['html']  = $content;
			$this->content['value'] = wp_strip_all_tags( $content );
		}
		$this->summary = get_the_excerpt( $post );
		$this->url     = get_permalink( $post );
		$this->uid     = get_permalink( $post );

		// Get a list of categories and extract their names
		$post_categories = get_the_terms( $post->ID, 'category' );
		if ( ! empty( $post_categories ) && ! is_wp_error( $post_categories ) ) {
			$this->category = wp_list_pluck( $post_categories, 'name' );
		}

		// Get a list of tags and extract their names
		$post_tags = get_the_terms( $post->ID, 'post_tag' );
		if ( ! empty( $post_tags ) && ! is_wp_error( $post_tags ) ) {
				$this->category = array_merge( $this->category, wp_list_pluck( $post_tags, 'name' ) );
		}

		if ( has_post_thumbnail( $post ) ) {
			$this->featured = wp_get_attachment_url( get_post_thumbnail_id( $post ), 'full' );
		}

		$this->author['type']  = 'card';
		$this->author['name']  = get_the_author_meta( 'display_name', $post->post_author );
		$this->author['value'] = get_the_author_meta( 'display_name', $post->post_author );
		$this->author['url']   = get_the_author_meta( 'user_url', $post->post_author ) ? get_the_author_meta( 'user_url', $post->post_author ) : get_author_posts_url( $post->post_author );
		$this->author['photo'] = get_avatar_url( $post->post_author );

		// add comments
		if ( $with_comments ) {
			foreach ( get_comments( array( 'post_id' => $post->ID ) ) as $post_comment ) {
				$comment                     = array();
				$comment['type']             = 'cite';
				$comment['content']['html']  = get_comment_text( $post_comment );
				$comment['content']['value'] = wp_strip_all_tags( $comment['content']['html'] );
				$comment['published']        = get_comment_date( DATE_W3C, $post_comment );
				$comment['author']['type']   = 'card';
				$comment['author']['name']   = get_comment_author( $post_comment );
				$comment['author']['value']  = $comment['author']['name'];

				if ( $post_comment->comment_author_url ) {
					$comment['author']['url'] = get_comment_author_url( $post_comment );
				}

				$this->comment[] = $comment;
			}
		}
	}

	public function to_mf2() {
		$entry = apply_filters( 'jf2_entry_array', $this->get_properties(), $this->_id );
		$entry = array_filter( $entry );
		$entry = apply_filters( 'mf2_entry_array', $this->jf2_to_mf2( $entry ), $this->_id );
		return $entry;
	}

	public function to_jf2() {
		$entry = apply_filters( 'jf2_entry_array', $this->get_properties(), $this->_id );
		return array_filter( $entry );
	}

	/**
	 * Returns the JF2 properties of the entry.
	 *
	 * Properties from the `mf2_*` post meta are merged in, but the
	 * properties derived from the post itself win.
	 *
	 * @return array
	 */
	public function get_properties() {
		return array_merge( $this->get_meta_properties(), array_filter( get_object_vars( $this ) ) );
	}

	/**
	 * Returns the JF2 properties stored as `mf2_*` post meta.
	 *
	 * This is the storage format used by the Micropub and Post Kinds plugins.
	 * The `mf2_` prefix is stripped, `mp-*` (Micropub commands) are skipped.
	 *
	 * @return array
	 */
	public function get_meta_properties() {
		$properties = array();

		foreach ( (array) get_post_meta( $this->_id ) as $key => $values ) {
			if ( 0 !== strpos( $key, 'mf2_' ) ) {
				continue;
			}

			$key = substr( $key, 4 );

			if ( 0 === strpos( $key, 'mp-' ) ) {
				continue;
			}

			$values = array_map( 'maybe_unserialize', (array) $values );

			// Post meta is always a list, unwrap single values.
			while ( is_array( $values ) && 1 === count( $values ) && isset( $values[0] ) ) {
				$values = $values[0];
			}

			$properties[ $key ] = $values;
		}

		/**
		 * Filters the properties read from the `mf2_*` post meta.
		 *
		 * @param array $properties The properties.
		 * @param int   $post_id    The post ID.
		 */
		return apply_filters( 'mf2_feed_meta_properties', $properties, $this->_id );
	}

	public function jf2_to_mf2( $entry ) {
		if ( ! $entry || ! is_array( $entry ) || isset( $entry['properties'] ) ) {
			return $entry;
		}

		$return               = array();
		$return['type']       = array( 'h-' . $entry['type'] );
		$return['properties'] = array();

		if ( ! empty( $entry['value'] ) ) {
			$return['type'] = $entry['value'];
		}

		unset( $entry['type'] );
		unset( $entry['value'] );

		foreach ( $entry as $key => $value ) {
			if ( empty( $value ) ) {
				continue;
			}
			if ( ! wp_is_numeric_array( $value ) && is_array( $value ) && array_key_exists( 'type', $value ) ) {
				$value = $this->jf2_to_mf2( $value );
			} elseif ( wp_is_numeric_array( $value ) && is_array( $value[0] ) && array_key_exists( 'type', $value[0] ) ) {
				foreach ( $value as $item ) {
					$items[] = $this->jf2_to_mf2( $item );
				}
				$value = $items;
			} elseif ( wp_is_numeric_array( $value ) && ! is_array( $value[0] ) ) {
				$value = $value;
			} elseif ( ! wp_is_numeric_array( $value ) ) {
				$value = array( $value );
			} else {
				continue;
			}

			$return['properties'][ $key ] = $value;
		}

		return array_filter( array( $return ) );
	}
}
