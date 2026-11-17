<?php
/**
 * Homepage content model: post types, taxonomies, entry details and Homepage Settings.
 *
 * Everything a non-technical editor needs to maintain the homepage lives here:
 *
 *  - Content entries (custom post types)   -> project, testimonial, range, capability, step, trust_stat
 *  - Grouping terms (taxonomies)           -> sector, project_tag   (on Projects)
 *  - Extra entry details (post meta)       -> mp_location_type, mp_role, mp_city, mp_rating, mp_week
 *  - One-off homepage text (single option) -> mp_homepage_settings (stored per language)
 *
 * Multilingual (Polylang):
 *  - The six content types and the two taxonomies are declared translatable by the
 *    theme (`mooblipoisid_polylang_translatable_content()`), so the settings are
 *    enforced in code: the content model stays complete on every deploy, and the
 *    entries are filtered by the active language automatically.
 *  - `mp_homepage_settings` holds one set of fields per language
 *    ( `array( 'et' => array( … ), 'ru' => array( … ), 'en' => array( … ) )` ) and
 *    `mooblipoisid_get_homepage_settings()` resolves the active language, falling
 *    back to Estonian for any field a language does not have yet.
 *
 * All content types are registered `public => true` + `show_ui => true` so that a
 * later multilingual plugin (Polylang) recognises them as translatable, while
 * `has_archive => false` and `exclude_from_search => true` keep them off the
 * public site: they only render as homepage cards for now.
 *
 * @package mooblipoisid-child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Text domain used by every label in this file.
 */
define( 'MOOBLIPOISID_TD', 'mooblipoisid-child' );

/* ==========================================================================
 * 1. Content entries (custom post types)
 * ========================================================================== */

/**
 * Configuration for every homepage content type.
 *
 * `label`  – plural menu name, `singular` – one entry, `desc` – admin blurb.
 * `supports` decides which standard editor panels the editor sees.
 *
 * @return array[]
 */
function mooblipoisid_content_post_types() {
	return array(
		'project'    => array(
			'label'       => __( 'Projects', 'mooblipoisid-child' ),
			'singular'    => __( 'Project', 'mooblipoisid-child' ),
			'desc'        => __( 'Featured project cards shown in the "Recent Venue & Retail Projects" section of the homepage. Use the short description for the card text and the featured image for the card photo.', 'mooblipoisid-child' ),
			'menu_icon'   => 'dashicons-portfolio',
			'supports'    => array( 'title', 'excerpt', 'thumbnail', 'page-attributes' ),
			'menu_position' => 21,
		),
		'testimonial' => array(
			'label'       => __( 'Client Stories', 'mooblipoisid-child' ),
			'singular'    => __( 'Client Story', 'mooblipoisid-child' ),
			'desc'        => __( 'Client quotes shown in the "What Our Clients Say" section of the homepage. Put the quote in the main text box and the person\'s details in the fields below.', 'mooblipoisid-child' ),
			'menu_icon'   => 'dashicons-format-quote',
			'supports'    => array( 'title', 'editor', 'excerpt', 'page-attributes' ),
			'menu_position' => 22,
		),
		'range'       => array(
			'label'       => __( 'Product Ranges', 'mooblipoisid-child' ),
			'singular'    => __( 'Product Range', 'mooblipoisid-child' ),
			'desc'        => __( 'The product range cards shown in the "The Full Commercial Range" section of the homepage.', 'mooblipoisid-child' ),
			'menu_icon'   => 'dashicons-grid-view',
			'supports'    => array( 'title', 'excerpt', 'thumbnail', 'page-attributes' ),
			'menu_position' => 23,
		),
		'capability'  => array(
			'label'       => __( 'Capabilities', 'mooblipoisid-child' ),
			'singular'    => __( 'Capability', 'mooblipoisid-child' ),
			'desc'        => __( 'The capability cards shown in the "One Workshop" section of the homepage (what we can make in-house).', 'mooblipoisid-child' ),
			'menu_icon'   => 'dashicons-hammer',
			'supports'    => array( 'title', 'excerpt', 'page-attributes' ),
			'menu_position' => 24,
		),
		'step'        => array(
			'label'       => __( 'Process Steps', 'mooblipoisid-child' ),
			'singular'    => __( 'Process Step', 'mooblipoisid-child' ),
			'desc'        => __( 'The numbered steps shown in the "From Brief to Opening Day" section of the homepage. Put the step name in the title and the week range in the field below.', 'mooblipoisid-child' ),
			'menu_icon'   => 'dashicons-list-view',
			'supports'    => array( 'title', 'excerpt', 'page-attributes' ),
			'menu_position' => 25,
		),
		'trust_stat'  => array(
			'label'       => __( 'Trust Facts', 'mooblipoisid-child' ),
			'singular'    => __( 'Trust Fact', 'mooblipoisid-child' ),
			'desc'        => __( 'The short facts shown in the strip below the hero. The title is the big number or value (e.g. "18 Years"), the short description is the caption under it.', 'mooblipoisid-child' ),
			'menu_icon'   => 'dashicons-awards',
			'supports'    => array( 'title', 'excerpt', 'page-attributes' ),
			'menu_position' => 26,
		),
	);
}

/**
 * Register the homepage content types.
 */
function mooblipoisid_register_content_post_types() {
	foreach ( mooblipoisid_content_post_types() as $slug => $config ) {
		$plural   = $config['label'];
		$singular = $config['singular'];

		register_post_type(
			$slug,
			array(
				'labels'              => array(
					'name'               => $plural,
					'singular_name'      => $singular,
					'menu_name'          => $plural,
					'name_admin_bar'     => $singular,
					'add_new'            => __( 'Add New', 'mooblipoisid-child' ),
					'add_new_item'       => sprintf( __( 'Add New %s', 'mooblipoisid-child' ), $singular ),
					'edit_item'          => sprintf( __( 'Edit %s', 'mooblipoisid-child' ), $singular ),
					'new_item'           => sprintf( __( 'New %s', 'mooblipoisid-child' ), $singular ),
					'view_item'          => sprintf( __( 'View %s', 'mooblipoisid-child' ), $singular ),
					'view_items'         => sprintf( __( 'View %s', 'mooblipoisid-child' ), $plural ),
					'search_items'       => sprintf( __( 'Search %s', 'mooblipoisid-child' ), $plural ),
					'not_found'          => sprintf( __( 'No %s found.', 'mooblipoisid-child' ), strtolower( $plural ) ),
					'not_found_in_trash' => sprintf( __( 'No %s found in Trash.', 'mooblipoisid-child' ), strtolower( $plural ) ),
					'all_items'          => sprintf( __( 'All %s', 'mooblipoisid-child' ), $plural ),
					'featured_image'     => __( 'Card Image', 'mooblipoisid-child' ),
					'set_featured_image' => __( 'Set card image', 'mooblipoisid-child' ),
					'remove_featured_image' => __( 'Remove card image', 'mooblipoisid-child' ),
				),
				'description'         => $config['desc'],
				'public'              => true,
				'show_ui'             => true,
				'show_in_rest'        => true,
				'has_archive'         => false,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'rewrite'             => false,
				'query_var'           => false,
				'menu_icon'           => $config['menu_icon'],
				'menu_position'       => $config['menu_position'],
				'supports'            => $config['supports'],
			)
		);
	}
}
add_action( 'init', 'mooblipoisid_register_content_post_types' );

/* ==========================================================================
 * 1b. Multilingual content model (Polylang)
 * ========================================================================== */

/**
 * The content model entries that have per-language versions.
 *
 * @return array {
 *     @type string[] $post_types Content types with a translation per language.
 *     @type string[] $taxonomies Grouping terms with a translation per language.
 * }
 */
function mooblipoisid_translatable_content() {
	return array(
		'post_types' => array( 'project', 'testimonial', 'range', 'capability', 'step', 'trust_stat' ),
		'taxonomies' => array( 'sector', 'project_tag' ),
	);
}

/**
 * Tell Polylang which content model entries are translatable.
 *
 * Polylang is configurable in its settings screen, but the theme declares the
 * content model here as well so the configuration travels with the code: the
 * homepage content model is complete on every environment, and the language
 * filter on queries can never be switched off by accident. The screen shows
 * these ticked and disabled.
 *
 * Must be hooked early (before `after_setup_theme`), the same as Polylang's own
 * documentation requires.
 *
 * @param string[] $post_types Post types offered for translation.
 * @param bool     $is_settings Whether the list is being shown in Polylang's settings.
 * @return string[] Post types to translate.
 */
function mooblipoisid_polylang_translatable_post_types( $post_types, $is_settings = false ) {
	if ( $is_settings ) {
		// The settings screen lists what is offered, not what is enforced.
		return $post_types;
	}

	$types = (array) $post_types;
	foreach ( mooblipoisid_translatable_content()['post_types'] as $post_type ) {
		$types[ $post_type ] = $post_type;
	}

	return $types;
}
add_filter( 'pll_get_post_types', 'mooblipoisid_polylang_translatable_post_types', 10, 2 );

/**
 * Tell Polylang which grouping terms are translatable.
 *
 * @param string[] $taxonomies Taxonomies offered for translation.
 * @param bool     $is_settings Whether the list is being shown in Polylang's settings.
 * @return string[] Taxonomies to translate.
 */
function mooblipoisid_polylang_translatable_taxonomies( $taxonomies, $is_settings = false ) {
	if ( $is_settings ) {
		// The settings screen lists what is offered, not what is enforced.
		return $taxonomies;
	}

	$taxes = (array) $taxonomies;
	foreach ( mooblipoisid_translatable_content()['taxonomies'] as $taxonomy ) {
		$taxes[ $taxonomy ] = $taxonomy;
	}

	return $taxes;
}
add_filter( 'pll_get_taxonomies', 'mooblipoisid_polylang_translatable_taxonomies', 10, 2 );

/**
 * Mirror those settings into the `polylang` option so both agree.
 *
 * The option is what `PLL()->model->get_translated_post_types()` and
 * `get_translated_taxonomies()` resolve first, and what the Polylang settings
 * screen submits. Every other key of the option is left untouched.
 */
function mooblipoisid_polylang_sync_translatable_content() {
	$content = mooblipoisid_translatable_content();
	$option  = get_option( 'polylang', array() );
	$option  = is_array( $option ) ? $option : array();
	$before  = $option;

	foreach ( array( 'post_types', 'taxonomies' ) as $key ) {
		$stored         = isset( $option[ $key ] ) ? (array) $option[ $key ] : array();
		$option[ $key ] = array_values( array_unique( array_merge( $stored, $content[ $key ] ) ) );
		sort( $option[ $key ] );
	}

	if ( $option !== $before ) {
		update_option( 'polylang', $option );
	}
}
add_action( 'admin_init', 'mooblipoisid_polylang_sync_translatable_content', 5 );

/**
 * Plain-language names for the standard editor panels, per content type.
 *
 * Keeps the admin non-technical: "Excerpt" becomes "Short description", etc.
 *
 * @return array[]
 */
function mooblipoisid_editor_panel_labels() {
	return array(
		'project'    => array(
			'title'     => __( 'Project Name', 'mooblipoisid-child' ),
			'excerpt'   => __( 'Short Description (shown on the card)', 'mooblipoisid-child' ),
			'thumbnail' => __( 'Card Image', 'mooblipoisid-child' ),
		),
		'testimonial' => array(
			'title'   => __( 'Name / Company', 'mooblipoisid-child' ),
			'editor'  => __( 'The Client\'s Quote', 'mooblipoisid-child' ),
			'excerpt' => __( 'Short Version of the Quote (optional)', 'mooblipoisid-child' ),
		),
		'range'      => array(
			'title'     => __( 'Range Name', 'mooblipoisid-child' ),
			'excerpt'   => __( 'Short Description', 'mooblipoisid-child' ),
			'thumbnail' => __( 'Card Image', 'mooblipoisid-child' ),
		),
		'capability' => array(
			'title'   => __( 'Capability Name', 'mooblipoisid-child' ),
			'excerpt' => __( 'Short Description', 'mooblipoisid-child' ),
		),
		'step'       => array(
			'title'   => __( 'Step Name', 'mooblipoisid-child' ),
			'excerpt' => __( 'Short Description', 'mooblipoisid-child' ),
		),
		'trust_stat' => array(
			'title'   => __( 'Value (e.g. "18 Years")', 'mooblipoisid-child' ),
			'excerpt' => __( 'Caption (the line under the value)', 'mooblipoisid-child' ),
		),
	);
}

/**
 * Rename the standard editor panels into plain language.
 *
 * Runs on `register_post_type_args` so the panel headings read like the rest of
 * the admin ("Card Image", "Short Description", "The Client's Quote", …).
 *
 * @param array  $args      Registration arguments for the post type.
 * @param string $post_type Post type being registered.
 * @return array Adjusted arguments.
 */
function mooblipoisid_adjust_editor_panel_labels( $args, $post_type ) {
	$map = mooblipoisid_editor_panel_labels();

	if ( ! isset( $map[ $post_type ] ) ) {
		return $args;
	}

	$labels = isset( $args['labels'] ) ? (array) $args['labels'] : array();

	foreach ( $map[ $post_type ] as $panel => $label ) {
		switch ( $panel ) {
			case 'title':
				$labels['name']         = $label;
				$labels['add_new_item'] = sprintf( __( 'Add New %s', 'mooblipoisid-child' ), $label );
				$labels['edit_item']    = sprintf( __( 'Edit %s', 'mooblipoisid-child' ), $label );
				$labels['new_item']     = $label;
				break;
			case 'excerpt':
				$labels['excerpt'] = $label;
				break;
			case 'editor':
				$labels['editor'] = $label;
				break;
			case 'thumbnail':
				$labels['featured_image']        = $label;
				$labels['set_featured_image']    = sprintf( __( 'Set %s', 'mooblipoisid-child' ), $label );
				$labels['remove_featured_image'] = sprintf( __( 'Remove %s', 'mooblipoisid-child' ), $label );
				break;
		}
	}

	$args['labels'] = $labels;

	return $args;
}
add_filter( 'register_post_type_args', 'mooblipoisid_adjust_editor_panel_labels', 10, 2 );

/**
 * Name the "Order on Homepage" panel in plain language.
 *
 * The block editor reads `__pageAttributesLabel` from the editor settings, so
 * the Attributes panel heading becomes "Order on Homepage" on our content types.
 *
 * @param array  $settings Editor settings.
 * @param string $context  Editor context.
 * @return array Adjusted settings.
 */
function mooblipoisid_editor_settings_labels( $settings ) {
	$settings['__pageAttributesLabel'] = __( 'Order on Homepage', 'mooblipoisid-child' );
	return $settings;
}
add_filter( 'block_editor_settings_all', 'mooblipoisid_editor_settings_labels' );

/**
 * Friendly names for the fields of each entry (used in admin help text).
 *
 * @return array[]
 */
function mooblipoisid_meta_fields() {
	return array(
		'project'    => array(
			'mp_location_type' => array(
				'label'       => __( 'Location • Type', 'mooblipoisid-child' ),
				'type'        => 'text',
				'description' => __( 'The small line above the project name, e.g. "Tallinn, Estonia • Café & Roastery".', 'mooblipoisid-child' ),
			),
		),
		'testimonial' => array(
			'mp_role'   => array(
				'label'       => __( 'Author Role', 'mooblipoisid-child' ),
				'type'        => 'text',
				'description' => __( 'Who gave the quote and what they do, e.g. "Owner, Café & Roastery".', 'mooblipoisid-child' ),
			),
			'mp_city'   => array(
				'label'       => __( 'City', 'mooblipoisid-child' ),
				'type'        => 'text',
				'description' => __( 'The city or country the client is based in, e.g. "Tallinn". Shown next to the quote.', 'mooblipoisid-child' ),
			),
			'mp_rating' => array(
				'label'       => __( 'Star Rating', 'mooblipoisid-child' ),
				'type'        => 'number',
				'description' => __( 'How many stars to show, from 1 to 5. Leave empty to show no stars.', 'mooblipoisid-child' ),
			),
		),
		'step'       => array(
			'mp_week' => array(
				'label'       => __( 'Week Label', 'mooblipoisid-child' ),
				'type'        => 'text',
				'description' => __( 'The time line shown at the bottom of the step, e.g. "Week 01 – 02".', 'mooblipoisid-child' ),
			),
		),
	);
}

/**
 * Register the entry detail meta keys (needed for the block editor / REST).
 */
function mooblipoisid_register_entry_meta() {
	foreach ( mooblipoisid_meta_fields() as $post_type => $fields ) {
		foreach ( $fields as $key => $field ) {
			register_post_meta(
				$post_type,
				$key,
				array(
					'type'              => 'string',
					'single'            => true,
					'show_in_rest'      => true,
					'sanitize_callback' => 'sanitize_text_field',
					'auth_callback'     => function () {
						return current_user_can( 'edit_posts' );
					},
				)
			);
		}
	}
}
add_action( 'init', 'mooblipoisid_register_entry_meta' );

/**
 * Render the "Entry Details" meta box for a content type.
 *
 * @param WP_Post $post Post being edited.
 */
function mooblipoisid_render_entry_meta_box( $post ) {
	$fields = mooblipoisid_meta_fields();
	$fields = isset( $fields[ $post->post_type ] ) ? $fields[ $post->post_type ] : array();

	if ( ! $fields ) {
		return;
	}

	wp_nonce_field( 'mooblipoisid_entry_meta', 'mooblipoisid_entry_meta_nonce' );
	?>
	<p class="description"><?php esc_html_e( 'A few extra details shown on the homepage card. Everything here is optional.', 'mooblipoisid-child' ); ?></p>
	<table class="form-table" role="presentation">
		<tbody>
		<?php foreach ( $fields as $key => $field ) : ?>
			<?php
			$value = get_post_meta( $post->ID, $key, true );
			$value = is_scalar( $value ) ? (string) $value : '';
			?>
			<tr>
				<th scope="row">
					<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
				</th>
				<td>
					<?php if ( 'number' === $field['type'] ) : ?>
						<input type="number" min="1" max="5" step="1" class="small-text" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $value ); ?>" />
					<?php else : ?>
						<input type="text" class="large-text" id="<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $value ); ?>" />
					<?php endif; ?>
					<p class="description"><?php echo esc_html( $field['description'] ); ?></p>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php
}

/**
 * Save the entry details with nonce, capability and sanitization checks.
 *
 * @param int $post_id Post being saved.
 */
function mooblipoisid_save_entry_meta( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}
	if ( ! isset( $_POST['mooblipoisid_entry_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mooblipoisid_entry_meta_nonce'] ) ), 'mooblipoisid_entry_meta' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$post_type = get_post_type( $post_id );
	$fields    = mooblipoisid_meta_fields();

	if ( ! isset( $fields[ $post_type ] ) ) {
		return;
	}

	foreach ( $fields[ $post_type ] as $key => $field ) {
		if ( ! isset( $_POST[ $key ] ) ) {
			continue;
		}

		$raw = wp_unslash( $_POST[ $key ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput

		if ( 'number' === $field['type'] ) {
			$number = '' === $raw ? '' : (int) $raw;
			$number = ( '' !== $number ) ? max( 1, min( 5, $number ) ) : '';
			update_post_meta( $post_id, $key, (string) $number );
			continue;
		}

		update_post_meta( $post_id, $key, sanitize_text_field( $raw ) );
	}
}
add_action( 'save_post', 'mooblipoisid_save_entry_meta' );

/**
 * Add the "Entry Details" meta box to every content type that needs one.
 */
function mooblipoisid_add_entry_meta_boxes() {
	foreach ( array_keys( mooblipoisid_meta_fields() ) as $post_type ) {
		add_meta_box(
			'mooblipoisid_entry_details',
			__( 'Entry Details', 'mooblipoisid-child' ),
			'mooblipoisid_render_entry_meta_box',
			$post_type,
			'normal',
			'default'
		);
	}
}
add_action( 'add_meta_boxes', 'mooblipoisid_add_entry_meta_boxes' );

/**
 * Tell the block editor to show the meta box (it is hidden by default there).
 *
 * @param array $settings Editor settings.
 * @return array Adjusted settings.
 */
function mooblipoisid_enable_meta_boxes_in_block_editor( $settings ) {
	$settings['enableCustomFields'] = true;
	return $settings;
}
add_filter( 'block_editor_settings_all', 'mooblipoisid_enable_meta_boxes_in_block_editor' );

/* ==========================================================================
 * 2. Grouping terms (taxonomies)
 * ========================================================================== */

/**
 * Register the two taxonomies used by Projects.
 */
function mooblipoisid_register_project_taxonomies() {
	// Sectors: the big groupings shown as cards (Hospitality / Retail / Private).
	register_taxonomy(
		'sector',
		array( 'project' ),
		array(
			'labels'            => array(
				'name'              => __( 'Sectors', 'mooblipoisid-child' ),
				'singular_name'     => __( 'Sector', 'mooblipoisid-child' ),
				'menu_name'         => __( 'Sectors', 'mooblipoisid-child' ),
				'all_items'         => __( 'All Sectors', 'mooblipoisid-child' ),
				'edit_item'         => __( 'Edit Sector', 'mooblipoisid-child' ),
				'view_item'         => __( 'View Sector', 'mooblipoisid-child' ),
				'update_item'       => __( 'Update Sector', 'mooblipoisid-child' ),
				'add_new_item'      => __( 'Add New Sector', 'mooblipoisid-child' ),
				'new_item_name'     => __( 'New Sector Name', 'mooblipoisid-child' ),
				'search_items'      => __( 'Search Sectors', 'mooblipoisid-child' ),
				'not_found'         => __( 'No sectors found.', 'mooblipoisid-child' ),
				'add_or_remove_items' => __( 'Add or remove sectors', 'mooblipoisid-child' ),
			),
			'description'       => __( 'The sector cards on the homepage (e.g. Hospitality, Retail, Private). The sector description is the card text, the sector image is the card photo and the sort order decides the card order.', 'mooblipoisid-child' ),
			'public'            => true,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'hierarchical'      => true,
			'rewrite'           => false,
			'query_var'         => false,
			'show_admin_column' => true,
			'meta_box_cb'       => false,
		)
	);

	// Project tags: materials and features used on a project (e.g. "Smoked Oak").
	register_taxonomy(
		'project_tag',
		array( 'project' ),
		array(
			'labels'            => array(
				'name'              => __( 'Materials & Features', 'mooblipoisid-child' ),
				'singular_name'     => __( 'Material / Feature', 'mooblipoisid-child' ),
				'menu_name'         => __( 'Materials & Features', 'mooblipoisid-child' ),
				'all_items'         => __( 'All Materials & Features', 'mooblipoisid-child' ),
				'edit_item'         => __( 'Edit Material / Feature', 'mooblipoisid-child' ),
				'view_item'         => __( 'View Material / Feature', 'mooblipoisid-child' ),
				'update_item'       => __( 'Update Material / Feature', 'mooblipoisid-child' ),
				'add_new_item'      => __( 'Add New Material / Feature', 'mooblipoisid-child' ),
				'new_item_name'     => __( 'New Material / Feature Name', 'mooblipoisid-child' ),
				'search_items'      => __( 'Search Materials & Features', 'mooblipoisid-child' ),
				'not_found'         => __( 'No materials or features found.', 'mooblipoisid-child' ),
				'add_or_remove_items' => __( 'Add or remove materials and features', 'mooblipoisid-child' ),
			),
			'description'       => __( 'Materials and features shown as small tags on the project card, e.g. "Smoked Oak", "Sintered Stone Top".', 'mooblipoisid-child' ),
			'public'            => true,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'hierarchical'      => false,
			'rewrite'           => false,
			'query_var'         => false,
			'show_admin_column' => true,
		)
	);
}
add_action( 'init', 'mooblipoisid_register_project_taxonomies' );

/**
 * Register the extra sector term meta (image + sort order).
 */
function mooblipoisid_register_sector_meta() {
	register_term_meta(
		'sector',
		'sector_image',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'esc_url_raw',
		)
	);

	register_term_meta(
		'sector',
		'sector_order',
		array(
			'type'              => 'integer',
			'single'            => true,
			'show_in_rest'      => true,
			'sanitize_callback' => 'absint',
		)
	);
}
add_action( 'init', 'mooblipoisid_register_sector_meta' );

/**
 * Extra sector fields on the "Add New Sector" screen.
 */
function mooblipoisid_sector_add_form_fields() {
	?>
	<div class="form-field">
		<label for="sector_image"><?php esc_html_e( 'Sector Image', 'mooblipoisid-child' ); ?></label>
		<input type="text" name="sector_image" id="sector_image" value="" placeholder="https://…" />
		<p class="description"><?php esc_html_e( 'Paste the image address (URL) of the photo shown on this sector card. Upload the photo under Media first, then copy its address.', 'mooblipoisid-child' ); ?></p>
	</div>
	<div class="form-field">
		<label for="sector_order"><?php esc_html_e( 'Sort Order', 'mooblipoisid-child' ); ?></label>
		<input type="number" name="sector_order" id="sector_order" value="" step="1" min="0" />
		<p class="description"><?php esc_html_e( 'Where this sector appears in the homepage list, e.g. 1, 2, 3. Lower numbers come first.', 'mooblipoisid-child' ); ?></p>
	</div>
	<?php
}
add_action( 'sector_add_form_fields', 'mooblipoisid_sector_add_form_fields' );

/**
 * Extra sector fields on the "Edit Sector" screen.
 *
 * @param WP_Term $term Term being edited.
 */
function mooblipoisid_sector_edit_form_fields( $term ) {
	$image = get_term_meta( $term->term_id, 'sector_image', true );
	$order = get_term_meta( $term->term_id, 'sector_order', true );
	?>
	<tr class="form-field">
		<th scope="row"><label for="sector_image"><?php esc_html_e( 'Sector Image', 'mooblipoisid-child' ); ?></label></th>
		<td>
			<input type="text" name="sector_image" id="sector_image" value="<?php echo esc_attr( $image ); ?>" placeholder="https://…" />
			<p class="description"><?php esc_html_e( 'Paste the image address (URL) of the photo shown on this sector card. Upload the photo under Media first, then copy its address.', 'mooblipoisid-child' ); ?></p>
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="sector_order"><?php esc_html_e( 'Sort Order', 'mooblipoisid-child' ); ?></label></th>
		<td>
			<input type="number" name="sector_order" id="sector_order" value="<?php echo esc_attr( $order ); ?>" step="1" min="0" />
			<p class="description"><?php esc_html_e( 'Where this sector appears in the homepage list, e.g. 1, 2, 3. Lower numbers come first.', 'mooblipoisid-child' ); ?></p>
		</td>
	</tr>
	<?php
}
add_action( 'sector_edit_form_fields', 'mooblipoisid_sector_edit_form_fields' );

/**
 * Save the extra sector fields with nonce, capability and sanitization checks.
 *
 * @param int $term_id Term being saved.
 */
function mooblipoisid_save_sector_meta( $term_id ) {
	if ( ! isset( $_POST['sector_image'] ) && ! isset( $_POST['sector_order'] ) ) {
		return;
	}

	// Both term screens send a nonce: `add-tag` on the add form, `_wpnonce` on edit.
	$nonce_ok = false;
	if ( isset( $_POST['add-tag'] ) && isset( $_POST['_wpnonce'] ) ) {
		$nonce_ok = (bool) wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'add-tag' );
	} elseif ( isset( $_POST['_wpnonce'] ) ) {
		$nonce_ok = (bool) wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'update-tag_' . $term_id );
	}

	if ( ! $nonce_ok ) {
		return;
	}

	if ( ! current_user_can( 'manage_categories' ) ) {
		return;
	}

	if ( isset( $_POST['sector_image'] ) ) {
		update_term_meta( $term_id, 'sector_image', esc_url_raw( wp_unslash( $_POST['sector_image'] ) ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	}

	if ( isset( $_POST['sector_order'] ) ) {
		update_term_meta( $term_id, 'sector_order', absint( wp_unslash( $_POST['sector_order'] ) ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	}
}
add_action( 'created_sector', 'mooblipoisid_save_sector_meta' );
add_action( 'edited_sector', 'mooblipoisid_save_sector_meta' );

/* ==========================================================================
 * 3. Homepage Settings (one-off text)
 * ========================================================================== */

/**
 * The single option holding all one-off homepage text.
 */
function mooblipoisid_homepage_option_name() {
	return 'mp_homepage_settings';
}

/**
 * Defaults for the Homepage Settings, matching the current homepage copy so the
 * site renders correctly before anyone edits the screen.
 *
 * @return array
 */
function mooblipoisid_homepage_settings_defaults() {
	return array(
		// Hero.
		'hero_headline'    => __( 'Custom furniture for venues that never stop working.', 'mooblipoisid-child' ),
		'hero_sub'         => __( 'Bars, counters, seating and shopfit joinery for cafés, restaurants and retail brands. Designed, built and installed by one team in Tallinn.', 'mooblipoisid-child' ),
		'hero_cta1_label'  => __( 'Request a Quote', 'mooblipoisid-child' ),
		'hero_cta1_href'   => '#consultation',
		'hero_cta2_label'  => __( 'See Commercial Projects', 'mooblipoisid-child' ),
		'hero_cta2_href'   => '#projects',
		'hero_image'       => 'https://mooblipoisid.ddev.site/wp-content/uploads/2026/11/mp-8.jpg',
		'hero_image_alt'   => __( 'Café-bar interior with banquette seating and brass footrail (placeholder, awaiting real photography)', 'mooblipoisid-child' ),
		// Ethos band.
		'ethos_eyebrow'    => __( 'Ethos & Provenance', 'mooblipoisid-child' ),
		'ethos_quote'      => '“Furniture for public use should look beautiful and outlast the lease.”',
		'ethos_body'       => __( 'A bar or a shop floor takes more punishment in a month than a home does in a year. We build commercial furniture with solid timber and multi-ply birch, joined properly, finished in hard-wearing surfaces and designed so that worn parts can be repaired rather than replaced.', 'mooblipoisid-child' ),
		// Consultation band.
		'consult_eyebrow'  => __( 'Start a Dialogue', 'mooblipoisid-child' ),
		'consult_headline' => __( 'Let’s plan your venue or store.', 'mooblipoisid-child' ),
		'consult_intro'    => __( 'Send us your floor plans, architect drawings or concept boards. We respond with initial feedback and a budget direction within 48 hours.', 'mooblipoisid-child' ),
	);
}

/**
 * Read the Homepage Settings, merged over the defaults.
 *
 * The option holds one field set per language:
 *
 *     array( 'et' => array( 'hero_headline' => …, … ), 'ru' => …, 'en' => … )
 *
 * and older installs stored the Estonian fields flat in the same option. A flat
 * option is detected and re-keyed under `et` once, on read or on save, so no
 * Estonian content is ever lost (see `mooblipoisid_migrate_homepage_settings()`).
 *
 * The fields of the active language are returned, with the Estonian value used
 * for any field a language does not have yet. When Polylang is not available the
 * Estonian (or the only stored) set is returned. The return shape — one flat
 * array of field keys — is unchanged, so the template parts need no changes.
 *
 * @return array
 */
function mooblipoisid_get_homepage_settings() {
	$stored = get_option( mooblipoisid_homepage_option_name(), array() );
	$stored = is_array( $stored ) ? $stored : array();
	$migrated = mooblipoisid_migrate_homepage_settings( $stored );

	// One-time, idempotent migration of an option saved before localisation.
	mooblipoisid_persist_homepage_settings_migration( $migrated, $stored );
	$stored = $migrated;

	$language = mooblipoisid_current_language_code();
	$default  = mooblipoisid_default_language_code();

	$values = isset( $stored[ $language ] ) && is_array( $stored[ $language ] ) ? $stored[ $language ] : array();
	$fallback = isset( $stored[ $default ] ) && is_array( $stored[ $default ] ) ? $stored[ $default ] : array();

	$values = wp_parse_args( $values, $fallback );

	return wp_parse_args( $values, mooblipoisid_homepage_settings_defaults() );
}

/**
 * The language code the homepage is being rendered for.
 *
 * @return string Language slug (`et`, `ru`, `en`), `et` when Polylang is not available.
 */
function mooblipoisid_current_language_code() {
	if ( function_exists( 'pll_current_language' ) ) {
		$slug = pll_current_language( 'slug' );
		if ( is_string( $slug ) && '' !== $slug ) {
			return $slug;
		}
	}

	return mooblipoisid_default_language_code();
}

/**
 * The default (source) language code: Estonian unless Polylang says otherwise.
 *
 * @return string Language slug.
 */
function mooblipoisid_default_language_code() {
	if ( function_exists( 'pll_default_language' ) ) {
		$slug = pll_default_language( 'slug' );
		if ( is_string( $slug ) && '' !== $slug ) {
			return $slug;
		}
	}

	return 'et';
}

/**
 * Whether an option value is the current per-language shape.
 *
 * @param mixed $value Option value.
 * @return bool True when the value is keyed by language code.
 */
function mooblipoisid_homepage_settings_is_localised( $value ) {
	if ( ! is_array( $value ) || empty( $value ) ) {
		return true; // Nothing to migrate: an empty option already has the right shape.
	}

	foreach ( array_keys( mooblipoisid_homepage_settings_fields() ) as $field_id ) {
		if ( array_key_exists( $field_id, $value ) ) {
			return false; // A field key at the top level: the old, flat shape.
		}
	}

	return true;
}

/**
 * Re-key a flat (pre-multilingual) option under its language.
 *
 * Idempotent: a per-language option is returned untouched, and a flat option
 * keeps every key it carries (editor-added keys included) but moves under the
 * default language. Callers that persist the result must only write when it
 * differs from what they read — see the read and save paths below.
 *
 * @param array $stored Raw option value.
 * @return array Language-keyed value.
 */
function mooblipoisid_migrate_homepage_settings( $stored ) {
	if ( mooblipoisid_homepage_settings_is_localised( $stored ) ) {
		return $stored;
	}

	return array( mooblipoisid_default_language_code() => $stored );
}

/**
 * Persist a flat option as the per-language shape (one-time migration).
 *
 * @param array $migrated Value returned by `mooblipoisid_migrate_homepage_settings()`.
 * @param array $raw      Value as it is stored right now.
 * @return void
 */
function mooblipoisid_persist_homepage_settings_migration( $migrated, $raw ) {
	if ( $migrated !== $raw ) {
		update_option( mooblipoisid_homepage_option_name(), $migrated );
	}
}

/* ==========================================================================
 * 3b. Graceful fallback for untranslated content
 * ========================================================================== */

/**
 * The content model fallbacks, in the order the homepage renders them.
 *
 * A section whose entries have not been translated yet falls back to the
 * Estonian entries instead of disappearing, so the page keeps its shape in
 * every language while the translation of the content model (task 5.x) fills
 * the gaps. Turning a key off here (or filtering the list) makes that section
 * strict again: only the active language's entries render.
 *
 * @return array Section id => content type (or `sector` for the terms).
 */
function mooblipoisid_homepage_section_content_types() {
	return array(
		'trust'        => 'trust_stat',
		'sectors'      => 'sector',
		'ranges'       => 'range',
		'projects'     => 'project',
		'capabilities' => 'capability',
		'process'      => 'step',
		'stories'      => 'testimonial',
	);
}

/**
 * The default-language fallbacks enabled for the homepage sections.
 *
 * @return array Section id => content type.
 */
function mooblipoisid_homepage_content_fallbacks() {
	/**
	 * Filter which homepage sections fall back to the Estonian entries when the
	 * active language has none of its own yet.
	 *
	 * @param array $fallbacks Section id => content type.
	 */
	return (array) apply_filters( 'mooblipoisid_homepage_content_fallbacks', mooblipoisid_homepage_section_content_types() );
}


/**
 * Languages a section's query fetches: the active one, plus the default one to
 * fall back on when the active language has no entries of its own yet.
 *
 * The fallback is per-entry: the query asks for the active language and the
 * default one together, so each entry renders in its own translation when it
 * has one and in Estonian when it does not. A fully translated section shows
 * only its own language; a partially translated one shows its translations
 * plus the Estonian originals for the rest, so no entry ever disappears while
 * a language is being translated.
 *
 * @param string          $post_type Content type of the section (or `sector`).
 * @param string|string[] $lang      Language slug(s) the query is asking for.
 * @return string|string[] Languages to ask for.
 */
function mooblipoisid_section_languages( $post_type, $lang = null ) {
	$language = null === $lang ? mooblipoisid_current_language_code() : $lang;
	$default  = mooblipoisid_default_language_code();

	if ( ! mooblipoisid_section_has_fallback( (string) $post_type ) ) {
		return $language;
	}

	if ( '' === $language || $language === $default ) {
		return $language;
	}

	// Ask for the active language and the default together. Polylang returns the
	// active-language entries plus every default-language entry that has no
	// translation yet, which is exactly the per-entry fallback we want.
	return array( $language, $default );
}

/**
 * Whether the default-language entries have at least one translation missing.
 *
 * Used by the section query to decide whether to include the default language
 * alongside the active one. When a content type has default-language entries
 * without a translation in the active language, those originals must render as
 * the fallback so the section is never short of entries mid-translation.
 *
 * @param string $post_type Content type (or `sector`).
 * @param string $language  Active language slug.
 * @return bool True when at least one default-language entry lacks a translation.
 */
function mooblipoisid_section_needs_default_fallback( $post_type, $language ) {
	$default = mooblipoisid_default_language_code();

	if ( '' === $language || $language === $default ) {
		return false;
	}

	if ( 'sector' === $post_type ) {
		$default_terms = get_terms(
			array(
				'taxonomy'               => $post_type,
				'hide_empty'             => false,
				'lang'                   => $default,
				'fields'                 => 'ids',
				'update_term_meta_cache' => false,
			)
		);

		if ( is_wp_error( $default_terms ) || empty( $default_terms ) ) {
			return false;
		}

		foreach ( $default_terms as $term_id ) {
			if ( ! function_exists( 'pll_get_term' ) || ! pll_get_term( (int) $term_id, $language ) ) {
				return true;
			}
		}

		return false;
	}

	$default_ids = get_posts(
		array(
			'post_type'              => $post_type,
			'post_status'            => 'publish',
			'posts_per_page'         => -1,
			'orderby'                => 'menu_order',
			'order'                  => 'ASC',
			'lang'                   => $default,
			'fields'                 => 'ids',
			'ignore_sticky_posts'    => true,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
		)
	);

	if ( empty( $default_ids ) ) {
		return false;
	}

	foreach ( $default_ids as $post_id ) {
		if ( ! function_exists( 'pll_get_post' ) || ! pll_get_post( (int) $post_id, $language ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Drop default-language originals whose translation is present in the query.
 *
 * When a section query asks for the active language and the default together
 * (the per-entry fallback), Polylang returns both a translated entry and its
 * Estonian original. The original is redundant once its translation exists, so
 * it is removed here; untranslated originals stay as the fallback. Ordering is
 * preserved because the remaining entries keep their query order.
 *
 * @param WP_Post[] $posts Posts fetched by the query.
 * @param WP_Query  $query The query.
 * @return WP_Post[] Filtered posts.
 */
function mooblipoisid_drop_translated_originals( $posts, $query ) {
	if ( ! is_array( $posts ) || empty( $posts ) || ! function_exists( 'pll_get_post' ) ) {
		return $posts;
	}

	$query_lang = $query->get( 'lang' );
	if ( ! is_array( $query_lang ) || count( $query_lang ) < 2 ) {
		return $posts;
	}

	$default = mooblipoisid_default_language_code();

	// The active language is the requested non-default one. Reading it from the
	// query (not the global current language) keeps the filter correct however
	// the query is issued.
	$language = '';
	foreach ( $query_lang as $slug ) {
		if ( $slug !== $default ) {
			$language = (string) $slug;
			break;
		}
	}

	if ( '' === $language ) {
		return $posts;
	}

	$kept = array();
	foreach ( $posts as $post ) {
		$post_lang = function_exists( 'pll_get_post_language' ) ? pll_get_post_language( $post->ID, 'slug' ) : $default;

		// Keep every active-language entry; keep a default-language entry only
		// when it has no translation in the active language (the fallback).
		if ( $post_lang !== $default ) {
			$kept[] = $post;
			continue;
		}

		if ( ! pll_get_post( (int) $post->ID, $language ) ) {
			$kept[] = $post;
		}
	}

	return $kept;
}
add_filter( 'the_posts', 'mooblipoisid_drop_translated_originals', 10, 2 );

/**
 * Whether a content type has entries in a language.
 *
 * Runs the section's own query shape (same ordering, ids only) so the decision
 * and the rendering can never disagree.
 *
 * @param string $post_type Content type (or `sector`).
 * @param string $language  Language slug.
 * @return bool
 */
function mooblipoisid_section_has_posts( $post_type, $language ) {
	if ( 'sector' === $post_type ) {
		$terms = get_terms(
			array(
				'taxonomy'               => $post_type,
				'hide_empty'             => false,
				'lang'                   => $language,
				'fields'                 => 'ids',
				'update_term_meta_cache' => false,
			)
		);

		return ! is_wp_error( $terms ) && ! empty( $terms );
	}

	$query = new WP_Query(
		array(
			'post_type'              => $post_type,
			'post_status'            => 'publish',
			'posts_per_page'         => 1,
			'orderby'                => 'menu_order',
			'order'                  => 'ASC',
			'lang'                   => $language,
			'fields'                 => 'ids',
			'ignore_sticky_posts'    => true,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
		)
	);

	$found = $query->have_posts();
	wp_reset_postdata();

	return $found;
}

/**
 * Whether a section falls back to the default language when it is empty.
 *
 * @param string $post_type Content type of the section.
 * @return bool
 */
function mooblipoisid_section_has_fallback( $post_type ) {
	return in_array( $post_type, mooblipoisid_homepage_content_fallbacks(), true );
}

/**
 * Drop default-language term originals whose translation is present in the list.
 *
 * Mirrors mooblipoisid_drop_translated_originals() for taxonomy terms: when a
 * term query asks for the active language and the default together, Polylang
 * returns both a translated term and its Estonian original. The original is
 * redundant once its translation exists, so it is removed; untranslated
 * originals stay as the fallback.
 *
 * @param WP_Term[] $terms   Terms fetched by the query.
 * @param string    $language Active language slug.
 * @return WP_Term[] Filtered terms.
 */
function mooblipoisid_drop_translated_original_terms( $terms, $language = null ) {
	if ( ! is_array( $terms ) || empty( $terms ) || ! function_exists( 'pll_get_term' ) ) {
		return $terms;
	}

	$default  = mooblipoisid_default_language_code();
	$language = null === $language ? mooblipoisid_current_language_code() : $language;

	if ( '' === $language || $language === $default ) {
		return $terms;
	}

	$kept = array();
	foreach ( $terms as $term ) {
		$term_lang = function_exists( 'pll_get_term_language' ) ? pll_get_term_language( $term->term_id, 'slug' ) : $default;

		if ( $term_lang !== $default ) {
			$kept[] = $term;
			continue;
		}

		if ( ! pll_get_term( (int) $term->term_id, $language ) ) {
			$kept[] = $term;
		}
	}

	return $kept;
}

/**
 * Field configuration for the Homepage Settings screen.
 *
 * @return array[]
 */
function mooblipoisid_homepage_settings_fields() {
	return array(
		'hero_headline'    => array(
			'label'       => __( 'Hero Headline', 'mooblipoisid-child' ),
			'description' => __( 'The big sentence at the top of the homepage.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'hero',
		),
		'hero_sub'         => array(
			'label'       => __( 'Hero Subline', 'mooblipoisid-child' ),
			'description' => __( 'The short paragraph under the headline.', 'mooblipoisid-child' ),
			'type'        => 'textarea',
			'section'     => 'hero',
		),
		'hero_cta1_label'  => array(
			'label'       => __( 'Primary Button Text', 'mooblipoisid-child' ),
			'description' => __( 'The main button, e.g. "Request a Quote".', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'hero',
		),
		'hero_cta1_href'   => array(
			'label'       => __( 'Primary Button Link', 'mooblipoisid-child' ),
			'description' => __( 'Where the main button goes, e.g. "#consultation" for the form further down the page.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'hero',
		),
		'hero_cta2_label'  => array(
			'label'       => __( 'Secondary Button Text', 'mooblipoisid-child' ),
			'description' => __( 'The second, quieter button, e.g. "See Commercial Projects".', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'hero',
		),
		'hero_cta2_href'   => array(
			'label'       => __( 'Secondary Button Link', 'mooblipoisid-child' ),
			'description' => __( 'Where the second button goes, e.g. "#projects".', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'hero',
		),
		'hero_image'       => array(
			'label'       => __( 'Hero Image Address', 'mooblipoisid-child' ),
			'description' => __( 'The image address (URL) of the large photo behind the hero. Upload the photo under Media first, then copy its address.', 'mooblipoisid-child' ),
			'type'        => 'url',
			'section'     => 'hero',
		),
		'hero_image_alt'   => array(
			'label'       => __( 'Hero Image Description', 'mooblipoisid-child' ),
			'description' => __( 'A short description of the photo, used by screen readers and search engines.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'hero',
		),

		'ethos_eyebrow'    => array(
			'label'       => __( 'Ethos Small Heading', 'mooblipoisid-child' ),
			'description' => __( 'The small label above the ethos quote, e.g. "Ethos & Provenance".', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'ethos',
		),
		'ethos_quote'      => array(
			'label'       => __( 'Ethos Quote', 'mooblipoisid-child' ),
			'description' => __( 'The large quote in the dark band.', 'mooblipoisid-child' ),
			'type'        => 'textarea',
			'section'     => 'ethos',
		),
		'ethos_body'       => array(
			'label'       => __( 'Ethos Body Text', 'mooblipoisid-child' ),
			'description' => __( 'The paragraph that explains how and why we build the way we do.', 'mooblipoisid-child' ),
			'type'        => 'textarea',
			'section'     => 'ethos',
		),

		'consult_eyebrow'  => array(
			'label'       => __( 'Consultation Small Heading', 'mooblipoisid-child' ),
			'description' => __( 'The small label above the consultation heading, e.g. "Start a Dialogue".', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'consult',
		),
		'consult_headline' => array(
			'label'       => __( 'Consultation Headline', 'mooblipoisid-child' ),
			'description' => __( 'The heading above the enquiry form.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'consult',
		),
		'consult_intro'    => array(
			'label'       => __( 'Consultation Intro Text', 'mooblipoisid-child' ),
			'description' => __( 'The paragraph under the consultation heading, above the enquiry form.', 'mooblipoisid-child' ),
			'type'        => 'textarea',
			'section'     => 'consult',
		),
	);
}

/**
 * Section titles and descriptions for the Homepage Settings screen.
 *
 * @return array[]
 */
function mooblipoisid_homepage_settings_sections() {
	return array(
		'hero'   => array(
			'title'       => __( 'Hero (the top of the homepage)', 'mooblipoisid-child' ),
			'description' => __( 'The large opening panel with the headline, two buttons and the background photo.', 'mooblipoisid-child' ),
		),
		'ethos'  => array(
			'title'       => __( 'Ethos Band', 'mooblipoisid-child' ),
			'description' => __( 'The dark band that explains how and why we build.', 'mooblipoisid-child' ),
		),
		'consult' => array(
			'title'       => __( 'Consultation Band', 'mooblipoisid-child' ),
			'description' => __( 'The introduction above the enquiry form at the bottom of the homepage.', 'mooblipoisid-child' ),
		),
	);
}

/**
 * Register the Homepage Settings screen (Settings → Homepage Settings).
 */
function mooblipoisid_register_homepage_settings() {
	$option = mooblipoisid_homepage_option_name();

	/*
	 * The screen edits one language at a time (see the language buttons in
	 * mooblipoisid_render_homepage_settings_page()), and every field is rendered
	 * for that language, so the language has to be known while the fields are
	 * registered too.
	 */
	$language = isset( $_GET['mp_lang'] ) ? sanitize_key( wp_unslash( $_GET['mp_lang'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$languages = function_exists( 'pll_languages_list' ) ? (array) pll_languages_list( array( 'fields' => 'slug' ) ) : array();
	if ( ! in_array( $language, $languages, true ) ) {
		$language = mooblipoisid_default_language_code();
	}

	register_setting(
		'mooblipoisid_homepage_settings_group',
		$option,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'mooblipoisid_sanitize_homepage_settings',
			'default'           => array( mooblipoisid_default_language_code() => mooblipoisid_homepage_settings_defaults() ),
		)
	);

	foreach ( mooblipoisid_homepage_settings_sections() as $section_id => $section ) {
		add_settings_section(
			'mooblipoisid_homepage_' . $section_id,
			$section['title'],
			function () use ( $section ) {
				echo '<p>' . esc_html( $section['description'] ) . '</p>';
			},
			'mooblipoisid_homepage_settings'
		);
	}

	foreach ( mooblipoisid_homepage_settings_fields() as $field_id => $field ) {
		add_settings_field(
			$field_id,
			$field['label'],
			'mooblipoisid_render_homepage_setting_field',
			'mooblipoisid_homepage_settings',
			'mooblipoisid_homepage_' . $field['section'],
			array(
				'label_for'   => $field_id,
				'field_id'    => $field_id,
				'field'       => $field,
				'option_name' => $option,
				'language'    => $language,
			)
		);
	}
}
add_action( 'admin_init', 'mooblipoisid_register_homepage_settings' );

/**
 * Add the Homepage Settings screen under Settings.
 */
function mooblipoisid_add_homepage_settings_page() {
	add_options_page(
		__( 'Homepage Settings', 'mooblipoisid-child' ),
		__( 'Homepage Settings', 'mooblipoisid-child' ),
		'manage_options',
		'mooblipoisid_homepage_settings',
		'mooblipoisid_render_homepage_settings_page'
	);
}
add_action( 'admin_menu', 'mooblipoisid_add_homepage_settings_page' );

/**
 * Render one Homepage Settings field for the language being edited.
 *
 * @param array $args Field arguments passed from add_settings_field().
 */
function mooblipoisid_render_homepage_setting_field( $args ) {
	$field_id    = $args['field_id'];
	$field       = $args['field'];
	$option_name = $args['option_name'];
	$language    = isset( $args['language'] ) ? (string) $args['language'] : mooblipoisid_current_language_code();

	$options = get_option( $option_name, array() );
	$options = is_array( $options ) ? $options : array();
	$options = mooblipoisid_migrate_homepage_settings( $options );

	$values = isset( $options[ $language ] ) && is_array( $options[ $language ] ) ? $options[ $language ] : array();
	$value  = isset( $values[ $field_id ] ) ? $values[ $field_id ] : '';
	$value  = is_scalar( $value ) ? (string) $value : '';

	/*
	 * Only the fields of the language being edited are submitted, so a language
	 * that has not been edited yet starts from the Estonian value (rather than
	 * from the English defaults) and the editor sees the copy to translate.
	 */
	if ( '' === $value ) {
		$default_language = mooblipoisid_default_language_code();
		$defaults         = isset( $options[ $default_language ] ) && is_array( $options[ $default_language ] ) ? $options[ $default_language ] : array();
		$defaults         = wp_parse_args( $defaults, mooblipoisid_homepage_settings_defaults() );
		$value            = isset( $defaults[ $field_id ] ) ? (string) $defaults[ $field_id ] : '';
	}

	$name = $option_name . '[' . $language . '][' . $field_id . ']';

	if ( 'textarea' === $field['type'] ) {
		printf(
			'<textarea name="%1$s" id="%2$s" rows="4" class="large-text code">%3$s</textarea>',
			esc_attr( $name ),
			esc_attr( $field_id ),
			esc_textarea( $value )
		);
	} else {
		printf(
			'<input type="text" name="%1$s" id="%2$s" value="%3$s" class="large-text" />',
			esc_attr( $name ),
			esc_attr( $field_id ),
			esc_attr( $value )
		);
	}

	if ( ! empty( $field['description'] ) ) {
		echo '<p class="description">' . esc_html( $field['description'] ) . '</p>';
	}
}

/**
 * Language picker and explanation for the Homepage Settings screen.
 *
 * Plain buttons rather than the technical Polylang editor language: each one
 * shows the fields in that language, and saving keeps the other languages as
 * they are.
 *
 * @param string   $current  Language code being edited.
 * @param string[] $language Language codes, in menu order.
 * @return void
 */
function mooblipoisid_render_homepage_settings_language_picker( $current, $languages ) {
	$names = array(
		'et' => __( 'Eesti', 'mooblipoisid-child' ),
		'ru' => __( 'Русский', 'mooblipoisid-child' ),
		'en' => __( 'English', 'mooblipoisid-child' ),
	);
	?>
	<div class="mooblipoisid-homepage-settings-lang" style="display:flex;flex-wrap:wrap;gap:8px;align-items:center;margin:16px 0 8px;">
		<strong style="margin-right:4px;"><?php esc_html_e( 'Language', 'mooblipoisid-child' ); ?>:</strong>
		<?php foreach ( (array) $languages as $language ) : ?>
			<?php
			$is_current = $language === $current;
			$url        = add_query_arg(
				array(
					'page'    => 'mooblipoisid_homepage_settings',
					'mp_lang' => $language,
				),
				admin_url( 'options-general.php' )
			);
			?>
			<a class="button <?php echo $is_current ? 'button-primary' : ''; ?>" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( isset( $names[ $language ] ) ? $names[ $language ] : strtoupper( $language ) ); ?></a>
		<?php endforeach; ?>
		<span class="description" style="margin-left:8px;"><?php esc_html_e( 'Edit the text in one language at a time. Saving never touches the other languages, and a language that has no text of its own shows the Estonian text on the site.', 'mooblipoisid-child' ); ?></span>
	</div>
	<?php
}

/**
 * Render the Homepage Settings screen.
 */
function mooblipoisid_render_homepage_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$languages = function_exists( 'pll_languages_list' ) ? (array) pll_languages_list( array( 'fields' => 'slug' ) ) : array();
	if ( empty( $languages ) ) {
		$languages = array( mooblipoisid_default_language_code() );
	}

	$current = isset( $_GET['mp_lang'] ) ? sanitize_key( wp_unslash( $_GET['mp_lang'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! in_array( $current, $languages, true ) ) {
		$current = mooblipoisid_default_language_code();
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Homepage Settings', 'mooblipoisid-child' ); ?></h1>
		<p class="description">
			<?php esc_html_e( 'The one-off text shown on the homepage. Change anything here and the homepage updates straight away — no code needed.', 'mooblipoisid-child' ); ?>
		</p>
		<?php mooblipoisid_render_homepage_settings_language_picker( $current, $languages ); ?>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'mooblipoisid_homepage_settings_group' );
			printf( '<input type="hidden" name="mp_settings_language" value="%s" />', esc_attr( $current ) );
			do_settings_sections( 'mooblipoisid_homepage_settings' );
			submit_button( __( 'Save Homepage Settings', 'mooblipoisid-child' ) );
			?>
		</form>
	</div>
	<?php
}

/**
 * Sanitize the Homepage Settings before they are stored.
 *
 * Only the language being edited is submitted, so the other languages keep
 * their values. An old flat option is migrated to the per-language shape on the
 * first save (idempotent: the option only ever holds language keys afterwards).
 *
 * @param array $input Raw input: the fields of one language.
 * @return array Complete per-language value to store.
 */
function mooblipoisid_sanitize_homepage_settings( $input ) {
	$fields = mooblipoisid_homepage_settings_fields();

	$language = isset( $_POST['mp_settings_language'] ) ? sanitize_key( wp_unslash( $_POST['mp_settings_language'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- verified by options.php + nonce in settings_fields()
	$languages = function_exists( 'pll_languages_list' ) ? (array) pll_languages_list( array( 'fields' => 'slug' ) ) : array();
	if ( ! in_array( $language, $languages, true ) ) {
		$language = mooblipoisid_default_language_code();
	}

	$input = is_array( $input ) ? $input : array();

	$stored = get_option( mooblipoisid_homepage_option_name(), array() );
	$stored = is_array( $stored ) ? $stored : array();
	$stored = mooblipoisid_migrate_homepage_settings( $stored );

	$values = isset( $stored[ $language ] ) && is_array( $stored[ $language ] ) ? $stored[ $language ] : array();
	$clean  = array();

	foreach ( $fields as $field_id => $field ) {
		$value = isset( $input[ $field_id ] ) ? $input[ $field_id ] : null;

		if ( null === $value ) {
			// Field not submitted: keep whatever this language already has.
			if ( isset( $values[ $field_id ] ) && '' !== $values[ $field_id ] ) {
				$clean[ $field_id ] = $values[ $field_id ];
			}
			continue;
		}

		if ( 'textarea' === $field['type'] ) {
			$clean[ $field_id ] = sanitize_textarea_field( $value );
			continue;
		}

		$value = sanitize_text_field( $value );

		if ( 'url' === $field['type'] ) {
			$value = esc_url_raw( $value );
		} elseif ( false !== strpos( $field_id, '_href' ) ) {
			// Allow in-page anchors (#consultation) as well as full URLs.
			$value = ( '' !== $value && '#' === $value[0] ) ? sanitize_text_field( $value ) : esc_url_raw( $value );
		}

		$clean[ $field_id ] = $value;
	}

	$stored[ $language ] = $clean;

	return $stored;
}