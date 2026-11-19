<?php
/**
 * Plugin Name: Mööblipoisid – SEO head, structured data, robots & llms.txt
 * Description: Front-end SEO plumbing that lives in mu-plugins so it always loads: document title overrides, meta descriptions, Open Graph/Twitter tags, canonical URLs, x-default hreflang, head-noise removal, one JSON-LD @graph (Organization / WebSite / WebPage / BreadcrumbList / FAQPage / OfferCatalog), an /llms.txt route, a trimmed sitemap (no users provider) and a one-time rewrite flush.
 * Version:     1.0.0
 *
 * Notes:
 * - mu-plugins load before plugins and the theme. Every hook callback guards
 *   cross-file helpers (mooblipoisid_get_homepage_settings, mooblipoisid_image_src,
 *   mooblipoisid_get_faqs, mooblipoisid_get_company_info, mooblipoisid_section_languages,
 *   mooblipoisid_polylang_canonical_home_url, pll_*) with function_exists() — the other
 *   files may not be loaded (or finished) when these callbacks run.
 * - Nothing runs at file-parse time except add_action/add_filter registrations.
 * - Data contract (theme content model): page meta keys _mooblipoisid_seo_title,
 *   _mooblipoisid_seo_description, _mooblipoisid_seo_social_image; homepage settings
 *   keys seo_meta_title, seo_meta_description, seo_social_image (plus hero_sub /
 *   hero_image which already exist).
 *
 * @package mooblipoisid
 */

defined( 'ABSPATH' ) || exit;

/* ==========================================================================
 * 1. Language & URL context
 * ========================================================================== */

/**
 * The current language slug (`et`, `ru`, `en`); `et` when Polylang is unavailable.
 *
 * @return string
 */
function mooblipoisid_seo_current_language() {
	if ( function_exists( 'pll_current_language' ) ) {
		$slug = pll_current_language( 'slug' );
		if ( is_string( $slug ) && '' !== $slug ) {
			return $slug;
		}
	}

	return 'et';
}

/**
 * Every site language slug, falling back to the known trilingual set.
 *
 * @return string[]
 */
function mooblipoisid_seo_languages() {
	if ( function_exists( 'pll_languages_list' ) ) {
		$slugs = pll_languages_list( array( 'fields' => 'slug' ) );
		if ( is_array( $slugs ) && ! empty( $slugs ) ) {
			return array_values( array_map( 'strval', $slugs ) );
		}
	}

	return array( 'et', 'ru', 'en' );
}

/**
 * Human-readable language label for llms.txt.
 *
 * @param string $lang Language slug.
 * @return string
 */
function mooblipoisid_seo_language_label( $lang ) {
	$labels = array(
		'et' => 'Estonian',
		'ru' => 'Russian',
		'en' => 'English',
	);

	return isset( $labels[ $lang ] ) ? $labels[ $lang ] : ucfirst( (string) $lang );
}

/**
 * Open Graph locale for a language slug (et→et_EE, ru→ru_RU, en→en_US).
 *
 * @param string|null $lang Language slug (current language when null).
 * @return string
 */
function mooblipoisid_seo_locale( $lang = null ) {
	$lang = null === $lang ? mooblipoisid_seo_current_language() : (string) $lang;
	$map   = array( 'et' => 'et_EE', 'ru' => 'ru_RU', 'en' => 'en_US' );

	return isset( $map[ $lang ] ) ? $map[ $lang ] : 'et_EE';
}

/**
 * HTML/JSON-LD inLanguage value for a language slug (et / ru-RU / en-US).
 *
 * @param string|null $lang Language slug (current language when null).
 * @return string
 */
function mooblipoisid_seo_html_language( $lang = null ) {
	$lang = null === $lang ? mooblipoisid_seo_current_language() : (string) $lang;
	$map   = array( 'et' => 'et', 'ru' => 'ru-RU', 'en' => 'en-US' );

	return isset( $map[ $lang ] ) ? $map[ $lang ] : 'et';
}

/**
 * The canonical front-page URL of a given language (language root, never /slug/).
 *
 * @param string $lang Language slug.
 * @return string Empty string when it cannot be resolved.
 */
function mooblipoisid_seo_language_home_url( $lang ) {
	$lang = (string) $lang;

	// Preferred: the theme helper's links-model branch. `home_url()` is filtered
	// by Polylang to the *current* language on front-end requests, so the
	// option-based branch would compound the prefix (`/ru/ru/`); the links model
	// resolves any language independently of the active one.
	if ( function_exists( 'PLL' ) && function_exists( 'mooblipoisid_polylang_canonical_home_url' ) && is_object( PLL() ) && isset( PLL()->links_model ) && is_callable( array( PLL()->links_model, 'home_url' ) ) ) {
		$url = mooblipoisid_polylang_canonical_home_url( $lang, PLL()->links_model );
		if ( is_string( $url ) && '' !== $url ) {
			return $url;
		}
	}

	if ( function_exists( 'pll_home_url' ) && '' !== $lang ) {
		$url = pll_home_url( $lang );
		if ( is_string( $url ) && '' !== $url ) {
			return $url;
		}
	}

	if ( function_exists( 'mooblipoisid_polylang_canonical_home_url' ) ) {
		$url = mooblipoisid_polylang_canonical_home_url( $lang );
		if ( is_string( $url ) && '' !== $url ) {
			return $url;
		}
	}

	return home_url( '/' );
}

/**
 * The current language's front-page URL.
 *
 * @return string
 */
function mooblipoisid_seo_home_url() {
	return mooblipoisid_seo_language_home_url( mooblipoisid_seo_current_language() );
}

/**
 * The default-language (Estonian) root home URL — used for x-default hreflang
 * and as the fallback everywhere.
 *
 * @return string
 */
function mooblipoisid_seo_default_home_url() {
	$default = 'et';
	if ( function_exists( 'pll_default_language' ) ) {
		$slug = pll_default_language( 'slug' );
		if ( is_string( $slug ) && '' !== $slug ) {
			$default = $slug;
		}
	}

	return mooblipoisid_seo_language_home_url( $default );
}

/**
 * The canonical URL of the current request: language home on the front page,
 * permalink on singular pages, empty anywhere else (archives, 404, search).
 *
 * @return string
 */
function mooblipoisid_seo_canonical_url() {
	if ( is_front_page() ) {
		return mooblipoisid_seo_home_url();
	}

	if ( is_singular() && is_main_query() ) {
		$link = get_permalink();
		if ( is_string( $link ) && '' !== $link ) {
			return $link;
		}
	}

	return '';
}

/* ==========================================================================
 * 2. Title & meta description resolution
 * ========================================================================== */

/**
 * Homepage Settings, guarded (the theme file may add fields concurrently).
 *
 * @return array
 */
function mooblipoisid_seo_front_settings() {
	if ( ! function_exists( 'mooblipoisid_get_homepage_settings' ) ) {
		return array();
	}

	$settings = mooblipoisid_get_homepage_settings();

	return is_array( $settings ) ? $settings : array();
}

/**
 * A single Homepage Settings string value.
 *
 * @param string $key Settings key.
 * @return string Trimmed value, empty when unset.
 */
function mooblipoisid_seo_setting( $key ) {
	$settings = mooblipoisid_seo_front_settings();

	return isset( $settings[ $key ] ) ? trim( (string) $settings[ $key ] ) : '';
}

/**
 * The per-page SEO title override for the queried singular object, if set.
 *
 * @return string Empty string when no override is stored.
 */
function mooblipoisid_seo_singular_title_override() {
	$post = is_singular() ? get_queried_object() : null;
	if ( ! $post instanceof WP_Post ) {
		return '';
	}

	return trim( (string) get_post_meta( $post->ID, '_mooblipoisid_seo_title', true ) );
}

/**
 * The resolved document title for og:title / JSON-LD:
 * - front page: SEO override → blogname;
 * - singular: SEO override → "Page – Site" (the core/Kadence pattern).
 *
 * @return string
 */
function mooblipoisid_seo_document_title() {
	if ( is_front_page() ) {
		$override = mooblipoisid_seo_setting( 'seo_meta_title' );

		return '' !== $override ? $override : (string) get_bloginfo( 'name' );
	}

	if ( is_singular() ) {
		$override = mooblipoisid_seo_singular_title_override();
		if ( '' !== $override ) {
			return $override;
		}

		$post       = get_queried_object();
		$page_title = $post instanceof WP_Post ? trim( (string) get_the_title( $post ) ) : '';
		$site_name  = (string) get_bloginfo( 'name' );

		if ( '' !== $page_title && '' !== $site_name ) {
			return $page_title . ' – ' . $site_name;
		}

		return '' !== $page_title ? $page_title : $site_name;
	}

	return (string) get_bloginfo( 'name' );
}

/**
 * pre_get_document_title filter:
 * - front page (any language): SEO override → blogname;
 * - singular: post meta override when set, otherwise the unfiltered default;
 * - everything else untouched.
 *
 * @param string $title Title from earlier filters ('' until core builds it).
 * @return string
 */
function mooblipoisid_seo_document_title_filter( $title ) {
	if ( is_front_page() ) {
		$override = mooblipoisid_seo_setting( 'seo_meta_title' );

		return '' !== $override ? $override : (string) get_bloginfo( 'name' );
	}

	if ( is_singular() && is_main_query() ) {
		$override = mooblipoisid_seo_singular_title_override();

		return '' !== $override ? $override : $title;
	}

	return $title;
}

/**
 * Multibyte-safe string length (byte length as a last-resort fallback).
 *
 * @param string $text Text.
 * @return int
 */
function mooblipoisid_seo_mb_strlen( $text ) {
	return function_exists( 'mb_strlen' ) ? (int) mb_strlen( (string) $text, 'UTF-8' ) : strlen( (string) $text );
}

/**
 * Multibyte-safe substring.
 *
 * @param string   $text  Text.
 * @param int      $start Start offset.
 * @param int|null $length Length (null = rest of the string).
 * @return string
 */
function mooblipoisid_seo_mb_substr( $text, $start, $length = null ) {
	if ( ! function_exists( 'mb_substr' ) ) {
		return null === $length ? substr( (string) $text, (int) $start ) : substr( (string) $text, (int) $start, (int) $length );
	}

	return null === $length ? mb_substr( (string) $text, (int) $start, null, 'UTF-8' ) : mb_substr( (string) $text, (int) $start, (int) $length, 'UTF-8' );
}

/**
 * Plain-text excerpt: strip shortcodes/tags/entities, collapse whitespace and
 * trim to ~$limit characters on a sentence boundary (word boundary + ellipsis
 * when no sentence end fits).
 *
 * @param string $text  Raw text (post content, excerpt, …).
 * @param int    $limit Character budget.
 * @return string
 */
function mooblipoisid_seo_excerpt( $text, $limit = 155 ) {
	$text = (string) $text;
	if ( '' === $text ) {
		return '';
	}

	$text = wp_strip_all_tags( strip_shortcodes( $text ) );
	$text = wp_specialchars_decode( $text, ENT_QUOTES );
	$text = trim( (string) preg_replace( '/\s+/u', ' ', $text ) );

	if ( '' === $text || mooblipoisid_seo_mb_strlen( $text ) <= $limit ) {
		return $text;
	}

	$cut = mooblipoisid_seo_mb_substr( $text, 0, $limit );

	// Prefer the last sentence terminator inside the budget.
	if ( preg_match( '/^.*[.!?…]/us', $cut, $matches ) && mooblipoisid_seo_mb_strlen( $matches[0] ) >= (int) floor( $limit / 2 ) ) {
		return trim( $matches[0] );
	}

	// Otherwise break at the last word boundary and close with an ellipsis.
	$space = function_exists( 'mb_strrpos' ) ? mb_strrpos( $cut, ' ' ) : strrpos( $cut, ' ' );
	$part  = false !== $space ? mooblipoisid_seo_mb_substr( $cut, 0, $space ) : $cut;
	$part  = rtrim( $part, " \t,;:.!?" );

	return '' !== $part ? $part . '…' : $cut;
}

/**
 * The resolved meta description (same value reused for og:description and
 * the WebPage node):
 * - front page: SEO override → hero_sub from the Homepage Settings;
 * - singular: post meta override → content excerpt (~155 chars);
 * - anything else / everything empty: empty string (tag skipped).
 *
 * @return string
 */
function mooblipoisid_seo_meta_description() {
	if ( is_front_page() ) {
		$override = mooblipoisid_seo_setting( 'seo_meta_description' );
		if ( '' !== $override ) {
			return $override;
		}

		return mooblipoisid_seo_setting( 'hero_sub' );
	}

	if ( is_singular() && is_main_query() ) {
		$post = get_queried_object();

		if ( $post instanceof WP_Post ) {
			$override = trim( (string) get_post_meta( $post->ID, '_mooblipoisid_seo_description', true ) );
			if ( '' !== $override ) {
				return $override;
			}

			return mooblipoisid_seo_excerpt( get_the_excerpt( $post ) );
		}
	}

	return '';
}

/* ==========================================================================
 * 3. Social image resolution
 * ========================================================================== */

/**
 * The custom logo URL (Appearance → Customize → Site Identity), large size.
 *
 * @return string Empty string when no logo is set.
 */
function mooblipoisid_seo_site_logo_url() {
	$logo_id = (int) get_theme_mod( 'custom_logo' );
	if ( ! $logo_id ) {
		return '';
	}

	$url = wp_get_attachment_image_url( $logo_id, 'large' );
	if ( ! is_string( $url ) || '' === $url ) {
		$url = wp_get_attachment_image_url( $logo_id, 'full' );
	}

	return is_string( $url ) ? trim( $url ) : '';
}

/**
 * The site icon URL at 512px when available.
 *
 * @return string Empty string when no site icon is set.
 */
function mooblipoisid_seo_site_icon_url() {
	if ( function_exists( 'has_site_icon' ) && ! has_site_icon() ) {
		return '';
	}

	$url = get_site_icon_url( 512 );

	return is_string( $url ) ? trim( $url ) : '';
}

/**
 * Resolve a stored image reference (URL, site-relative path or attachment ID)
 * to a usable URL, via the theme helper when available.
 *
 * @param mixed $value Stored image value.
 * @return string Empty string when it cannot be resolved.
 */
function mooblipoisid_seo_resolve_image( $value ) {
	$value = is_scalar( $value ) ? trim( (string) $value ) : '';
	if ( '' === $value ) {
		return '';
	}

	if ( ctype_digit( $value ) ) {
		$url = wp_get_attachment_image_url( (int) $value, 'large' );
		if ( is_string( $url ) && '' !== $url ) {
			return $url;
		}

		return '';
	}

	$url = function_exists( 'mooblipoisid_image_src' ) ? mooblipoisid_image_src( $value, 'large' ) : $value;

	return is_string( $url ) && '' !== $url ? $url : '';
}

/**
 * The Open Graph image URL. Resolution chain:
 * page meta _mooblipoisid_seo_social_image → Homepage Settings seo_social_image
 * → hero_image → custom logo → site icon.
 *
 * @return string Empty string when nothing resolves.
 */
function mooblipoisid_seo_social_image_url() {
	$candidates = array();

	$post = is_singular() ? get_queried_object() : null;
	if ( $post instanceof WP_Post ) {
		$candidates[] = get_post_meta( $post->ID, '_mooblipoisid_seo_social_image', true );
	}

	$candidates[] = mooblipoisid_seo_front_settings()['seo_social_image'] ?? '';
	$candidates[] = mooblipoisid_seo_front_settings()['hero_image'] ?? '';
	$candidates[] = mooblipoisid_seo_site_logo_url();
	$candidates[] = mooblipoisid_seo_site_icon_url();

	foreach ( $candidates as $candidate ) {
		$url = mooblipoisid_seo_resolve_image( $candidate );
		if ( '' !== $url ) {
			return $url;
		}
	}

	return '';
}

/**
 * Map a media URL on this site to a local file path (with a containment check).
 *
 * @param string $url Image URL.
 * @return string Absolute path, empty when the URL is off-site or the file is missing.
 */
function mooblipoisid_seo_url_to_path( $url ) {
	$path = (string) wp_parse_url( (string) $url, PHP_URL_PATH );
	if ( '' === $path ) {
		return '';
	}

	$file = wp_normalize_path( ABSPATH . ltrim( $path, '/' ) );
	$root = wp_normalize_path( realpath( ABSPATH ) );
	$real = realpath( $file );

	if ( $real && $root && 0 === strpos( wp_normalize_path( $real ), $root ) && is_file( $real ) ) {
		return $real;
	}

	return '';
}

/**
 * Width/height for an image URL: attachment metadata first, getimagesize on
 * the local file as a fallback, null when both fail (tags are then omitted).
 *
 * @param string $url Image URL.
 * @return array{0:int,1:int}|null
 */
function mooblipoisid_seo_image_dimensions( $url ) {
	$url     = (string) $url;
	$width   = 0;
	$height  = 0;

	if ( function_exists( 'attachment_url_to_postid' ) ) {
		$attachment_id = (int) attachment_url_to_postid( $url );
		if ( $attachment_id ) {
			$meta = wp_get_attachment_metadata( $attachment_id );
			if ( is_array( $meta ) ) {
				$width  = isset( $meta['width'] ) ? (int) $meta['width'] : 0;
				$height = isset( $meta['height'] ) ? (int) $meta['height'] : 0;

				// The resolved URL may be a sized crop: prefer that size's dimensions.
				if ( ! empty( $meta['sizes'] ) && is_array( $meta['sizes'] ) ) {
					foreach ( $meta['sizes'] as $size ) {
						if ( ! empty( $size['file'] ) && false !== strpos( $url, (string) $size['file'] ) && ! empty( $size['width'] ) && ! empty( $size['height'] ) ) {
							$width  = (int) $size['width'];
							$height = (int) $size['height'];
							break;
						}
					}
				}
			}
		}
	}

	if ( ! $width || ! $height ) {
		$path = mooblipoisid_seo_url_to_path( $url );
		if ( '' !== $path && function_exists( 'getimagesize' ) ) {
			$info = getimagesize( $path ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.file_system_operations_getimagesize -- read-only local dimension probe.
			if ( is_array( $info ) && ! empty( $info[0] ) && ! empty( $info[1] ) ) {
				$width  = (int) $info[0];
				$height = (int) $info[1];
			}
		}
	}

	return ( $width && $height ) ? array( $width, $height ) : null;
}

/* ==========================================================================
 * 4. Data bridges (company info, FAQs, product ranges)
 * ========================================================================== */

/**
 * Company details from the theme's company-info getter (when present), with
 * the shapes the JSON-LD builder needs. Omitted fields stay empty — nothing
 * is ever invented here.
 *
 * @return array{email:string,telephone:string,reg_code:string,same_as:string[]}
 */
function mooblipoisid_seo_company_info() {
	$info = array(
		'email'     => '',
		'telephone' => '',
		'reg_code'  => '',
		'same_as'   => array(),
	);

	if ( ! function_exists( 'mooblipoisid_get_company_info' ) ) {
		return $info;
	}

	$data = mooblipoisid_get_company_info();
	if ( ! is_array( $data ) ) {
		return $info;
	}

	$email = isset( $data['email'] ) ? trim( (string) $data['email'] ) : '';
	if ( '' === $email && isset( $data['email_alt'] ) ) {
		$email = trim( (string) $data['email_alt'] );
	}

	$phone = isset( $data['phone'] ) ? trim( (string) $data['phone'] ) : '';
	if ( '' === $phone && isset( $data['phone_alt'] ) ) {
		$phone = trim( (string) $data['phone_alt'] );
	}

	$info['email']     = $email;
	$info['telephone'] = $phone;
	$info['reg_code']  = isset( $data['reg_code'] ) ? trim( (string) $data['reg_code'] ) : '';

	foreach ( array( 'facebook', 'instagram', 'linkedin' ) as $social_key ) {
		$url = isset( $data[ $social_key ] ) ? trim( (string) $data[ $social_key ] ) : '';
		if ( '' !== $url && '' !== esc_url_raw( $url ) ) {
			$info['same_as'][] = esc_url_raw( $url );
		}
	}

	return $info;
}

/**
 * FAQs from the theme getter (guarded): array of ['id','question','answer'].
 * Answers arrive as raw post content; they are sanitized only at output time.
 *
 * @return array[]
 */
function mooblipoisid_seo_faqs() {
	if ( ! function_exists( 'mooblipoisid_get_faqs' ) ) {
		return array();
	}

	$faqs = mooblipoisid_get_faqs();

	return is_array( $faqs ) ? $faqs : array();
}

/**
 * The published product ranges in menu order — the exact data source the
 * homepage ranges template uses (CPT `range`, `lang` fallback via the theme
 * helper, lightweight query flags mirroring template-parts/home/ranges.php).
 *
 * @return WP_Post[]
 */
function mooblipoisid_seo_ranges() {
	$lang = function_exists( 'mooblipoisid_section_languages' )
		? mooblipoisid_section_languages( 'range' )
		: mooblipoisid_seo_current_language();

	$query = new WP_Query(
		array(
			'post_type'              => 'range',
			'post_status'            => 'publish',
			'posts_per_page'         => -1,
			'orderby'                => 'menu_order',
			'order'                  => 'ASC',
			'lang'                   => $lang,
			'ignore_sticky_posts'    => true,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
		)
	);

	$posts = $query->posts;
	wp_reset_postdata();

	return is_array( $posts ) ? $posts : array();
}

/**
 * The schema.org OfferCatalog for the homepage "Full Commercial Range"
 * section: the ordered range list split into the same two columns
 * (hospitality / retail) the template renders.
 *
 * @return array Empty array when no ranges resolve (catalog skipped entirely).
 */
function mooblipoisid_seo_range_catalog() {
	$ranges = mooblipoisid_seo_ranges();
	if ( empty( $ranges ) ) {
		return array();
	}

	$split     = (int) floor( count( $ranges ) / 2 );
	$columns   = array(
		array(
			'name'    => mooblipoisid_seo_setting( 'ranges_col_hospitality' ),
			'entries' => array_slice( $ranges, 0, $split ),
		),
		array(
			'name'    => mooblipoisid_seo_setting( 'ranges_col_retail' ),
			'entries' => array_slice( $ranges, $split ),
		),
	);

	$groups = array();
	foreach ( $columns as $column ) {
		$items = array();
		foreach ( $column['entries'] as $entry ) {
			$name = trim( (string) get_the_title( $entry ) );
			if ( '' === $name ) {
				continue;
			}

			$item = array(
				'@type'        => 'Offer',
				'itemOffered'  => array(
					'@type' => 'Product',
					'name'  => $name,
				),
			);

			$description = mooblipoisid_seo_excerpt( get_the_excerpt( $entry ), 200 );
			if ( '' !== $description ) {
				$item['description'] = $description;
			}

			$items[] = $item;
		}

		if ( empty( $items ) ) {
			continue;
		}

		$group = array(
			'@type'           => 'OfferCatalog',
			'itemListElement' => $items,
		);
		if ( '' !== $column['name'] ) {
			$group['name'] = $column['name'];
		}

		$groups[] = $group;
	}

	if ( empty( $groups ) ) {
		return array();
	}

	$catalog = array(
		'@type'           => 'OfferCatalog',
		'itemListElement' => $groups,
	);

	$title = mooblipoisid_seo_setting( 'ranges_title' );
	if ( '' !== $title ) {
		$catalog['name'] = $title;
	}

	return $catalog;
}

/* ==========================================================================
 * 5. wp_head output (basics, social, JSON-LD)
 * ========================================================================== */

/**
 * Print one meta tag (only when the value is non-empty).
 *
 * @param string $attribute Tag attribute name ('property', 'name').
 * @param string $key       Attribute value (e.g. 'og:title').
 * @param string $value     Raw tag value.
 * @param string $escape    'attr' (default) or 'url' to run esc_url() instead.
 * @return void
 */
function mooblipoisid_seo_print_meta( $attribute, $key, $value, $escape = 'attr' ) {
	$key   = trim( (string) $key );
	$value = trim( (string) $value );

	if ( '' === $key || '' === $value ) {
		return;
	}

	$safe = 'url' === $escape ? esc_url( $value ) : esc_attr( $value );

	echo '<meta ' . esc_html( $attribute ) . '="' . esc_html( $key ) . '" content="' . $safe . '" />' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $safe is escaped above by mode.
}

/**
 * wp_head, early: canonical URL, meta description and the x-default hreflang
 * link (front pages + singular pages only; nothing on archives/404).
 *
 * @return void
 */
function mooblipoisid_seo_head_basics() {
	$canonical = mooblipoisid_seo_canonical_url();
	if ( '' === $canonical ) {
		return;
	}

	echo '<link rel="canonical" href="' . esc_url( $canonical ) . '" />' . "\n";

	$description = mooblipoisid_seo_meta_description();
	if ( '' !== $description ) {
		echo '<meta name="description" content="' . esc_attr( $description ) . '" />' . "\n";
	}

	echo '<link rel="alternate" hreflang="x-default" href="' . esc_url( mooblipoisid_seo_default_home_url() ) . '" />' . "\n";
}

/**
 * wp_head: Open Graph + Twitter Card tags. Only front pages and singular
 * pages emit these (the canonical must exist). Every tag requires a
 * non-empty value.
 *
 * @return void
 */
function mooblipoisid_seo_head_social() {
	$canonical = mooblipoisid_seo_canonical_url();
	if ( '' === $canonical ) {
		return;
	}

	mooblipoisid_seo_print_meta( 'property', 'og:site_name', (string) get_bloginfo( 'name' ) );
	mooblipoisid_seo_print_meta( 'property', 'og:title', mooblipoisid_seo_document_title() );
	mooblipoisid_seo_print_meta( 'property', 'og:description', mooblipoisid_seo_meta_description() );
	mooblipoisid_seo_print_meta( 'property', 'og:type', is_front_page() ? 'website' : 'article' );
	mooblipoisid_seo_print_meta( 'property', 'og:url', $canonical, 'url' );
	mooblipoisid_seo_print_meta( 'property', 'og:locale', mooblipoisid_seo_locale() );

	$image_url = mooblipoisid_seo_social_image_url();
	if ( '' !== $image_url ) {
		mooblipoisid_seo_print_meta( 'property', 'og:image', $image_url, 'url' );

		$dimensions = mooblipoisid_seo_image_dimensions( $image_url );
		if ( null !== $dimensions ) {
			mooblipoisid_seo_print_meta( 'property', 'og:image:width', (string) $dimensions[0] );
			mooblipoisid_seo_print_meta( 'property', 'og:image:height', (string) $dimensions[1] );
		}
	}

	mooblipoisid_seo_print_meta( 'name', 'twitter:card', 'summary_large_image' );
}

/**
 * wp_head: one JSON-LD <script> with a single @graph. Front pages and
 * singular pages only.
 *
 * @return void
 */
function mooblipoisid_seo_head_jsonld() {
	if ( ! is_front_page() && ! ( is_singular() && is_main_query() ) ) {
		return;
	}

	$graph = mooblipoisid_seo_jsonld_graph();
	if ( empty( $graph ) ) {
		return;
	}

	$payload = array(
		'@context' => 'https://schema.org',
		'@graph'   => $graph,
	);

	$json = json_encode(
		$payload,
		JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_LINE_TERMINATORS
	);

	if ( false === $json || '' === $json ) {
		return;
	}

	// Keep the inline script safe: escape any closing-tag sequence in the data.
	$json = str_replace( '</', '<\/', $json );

	echo '<script type="application/ld+json">' . $json . "</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- valid JSON with </ sequences escaped.
}

/**
 * Recursively drop empty strings / nulls / empty arrays from the JSON-LD tree
 * (numeric lists keep their non-empty members; string values are trimmed).
 *
 * @param mixed $value Value to clean.
 * @return mixed
 */
function mooblipoisid_seo_jsonld_clean( $value ) {
	if ( is_array( $value ) ) {
		$clean = array();

		foreach ( $value as $key => $item ) {
			$filtered = mooblipoisid_seo_jsonld_clean( $item );

			$is_empty = null === $filtered
				|| ( is_string( $filtered ) && '' === $filtered )
				|| ( is_array( $filtered ) && array() === $filtered );

			if ( $is_empty ) {
				continue;
			}

			if ( is_int( $key ) ) {
				$clean[] = $filtered;
			} else {
				$clean[ $key ] = $filtered;
			}
		}

		return $clean;
	}

	if ( is_string( $value ) ) {
		return trim( $value );
	}

	return $value;
}

/**
 * The JSON-LD @graph: Organization (+ contact, identifier, sameAs, OfferCatalog),
 * WebSite, WebPage, BreadcrumbList (non-front singulars) and FAQPage (homepage
 * only, when the theme provides FAQs).
 *
 * @return array Empty when no canonical URL exists for the request.
 */
function mooblipoisid_seo_jsonld_graph() {
	$canonical = mooblipoisid_seo_canonical_url();
	if ( '' === $canonical ) {
		return array();
	}

	$home        = mooblipoisid_seo_home_url();
	$organization_id = home_url( '/' ) . '#organization';
	$website_id  = home_url( '/' ) . '#website';
	$in_language = mooblipoisid_seo_html_language();
	$company     = mooblipoisid_seo_company_info();

	/*
	 * Organization — @id built from the un-prefixed home_url() so it stays
	 * stable across the et/ru/en front pages.
	 */
	$organization = array(
		'@type' => 'Organization',
		'@id'   => $organization_id,
		'name'  => 'Mööblipoisid',
		'url'   => $home,
	);

	$logo = mooblipoisid_seo_site_icon_url();
	if ( '' === $logo ) {
		$logo = mooblipoisid_seo_site_logo_url();
	}
	if ( '' !== $logo ) {
		$organization['logo'] = $logo;
	}

	if ( '' !== $company['email'] ) {
		$organization['email'] = $company['email'];
	}
	if ( '' !== $company['telephone'] ) {
		$organization['telephone'] = $company['telephone'];
	}
	if ( '' !== $company['reg_code'] ) {
		$organization['identifier'] = array(
			'@type'      => 'PropertyValue',
			'propertyID' => 'registry-code',
			'value'      => $company['reg_code'],
		);
	}

	$contact = array(
		'@type'             => 'ContactPoint',
		'contactType'       => 'sales',
		'availableLanguage' => array( 'et', 'ru', 'en' ),
	);
	$has_contact = false;
	if ( '' !== $company['email'] ) {
		$contact['email'] = $company['email'];
		$has_contact      = true;
	}
	if ( '' !== $company['telephone'] ) {
		$contact['telephone'] = $company['telephone'];
		$has_contact          = true;
	}
	if ( $has_contact ) {
		$organization['contactPoint'] = $contact;
	}

	if ( ! empty( $company['same_as'] ) ) {
		$organization['sameAs'] = $company['same_as'];
	}

	$catalog = mooblipoisid_seo_range_catalog();
	if ( ! empty( $catalog ) ) {
		$organization['hasOfferCatalog'] = $catalog;
	}

	$website = array(
		'@type'      => 'WebSite',
		'@id'        => $website_id,
		'name'       => (string) get_bloginfo( 'name' ),
		'url'        => $home,
		'inLanguage' => $in_language,
		'publisher'  => array( '@id' => $organization_id ),
	);

	$webpage = array(
		'@type'      => 'WebPage',
		'@id'        => $canonical . '#webpage',
		'name'       => mooblipoisid_seo_document_title(),
		'url'        => $canonical,
		'inLanguage' => $in_language,
		'isPartOf'   => array( '@id' => $website_id ),
		'about'      => array( '@id' => $organization_id ),
	);

	$description = mooblipoisid_seo_meta_description();
	if ( '' !== $description ) {
		$webpage['description'] = $description;
	}

	$graph = array( $organization, $website, $webpage );

	// BreadcrumbList on non-front singular pages: Home > page title.
	if ( is_singular() && ! is_front_page() ) {
		$graph[] = array(
			'@type'           => 'BreadcrumbList',
			'itemListElement' => array(
				array(
					'@type'    => 'ListItem',
					'position' => 1,
					'name'     => (string) get_bloginfo( 'name' ),
					'item'     => $home,
				),
				array(
					'@type'    => 'ListItem',
					'position' => 2,
					'name'     => mooblipoisid_seo_document_title(),
					'item'     => $canonical,
				),
			),
		);
	}

	// FAQPage — homepage only: the FAQ section renders there, and repeating
	// the identical node on every singular page would duplicate it site-wide.
	if ( is_front_page() ) {
		$questions = array();
		foreach ( mooblipoisid_seo_faqs() as $faq ) {
			if ( ! is_array( $faq ) ) {
				continue;
			}

			$question = trim( (string) ( $faq['question'] ?? '' ) );
			$answer   = mooblipoisid_seo_excerpt( (string) ( $faq['answer'] ?? '' ), 1000 );
			if ( '' === $question || '' === $answer ) {
				continue;
			}

			$questions[] = array(
				'@type'           => 'Question',
				'name'            => $question,
				'acceptedAnswer'  => array(
					'@type' => 'Answer',
					'text'  => $answer,
				),
			);
		}
		if ( ! empty( $questions ) ) {
			$graph[] = array(
				'@type'      => 'FAQPage',
				'mainEntity' => $questions,
			);
		}
	}

	return mooblipoisid_seo_jsonld_clean( $graph );
}

/* ==========================================================================
 * 6. Head noise removal
 * ========================================================================== */

/**
 * Remove WordPress head noise the user approved: emoji detection/styles,
 * generator meta, RSD + WLW manifest, shortlink, oEmbed discovery and both
 * feed-link sets (the blog has no public posts), plus core's canonical
 * (replaced by our own above).
 *
 * @return void
 */
function mooblipoisid_seo_remove_head_noise() {
	// Emoji detection + printing.
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

	// Generators, editors, shortlinks, oEmbed discovery.
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

	// Feeds (no public posts) and core canonical (we print our own).
	remove_action( 'wp_head', 'feed_links', 2 );
	remove_action( 'wp_head', 'feed_links_extra', 3 );
	remove_action( 'wp_head', 'rel_canonical' );
}

/* ==========================================================================
 * 7. llms.txt route
 * ========================================================================== */

/**
 * Register the /llms.txt rewrite (top priority so it wins over query matches).
 *
 * @return void
 */
function mooblipoisid_seo_register_rewrite() {
	add_rewrite_rule( '^llms\.txt$', 'index.php?mooblipoisid_llms=1', 'top' );
}

/**
 * Register the mooblipoisid_llms query var.
 *
 * @param string[] $vars Registered query vars.
 * @return string[]
 */
function mooblipoisid_seo_query_vars( $vars ) {
	$vars[] = 'mooblipoisid_llms';

	return $vars;
}

/**
 * Translation-set front page IDs (all languages' home pages), so llms.txt can
 * list every other published page.
 *
 * @return int[]
 */
function mooblipoisid_seo_front_page_ids() {
	$front = (int) get_option( 'page_on_front' );
	if ( ! $front ) {
		return array();
	}

	if ( function_exists( 'pll_get_post_translations' ) ) {
		$translations = pll_get_post_translations( $front );
		if ( is_array( $translations ) && ! empty( $translations ) ) {
			return array_values( array_map( 'intval', $translations ) );
		}
	}

	return array( $front );
}

/**
 * Links for the llms.txt Links section: every published page that is not a
 * language front page, labelled with its title and language.
 *
 * @return string[] Lines (without the leading "- ").
 */
function mooblipoisid_seo_llms_page_links() {
	$lines     = array();
	$front_ids = mooblipoisid_seo_front_page_ids();
	$pages     = get_pages(
		array(
			'sort_column'  => 'menu_order,title',
			'hierarchical' => 0,
		)
	);

	if ( empty( $pages ) ) {
		return $lines;
	}

	$seen = array();

	foreach ( $pages as $page ) {
		if ( in_array( (int) $page->ID, $front_ids, true ) ) {
			continue;
		}

		// Polylang filters get_pages() to the active language: expand every
		// page to its full translation set so all languages are listed.
		$page_ids = array( (int) $page->ID );
		if ( function_exists( 'pll_get_post_translations' ) ) {
			$translations = pll_get_post_translations( (int) $page->ID );
			if ( is_array( $translations ) && ! empty( $translations ) ) {
				$page_ids = array_map( 'intval', array_values( $translations ) );
			}
		}

		foreach ( $page_ids as $page_id ) {
			if ( in_array( $page_id, $front_ids, true ) || isset( $seen[ $page_id ] ) ) {
				continue;
			}
			$seen[ $page_id ] = true;

			$post = get_post( $page_id );
			if ( ! $post instanceof WP_Post || 'publish' !== $post->post_status ) {
				continue;
			}

			$link = get_permalink( $post );
			if ( ! is_string( $link ) || '' === $link ) {
				continue;
			}

			$lang = function_exists( 'pll_get_post_language' ) ? pll_get_post_language( $page_id, 'slug' ) : '';
			$lang = is_string( $lang ) && '' !== $lang ? $lang : 'et';
			$name = wp_strip_all_tags( (string) get_the_title( $post ) );

			$lines[] = $name . ' (' . strtoupper( $lang ) . '): ' . $link;
		}
	}

	return $lines;
}

/**
 * Build the llms.txt document (English, plain text).
 *
 * @return string
 */
function mooblipoisid_seo_llms_content() {
	$lines = array();

	$lines[] = '# Mööblipoisid';
	$lines[] = '';
	$lines[] = 'Mööblipoisid is an Estonian custom furniture manufacturer based in Tallinn. The company designs, produces and installs commercial furniture and interior fit-outs for cafés, bars, restaurants and retail brands: bar and service counters, banquette seating, dining furniture, and shopfit joinery such as checkout counters, display shelving, fitting rooms and branded feature walls. Everything is built in its own workshop from solid timber and birch ply with hard-wearing finishes, and delivered as one complete package — design, production and installation — so a venue opens on schedule with a single team responsible end to end.';

	$company = mooblipoisid_seo_company_info();
	if ( '' !== $company['telephone'] || '' !== $company['email'] || '' !== $company['reg_code'] ) {
		$lines[]        = '';
		$lines[]        = '## Contact';
		$raw_company    = function_exists( 'mooblipoisid_get_company_info' ) ? (array) mooblipoisid_get_company_info() : array();
		$phone_alt      = trim( (string) ( $raw_company['phone_alt'] ?? '' ) );
		$email_alt      = trim( (string) ( $raw_company['email_alt'] ?? '' ) );
		if ( '' !== $company['telephone'] ) {
			$lines[] = 'Phone: ' . $company['telephone'];
		}
		if ( '' !== $phone_alt && $phone_alt !== $company['telephone'] ) {
			$lines[] = 'Phone: ' . $phone_alt;
		}
		if ( '' !== $company['email'] ) {
			$lines[] = 'Email: ' . $company['email'];
		}
		if ( '' !== $email_alt && $email_alt !== $company['email'] ) {
			$lines[] = 'Email: ' . $email_alt;
		}
		if ( '' !== $company['reg_code'] ) {
			$lines[] = 'Registry code: ' . $company['reg_code'];
		}
	}

	$ranges = mooblipoisid_seo_ranges();
	if ( ! empty( $ranges ) ) {
		$names = array();
		foreach ( $ranges as $range ) {
			$name = trim( (string) get_the_title( $range ) );
			if ( '' !== $name ) {
				$names[] = $name;
			}
		}
		$names = array_values( array_unique( $names ) );

		if ( ! empty( $names ) ) {
			$lines[] = '';
			$lines[] = '## Products';
			foreach ( $names as $name ) {
				$lines[] = '- ' . $name;
			}
		}
	}

	$lines[] = '';
	$lines[] = '## Links';
	foreach ( mooblipoisid_seo_languages() as $lang ) {
		$url = mooblipoisid_seo_language_home_url( $lang );
		if ( '' !== $url ) {
			$lines[] = '- Homepage (' . mooblipoisid_seo_language_label( $lang ) . '): ' . $url;
		}
	}

	foreach ( mooblipoisid_seo_llms_page_links() as $page_line ) {
		$lines[] = '- ' . $page_line;
	}

	$lines[] = '- This file: ' . home_url( '/llms.txt' );

	// Plain-text hygiene: no tags, no control characters (tabs/newlines kept).
	$content = wp_strip_all_tags( implode( "\n", $lines ) );
	$content = (string) preg_replace( '/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $content );

	return $content . "\n";
}

/**
 * template_redirect: serve /llms.txt as text/plain and stop.
 *
 * @return void
 */
function mooblipoisid_seo_deliver_llms_txt() {
	if ( ! get_query_var( 'mooblipoisid_llms' ) ) {
		return;
	}

	header( 'Content-Type: text/plain; charset=UTF-8' );
	echo mooblipoisid_seo_llms_content(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- text/plain payload, sanitized in mooblipoisid_seo_llms_content().
	exit;
}

/* ==========================================================================
 * 8. Sitemap trim & one-time rewrite flush
 * ========================================================================== */

/**
 * Drop the users sitemap provider; posts/taxonomies stay.
 *
 * @param mixed  $provider Sitemap provider object.
 * @param string $name     Provider name ('users', 'posts', 'taxonomies').
 * @return mixed False removes the provider.
 */
/**
 * Trim the core sitemap: the users provider goes (no public authors) and the
 * taxonomies provider goes too — term archives are thin, unlinked content,
 * and Polylang's query-string term URLs for RU/EN redirect-loop (pre-existing
 * issue) while being advertised in the sitemap.
 *
 * @param mixed  $provider Provider instance or null.
 * @param string $name     Provider name.
 * @return mixed Provider to keep, false to remove.
 */
function mooblipoisid_seo_sitemap_providers( $provider, $name ) {
	if ( 'users' === $name || 'taxonomies' === $name ) {
		return false;
	}

	return $provider;
}

/**
 * Noindex taxonomy term archives (categories, tags, custom taxonomies): thin
 * content, unlinked from the site, and intentionally left out of the sitemap.
 *
 * @param array $robots Robots directives.
 * @return array Filtered directives.
 */
function mooblipoisid_seo_robots_archives( $robots ) {
	if ( is_category() || is_tag() || is_tax() ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
	}

	return $robots;
}
add_filter( 'wp_robots', 'mooblipoisid_seo_robots_archives' );

/**
 * Flush rewrite rules exactly once (after the /llms.txt rule exists), guarded
 * by an option flag so it never repeats.
 *
 * @return void
 */
function mooblipoisid_seo_maybe_flush_rewrites() {
	if ( get_option( 'mooblipoisid_seo_rules_flushed' ) ) {
		return;
	}

	flush_rewrite_rules();
	update_option( 'mooblipoisid_seo_rules_flushed', 1 );
}

/* ==========================================================================
 * 9. Hook registration (the only parse-time statements)
 * ========================================================================== */

// Front-end head plumbing only; wp_head never runs in the admin.
if ( ! is_admin() ) {
	add_filter( 'pre_get_document_title', 'mooblipoisid_seo_document_title_filter', 20 );
	add_action( 'wp_head', 'mooblipoisid_seo_head_basics', 1 );
	add_action( 'wp_head', 'mooblipoisid_seo_head_social', 5 );
	add_action( 'wp_head', 'mooblipoisid_seo_head_jsonld', 20 );
	add_action( 'init', 'mooblipoisid_seo_remove_head_noise' );
}

// /llms.txt route (registered everywhere so wp-cli rewrite flush keeps it).
add_action( 'init', 'mooblipoisid_seo_register_rewrite', 5 );
add_filter( 'query_vars', 'mooblipoisid_seo_query_vars' );
add_action( 'template_redirect', 'mooblipoisid_seo_deliver_llms_txt', 0 );

// One-time rewrite flush (init covers the first front-end hit; admin_init the first admin visit).
add_action( 'init', 'mooblipoisid_seo_maybe_flush_rewrites', 99 );
add_action( 'admin_init', 'mooblipoisid_seo_maybe_flush_rewrites', 99 );

// Sitemap: remove the users provider.
add_filter( 'wp_sitemaps_add_provider', 'mooblipoisid_seo_sitemap_providers', 10, 2 );
