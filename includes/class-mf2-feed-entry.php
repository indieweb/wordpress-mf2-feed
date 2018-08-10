<?php
/**
 * MF2 Feed Entry Class
 *
 * Assists in generating microformats2 properties from a post
 *
 * Based on the work of @dshankse: https://github.com/dshanske/indieweb-post-kinds/blob/master/includes/class-mf2-post.php
 */
class Mf2_Feed_Entry {
	public $type;
	public $name;
	public $url;
	public $author = array();
	public $published;
	public $updated;
	public $content;
	public $summary;
	public $category = array();
	public $featured;
	public $comment = array();

	public function __construct( $post ) {
		$post = get_post( $post );
		if ( ! $post ) {
			return false;
		}

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
		$this->author['url']   = get_the_author_meta( 'user_url', $post->post_author ) ? get_the_author_meta( 'user_url', $post->post_author ) : get_author_posts_url( $post->post_author );
		$this->author['photo'] = get_avatar_url( $post->post_author );
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

	/**
	 * Retrieve value
	 *
	 * @param  string $key The key to retrieve.
	 * @param  boolean $single Whether to return a a single value or array if there is only one value.
	 * @return boolean|string|array The result or false if does not exist.
	 */
	public function to_mf2() {
		$this->author = $this->jf2_to_mf2( $this->author );
		$entry        = get_object_vars( $this );
		$entry        = $this->jf2_to_mf2( $entry );

		return array_filter( $entry );
	}

	public function to_jf2() {
		$entry = get_object_vars( $this );

		return array_filter( $entry );
	}

	public function jf2_to_mf2( $entry ) {
		if ( ! $entry || ! is_array( $entry ) | isset( $entry['properties'] ) ) {
			return $entry;
		}

		$return               = array();
		$return['type']       = array( 'h-' . $entry['type'] );
		$return['properties'] = array();

		unset( $entry['type'] );

		foreach ( $entry as $key => $value ) {
			if ( empty( $value ) ) {
				continue;
			}
			if ( ! wp_is_numeric_array( $value ) ) {
				$value = array( $value );
			}
			$return['properties'][ $key ] = $value;
		}

		return array_filter( $return );
	}
}
