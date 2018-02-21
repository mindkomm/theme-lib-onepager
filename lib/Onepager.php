<?php

namespace Theme\Onepager;

use Theme\Onepager\Integrations\Caching;
use Theme\Onepager\Integrations\WooCommerce;
use Theme\Onepager\Integrations\Yoast;

/**
 * Class Onepager
 *
 * Overwrites URL for child pages to be anchor links. Replaces last segment of a URL with # prefix
 * and removes trailing slash. For example: /hotel/host/ > /hotel/#host.
 *
 * You can use this when you append child pages to the parent page in the sense of a onepager or
 * when you want to list all pages in a single onepager.
 */
class Onepager {
	/**
	 * Theme Type
	 *
	 * Define how the page structure is used in the theme.
	 *
	 * simple Non-hierarchical structure where all pages are dislayed after each other as a onepager.
	 * multi  Hierarchical theme structure where subpages are appended to the parent site.
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
	 * @param string $type       Onepager type. Default 'multi'.
	 * @param array  $post_types The post types to apply the Onepager functionality to.
	 *                           Default `[ 'page' ]`.
	 */
	public function __construct( $type = 'multi', $post_types = [ 'page' ] ) {
		$this->type       = $type;
		$this->post_types = $post_types;
	}

	/**
	 * Init hooks.
	 */
	public function init() {
		add_filter( 'page_link', [ $this, 'filter_page_link' ], 10, 2 );
		add_filter( 'get_sample_permalink', [ $this, 'filter_get_sample_permalink' ], 10, 5 );

		// Init third party integrations
		( new Caching() )->init();
		( new WooCommerce() )->init();
		( new Yoast() )->init();
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

		/**
		 * Filters whether the page link should be filtered.
		 *
		 * In certain setups, you might have subpages where you don’t want to filter the links for.
		 * This filter allows you to disable filtering for certain links.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $bailout   Whether to skip filtering the link. Passing a truthy value will
		 *                        bail out of the filter and return the default link. Default `false`.
		 * @param string $link    The link to filter.
		 * @param int    $post_id The post ID the link belongs to.
		 */
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
		/**
		 * Filters whether the page link should be filtered.
		 *
		 * In certain setups, you might have subpages where you don’t want to filter the links for.
		 * This filter allows you to disable filtering for certain links.
		 *
		 * @since 1.0.0
		 *
		 * @param bool   $bailout   Whether to skip filtering the link. Passing a truthy value will
		 *                          bail out of the filter and return the default link. Default `false`.
		 * @param string $permalink The link to filter.
		 * @param int    $post_id   The post ID the link belongs to.
		 */
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
