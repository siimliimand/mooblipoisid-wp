<?php
/**
 * One-time setup: creates Estonian and Russian translations of the navigation
 * menu and assigns one menu per language to the theme locations via Polylang.
 *
 * The existing "Main Navigation" menu carries English labels, so it becomes
 * the English menu. Estonian and Russian menus are created with translated
 * labels and linked to it as menu translations. The footer renders this same
 * menu (the footer widget is wired to it in the child theme), so one menu
 * serves the header, the mobile drawer and the footer.
 *
 * Safe to re-run: menus that already exist (matched by slug) and menus that
 * already have items are left untouched.
 *
 * Usage: ddev wp eval-file bin/seed-menus.php
 *
 * @package mooblipoisid
 */

do_action( 'init' ); // Polylang's pll_* helpers are no-ops before init.

if ( ! function_exists( 'pll_set_term_language' ) || ! function_exists( 'pll_save_term_translations' ) ) {
	echo "Polylang is not active — aborting.\n";
	exit( 1 );
}

$stylesheet = get_option( 'stylesheet' );

/*
 * Anchor navigation. Keys are the language slugs; the English menu is the one
 * that already exists in the database (slug main-navigation-en, renamed from
 * the original main-navigation).
 */
$definitions = array(
	'main'   => array(
		'en_slug'   => 'main-navigation-en',
		'en_name'   => 'Main Navigation (EN)',
		'locations' => array( 'primary', 'mobile' ),
		'items'     => array(
			'et' => array(
				array( 'Kes', '#hospitality' ),
				array( 'Mis', '#ranges' ),
				array( 'Projektid', '#projects' ),
				array( 'Võimalused', '#capabilities' ),
				array( 'Protsess', '#process' ),
				array( 'Meist', '#maker' ),
			),
			'ru' => array(
				array( 'Кто', '#hospitality' ),
				array( 'Что', '#ranges' ),
				array( 'Проекты', '#projects' ),
				array( 'Возможности', '#capabilities' ),
				array( 'Процесс', '#process' ),
				array( 'О нас', '#maker' ),
			),
		),
	),
);

$menus  = array(); // group => lang => menu_id.
$filled = 0;
$kept   = 0;

foreach ( $definitions as $group => $definition ) {
	$menus[ $group ] = array();

	// English menu: the existing one (rename for clarity, slug stays the same).
	$en_term = get_term_by( 'slug', $definition['en_slug'], 'nav_menu' );
	if ( ! $en_term ) {
		echo "Existing menu with slug {$definition['en_slug']} not found — aborting.\n";
		exit( 1 );
	}
	$en_id = (int) $en_term->term_id;
	if ( $en_term->name !== $definition['en_name'] ) {
		wp_update_nav_menu_object( $en_id, array( 'menu-name' => $definition['en_name'] ) );
	}
	$menus[ $group ]['en'] = $en_id;
	pll_set_term_language( $en_id, 'en' );

	// Estonian and Russian menus: create if missing, fill if empty.
	foreach ( array( 'et', 'ru' ) as $lang ) {
		$slug = $definition['en_slug'] . '-' . $lang;
		$name = str_replace( '(EN)', '(' . strtoupper( $lang ) . ')', $definition['en_name'] );
		$term = get_term_by( 'slug', $slug, 'nav_menu' );

		if ( ! $term ) {
			$menu_id = wp_create_nav_menu( $name );
			if ( is_wp_error( $menu_id ) ) {
				echo "Could not create menu {$name}: " . $menu_id->get_error_message() . "\n";
				exit( 1 );
			}
		} else {
			$menu_id = (int) $term->term_id;
		}

		 pll_set_term_language( $menu_id, $lang );

		// Idempotent: only fill a menu that has no items yet.
		$items = wp_get_nav_menu_items( $menu_id );
		if ( empty( $items ) ) {
			$position = 1;
			foreach ( $definition['items'][ $lang ] as $item ) {
				wp_update_nav_menu_item(
					$menu_id,
					0,
					array(
						'menu-item-title'    => $item[0],
						'menu-item-url'      => $item[1],
						'menu-item-type'     => 'custom',
						'menu-item-object'   => 'custom',
						'menu-item-object-id' => 0,
						'menu-item-status'   => 'publish',
						'menu-item-position' => $position++,
					)
				);
			}
			$filled++;
		} else {
			$kept++;
		}

		$menus[ $group ][ $lang ] = $menu_id;
	}

	// Link the three menus as translations of each other.
	pll_save_term_translations(
		array(
			'et' => $menus[ $group ]['et'],
			'ru' => $menus[ $group ]['ru'],
			'en' => $menus[ $group ]['en'],
		)
	);
}

// One menu per language on every theme location.
$option              = get_option( 'polylang', array() );
$option              = is_array( $option ) ? $option : array();
$option['nav_menus'] = array(
	$stylesheet => array(
		'primary'   => array(
			'et' => $menus['main']['et'],
			'ru' => $menus['main']['ru'],
			'en' => $menus['main']['en'],
		),
		'secondary' => array(
			'et' => 0,
			'ru' => 0,
			'en' => 0,
		),
		'mobile'    => array(
			'et' => $menus['main']['et'],
			'ru' => $menus['main']['ru'],
			'en' => $menus['main']['en'],
		),
		'footer'    => array(
			'et' => 0,
			'ru' => 0,
			'en' => 0,
		),
	),
);
update_option( 'polylang', $option );

// Materialize the theme mod for the default language (the value the front end
// falls back to before Polylang's filter swaps in the current language).
$default_lang = function_exists( 'pll_default_language' ) ? pll_default_language() : 'et';
$locations    = array(
	'primary'   => $menus['main'][ $default_lang ],
	'secondary' => 0,
	'mobile'    => $menus['main'][ $default_lang ],
	'footer'    => 0,
);
set_theme_mod( 'nav_menu_locations', $locations );

echo "seeded: {$filled} menus filled, {$kept} menus already had items (kept).\n";
foreach ( $menus as $group => $per_lang ) {
	echo "  {$group}: " . implode( ', ', array_map(
		function ( $lang, $id ) {
			return "{$lang}=#{$id}";
		},
		array_keys( $per_lang ),
		$per_lang
	) ) . "\n";
}
