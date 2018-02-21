<?php

namespace Theme\Onepager;

/**
 * Class Onepager
 *
 * Overwrites URL for child pages to be anchor links. Replaces last segment of a URL with # prefix
 * and removes trailing slash e.g. /hotel/host/ > /hotel/#host.
 *
 * You will need this when you append child pages to the main page in the sense of a onepager or
 * when you list all pages in a single onepager.
 */
class Onepager {
	/**
	 * Theme Type
	 *
	 * Define how the page structure is used in the theme.
	 *
	 * simple      Non-hierarchical structure where all the pages are dislayed after each other to form a onepager.
	 * multi       Hierarchical theme structure where subpages are appended to the parent site as a onepager.
	 *
	 * @var string
	 */
	public $type = '';

	/**
	 * The post types to apply the Onepager functionality to.
	 *
	 * @var array
	 */
	public $post_types;

	/**
	 * Onepager constructor.
	 *
	 * @param string $type       Onepager type.
	 * @param array  $post_types The post types to apply the Onepager functionality to. Default `page`.
	 */
	public function __construct( $type = '', $post_types = [ 'page' ] ) {
		$this->type       = $type;
		$this->post_types = $post_types;
	}

	/**
	 * Init hooks.
	 */
	public function init() {
		add_filter( 'page_link', [ $this, 'filter_page_link' ], 10, 2 );
		add_filter( 'get_sample_permalink', [ $this, 'filter_get_sample_permalink' ], 10, 5 );
	}

	/**
	 * Filters the permalink.
	 *
	 * @param string $link    The permalink.
	 * @param int    $post_id The post ID.
	 *
	 * @return string
	 */
	public function filter_page_link( $link, $post_id ) {
		$post = get_post( $post_id );

		$bailout = apply_filters( 'theme/onepager/apply_link_filter', false, $link, $post_id );

		if ( $bailout ) {
			return $link;
		}

		if ( $this->is_onepager_section( $post )
			// Make sure we have no permastruct
			&& ! preg_match( '/\%\S+\%/i', $link ) >= 1
		) {
			$link = make_anchor_link( $link );
		}

		return $link;
	}

	/**
	 * Filters the editable permalink.
	 *
	 * @param string   $permalink The permalink.
	 * @param int      $post_id   The post ID.
	 * @param string   $title     The post title.
	 * @param string   $name      The post name.
	 * @param \WP_Post $post      The post object.
	 *
	 * @return string
	 */
	public function filter_get_sample_permalink( $permalink, $post_id, $title, $name, $post ) {
		$bailout = apply_filters( 'theme/onepager/apply_link_filter', false, $permalink, $post_id );

		if ( $bailout ) {
			return $permalink;
		}

		if ( $this->is_onepager_section( $post ) ) {
			$permalink[0] = str_replace( '#', '', $permalink[0] );
			$permalink[0] = make_anchor_link( $permalink[0] );
		}

		return $permalink;
	}

	/**
	 * Checks if post is a section in a onepager.
	 *
	 * @param \WP_Post $post The post object.
	 * @return bool
	 */
	public function is_onepager_section( $post ) {
		return in_array( $post->post_type, $this->post_types, true )
			&& ( ( 'multi' === $this->type && 0 !== (int) $post->post_parent )
			|| ( 'simple' === $this->type && 0 === (int) $post->post_parent )
		);
	}
}
