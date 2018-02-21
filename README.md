# Onepager

Onepager functionality for WordPress themes.

Overwrites URL for child pages to be anchor links. Replaces last segment of a URL with # prefix and removes trailing slash. For example `/hotel/host/` > `/hotel/#host`.

You can use this when you append child pages to the parent page in the sense of a onepager or when you want to list all pages in a single onepager.

## Installation

You can install the package via Composer:

```bash
composer require mindkomm/theme-lib-onepager
```

## Usage

**functions.php**

```php
$onepager = new Theme\Onepager\Onepager();
$onepager->init();
```

The Onepager constructor takes two parameters:

**$type**

Defines the type of the onepager that you want to use. Default `multi`.

- `multi` – Hierarchical theme structure where subpages are appended to the parent site.
- `simple` – Non-hierarchical structure where all pages are dislayed after each other as a onepager.

**$post_types**

The post types to apply the Onepager functionality to. Default `[ 'page' ]`.

## Ignoring pages

In certain setups, you might have subpages that don’t belong to a onepager. For example, in WooCommerce, the checkout page is a subpage of the shop page. Filtering the link for the checkout page would break the functionality of the checkout page.

The `theme/onepager/apply_link_filter` filter allows you to disable filtering for certain links. Here’s an example that’s already included in the library:

```php
/**
 * Do not apply Onepager link filtering for WooCommerce pages.
 *
 * @param bool   $bailout Whether to bail out early.
 * @param string $link    The link to filter.
 * @param string $post_id The ID of the post to filter the link for.
 *
 * @return bool
 */
add_filter( 'theme/onepager/apply_link_filter', function( $bailout, $link, $post_id ) {
    if ( wc_get_page_id( 'shop' ) === $post_id || wc_get_page_id( 'checkout' ) === $post_id ) {
        return true;
    }
    
    return $bailout;
}, 10, 3 );
```

## Sitemaps

When you have Yoast SEO installed, this library will make sure that child pages are ignored when the page sitemap is generated. This will not consider the filter to ignore filtering of certain pages. It will only include pages with `post_parent` set to `0`. At the current state, this works well for us. In the future, we might add more control for this.

## Caching

Usually, caching plugins flush the cache for a page when a page is edited and saved. However, when a child page is saved that belongs to a onepager, the parent page defines the cache. This library will handle purging of parent pages for the following plugins:

- WP Rocket
- WP Fastest Cache
- W3 Total Cache

## Support

This is a library that we use at MIND to develop WordPress themes. You’re free to use it, but currently, we don’t provide any support.
