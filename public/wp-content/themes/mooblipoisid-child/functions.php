<?php
/**
 * Mööblipoisid child theme functions.
 *
 * @package mooblipoisid-child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Load the theme's translations (languages/*.mo) for the `mooblipoisid-child`
 * domain so the site can show each language's strings (Estonian source, with
 * Russian and English translations). Loaded on `init` (after Polylang has set
 * the request locale) and reloaded when the locale is switched, using
 * load_textdomain() so the exact per-locale .mo file is always picked up.
 */
function mooblipoisid_load_child_textdomain() {
	$domain = 'mooblipoisid-child';
	$dir    = get_stylesheet_directory() . '/languages';
	$locale = determine_locale();
	$mofile = $dir . '/' . $domain . '-' . $locale . '.mo';

	if ( is_textdomain_loaded( $domain ) ) {
		unload_textdomain( $domain );
	}
	if ( file_exists( $mofile ) ) {
		load_textdomain( $domain, $mofile );
	}
}
add_action( 'init', 'mooblipoisid_load_child_textdomain', 20 );
add_action( 'change_locale', 'mooblipoisid_load_child_textdomain' );

/**
 * Resolve a stored upload URL (settings / term meta) for rendering.
 *
 * Placeholder content stores absolute URLs that may point at another host
 * (e.g. the dev domain), so first rebuild the URL against the current site.
 * When the file maps to a media-library attachment, serve the requested
 * (smaller) size; otherwise fall back to the normalized original.
 *
 * @param string $url  Stored image URL.
 * @param string $size Requested image size (e.g. 'large', 'full').
 * @return string URL usable on the current site.
 */
function mooblipoisid_image_src( $url, $size = 'large' ) {
	$url = trim( (string) $url );
	if ( '' === $url ) {
		return '';
	}

	$host = wp_parse_url( $url, PHP_URL_HOST );
	if ( null === $host || '' === $host ) {
		// Already a site-relative path.
		$normalized = home_url( '/' . ltrim( $url, '/' ) );
	} elseif ( strtolower( (string) wp_parse_url( home_url(), PHP_URL_HOST ) ) !== strtolower( (string) $host ) ) {
		// Stored URL points at another host: keep only the /wp-content/ path.
		$path = (string) wp_parse_url( $url, PHP_URL_PATH );
		if ( false !== strpos( $path, '/wp-content/' ) ) {
			$normalized = home_url( substr( $path, (int) strpos( $path, '/wp-content/' ) ) );
		} else {
			$normalized = $url;
		}
	} else {
		$normalized = $url;
	}

	if ( function_exists( 'attachment_url_to_postid' ) ) {
		$attach_id = (int) attachment_url_to_postid( $normalized );
		if ( $attach_id ) {
			$sized = wp_get_attachment_image_url( $attach_id, $size );
			if ( $sized ) {
				return $sized;
			}
		}
	}

	return $normalized;
}

/**
 * Translate the footer widget / legal-bar text (stored as plain Estonian HTML
 * in the database) with the per-language values from the Homepage Settings
 * screen. The stored HTML keeps its structure; only the known Estonian text
 * pieces are swapped for the active language's fields. The "Explore" links
 * column is rebuilt with the multilingual navigation menu (same items as the
 * header; Polylang picks the menu for the active language). Upload URLs
 * pointing at a stale host (e.g. the dev domain) are rebuilt against the
 * current site.
 *
 * @param string $content Widget HTML.
 * @return string Translated widget HTML.
 */
function mooblipoisid_translate_footer_html( $content ) {
	if ( ! is_string( $content ) || '' === $content ) {
		return $content;
	}

	// Rebuild upload URLs stored with another host against the current site.
	$content = preg_replace_callback(
		'/https?:\/\/[^\/\s"\'<>]+(\/wp-content\/[^\s"\'<>]+)/i',
		function ( $matches ) {
			return home_url( $matches[1] );
		},
		$content
	);

	/*
	 * The footer logo follows the header logo: whatever is set under
	 * Appearance → Customize → Site Identity ("Logo") is rendered here too,
	 * with the same responsive image markup. The stored fallback image only
	 * remains if no logo is set at all.
	 */
	$logo_id = (int) get_theme_mod( 'custom_logo' );
	if ( $logo_id && false !== strpos( $content, 'mp-footer-logo' ) ) {
		$logo_html = wp_get_attachment_image(
			$logo_id,
			'full',
			false,
			array(
				'class'   => 'mp-footer-logo',
				'alt'     => get_bloginfo( 'name' ),
				'loading' => 'lazy',
				'decoding' => 'async',
			)
		);
		if ( $logo_html ) {
			$content = preg_replace( '/<img[^>]*mp-footer-logo[^>]*>/i', $logo_html, $content );
		}
	}

	/*
	 * The legal-bar links point at the three legal pages (stored as
	 * Estonian root-relative paths). Each page has linked translations, so
	 * the Estonian path is swapped for the active language's permalink here.
	 */
	$legal_slugs = array( 'komisjonitingimused', 'materjalide-paritolu', 'privaatsuspoliitika' );
	foreach ( $legal_slugs as $legal_slug ) {
		$et_page = get_page_by_path( $legal_slug );
		if ( ! $et_page ) {
			continue;
		}
		$target_id = $et_page->ID;
		if ( function_exists( 'pll_get_post' ) ) {
			$target_id = (int) pll_get_post( $et_page->ID ) ?: $et_page->ID;
		}
		$relative = get_permalink( $target_id ) ? wp_make_link_relative( get_permalink( $target_id ) ) : '';
		if ( $relative ) {
			$content = str_replace( '/' . $legal_slug . '/', $relative, $content );
		}
	}

	/*
	 * The "Explore" footer column shows the same menu as the header. Passing
	 * one of the linked menus by name lets Polylang swap in the current
	 * language's menu (all three are translations of each other). Do not
	 * rename the "Main Navigation (ET)" menu, or pass its new name here.
	 */
	if ( false !== strpos( $content, 'mp-footer-links' ) ) {
		$menu_html = wp_nav_menu(
			array(
				'menu'        => 'main-navigation-et',
				'container'   => false,
				'menu_class'  => 'mp-footer-links',
				'depth'       => 1,
				'echo'        => false,
				'fallback_cb' => '__return_empty_string',
			)
		);
		if ( is_string( $menu_html ) && '' !== $menu_html ) {
			$content = preg_replace( '/<ul class="mp-footer-links">.*?<\/ul>/s', $menu_html, $content );
		}
	}

	$settings = function_exists( 'mooblipoisid_get_homepage_settings' ) ? mooblipoisid_get_homepage_settings() : array();

	/*
	 * Estonian source piece => settings field holding the active language's
	 * text. Longest pieces first so no shorter replacement breaks a longer one.
	 */
	$sources = array(
		'footer_tagline'          => 'Erilahendusega mööbel ja sisustus kohvikutele, baaridele, restoranidele ja jaemüügile, lisaks eraköögid, valmistatud kestva põhjalikkusega.',
		'footer_searches_text'    => 'Äripindade siseviimistluse, jaemüügikettide laiendamise ja arhitektide koostöö jaoks saatke oma brief otse meie projektide lauale.',
		'footer_legal_materials'  => 'Materjalide päritolu',
		'footer_legal_commission' => 'Komisjonitingimused',
		'footer_legal_privacy'    => 'Privaatsuspoliitika',
		'footer_copyright'        => 'Kõik õigused kaitstud.',
		'footer_label_workshop'   => 'Töökoda',
		'footer_label_discover'   => 'Avasta',
		'footer_label_searches'   => 'Otsingud',
		'footer_cta_label'        => 'Küsi pakkumist',
	);

	foreach ( $sources as $field => $source ) {
		$replacement = isset( $settings[ $field ] ) ? (string) $settings[ $field ] : '';
		if ( '' === $replacement || $replacement === $source ) {
			continue;
		}
		$content = str_replace( $source, $replacement, $content );
	}

	return $content;
}
add_filter( 'widget_custom_html_content', 'mooblipoisid_translate_footer_html', 10, 1 );
add_filter( 'mooblipoisid_footer_html', 'mooblipoisid_translate_footer_html', 10, 1 );

/**
 * Translate the Kadence footer legal-bar HTML (stored in the `footer_html_content`
 * theme mod) through the theme's gettext domain, per language.
 *
 * @param string $value Theme mod value.
 * @return string Translated value.
 */
function mooblipoisid_translate_footer_html_mod( $value ) {
	return mooblipoisid_translate_footer_html( $value );
}
add_filter( 'theme_mod_footer_html_content', 'mooblipoisid_translate_footer_html_mod', 10, 1 );

/**
 * Resolve the Kadence header button label per language from the Homepage
 * Settings screen (`header_cta_label`), falling back to the stored theme mod.
 *
 * @param string $value Theme mod value.
 * @return string Translated value.
 */
function mooblipoisid_translate_header_button_label( $value ) {
	$settings = function_exists( 'mooblipoisid_get_homepage_settings' ) ? mooblipoisid_get_homepage_settings() : array();
	$label    = isset( $settings['header_cta_label'] ) ? trim( (string) $settings['header_cta_label'] ) : '';

	if ( '' !== $label ) {
		return $label;
	}

	if ( ! is_string( $value ) || '' === $value ) {
		return $value;
	}
	return __( $value, 'mooblipoisid-child' );
}
add_filter( 'theme_mod_header_button_label', 'mooblipoisid_translate_header_button_label', 10, 1 );

/**
 * Render a language switcher dropdown (with flags) for the site header.
 *
 * The switcher shows the current language (flag + name + caret); activating it
 * reveals a dropdown listing every language (flag + name) linking to the same
 * page in that language. Data comes from Polylang's raw switcher output so the
 * per-language URLs point at the current page, not the home page.
 *
 * @return void
 */
function mooblipoisid_header_language_switcher() {
	if ( ! function_exists( 'pll_the_languages' ) ) {
		return;
	}

	$languages = pll_the_languages( array( 'raw' => 1 ) );
	if ( empty( $languages ) || ! is_array( $languages ) ) {
		return;
	}

	$current = null;
	foreach ( $languages as $lang ) {
		if ( ! empty( $lang['current_lang'] ) ) {
			$current = $lang;
			break;
		}
	}
	if ( null === $current ) {
		$current = reset( $languages );
	}

	$uid = 'mp-lang-switcher-' . wp_unique_id();
	?>
	<div class="mp-langswitch site-header-item site-header-focus-item" data-section="mooblipoisid_language_switcher">
		<button type="button" class="mp-langswitch__toggle" id="<?php echo esc_attr( $uid ); ?>"
			aria-haspopup="true" aria-expanded="false" aria-controls="<?php echo esc_attr( $uid ); ?>-menu"
			data-mp-langswitch-toggle>
			<img class="mp-langswitch__flag" src="<?php echo esc_url( $current['flag'] ); ?>" alt="<?php echo esc_attr( $current['name'] ); ?>" width="20" height="15">
			<svg class="mp-langswitch__caret" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 9l6 6 6-6"></path></svg>
		</button>
		<ul class="mp-langswitch__menu" id="<?php echo esc_attr( $uid ); ?>-menu" role="menu" aria-labelledby="<?php echo esc_attr( $uid ); ?>" hidden>
			<?php foreach ( $languages as $lang ) : ?>
				<?php $is_current = ! empty( $lang['current_lang'] ); ?>
				<li class="mp-langswitch__item" role="none">
					<a class="mp-langswitch__link<?php echo $is_current ? ' is-current' : ''; ?>" role="menuitem"
						href="<?php echo esc_url( $lang['url'] ); ?>"
						<?php if ( $is_current ) : ?>aria-current="true"<?php endif; ?>
						hreflang="<?php echo esc_attr( $lang['slug'] ); ?>" lang="<?php echo esc_attr( $lang['locale'] ); ?>">
						<img class="mp-langswitch__flag" src="<?php echo esc_url( $lang['flag'] ); ?>" alt="<?php echo esc_attr( $lang['name'] ); ?>" width="20" height="15">
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php
}
add_action( 'kadence_header_button', 'mooblipoisid_header_language_switcher', 20 );

/**
 * Resolve anchor-only nav-menu links ("#projects") against the current
 * language's home page. The menu items point at homepage sections; raw
 * anchors would do nothing on any other page (e.g. the legal pages), so the
 * item URLs are prefixed with the active language's home URL. On the home
 * page itself this is a same-page fragment navigation, exactly like the raw
 * anchor. Hooked at the data level so every consumer (Kadence header nav,
 * mobile drawer, the footer menu) picks it up; skipped in the admin so the
 * menu editor keeps showing the raw anchors.
 *
 * @param array $items Menu items.
 * @return array Updated menu items.
 */
function mooblipoisid_menu_anchor_urls( $items ) {
	if ( is_admin() || empty( $items ) ) {
		return $items;
	}

	$home = function_exists( 'pll_home_url' ) ? pll_home_url() : home_url( '/' );
	$home = untrailingslashit( $home );

	foreach ( $items as $item ) {
		if ( isset( $item->object, $item->url ) && 'custom' === $item->object
			&& preg_match( '/^#[a-z0-9_-]+$/i', $item->url ) ) {
			$item->url = $home . $item->url;
		}
	}

	return $items;
}
add_filter( 'wp_get_nav_menu_items', 'mooblipoisid_menu_anchor_urls', 10, 1 );

/**
 * Anchor-only nav-menu items are section shortcuts, not pages — they should
 * never be flagged as the "current" page (the homepage match would otherwise
 * visually highlight the first item). Strips the current/home context classes
 * from anchor items on the front end.
 *
 * @param array  $classes CSS classes for the menu item.
 * @param object $item    The menu item.
 * @return array Updated classes.
 */
function mooblipoisid_menu_anchor_classes( $classes, $item ) {
	if ( is_admin() || ! isset( $item->object ) || 'custom' !== $item->object ) {
		return $classes;
	}

	$url = (string) get_post_meta( $item->ID, '_menu_item_url', true );
	if ( ! preg_match( '/^#[a-z0-9_-]+$/i', $url ) ) {
		return $classes;
	}

	return array_values(
		array_diff(
			(array) $classes,
			array( 'current-menu-item', 'current_page_item', 'menu-item-home', 'current-menu-parent', 'current-menu-ancestor' )
		)
	);
}
add_filter( 'nav_menu_css_class', 'mooblipoisid_menu_anchor_classes', 10, 2 );

/**
 * Front-end assets (and the tiny toggle script) for the header language switcher.
 *
 * @return void
 */
function mooblipoisid_language_switcher_assets() {
	wp_register_script(
		'mooblipoisid-langswitch',
		'',
		array(),
		wp_get_theme( 'mooblipoisid-child' )->get( 'Version' ),
		true
	);
	wp_enqueue_script( 'mooblipoisid-langswitch' );
	wp_add_inline_script(
		'mooblipoisid-langswitch',
		<<<'JS'
(function () {
	function closeAll(except) {
		document.querySelectorAll('[data-mp-langswitch-toggle]').forEach(function (btn) {
			if (btn === except) { return; }
			btn.setAttribute('aria-expanded', 'false');
			var menu = document.getElementById(btn.getAttribute('aria-controls'));
			if (menu) { menu.hidden = true; }
		});
	}
	function init(btn) {
		var menu = document.getElementById(btn.getAttribute('aria-controls'));
		if (!menu) { return; }
		btn.addEventListener('click', function (e) {
			e.stopPropagation();
			var open = btn.getAttribute('aria-expanded') === 'true';
			closeAll(btn);
			btn.setAttribute('aria-expanded', open ? 'false' : 'true');
			menu.hidden = open;
		});
		menu.addEventListener('click', function (e) { e.stopPropagation(); });
	}
	function boot() {
		document.querySelectorAll('[data-mp-langswitch-toggle]').forEach(init);
		document.addEventListener('click', function () { closeAll(null); });
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') { closeAll(null); }
		});
	}
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
JS
	);
}
add_action( 'wp_enqueue_scripts', 'mooblipoisid_language_switcher_assets' );

/**
 * Back-to-top button: markup + behaviour printed just before </body>.
 *
 * The button is hidden until the page is scrolled down, then fades in at the
 * bottom-right. Clicking it scrolls smoothly back to the top.
 *
 * @return void
 */
function mooblipoisid_back_to_top() {
	$mp_settings = function_exists( 'mooblipoisid_get_homepage_settings' ) ? mooblipoisid_get_homepage_settings() : array();
	$label       = isset( $mp_settings['backtotop_label'] ) && '' !== trim( (string) $mp_settings['backtotop_label'] )
		? (string) $mp_settings['backtotop_label']
		: __( 'Tagasi üles', 'mooblipoisid-child' );
	echo '<button type="button" class="mp-backtotop" id="mp-backtotop" aria-label="' . esc_attr( $label ) . '" hidden>';
	echo '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 19V5M6 11l6-6 6 6"/></svg>';
	echo '</button>';
	?>
	<script>
	(function () {
		var btn = document.getElementById('mp-backtotop');
		if (!btn) { return; }
		var threshold = 400;
		function onScroll() {
			if (window.scrollY > threshold) {
				btn.hidden = false;
				btn.classList.add('is-visible');
			} else {
				btn.classList.remove('is-visible');
			}
		}
		btn.addEventListener('click', function () {
			window.scrollTo({ top: 0, behavior: 'smooth' });
		});
		window.addEventListener('scroll', onScroll, { passive: true });
		onScroll();
	})();
	</script>
	<?php
}
add_action( 'wp_footer', 'mooblipoisid_back_to_top', 20 );

/**
 * Homepage content model: post types, taxonomies, entry details and the
 * Homepage Settings screen. Kept in its own file so functions.php stays
 * focused on the front-end behaviour.
 */
require get_stylesheet_directory() . '/inc/content-model.php';

/**
 * Polylang front-page URL normalization: keeps the language front pages on the
 * language roots (`/`, `/ru/`, `/en/`) instead of Polylang's `/ru/home/` style URLs,
 * and prevents the permanent redirect loop Polylang otherwise produces when the
 * translated front pages carry custom slugs.
 */
require get_stylesheet_directory() . '/inc/polylang-front-page-urls.php';

/**
 * Load the child stylesheet after the parent's compiled styles.
 */
add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_style(
			'mooblipoisid-child',
			get_stylesheet_directory_uri() . '/style.css',
			array( 'kadence-global' ),
			wp_get_theme()->get( 'Version' ) . '.' . (string) filemtime( get_stylesheet_directory() . '/style.css' )
		);
	},
	20
);

/**
 * Recipients of the enquiry form submissions.
 */
function mooblipoisid_enquiry_recipients() {
	return array( 'andre@mooblipoisid.ee', 'marilyn@mooblipoisid.ee' );
}

/**
 * Render the project enquiry form. Every label, placeholder, dropdown option
 * and message comes from the Homepage Settings screen in the active language.
 */
function mooblipoisid_enquiry_form_shortcode() {
	$success = isset( $_GET['enquiry'] ) && 'sent' === $_GET['enquiry']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$error   = isset( $_GET['enquiry'] ) && 'error' === $_GET['enquiry']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	$s = function_exists( 'mooblipoisid_get_homepage_settings' ) ? mooblipoisid_get_homepage_settings() : array();
	$t = function ( $key ) use ( $s ) {
		return isset( $s[ $key ] ) ? (string) $s[ $key ] : '';
	};

	ob_start();
	?>
	<form class="mp-form" method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="mooblipoisid_enquiry" />
		<?php wp_nonce_field( 'mooblipoisid_enquiry', 'mooblipoisid_enquiry_nonce' ); ?>
		<div class="mp-form__hp" aria-hidden="true">
			<label><?php echo esc_html( $t( 'form_honeypot_label' ) ); ?><input type="text" name="mp_hp" tabindex="-1" autocomplete="off" /></label>
		</div>

		<div class="mp-form__panel">
		<?php if ( $success ) : ?>
			<p class="mp-form__success" role="status"><svg aria-hidden="true" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" style="flex:none"><path d="M20 6L9 17l-5-5"/></svg><?php echo esc_html( $t( 'form_success' ) ); ?></p>
		<?php endif; ?>
		<?php if ( $error ) : ?>
			<p class="mp-form__error" role="alert"><?php echo esc_html( $t( 'form_error' ) ); ?></p>
		<?php endif; ?>
		<div class="mp-form__row">
			<div>
				<label for="mp_name"><?php echo esc_html( $t( 'form_label_name' ) ); ?></label>
				<input type="text" id="mp_name" name="mp_name" placeholder="<?php echo esc_attr( $t( 'form_placeholder_name' ) ); ?>" required />
			</div>
			<div>
				<label for="mp_company"><?php echo esc_html( $t( 'form_label_company' ) ); ?></label>
				<input type="text" id="mp_company" name="mp_company" placeholder="<?php echo esc_attr( $t( 'form_placeholder_company' ) ); ?>" required />
			</div>
		</div>
		<div class="mp-form__row">
			<div>
				<label for="mp_email"><?php echo esc_html( $t( 'form_label_email' ) ); ?></label>
				<input type="email" id="mp_email" name="mp_email" placeholder="nimi@ettevote.ee" required />
			</div>
			<div>
				<label for="mp_phone"><?php echo esc_html( $t( 'form_label_phone' ) ); ?></label>
				<input type="tel" id="mp_phone" name="mp_phone" placeholder="+372 5000 0000" />
			</div>
		</div>
		<div>
			<label for="mp_project_type"><?php echo esc_html( $t( 'form_label_project_type' ) ); ?></label>
			<select id="mp_project_type" name="mp_project_type" required>
				<option value=""><?php echo esc_html( $t( 'form_option_pick' ) ); ?></option>
				<option><?php echo esc_html( $t( 'form_opt_type_cafe' ) ); ?></option>
				<option><?php echo esc_html( $t( 'form_opt_type_retail' ) ); ?></option>
				<option><?php echo esc_html( $t( 'form_opt_type_counters' ) ); ?></option>
				<option><?php echo esc_html( $t( 'form_opt_type_multi' ) ); ?></option>
				<option><?php echo esc_html( $t( 'form_opt_type_private' ) ); ?></option>
			</select>
		</div>
		<div>
			<label for="mp_location"><?php echo esc_html( $t( 'form_label_location' ) ); ?></label>
			<input type="text" id="mp_location" name="mp_location" placeholder="<?php echo esc_attr( $t( 'form_placeholder_location' ) ); ?>" />
		</div>
		<div>
			<label for="mp_brief"><?php echo esc_html( $t( 'form_label_brief' ) ); ?></label>
			<textarea id="mp_brief" name="mp_brief" rows="4" placeholder="<?php echo esc_attr( $t( 'form_placeholder_brief' ) ); ?>"></textarea>
		</div>
		<div class="mp-form__upload">
			<svg aria-hidden="true" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><path d="M17 8l-5-5-5 5"/><path d="M12 3v12"/></svg>
			<label for="mp_files"><?php echo esc_html( $t( 'form_upload_label' ) ); ?></label>
			<input type="file" id="mp_files" name="mp_files[]" multiple accept=".pdf,.dwg,.png,.jpg,.jpeg,application/pdf,image/png,image/jpeg" />
			<p class="mp-form__upload-hint"><?php echo esc_html( $t( 'form_upload_hint' ) ); ?></p>
		</div>
		<ul class="mp-form__files" id="mp_files_list" data-remove-label="<?php echo esc_attr( $t( 'form_file_remove' ) ); ?>" hidden></ul>
		<div class="mp-form__submit">
			<button type="submit" class="mp-btn">
				<span><?php echo esc_html( $t( 'form_submit' ) ); ?></span>
				<svg aria-hidden="true" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
			</button>
		</div>
		</div>
	</form>
	<script>
	(function () {
		var input = document.getElementById('mp_files');
		var list = document.getElementById('mp_files_list');
		if (!input || !list || typeof DataTransfer === 'undefined') { return; }
		var removeLabel = list.getAttribute('data-remove-label') || '\u00d7';

		function formatSize(bytes) {
			if (bytes >= 1048576) { return (bytes / 1048576).toFixed(1) + ' MB'; }
			if (bytes >= 1024) { return Math.round(bytes / 1024) + ' KB'; }
			return bytes + ' B';
		}

		function render() {
			list.innerHTML = '';
			Array.prototype.forEach.call(input.files, function (file, index) {
				var item = document.createElement('li');
				item.className = 'mp-form__file';

				var name = document.createElement('span');
				name.className = 'mp-form__file-name';
				name.textContent = file.name;

				var size = document.createElement('span');
				size.className = 'mp-form__file-size';
				size.textContent = formatSize(file.size);

				var remove = document.createElement('button');
				remove.type = 'button';
				remove.className = 'mp-form__file-remove';
				remove.setAttribute('aria-label', removeLabel + ': ' + file.name);
				remove.textContent = '\u00d7';
				remove.addEventListener('click', function () { removeFile(index); });

				item.appendChild(name);
				item.appendChild(size);
				item.appendChild(remove);
				list.appendChild(item);
			});
			list.hidden = input.files.length === 0;
		}

		function removeFile(index) {
			var dt = new DataTransfer();
			Array.prototype.forEach.call(input.files, function (file, i) {
				if (i !== index) { dt.items.add(file); }
			});
			input.files = dt.files;
			render();
		}

		input.addEventListener('change', render);
	})();
	</script>
	<?php

	return ob_get_clean();
}
add_shortcode( 'mooblipoisid_enquiry_form', 'mooblipoisid_enquiry_form_shortcode' );

/**
 * Run shortcodes inside core/html blocks so the enquiry form can live inside
 * the consultation section's HTML markup.
 */
function mooblipoisid_do_shortcodes_in_html_blocks( $block_content, $block ) {
	if ( 'core/html' === ( $block['blockName'] ?? '' ) && false !== strpos( $block_content, '[' ) ) {
		return do_shortcode( $block_content );
	}
	return $block_content;
}
add_filter( 'render_block', 'mooblipoisid_do_shortcodes_in_html_blocks', 10, 2 );

/**
 * Handle the enquiry form submission: validate, email both managers, redirect.
 */
function mooblipoisid_handle_enquiry() {
	$redirect = wp_get_referer() ? wp_get_referer() : home_url( '/' );
	$redirect = remove_query_arg( array( 'enquiry' ), $redirect );

	if ( ! isset( $_POST['mooblipoisid_enquiry_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mooblipoisid_enquiry_nonce'] ) ), 'mooblipoisid_enquiry' ) ) {
		wp_safe_redirect( add_query_arg( 'enquiry', 'error', $redirect . '#consultation' ) );
		exit;
	}

	if ( ! empty( $_POST['mp_hp'] ) ) {
		wp_safe_redirect( add_query_arg( 'enquiry', 'sent', $redirect . '#consultation' ) );
		exit;
	}

	$required = array( 'mp_name', 'mp_company', 'mp_email', 'mp_project_type' );
	foreach ( $required as $field ) {
		if ( empty( $_POST[ $field ] ) ) {
			wp_safe_redirect( add_query_arg( 'enquiry', 'error', $redirect . '#consultation' ) );
			exit;
		}
	}

	$fields = array(
		'name'         => sanitize_text_field( wp_unslash( $_POST['mp_name'] ?? '' ) ),
		'company'      => sanitize_text_field( wp_unslash( $_POST['mp_company'] ?? '' ) ),
		'email'        => sanitize_email( wp_unslash( $_POST['mp_email'] ?? '' ) ),
		'phone'        => sanitize_text_field( wp_unslash( $_POST['mp_phone'] ?? '' ) ),
		'project_type' => sanitize_text_field( wp_unslash( $_POST['mp_project_type'] ?? '' ) ),
		'location'     => sanitize_text_field( wp_unslash( $_POST['mp_location'] ?? '' ) ),
		'brief'        => sanitize_textarea_field( wp_unslash( $_POST['mp_brief'] ?? '' ) ),
	);

	if ( ! is_email( $fields['email'] ) ) {
		wp_safe_redirect( add_query_arg( 'enquiry', 'error', $redirect . '#consultation' ) );
		exit;
	}

	// Validate and stage uploaded attachments (PDF, DWG, PNG, JPEG up to 25MB each).
	$attachments = array();
	$allowed_ext = array( 'pdf', 'dwg', 'png', 'jpg', 'jpeg' );
	$max_bytes   = 25 * 1024 * 1024;

	if ( ! empty( $_FILES['mp_files']['name'][0] ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		foreach ( $_FILES['mp_files']['name'] as $i => $file_name ) { // phpcs: ignore WordPress.Security.ValidatedSanitizedInput
			if ( ! is_uploaded_file( $_FILES['mp_files']['tmp_name'][ $i ] ) ) { // phpcs: ignore WordPress.Security.ValidatedSanitizedInput
				continue;
			}
			if ( UPLOAD_ERR_OK !== $_FILES['mp_files']['error'][ $i ] || $_FILES['mp_files']['size'][ $i ] > $max_bytes ) { // phpcs: ignore WordPress.Security.ValidatedSanitizedInput
				wp_safe_redirect( add_query_arg( 'enquiry', 'error', $redirect . '#consultation' ) );
				exit;
			}
			$ext = strtolower( pathinfo( sanitize_file_name( $file_name ), PATHINFO_EXTENSION ) );
			if ( ! in_array( $ext, $allowed_ext, true ) ) {
				wp_safe_redirect( add_query_arg( 'enquiry', 'error', $redirect . '#consultation' ) );
				exit;
			}
			/*
			 * Stage the upload under its own sanitized name. wp_mail() uses
			 * the file's basename as the attachment name, so wp_tempnam()
			 * (which appends ".tmp") would make every attachment arrive as
			 * ".tmp" regardless of its real type.
			 */
			$staged = get_temp_dir() . uniqid( 'mp-enquiry-' ) . '-' . sanitize_file_name( $file_name );
			if ( ! @move_uploaded_file( $_FILES['mp_files']['tmp_name'][ $i ], $staged ) ) { // phpcs: ignore WordPress.PHP.NoSilencedErrors.Discouraged
				wp_safe_redirect( add_query_arg( 'enquiry', 'error', $redirect . '#consultation' ) );
				exit;
			}
			$attachments[] = $staged;
		}
	}

	$lines   = array(
		'New project enquiry from mooblipoisid.ee',
		'',
		'Name: ' . $fields['name'],
		'Company / Venue: ' . $fields['company'],
		'Email: ' . $fields['email'],
		'Phone: ' . ( $fields['phone'] ? $fields['phone'] : '—' ),
		'Project type: ' . $fields['project_type'],
		'Location: ' . ( $fields['location'] ? $fields['location'] : '—' ),
		'',
		'Brief & dimensions:',
		( $fields['brief'] ? $fields['brief'] : '—' ),
		'',
		'Reply to: ' . $fields['name'] . ' <' . $fields['email'] . '>',
	);
	$subject = sprintf( '[mooblipoisid.ee] %s — %s', $fields['project_type'], $fields['company'] );
	$headers = array( 'Reply-To: ' . $fields['name'] . ' <' . $fields['email'] . '>' );

	$sent = wp_mail( mooblipoisid_enquiry_recipients(), $subject, implode( "\n", $lines ), $headers, $attachments );

	foreach ( $attachments as $attachment ) {
		@unlink( $attachment ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	}

	wp_safe_redirect( add_query_arg( 'enquiry', $sent ? 'sent' : 'error', $redirect . '#consultation' ) );
	exit;
}
add_action( 'admin_post_mooblipoisid_enquiry', 'mooblipoisid_handle_enquiry' );
add_action( 'admin_post_nopriv_mooblipoisid_enquiry', 'mooblipoisid_handle_enquiry' );
