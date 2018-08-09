<?php
/**
 * MF2 Post Class
 *
 * @package Post Kinds
 *
 * Assists in retrieving/saving microformats 2 properties from a post
 */
class Mf2_Feed_Post {
	public $ID;
	private $h_entry = array();
	private $mf2;

	public function __construct( $post ) {
		$post = get_post( $post );
		if ( ! $post ) {
			return false;
		}

		$this->ID                      = $post->ID;
		$this->h_entry['published'][0] = mysql2date( DATE_W3C, $post->post_date );
		$this->h_entry['updated'][0]   = mysql2date( DATE_W3C, $post->post_modified );
		$this->h_entry['content'][0]   = $post->post_content;
		$this->h_entry['summary'][0]   = $post->post_excerpt;
		$this->h_entry['url'][0]       = get_permalink( $this->ID );
		$this->h_entry['name'][0]      = $post->post_name;
		$this->mf2                     = $this->get_mf2meta();

		// Get a list of categories and extract their names
		$post_categories = get_the_terms( $post->ID, 'category' );
		if ( ! empty( $post_categories ) && ! is_wp_error( $post_categories ) ) {
			$this->h_entry['category'] = wp_list_pluck( $post_categories, 'name' );
		}

		// Get a list of tags and extract their names
		$post_tags = get_the_terms( $post->ID, 'post_tag' );
		if ( ! empty( $post_tags ) && ! is_wp_error( $post_tags ) ) {
				$this->h_entry['category'] = array_merge( $this->h_entry['category'], wp_list_pluck( $post_tags, 'name' ) );
		}

		if ( has_post_thumbnail( $post ) ) {
			$this->h_entry['featured'] = wp_get_attachment_url( get_post_thumbnail_id( $post ), 'full' );
		}
	}

	public static function get_post() {
		return get_post( $this->ID );
	}

	/**
	 * Is prefix in string.
	 *
	 * @param  string $source The source string.
	 * @param  string $prefix The prefix you wish to check for in source.
	 * @return boolean The result.
	 */
	public static function str_prefix( $source, $prefix ) {
		return strncmp( $source, $prefix, strlen( $prefix ) ) === 0;
	}

	/**
	 * Is String a URL.
	 *
	 * @param  string $url A string.
	 * @return boolean Whether string is a URL.
	 */
	public static function is_url( $url ) {
		return filter_var( $url, FILTER_VALIDATE_URL ) !== false;
	}

	/**
	 * Returns True if Array is Multidimensional.
	 *
	 * @param array $arr array.
	 *
	 * @return boolean result
	 */
	public static function is_multi_array( $arr ) {
		if ( count( $arr ) === count( $arr, COUNT_RECURSIVE ) ) {
			return false;
		} else {
			return true;
		}
	}

	public static function sanitize_content( $value ) {
		if ( ! is_string( $value ) ) {
			return $value;
		}
		$allowed = wp_kses_allowed_html( 'post' );
		return wp_kses( $value, $allowed );
	}

	public static function sanitize_text( $value ) {
		if ( is_array( $value ) ) {
			return array_map( array( $this, 'sanitize_text' ), $value );
		}
		if ( self::is_url( $value ) ) {
			$value = esc_url_raw( $value );
		} else {
			$value = esc_attr( $value );
		}
		return $value;
	}

	/**
	 * Sets an array with only the mf2 prefixed meta.
	 *
	 */
	private function get_mf2meta() {
		$meta = get_post_meta( $this->ID );
		if ( isset( $meta['response'] ) ) {
			$response = maybe_unserialize( $meta['response'] );
			// Retrieve from the old response array and store in new location.
			if ( ! empty( $response ) ) {
				$new = array();
				// Convert to new format and update.
				if ( ! empty( $response['title'] ) ) {
					$new['name'] = $response['title'];
				}
				if ( ! empty( $response['url'] ) ) {
					$new['url'] = $response['url'];
				}
				if ( ! empty( $response['content'] ) ) {
					$new['content'] = $response['content'];
				}
				if ( ! empty( $response['published'] ) ) {
					$new['published'] = $response['published'];
				}
				if ( ! empty( $response['author'] ) ) {
					$new['card']         = array();
					$new['card']['name'] = $response['author'];
					if ( ! empty( $response['icon'] ) ) {
						$new['card']['photo'] = $response['icon'];
					}
				}
				$new         = array_unique( $new );
				$new['card'] = array_unique( $new['card'] );
				if ( isset( $new ) ) {
					update_post_meta( $this->ID, 'mf2_cite', $new );
					delete_post_meta( $this->ID, 'response' );
					$meta['cite'] = $new;
				}
			}
		}
		foreach ( $meta as $key => $value ) {
			if ( ! self::str_prefix( $key, 'mf2_' ) ) {
				unset( $meta[ $key ] );
			} else {
				unset( $meta[ $key ] );
				$key = str_replace( 'mf2_', '', $key );
				// Do not save microput prefixed instructions
				if ( self::str_prefix( $key, 'mp-' ) ) {
					continue;
				}
				$value = array_map( 'maybe_unserialize', $value );
				if ( 1 === count( $value ) ) {
					$value = array_shift( $value );
				}
				$meta[ $key ] = $value;
			}
		}
		return array_filter( $meta );
	}

	/**
	 * Retrieve value
	 *
	 * @param  string $key The key to retrieve.
	 * @param  boolean $single Whether to return a a single value or array if there is only one value.
	 * @return boolean|string|array The result or false if does not exist.
	 */
	public function get() {
		$properties = array_merge( $this->h_entry, $this->mf2 );

		$container = array( 'items' => array( array( 'type' => array( 'h-entry' ), 'properties' => $properties ) ) );

		return array_filter( $container );
	}

	public function has_key( $key ) {
		$keys = array_merge( get_object_vars( $this ), $this->mf2 );
		return isset( $keys[ $key ] );
	}

	private function single_array( $value, $discard = false ) {
		if ( ! is_array( $value ) ) {
			return $value;
		}
		if ( 1 === count( $value ) ) {
			return array_shift( $value );
		}
		if ( $discard && wp_is_numeric_array( $value ) ) {
			return array_shift( $value );
		}
		if ( self::is_multi_array( $value ) ) {
			return array_map( array( $this, 'single_array' ), $value );
		}
		return $value;
	}

	public function delete( $key ) {
		delete_post_meta( $this->ID, 'mf2_' . $key );
	}

	public function mf2_to_jf2( $cite ) {
		if ( ! $cite ) {
			return $cite;
		}
		if ( ! is_array( $cite ) ) {
			return $cite;
		}
		if ( ! isset( $cite['properties'] ) ) {
			return $this->single_array( $cite );
		}
		$return = array();
		if ( isset( $cite['type'] ) ) {
			$return['type'] = array_shift( $cite['type'] );
		}
		foreach ( $cite['properties'] as $key => $value ) {
			if ( is_array( $value ) && 1 === count( $value ) && wp_is_numeric_array( $value ) ) {
				$value = array_shift( $value );
				$value = $this->mf2_to_jf2( $value );
			}
			$return[ $key ] = $value;
		}
		return array_filter( $return );
	}

	public function get_single( $value ) {
		if ( is_array( $value ) ) {
			return array_shift( $value );
		}
		return $value;
	}
}
