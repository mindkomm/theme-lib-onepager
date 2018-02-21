<?php

namespace Theme\Onepager\Integrations;

/**
 * Class Yoast
 */
class Yoast {
	/**
	 * Init hooks.
	 */
	public function init() {
		if ( $this->is_yoast_active() ) {
			add_filter( 'wpseo_posts_where', [ $this, 'exclude_child_pages' ], 10, 2 );
		}
	}

	/**
	 * Excludes child pages from Yoast page sitemap.
	 *
	 * @param string $where Where clause.
	 * @param string $post_type Post type.
	 *
	 * @return string
	 */
	public function exclude_child_pages( $where, $post_type ) {
		if ( 'page' !== $post_type ) {
			return $where;
		}

		global $wpdb;

		return "AND {$wpdb->posts}.post_parent = 0";
	}

	/**
	 * Checks whether Yoast SEO plugin is active.
	 *
	 * @return bool
	 */
	public function is_yoast_active() {
		return in_array(
			'wordpress-seo/wp-seo.php',
			apply_filters( 'active_plugins', get_option( 'active_plugins' ) ),
			true
		);
	}
}
