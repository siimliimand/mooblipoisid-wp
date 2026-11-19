<?php
/**
 * Early Polylang front-page URL normalization.
 *
 * Why this exists
 * ---------------
 * When Polylang cold-rebuilds its `pll_languages_list` transient, the rebuild
 * runs during `setup_theme` — BEFORE the theme's own normalization
 * (inc/polylang-front-page-urls.php, loaded from functions.php) is available.
 * The rebuild then persists stale `/ru/home/`-style `home_url` values into the
 * transient, and the first request afterwards can 301 `/ru/ -> /ru/home/ -> /`
 * until the theme's repair pass heals the cached data.
 *
 * mu-plugins load before regular plugins, so registering the
 * `pll_additional_language_data` normalization here guarantees it is in place
 * before Polylang's earliest rebuild, and the persisted transient is born
 * correct instead of being repaired after the fact.
 *
 * The canonical-URL computation mirrors the theme's
 * mooblipoisid_polylang_canonical_home_url() fallback path (direct `polylang`
 * option read — `PLL()->options` resolves `hide_default` to its string key
 * while the list is being built, so the option is the reliable source). It is
 * data-only: it never mutates options. Both this filter and the theme's own
 * filter (same priority 999) write the same value, so running both is
 * idempotent.
 *
 * Note: this assumes the directory-based links model (`/ru/`, `/en/` prefixes,
 * default language hidden). If the site ever switches to per-domain or
 * subdomain language URLs, revisit this computation together with the theme's
 * copy.
 *
 * @package mooblipoisid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Canonical home URL for a language slug (default language may hide its prefix).
 *
 * @param string $slug Language slug.
 * @return string Canonical home URL, empty string when the slug is unknown.
 */
function mooblipoisid_early_canonical_home_url( $slug ) {
	$slug = (string) $slug;

	if ( '' === $slug ) {
		return '';
	}

	$options      = (array) get_option( 'polylang', array() );
	$default_lang = isset( $options['default_lang'] ) ? (string) $options['default_lang'] : '';
	$hide_default = array_key_exists( 'hide_default', $options ) ? (bool) $options['hide_default'] : true;

	$hide = ( $default_lang === $slug ) && $hide_default;

	return $hide ? trailingslashit( home_url( '/' ) ) : trailingslashit( home_url( '/' ) . $slug );
}

/**
 * Normalizes the per-language `home_url` inside Polylang's language data.
 *
 * Prefers the `page_on_front` value carried in the language data; when it is
 * not set yet (it can be absent while the list is being built), falls back to
 * the translations of the site's static front page, exactly like the theme's
 * mooblipoisid_polylang_front_page_home_url().
 *
 * @param array $additional_data Additional language data being built.
 * @param array $language        Language data.
 * @return array Modified language data.
 */
function mooblipoisid_early_front_page_home_url( $additional_data, $language ) {
	if ( ! is_array( $language ) || empty( $language['slug'] ) ) {
		return $additional_data;
	}

	$front_page_id = 0;

	if ( ! empty( $language['page_on_front'] ) ) {
		$front_page_id = (int) $language['page_on_front'];
	} elseif ( function_exists( 'PLL' ) && isset( PLL()->model ) && is_object( PLL()->model ) ) {
		$site_front_page = (int) get_option( 'page_on_front' );
		if ( $site_front_page > 0 ) {
			$translations = PLL()->model->post->get_raw_translations( $site_front_page );
			$front_page_id = isset( $translations[ $language['slug'] ] ) ? (int) $translations[ $language['slug'] ] : 0;
		}
	}

	if ( $front_page_id <= 0 ) {
		return $additional_data;
	}

	$home_url = mooblipoisid_early_canonical_home_url( (string) $language['slug'] );
	if ( $home_url ) {
		$additional_data['home_url'] = $home_url;
	}

	return $additional_data;
}

// Registered at parse time: mu-plugins load before regular plugins, so the
// normalization is in place before Polylang's earliest language-list rebuild.
add_filter( 'pll_additional_language_data', 'mooblipoisid_early_front_page_home_url', 999, 2 );
