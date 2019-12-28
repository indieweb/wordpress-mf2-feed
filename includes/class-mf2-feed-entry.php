<?php
/**
 * MF2 Feed Entry Class
 *
 * Assists in generating microformats2 properties from a post
 *
 * Based on the work of @dshankse: https://github.com/dshanske/indieweb-post-kinds/blob/master/includes/class-mf2-post.php
 */
class Mf2_Feed_Entry {
	public $_id;
	public $type;
	public $name;
	public $url;
	public $author = array();
	public $published;
	public $updated;
	public $content = array();
	public $summary;
	public $category = array();
	public $featured;
	public $comment = array();

	public function __construct( $post, $with_comments = false ) {
		$this->_id              = $post->ID;
		$this->type             = 'entry';
		$this->name             = $post->post_name;
		$this->published        = mysql2date( DATE_W3C, $post->post_date );
		$this->updated          = mysql2date( DATE_W3C, $post->post_modified );
		$this->content['html']  = $this->get_content_by_id( $post->ID );
		$this->content['value'] = wp_strip_all_tags( $post->post_content );
		$this->summary          = $this->get_excerpt_by_id( $post->ID );
		$this->url              = get_permalink( $post->ID );

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
				$comment['content']['html']  = $post_comment->comment_content;
				$comment['content']['value'] = wp_strip_all_tags( $post_comment->comment_content );
				$comment['published']        = mysql2date( DATE_W3C, $post_comment->comment_date_gmt );
				$comment['author']['type']   = 'card';
				$comment['author']['name']   = $post_comment->comment_author;
				$comment['author']['value']  = $post_comment->comment_author;

				if ( $post_comment->comment_author_url ) {
					$comment['author']['url'] = $post_comment->comment_author_url;
				}

				$this->comment[] = $comment;
			}
		}
	}

	/**
	 * Display the post content. Optinally allows post ID to be passed
	 * @uses the_content()
	 *
	 * @param int $id Optional. Post ID.
	 * @param string $more_link_text Optional. Content for when there is more text.
	 * @param bool $stripteaser Optional. Strip teaser content before the more text. Default is false.
	 */
	private function get_content_by_id( $post_id = 0, $more_link_text = null, $stripteaser = false ) {
		global $post;
		$post = get_post( $post_id );
		setup_postdata( $post, $more_link_text, $stripteaser );
		$content = get_the_content();
		wp_reset_postdata( $post );

		return $content;
	}

	/**
	 * Display the excerpt content. Optinally allows post ID to be passed
	 * @uses the_content()
	 *
	 * @param int $id Optional. Post ID.
	 * @param string $more_link_text Optional. Content for when there is more text.
	 * @param bool $stripteaser Optional. Strip teaser content before the more text. Default is false.
	 */
	private function get_excerpt_by_id( $post_id = 0, $more_link_text = null, $stripteaser = false ) {
		global $post;
		$post = get_post( $post_id );
		setup_postdata( $post, $more_link_text, $stripteaser );
		$content = get_the_excerpt();
		wp_reset_postdata( $post );

		return $content;
	}

	public function to_mf2() {
		$entry = apply_filters( 'jf2_entry_array', get_object_vars( $this ), $this->_id );
		$entry = apply_filters( 'mf2_entry_array', $this->jf2_to_mf2( $entry ), $this->_id );

		return array_filter( $entry );
	}

	public function to_jf2() {
		$entry = apply_filters( 'jf2_entry_array', get_object_vars( $this ), $this->_id );

		return array_filter( $entry );
	}

	public function jf2_to_mf2( $entry ) {
		if ( ! $entry || ! is_array( $entry ) | isset( $entry['properties'] ) ) {
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
