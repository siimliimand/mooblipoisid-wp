<?php
/**
 * Polylang front-page URL normalization for the multilingual content model.
 *
 * Why this exists
 * ---------------
 * Polylang resolves each language's front page as the translation of the static
 * front page and builds its public URL from the *page slug* of that translation
 * (`PLL_Links_Permalinks::front_page_url()` + `PLL_Static_Pages::page_link()`).
 *
 * That produces `/ru/home/`, `/en/home/` for the translated front pages instead
 * of the language roots `/ru/`, `/en/` the multilingual content model requires.
 *
 * Worse, if the translated front pages are given a custom slug to shorten the URL,
 * WordPress' `wp_unique_post_slug()` appends numeric suffixes because it treats
 * the slug as taken by the Estonian front page (core does not scope post names by
 * language). The slugs then become `home-2`, `home-3`, the generated URLs become
 * `/ru/home-2/`, and every request bounces between `/ru/` -> `/ru/home-2/` ->
 * `/ru/` in a permanent redirect loop.
 *
 * This file therefore:
 *
 * 1. Filters `get_page_uri` so every language's front page reports the *site home
 *    path* as its URI. Core and Polylang then generate exactly:
 *      - et (default): https://site/          (kept by `hide_default`)
 *      - ru:           https://site/ru/
 *      - en:           https://site/en/
 *    The slugs in the database (`home`, `home-2`, `home-3`) are never used to build
 *    a front-page URL anymore, so they no longer matter - and no longer trigger
 *    redirect loops.
 *
 * 2. Un-hooks `PLL_Static_Pages::page_link()` for the front pages, so the (stale)
 *    cached per-language `home_url` cannot override the canonical URL. Without
 *    this, a stale cache produced a 301 ping-pong between `/ru/` and `/ru/<slug>/`.
 *
 * 3. Keeps the in-memory and persisted Polylang language caches consistent with
 *    the canonical URLs, since `PLL_Static_Pages::page_link()` can rewrite
 *    `PLL_Language::$home_url` through `PLL_Language::to_array()` during a request.
 *
 * No core, plugin or third-party files are modified. Request routing is untouched:
 * Polylang still matches `/ru/` and `/en/` by their rewrite rules and serves the
 * corresponding front page.
 *
 * A dedicated `pll_additional_language_data` callback owns the front-page
 * `home_url` (it runs at priority 999 - after Polylang's own builder at 10 - and is
 * left in place), while `PLL_Links_Model::set_language_home_urls` is un-hooked.
 * This keeps the persisted `pll_languages_list` transient from ever storing the
 * `/ru/home/`-style URLs, including when the cache is rebuilt on a cold request.
 *
 * @package mooblipoisid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Tells whether Polylang (free) is available and booted with its API.
 *
 * @return bool
 */
function mooblipoisid_polylang_available() {
	return function_exists( 'PLL' ) && ( is_object( PLL() ) || PLL() instanceof PLL_Base ) && function_exists( 'pll_default_language' );
}

/**
 * Returns the front page ID set for the given language, 0 if none.
 *
 * @param PLL_Language|object|null $language Language object.
 * @return int
 */
function mooblipoisid_polylang_front_page_id( $language ) {
	if ( empty( $language ) || empty( $language->page_on_front ) ) {
		return 0;
	}

	return (int) $language->page_on_front;
}

/**
 * Normalizes `get_page_uri()` for the language front pages.
 *
 * The front page's URI is the home path of the site, e.g. `''`. Core then builds
 * `home + lang prefix + uri`, which yields `/`, `/ru/`, `/en/`.
 *
 * @param string   $uri  Page URI.
 * @param WP_Post  $page Page object.
 * @return string Filtered URI.
 */
function mooblipoisid_polylang_front_page_uri( $uri, $page ) {
	if ( ! mooblipoisid_polylang_available() || empty( $page->ID ) ) {
		return $uri;
	}

	$language = PLL()->model->post->get_language( (int) $page->ID );
	if ( mooblipoisid_polylang_front_page_id( $language ) === (int) $page->ID ) {
		// '' == the site home path (site lives at the domain root, as with DDEV).
		return '';
	}

	return $uri;
}

/**
 * Un-hooks `PLL_Static_Pages::page_link()` for the front pages.
 *
 * `PLL_Static_Pages::page_link()` returns the *cached* per-language `home_url`
 * (e.g. `/ru/home-2/` when the cache is stale) instead of the canonical
 * `/ru/`-style URL. Keeping it hooked is what caused the redirect ping-pong.
 * For non-front pages the filter does nothing, so removing it is safe.
 */
function mooblipoisid_polylang_detach_stale_front_page_filter() {
	if ( ! mooblipoisid_polylang_available() || ! isset( PLL()->links_model ) || ! is_object( PLL()->links_model ) ) {
		return;
	}

	// Owns the per-language `home_url` for languages with a front page.
	// Must run after Polylang's builder (priority 10) and must stay hooked,
	// otherwise Polylang persists `/ru/home/`-style URLs into the languages transient.
	add_filter( 'pll_additional_language_data', 'mooblipoisid_polylang_front_page_home_url', 999, 2 );

	// `PLL_Static_Pages::page_link()` (and its admin twin) return the *cached*
	// per-language `home_url` for the front pages instead of the canonical
	// language-root URL, which caused the `/ru/` <-> `/ru/home-2/` redirect
	// ping-pong. For non-front pages the filter is a no-op, so removing it is safe.
	$static_pages = isset( PLL()->static_pages ) ? PLL()->static_pages : null;
	if ( is_object( $static_pages ) && is_callable( array( $static_pages, 'page_link' ) ) ) {
		remove_filter( 'page_link', array( $static_pages, 'page_link' ), 20 );
	}
}

/**
 * Rewrites the per-language `home_url` of the front pages to the canonical URLs.
 *
 * Applied to both the in-memory language objects and the persisted
 * `pll_languages_list` transient, so cached and computed URLs agree.
 *
 * @param array $languages_data Language data arrays keyed like the transient.
 * @return array
 */
function mooblipoisid_polylang_normalize_language_home_urls( $languages_data ) {
	if ( ! is_array( $languages_data ) ) {
		return $languages_data;
	}

	$links_model = mooblipoisid_polylang_available() && isset( PLL()->links_model ) ? PLL()->links_model : null;

	foreach ( $languages_data as $key => $language ) {
		if ( ! is_array( $language ) || empty( $language['page_on_front'] ) ) {
			continue;
		}

		$home_url = mooblipoisid_polylang_canonical_home_url( $language['slug'] ?? '', $links_model );
		if ( $home_url ) {
			$languages_data[ $key ]['home_url'] = $home_url;
		}
	}

	return $languages_data;
}

/**
 * Provides the canonical `home_url` for a language whose front page is set.
 *
 * Runs at priority 999 - after Polylang's `PLL_Links_Model::set_language_home_urls`
 * (priority 10), whose `/ru/home/`-style value is overridden here - so the languages
 * transient, written right after this filter, already stores the language-root URL.
 *
 * @param array $additional_data Language data.
 * @param array $language        Language data being built.
 * @return array Modified language data.
 */
function mooblipoisid_polylang_front_page_home_url( $additional_data, $language ) {
	// Prefer Polylang's own resolution (front page of this language) and fall back
	// to the translations of the site's static front page. The fallback matters
	// while the languages list is being (re)built: the language objects do not
	// carry `page_on_front` yet at that point.
	$front_page_id = 0;

	if ( ! empty( $language['page_on_front'] ) ) {
		$front_page_id = (int) $language['page_on_front'];
	} elseif ( ! empty( $language['slug'] ) && mooblipoisid_polylang_available() ) {
		$site_front_page = (int) get_option( 'page_on_front' );
		if ( $site_front_page > 0 ) {
			$translations = PLL()->model->post->get_raw_translations( $site_front_page );
			$front_page_id = isset( $translations[ $language['slug'] ] ) ? (int) $translations[ $language['slug'] ] : 0;
		}
	}

	if ( $front_page_id <= 0 ) {
		return $additional_data;
	}

	$home_url = mooblipoisid_polylang_canonical_home_url( $language['slug'] ?? '' );
	if ( $home_url ) {
		$additional_data['home_url'] = $home_url;
	}

	return $additional_data;
}

/**
 * Builds the canonical home URL of a language (front page at the language root).
 *
 * @param string                $slug       Language slug.
 * @param PLL_Links_Model|null  $links_model Polylang links model.
 * @return string Canonical URL, empty string if it cannot be built.
 */
function mooblipoisid_polylang_canonical_home_url( $slug, $links_model = null ) {
	if ( $links_model && is_callable( array( $links_model, 'home_url' ) ) ) {
		$url = $links_model->home_url( $slug );
		return is_string( $url ) ? $url : '';
	}

	if ( ! $slug ) {
		return '';
	}

	$polylang_options = (array) get_option( 'polylang', array() );
	$default          = isset( $polylang_options['default_lang'] ) ? (string) $polylang_options['default_lang'] : '';

	// Mirror Polylang's own rule for the default language (PLL_Links_Directory::home_url()):
	// when "Hide URL language information for default language" is enabled, the default
	// language lives at the site root without a language prefix. The `polylang` option is
	// read directly because `pll_default_language()` returns an empty string and
	// `PLL()->options` resolves the boolean `hide_default` to its string key while the
	// language list is being built, which would wrongly produce `/et/`.
	$hide_default = array_key_exists( 'hide_default', $polylang_options ) ? (bool) $polylang_options['hide_default'] : true;

	$hide = ( $default === $slug ) && $hide_default;

	return $hide ? trailingslashit( home_url( '/' ) ) : trailingslashit( home_url( '/' ) . $slug );
}

/**
 * Sanitizes the stored `pll_languages_list` transient to canonical front-page URLs.
 *
 * @return void
 */
function mooblipoisid_polylang_refresh_persisted_language_cache() {
	if ( ! mooblipoisid_polylang_available() ) {
		return;
	}

	$stored = get_transient( 'pll_languages_list' );
	if ( ! is_array( $stored ) ) {
		return;
	}

	$normalized = mooblipoisid_polylang_normalize_language_home_urls( $stored );

	if ( $normalized !== $stored ) {
		set_transient( 'pll_languages_list', $normalized );
	}
}

// Canonical front-page URIs in every generated URL (permalinks, redirects, hreflang).
add_filter( 'get_page_uri', 'mooblipoisid_polylang_front_page_uri', 20, 2 );

// Canonical front-page `home_url` per language. Priority 999 = runs after
// Polylang's own builder (priority 10) and owns the final value.
add_filter( 'pll_additional_language_data', 'mooblipoisid_polylang_front_page_home_url', 999, 2 );

// Detach the stale-cache-sensitive `page_link` filter once Polylang is fully booted.
add_action( 'pll_language_defined', 'mooblipoisid_polylang_detach_stale_front_page_filter', 0 );
add_action( 'wp_loaded', 'mooblipoisid_polylang_detach_stale_front_page_filter', 20 );

// Keep the persisted language cache consistent with the canonical URLs.
add_action( 'pll_language_defined', 'mooblipoisid_polylang_refresh_persisted_language_cache', 20 );