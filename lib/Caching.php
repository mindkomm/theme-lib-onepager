<?php

namespace Theme\Onepager;

use WP_Post;

/**
 * Class Caching
 *
 * Handles flushing the cache for a set of popular plugins when child page is saved.
 *
 * The following plugins are supported:
 * - WP Rocket
 * - WP Fastest Cache
 * - W3 Total Cache
 *
 * Sometimes child pages are appended to their parent pages. They have their own permalink, but are never displayed
 * directly. Because they are displayed only when the parent page is accessed, their content is also saved inside the
 * parent’s cache.
 *
 * When the child page is saved, the cache for the parent persists. We need to tell the caching plugins that they
 * should always flush caches when child pages are saved or even better: flush the cache of the parent page when a
 * child page is saved.
 *
 * @package Theme\Onepager
 */
class Caching {
	/**
	 * Init hooks.
	 */
	public function init() {
		add_action( 'transition_post_status', [ $this, 'transition_post_status' ], 10, 3 );
	}

	/**
	 * Transition a post from one status to another.
	 *
	 * @param string  $new_status New post status.
	 * @param string  $old_status Old post status.
	 * @param WP_Post $post       Post object.
	 */
	public function transition_post_status( $new_status, $old_status, $post ) {
		// Bailout if not page or top level page
		if ( 'page' !== $post->post_type || 0 === (int) $post->post_parent ) {
			return;
		}

		global $wp_fastest_cache;

		if ( function_exists( 'rocket_clean_post' ) ) {
			// WP Rocket
			rocket_clean_post( $post->post_parent );

		} elseif ( method_exists( $wp_fastest_cache, 'singleDeleteCache' ) ) {
			// WP Fastest Cache
			$wp_fastest_cache->singleDeleteCache( false, $post->post_parent );

		} elseif ( function_exists( 'w3tc_pgcache_flush_post' ) ) {
			// W3 Total Cache
			w3tc_pgcache_flush_post( $post->post_parent );
		}
	}
}
