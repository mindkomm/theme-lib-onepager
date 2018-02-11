<?php

namespace Theme\Onepager\Integrations;

/**
 * Class WooCommerce
 *
 * @package Theme\Onepager\Integrations
 */
class WooCommerce {
	/**
	 * Init hooks.
	 */
	public function init() {
		add_filter( 'theme/onepager/apply_link_filter', [ $this, 'filter_apply_link' ], 10, 3 );
	}

	/**
	 * Do not apply Onepager link filtering for WooCommerce pages.
	 *
	 * @param bool   $bailout Whether to bail out early.
	 * @param string $link    The link to filter.
	 * @param string $post_id The ID of the post to filter the link for.
	 *
	 * @return bool
	 */
	public function filter_apply_link( $bailout, $link, $post_id ) {
		if ( wc_get_page_id( 'shop' ) === $post_id || wc_get_page_id( 'checkout' ) === $post_id ) {
			return true;
		}

		return false;
	}
}
