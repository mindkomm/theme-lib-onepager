<?php

use Theme\Onepager\Integrations\WooCommerce;

if ( function_exists( 'wc_get_page_id' ) ) {
	$woocommerce_integration = new WooCommerce();
	$woocommerce_integration->init();
}
