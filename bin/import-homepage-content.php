<?php
/**
 * One-time, idempotent import of the homepage content into the structured
 * content model (custom posts / terms / options).
 *
 * Source of truth: docs/homepage-content.html (identical to Home page post 5).
 * Run with:  ddev wp eval-file bin/import-homepage-content.php
 *
 * Every creation is guarded on the `_mp_imported` marker meta (or, for terms,
 * on the slug) so re-running never creates duplicates: it updates in place.
 *
 * @package mooblipoisid-child
 */

defined( 'ABSPATH' ) || exit;



/**
 * The summary this run collects (created / updated / unchanged / …).
 *
 * File-scope variables are not `$GLOBALS` under WP-CLI's `eval-file`, so the
 * summary lives in one place that both the import steps and the report read.
 */
function &mp_summary() {
	static $summary = null;

	if ( null === $summary ) {
		$summary = array(
			'created'     => array(),
			'updated'     => array(),
			'unchanged'   => array(),
			'thumbnails'  => array(),
			'thumb_notes' => array(),
			'notes'       => array(),
		);
	}

	return $summary;
}

/**
 * Record a result line in the summary under the right bucket.
 */
function mp_record( $status, $line ) {
	$summary = &mp_summary();
	$bucket  = in_array( $status, array( 'created', 'updated', 'unchanged' ), true ) ? $status : 'notes';
	$summary[ $bucket ][] = $line;
}

/**
 * Extract the plain text of an HTML fragment (entities decoded).
 */
function mp_text( $html ) {
	return trim( html_entity_decode( strip_tags( $html ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
}

/**
 * Find or create a media attachment for an image URL that already exists on
 * disk under wp-content/uploads. Nothing is uploaded: the file is registered
 * so WordPress can serve it via `wp_get_attachment_url()` and set it as a
 * featured image.
 */
function mp_attachment_id_for_upload_url( $url ) {
	$existing = mp_attachment_id_by_url( $url );
	if ( $existing ) {
		return $existing;
	}

	$path = wp_parse_url( $url, PHP_URL_PATH );
	if ( ! $path ) {
		return 0;
	}

	$uploads = wp_get_upload_dir();
	$base    = wp_parse_url( $uploads['baseurl'], PHP_URL_PATH );
	if ( $base && 0 === strpos( $path, $base . '/' ) ) {
		$rel = substr( $path, strlen( $base ) + 1 );
	} else {
		$marker = '/wp-content/uploads/';
		$pos    = strpos( $path, $marker );
		if ( false === $pos ) {
			return 0;
		}
		$rel = substr( $path, $pos + strlen( $marker ) );
	}

	$file = $uploads['basedir'] . '/' . $rel;
	if ( ! file_exists( $file ) ) {
		return 0;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';

	$attachment = array(
		'guid'           => $uploads['baseurl'] . '/' . $rel,
		'post_mime_type' => wp_check_filetype( $file, null )['type'] ?: 'image/jpeg',
		'post_title'     => preg_replace( '/\.[^.]+$/', '', wp_basename( $file ) ),
		'post_content'   => '',
		'post_status'    => 'inherit',
	);

	$attach_id = wp_insert_attachment( $attachment, $file );
	if ( is_wp_error( $attach_id ) || ! $attach_id ) {
		return 0;
	}

	$meta = wp_generate_attachment_metadata( $attach_id, $file );
	wp_update_attachment_metadata( $attach_id, $meta );
	return (int) $attach_id;
}

/**
 * Find an existing media attachment for an image URL.
 *
 * `attachment_url_to_postid()` relies on a GUID match which fails when the site
 * URL differs from the URL stored in the markup, so fall back to looking up the
 * file path in `wp_postmeta`.
 */
function mp_attachment_id_by_url( $url ) {
	$id = attachment_url_to_postid( $url );
	if ( $id ) {
		return $id;
	}

	$path = wp_parse_url( $url, PHP_URL_PATH );
	if ( ! $path ) {
		return 0;
	}

	$uploads = wp_get_upload_dir();
	$base    = wp_parse_url( $uploads['baseurl'], PHP_URL_PATH );
	if ( $base && 0 === strpos( $path, $base . '/' ) ) {
		$path = substr( $path, strlen( $base ) + 1 );
	} else {
		$marker = '/wp-content/uploads/';
		$pos    = strpos( $path, $marker );
		if ( false === $pos ) {
			return 0;
		}
		$path = substr( $path, $pos + strlen( $marker ) );
	}

	global $wpdb;
	$ids = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value = %s LIMIT 1",
			$path
		)
	);

	return $ids ? (int) $ids[0] : 0;
}

/**
 * The language the import writes (the site's default/source language).
 *
 * The import copies the Estonian homepage, so every entry it creates belongs to
 * Estonian: Polylang would otherwise leave new entries without a language and
 * the homepage queries (which are language-aware) would not find them.
 */
function mp_import_language() {
	return function_exists( 'pll_default_language' ) ? (string) pll_default_language( 'slug' ) : 'et';
}

/**
 * Upsert a post. Matches first on the stable `_mp_import_key` marker (unique
 * per content item), then on exact post title (any status) within the type.
 * Creates only when neither matches.
 *
 * @return array{ID:int,status:string} status = created|updated|unchanged
 */
function mp_upsert_post( $type, $import_key, $title, $fields, $menu_order, $thumb_url = '' ) {
	global $wpdb;

	$existing = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_mp_import_key' AND meta_value = %s LIMIT 1",
			$type . ':' . $import_key
		)
	);

	if ( ! $existing ) {
		$by_title = get_posts(
			array(
				'post_type'      => $type,
				'title'          => $title,
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);
		$existing = $by_title ? (int) $by_title[0] : 0;
	}

	if ( $existing ) {
		$post    = get_post( $existing );
		$changes = array();

		if ( $post->post_title !== $title ) {
			$changes['post_title'] = $title;
		}
		if ( isset( $fields['post_content'] ) && $post->post_content !== $fields['post_content'] ) {
			$changes['post_content'] = $fields['post_content'];
		}
		if ( isset( $fields['post_excerpt'] ) && $post->post_excerpt !== $fields['post_excerpt'] ) {
			$changes['post_excerpt'] = $fields['post_excerpt'];
		}
		if ( (int) $post->menu_order !== (int) $menu_order ) {
			$changes['menu_order'] = (int) $menu_order;
		}
		if ( 'publish' !== $post->post_status ) {
			$changes['post_status'] = 'publish';
		}

		if ( $changes ) {
			$changes['ID'] = $existing;
			wp_update_post( $changes );
		}

		// Extra meta (e.g. mp_location_type, mp_role, mp_week, mp_rating).
		foreach ( $fields as $key => $value ) {
			if ( in_array( $key, array( 'post_content', 'post_excerpt' ), true ) ) {
				continue;
			}
			$before = get_post_meta( $existing, $key, true );
			if ( (string) $before !== (string) $value ) {
				update_post_meta( $existing, $key, $value );
				$changes[ $key ] = $value;
			}
		}

		if ( $thumb_url ) {
			$thumb_id = mp_attachment_id_for_upload_url( $thumb_url );
			if ( $thumb_id && (int) get_post_thumbnail_id( $existing ) !== $thumb_id ) {
				set_post_thumbnail( $existing, $thumb_id );
				$changes['_thumbnail_id'] = $thumb_id;
			}
		}

		// Persist the import marker so re-runs stay idempotent.
		if ( get_post_meta( $existing, '_mp_import_key', true ) !== $type . ':' . $import_key ) {
			update_post_meta( $existing, '_mp_import_key', $type . ':' . $import_key );
		}
		if ( get_post_meta( $existing, '_mp_imported', true ) !== '1' ) {
			update_post_meta( $existing, '_mp_imported', '1' );
		}

		// Entries created before the content model was localised have no
		// language yet: the homepage queries are language-aware, so give them
		// the source language (idempotent, never overwrites a set language).
		if ( function_exists( 'pll_get_post_language' ) && ! pll_get_post_language( $existing ) ) {
			pll_set_post_language( $existing, mp_import_language() );
		}

		$status = $changes ? 'updated' : 'unchanged';
		return array( 'ID' => $existing, 'status' => $status );
	}

	$postarr = array(
		'post_type'    => $type,
		'post_status'  => 'publish',
		'post_title'   => $title,
		'post_content' => isset( $fields['post_content'] ) ? $fields['post_content'] : '',
		'post_excerpt' => isset( $fields['post_excerpt'] ) ? $fields['post_excerpt'] : '',
		'menu_order'   => (int) $menu_order,
	);

	$new_id = wp_insert_post( $postarr, true );
	if ( is_wp_error( $new_id ) ) {
		echo 'ERROR creating ' . $type . ' "' . $title . '": ' . $new_id->get_error_message() . "\n";
		return array( 'ID' => 0, 'status' => 'error' );
	}

	foreach ( $fields as $key => $value ) {
		if ( in_array( $key, array( 'post_content', 'post_excerpt' ), true ) ) {
			continue;
		}
		update_post_meta( $new_id, $key, $value );
	}

	if ( $thumb_url ) {
		$thumb_id = mp_attachment_id_for_upload_url( $thumb_url );
		if ( $thumb_id ) {
			set_post_thumbnail( $new_id, $thumb_id );
		}
	}

	update_post_meta( $new_id, '_mp_import_key', $type . ':' . $import_key );
	update_post_meta( $new_id, '_mp_imported', '1' );

	if ( function_exists( 'pll_set_post_language' ) ) {
		pll_set_post_language( $new_id, mp_import_language() );
	}

	return array( 'ID' => $new_id, 'status' => 'created' );
}

/**
 * Upsert a hierarchical `sector` term by name, with image + order meta.
 */
function mp_upsert_sector( $name, $description, $image_url, $order ) {
	$name        = html_entity_decode( $name, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	$description = html_entity_decode( $description, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	$term        = get_term_by( 'name', $name, 'sector' );
	if ( ! $term ) {
		$term = get_term_by( 'slug', sanitize_title( $name ), 'sector' );
	}

	if ( $term && ! is_wp_error( $term ) ) {
		$term_id = (int) $term->term_id;
		$changes = array();
		if ( $term->description !== $description ) {
			wp_update_term( $term_id, 'sector', array( 'description' => $description ) );
			$changes[] = 'description';
		}
		// Terms created before the content model was localised have no
		// language yet: give them the source language (idempotent).
		if ( function_exists( 'pll_get_term_language' ) && ! pll_get_term_language( $term_id ) ) {
			pll_set_term_language( $term_id, mp_import_language() );
		}
		$img_before = get_term_meta( $term_id, 'sector_image', true );
		if ( (string) $img_before !== (string) $image_url ) {
			update_term_meta( $term_id, 'sector_image', $image_url );
			$changes[] = 'sector_image';
		}
		$ord_before = get_term_meta( $term_id, 'sector_order', true );
		if ( (string) $ord_before !== (string) $order ) {
			update_term_meta( $term_id, 'sector_order', $order );
			$changes[] = 'sector_order';
		}
		return array( 'term_id' => $term_id, 'status' => $changes ? 'updated' : 'unchanged' );
	}

	$result = wp_insert_term( $name, 'sector', array( 'description' => $description ) );
	if ( is_wp_error( $result ) ) {
		echo 'ERROR creating sector "' . $name . '": ' . $result->get_error_message() . "\n";
		return array( 'term_id' => 0, 'status' => 'error' );
	}

	$term_id = (int) $result['term_id'];
	update_term_meta( $term_id, 'sector_image', $image_url );
	update_term_meta( $term_id, 'sector_order', $order );

	if ( function_exists( 'pll_set_term_language' ) ) {
		pll_set_term_language( $term_id, mp_import_language() );
	}

	return array( 'term_id' => $term_id, 'status' => 'created' );
}

/**
 * Give a freshly created tag the next term relationship, in creation order.
 *
 * The homepage renders a project's tags in term-relationship id order (that is
 * the order the editor added them). New relationships are numbered from the
 * shared term id sequence, so a tag created later can end up with a *smaller*
 * id than a tag that should render after it (as happened with the first
 * translations). Re-numbering keeps the id order identical to the creation
 * order the import uses. Idempotent: an id that is already the largest is left
 * alone.
 *
 * @param int $term_id `project_tag` term whose relationships are re-numbered.
 * @return void
 */
function mp_bump_tag_relationships( $term_id ) {
	global $wpdb;

	$tt_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT term_taxonomy_id FROM {$wpdb->term_taxonomy} WHERE term_id = %d AND taxonomy = 'project_tag'", (int) $term_id ) );
	if ( ! $tt_id ) {
		return;
	}

	$ids = array_map( 'intval', (array) $wpdb->get_col( "SELECT term_relationship_id FROM {$wpdb->term_relationships} WHERE term_taxonomy_id IN (SELECT term_taxonomy_id FROM {$wpdb->term_taxonomy} WHERE taxonomy = 'project_tag')" ) );
	$max = $ids ? max( $ids ) : 0;

	foreach ( (array) $wpdb->get_col( $wpdb->prepare( "SELECT term_relationship_id FROM {$wpdb->term_relationships} WHERE term_taxonomy_id = %d", $tt_id ) ) as $rel_id ) {
		if ( (int) $rel_id < $max ) {
			$wpdb->update( $wpdb->term_relationships, array( 'term_relationship_id' => $max + 1 ), array( 'term_relationship_id' => (int) $rel_id ) );
		}
	}
}

/**
 * Upsert a flat `project_tag` term by name. Returns term_id.
 */
function mp_upsert_tag( $name ) {
	// `wp_insert_term()` applies `sanitize_term()` which can double-encode
	// ampersands (e.g. "Glass & Oak" -> "Glass &amp; Oak"), so decode first.
	$name = html_entity_decode( $name, ENT_QUOTES | ENT_HTML5, 'UTF-8' );

	/*
	 * Load the terms without a name filter: `get_term_by( 'name', … )` compares
	 * with `=` so it misses names stored HTML-encoded, and it would otherwise
	 * leave the encoded duplicates ("Glass &amp; Oak • Tallinn") in place.
	 */
	$term  = null;
	$terms = get_terms(
		array(
			'taxonomy'               => 'project_tag',
			'hide_empty'             => false,
			'lang'                   => '',
			'update_term_meta_cache' => false,
		)
	);

	foreach ( (array) $terms as $candidate ) {
		if ( is_wp_error( $candidate ) ) {
			continue;
		}
		if ( html_entity_decode( $candidate->name, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) === $name ) {
			$term = $candidate;
			break;
		}
	}

	if ( $term instanceof WP_Term ) {
		// A term created by an earlier import may carry the encoded name.
		if ( $term->name !== $name ) {
			wp_update_term( (int) $term->term_id, 'project_tag', array( 'name' => $name ) );
		}
		if ( function_exists( 'pll_get_term_language' ) && ! pll_get_term_language( (int) $term->term_id ) ) {
			pll_set_term_language( (int) $term->term_id, mp_import_language() );
		}

		return (int) $term->term_id;
	}

	$result = wp_insert_term( $name, 'project_tag' );
	if ( is_wp_error( $result ) ) {
		echo 'ERROR creating project_tag "' . $name . '": ' . $result->get_error_message() . "\n";
		return 0;
	}

	if ( function_exists( 'pll_set_term_language' ) ) {
		pll_set_term_language( (int) $result['term_id'], mp_import_language() );
	}

	// New tags get a relationship id above every existing one, so the order in
	// which the import adds them is also the order they render in.
	mp_bump_tag_relationships( (int) $result['term_id'] );

	return (int) $result['term_id'];
}

/* ==========================================================================
 * 1. Sector terms (cards in #hospitality)
 * ========================================================================== */
$mp_sectors = array(
	array(
		'name'  => 'Hospitality',
		'desc'  => 'Service counters and bars with integrated refrigeration, sinks and espresso stations. Built-in banquettes and booths, commercial dining tables, barstools, waiter stations and acoustic wall panelling.',
		'image' => 'https://mooblipoisid.ddev.site/wp-content/uploads/2026/11/mp-7.jpg',
		'order' => 1,
	),
	array(
		'name'  => 'Retail',
		'desc'  => 'Checkout counters, modular shelving, clothing racks, illuminated glass cases, fitting rooms and branded feature walls.',
		'image' => 'https://mooblipoisid.ddev.site/wp-content/uploads/2026/11/mp-10.jpg',
		'order' => 2,
	),
	array(
		'name'  => 'Private',
		'desc'  => 'Bespoke cabinetry, islands and pantries, made in the same workshops that produce our commercial bars.',
		'image' => 'https://mooblipoisid.ddev.site/wp-content/uploads/2026/11/mp-11.jpg',
		'order' => 3,
	),
);

foreach ( $mp_sectors as $sec ) {
	$r = mp_upsert_sector( $sec['name'], $sec['desc'], $sec['image'], $sec['order'] );
	mp_record( $r['status'], 'sector ' . $sec['name'] . ' (term ' . $r['term_id'] . ')' );
}

/* ==========================================================================
 * 2. Range posts (two groups of six product categories, #ranges)
 * ========================================================================== */
$mp_ranges = array(
	// Hospitality group.
	array( 'Bars & Service Counters', 'Integrated refrigeration, sinks, espresso stations and back-bar units.' ),
	array( 'Banquettes & Booths', 'Built-in seating fitted to your floor plan, upholstered in contract-grade fabrics.' ),
	array( 'Dining Tables', 'Compact laminate, sintered stone or treated hardwood tops that take daily abuse.' ),
	array( 'Barstools & Chairs', 'Solid timber and metal frames, built to be stacked, scraped and sat on all day.' ),
	array( 'Waiter Stations', 'Compact service points with POS housing, cutlery drawers and storage.' ),
	array( 'Acoustic Wall Panelling', 'Timber slat and fabric panels that cut noise and add warmth.' ),
	// Retail group.
	array( 'POS & Checkout Counters', 'Cable-managed, durable and designed around your till and queue flow.' ),
	array( 'Modular Display Shelving', 'Reconfigurable systems that adapt as ranges and seasons change.' ),
	array( 'Clothing Racks', 'Timber and metal rails and wall systems built to your brand finish.' ),
	array( 'Illuminated Glass Cases', 'Lit display cabinets for jewellery, cosmetics and premium product.' ),
	array( 'Fitting Room Joinery', 'Complete cubicles with doors, mirrors, hooks and seating.' ),
	array( 'Branded Feature Walls', 'Statement walls that put your identity in front of every customer.' ),
);

foreach ( $mp_ranges as $i => $range ) {
	$r = mp_upsert_post(
		'range',
		'range-' . ( $i + 1 ) . '-' . sanitize_title( $range[0] ),
		$range[0],
		array( 'post_excerpt' => $range[1] ),
		$i + 1
	);
	mp_record( $r['status'], 'range "' . $range[0] . '" (ID ' . $r['ID'] . ')' );
}

/* ==========================================================================
 * 3. Project cards (#projects). First card is the lead -> menu_order 0.
 * ========================================================================== */
$mp_projects = array(
	array(
		'title'  => 'Specialty Coffee Bar',
		'excerpt' => 'Service counter with integrated espresso station and refrigeration, fitted banquettes and acoustic wall slats.',
		'meta'   => 'Tallinn, Estonia • Café & Roastery',
		'tags'   => array( 'Smoked Oak', 'Sintered Stone Top' ),
		'image'  => 'https://mooblipoisid.ddev.site/wp-content/uploads/2026/11/mp-9.jpg',
		'order'  => 0, // Lead card: renders first.
	),
	array(
		'title'  => 'Neighbourhood Bistro',
		'excerpt' => 'Booth seating, compact dining tables and a waiter station designed around a tight floor plan.',
		'meta'   => 'Stockholm • Restaurant',
		'tags'   => array( 'Fitted Booths', 'Fluted Panelling' ),
		'image'  => 'https://mooblipoisid.ddev.site/wp-content/uploads/2026/11/mp-4.jpg',
		'order'  => 1,
	),
	array(
		'title'  => 'Cocktail Bar',
		'excerpt' => 'Bar with stone top, brass back-bar shelving and concealed under-counter refrigeration.',
		'meta'   => 'Helsinki • Bar',
		'tags'   => array( 'Blackened Ash • Helsinki' ),
		'image'  => 'https://mooblipoisid.ddev.site/wp-content/uploads/2026/11/mp-1.jpg',
		'order'  => 2,
	),
	array(
		'title'  => 'Fashion Boutique',
		'excerpt' => 'Clothing racks, fitting rooms, POS counter and a branded feature wall, delivered as one package.',
		'meta'   => 'Stockholm • Retail',
		'tags'   => array( 'White Oak • Stockholm' ),
		'image'  => 'https://mooblipoisid.ddev.site/wp-content/uploads/2026/11/mp-6.jpg',
		'order'  => 3,
	),
	array(
		'title'  => 'Concept Store',
		'excerpt' => 'Illuminated glass display cases and modular shelving that can be rearranged by store staff.',
		'meta'   => 'Tallinn • Retail',
		'tags'   => array( 'Glass & Oak • Tallinn' ),
		'image'  => 'https://mooblipoisid.ddev.site/wp-content/uploads/2026/11/mp-5.jpg',
		'order'  => 4,
	),
);

foreach ( $mp_projects as $proj ) {
	$r = mp_upsert_post(
		'project',
		sanitize_title( $proj['title'] ),
		$proj['title'],
		array(
			'post_excerpt'    => $proj['excerpt'],
			'mp_location_type' => $proj['meta'],
		),
		$proj['order'],
		$proj['image']
	);

	mp_record( $r['status'], 'project "' . $proj['title'] . '" (ID ' . $r['ID'] . ')' );

	if ( $r['ID'] ) {
		$tag_ids = array();
		foreach ( $proj['tags'] as $tag_name ) {
			$tag_id = mp_upsert_tag( $tag_name );
			if ( $tag_id ) {
				$tag_ids[] = $tag_id;
			}
		}
		wp_set_post_terms( $r['ID'], $tag_ids, 'project_tag', false );

		$thumb_id = mp_attachment_id_for_upload_url( $proj['image'] );
		$mp_summary = &mp_summary();
		$mp_summary['thumbnails'][ $proj['title'] ] = $thumb_id;
		if ( ! $thumb_id ) {
			$mp_summary['thumb_notes'][] = $proj['title'] . ': no attachment found/created for ' . $proj['image'];
		}
	}
}

/* ==========================================================================
 * 4. Capability cards (#capabilities)
 * ========================================================================== */
$mp_capabilities = array(
	array( 'CNC Woodworking', 'Precise, repeatable production of counters, panels and seating frames, from one-off bars to multi-site roll-outs.' ),
	array( 'Metalworking', 'Steel and brass frames, footrails, shelving brackets and display structures in finishes that match your interior.' ),
	array( 'Stone Fabrication', 'Sintered stone and natural stone bar tops, cut, polished and fitted with cut-outs for sinks and equipment.' ),
	array( 'Commercial-Grade Surfaces', 'Compact laminate, sintered stone and treated hardwood chosen to resist heat, scratches and spills.' ),
);

foreach ( $mp_capabilities as $i => $cap ) {
	$r = mp_upsert_post( 'capability', 'capability-' . ( $i + 1 ) . '-' . sanitize_title( $cap[0] ), $cap[0], array( 'post_excerpt' => $cap[1] ), $i + 1 );
	mp_record( $r['status'], 'capability "' . $cap[0] . '" (ID ' . $r['ID'] . ')' );
}

/* ==========================================================================
 * 5. Process steps (#process)
 * ========================================================================== */
$mp_steps = array(
	array( 'Brief & Site Survey', 'We review your drawings and concept, then measure the space on site: floor levels, services, equipment and customer flow.', 'Week 01 – 02' ),
	array( 'Design & Shop Drawings', 'Detailed drawings, 3D visuals and material samples, with equipment cut-outs and service routes confirmed before production.', 'Week 03 – 04' ),
	array( 'Workshop Production', 'CNC, metal and stone fabrication in our own workshops, followed by pre-assembly and quality checks before anything ships.', 'Week 05 – 10' ),
	array( 'Installation & Handover', 'Installed by the team that built it, scheduled around your fit-out and opening date, with final adjustments and care instructions at handover.', 'Week 11 – 12' ),
);

foreach ( $mp_steps as $i => $step ) {
	$r = mp_upsert_post(
		'step',
		'step-' . ( $i + 1 ) . '-' . sanitize_title( $step[0] ),
		$step[0],
		array(
			'post_excerpt' => $step[1],
			'mp_week'      => $step[2],
		),
		$i + 1
	);
	mp_record( $r['status'], 'step "' . $step[0] . '" (ID ' . $r['ID'] . ')' );
}

/* ==========================================================================
 * 6. Trust strip facts (#trust)
 * ========================================================================== */
$mp_trust = array(
	array( '18 Years', 'Joinery & shopfitting experience' ),
	array( 'In-House Workshops', 'CNC wood, metal & stone under one roof' ),
	array( 'Built for Service', 'Heat-, scratch- & moisture-resistant surfaces' ),
	array( 'Out-of-Hours Install', 'Minimal downtime for your opening date' ),
);

foreach ( $mp_trust as $i => $fact ) {
	$r = mp_upsert_post( 'trust_stat', 'trust-' . ( $i + 1 ) . '-' . sanitize_title( $fact[0] ), $fact[0], array( 'post_excerpt' => $fact[1] ), $i + 1 );
	mp_record( $r['status'], 'trust_stat "' . $fact[0] . '" (ID ' . $r['ID'] . ')' );
}

/* ==========================================================================
 * 7. Testimonials / Client Stories (#stories)
 *    No `.mp-quote__city` exists in the source markup; the city field is left
 *    empty (it is optional in the content model).
 * ========================================================================== */
$mp_testimonials = array(
	array(
		'title'   => 'The bar was installed on schedule',
		'content' => '“The bar was installed on schedule and we opened on the planned date. Two years of daily service later, the counter still looks as good as it did on day one.”',
		'role'    => 'Owner, Café & Roastery',
		'rating'  => '5',
	),
	array(
		'title'   => 'One supplier handled the counters',
		'content' => '“One supplier handled the counters, shelving, fitting rooms and feature wall. Everything matched, and the install was done before our stock arrived.”',
		'role'    => 'Operations Manager, Fashion Retailer',
		'rating'  => '5',
	),
);

foreach ( $mp_testimonials as $i => $quote ) {
	$r = mp_upsert_post(
		'testimonial',
		'testimonial-' . ( $i + 1 ) . '-' . sanitize_title( $quote['title'] ),
		$quote['title'],
		array(
			'post_content' => $quote['content'],
			'mp_role'      => $quote['role'],
			'mp_rating'    => $quote['rating'],
		),
		$i + 1
	);
	mp_record( $r['status'], 'testimonial "' . $quote['title'] . '" (ID ' . $r['ID'] . ')' );
}

/* ==========================================================================
 * 8. Homepage Settings option (hero / ethos / consultation text)
 * ========================================================================== */
$mp_settings = array(
	'hero_headline'    => 'Custom furniture for venues that never stop working.',
	'hero_sub'         => 'Bars, counters, seating and shopfit joinery for cafés, restaurants and retail brands. Designed, built and installed by one team in Tallinn.',
	'hero_cta1_label'  => 'Request a Quote',
	'hero_cta1_href'   => '#consultation',
	'hero_cta2_label'  => 'See Commercial Projects',
	'hero_cta2_href'   => '#projects',
	'hero_image'       => 'https://mooblipoisid.ddev.site/wp-content/uploads/2026/11/mp-8.jpg',
	'hero_image_alt'   => 'Café-bar interior with banquette seating and brass footrail (placeholder, awaiting real photography)',
	'ethos_eyebrow'    => 'Ethos & Provenance',
	'ethos_quote'      => '“Furniture for public use should look beautiful and outlast the lease.”',
	'ethos_body'       => 'A bar or a shop floor takes more punishment in a month than a home does in a year. We build commercial furniture with solid timber and multi-ply birch, joined properly, finished in hard-wearing surfaces and designed so that worn parts can be repaired rather than replaced.',
	'consult_eyebrow'  => 'Start a Dialogue',
	'consult_headline' => 'Let’s plan your venue or store.',
	'consult_intro'    => 'Send us your floor plans, architect drawings or concept boards. We respond with initial feedback and a budget direction within 48 hours.',
);

$mp_before = get_option( 'mp_homepage_settings', array() );
$mp_before = is_array( $mp_before ) ? $mp_before : array();

/*
 * The option stores one field set per language (`array( 'et' => … )`), while
 * older runs stored the Estonian fields flat. A flat value is re-keyed under
 * the source language here (idempotent) so a re-run never turns the option back
 * into the flat shape and never overwrites the other languages.
 */
$mp_language = mp_import_language();
$mp_localised = true;
foreach ( array_keys( $mp_settings ) as $mp_field ) {
	if ( array_key_exists( $mp_field, $mp_before ) ) {
		$mp_localised = false;
		break;
	}
}
if ( ! $mp_localised ) {
	$mp_before = array( $mp_language => $mp_before );
}

$mp_lang_before = isset( $mp_before[ $mp_language ] ) && is_array( $mp_before[ $mp_language ] ) ? $mp_before[ $mp_language ] : array();
$mp_after       = wp_parse_args( $mp_settings, $mp_lang_before );

if ( $mp_lang_before === $mp_after ) {
	mp_record( 'unchanged', 'mp_homepage_settings option (' . $mp_language . ')' );
} else {
	// Only write keys we manage so editor-added keys survive a re-run.
	$mp_before[ $mp_language ] = array_merge( $mp_lang_before, $mp_settings );
	update_option( 'mp_homepage_settings', $mp_before );
	mp_record( 'updated', 'mp_homepage_settings option (' . $mp_language . ')' );
}

/* ==========================================================================
 * Report
 * ========================================================================== */
$mp_summary = &mp_summary();

echo "=== mp import summary ===\n";
foreach ( array( 'created', 'updated', 'unchanged' ) as $bucket ) {
	echo str_pad( $bucket . ':', 11 ) . count( $mp_summary[ $bucket ] ) . "\n";
	foreach ( $mp_summary[ $bucket ] as $line ) {
		echo '   ' . ( 'created' === $bucket ? '+' : ( 'updated' === $bucket ? '~' : '=' ) ) . ' ' . $line . "\n";
	}
}

echo "thumbnails (attachment IDs found by file):\n";
foreach ( $mp_summary['thumbnails'] as $title => $tid ) {
	echo '  ' . $title . ' -> ' . ( $tid ? $tid : 'NOT FOUND' ) . "\n";
}
foreach ( $mp_summary['thumb_notes'] as $note ) {
	echo '  note: ' . $note . "\n";
}
echo "done.\n";