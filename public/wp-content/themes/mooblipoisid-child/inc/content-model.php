<?php
/**
 * Homepage content model: post types, taxonomies, entry details and Homepage Settings.
 *
 * Everything a non-technical editor needs to maintain the homepage lives here:
 *
 *  - Content entries (custom post types)   -> project, testimonial, range, capability, step, trust_stat
 *  - Grouping terms (taxonomies)           -> sector, project_tag   (on Projects)
 *  - Extra entry details (post meta)       -> mp_location_type, mp_role, mp_city, mp_rating, mp_week
 *  - One-off homepage text (single option) -> mp_homepage_settings
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
 * @return array
 */
function mooblipoisid_get_homepage_settings() {
	$saved = get_option( mooblipoisid_homepage_option_name(), array() );
	$saved = is_array( $saved ) ? $saved : array();

	return wp_parse_args( $saved, mooblipoisid_homepage_settings_defaults() );
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

	register_setting(
		'mooblipoisid_homepage_settings_group',
		$option,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'mooblipoisid_sanitize_homepage_settings',
			'default'           => mooblipoisid_homepage_settings_defaults(),
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
 * Render one Homepage Settings field.
 *
 * @param array $args Field arguments passed from add_settings_field().
 */
function mooblipoisid_render_homepage_setting_field( $args ) {
	$field_id    = $args['field_id'];
	$field       = $args['field'];
	$option_name = $args['option_name'];

	$options = get_option( $option_name, array() );
	$options = is_array( $options ) ? $options : array();
	$value   = isset( $options[ $field_id ] ) ? $options[ $field_id ] : '';
	$value   = is_scalar( $value ) ? (string) $value : '';

	if ( '' === $value ) {
		$defaults = mooblipoisid_homepage_settings_defaults();
		$value    = isset( $defaults[ $field_id ] ) ? $defaults[ $field_id ] : '';
	}

	$name = $option_name . '[' . $field_id . ']';

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
 * Render the Homepage Settings screen.
 */
function mooblipoisid_render_homepage_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Homepage Settings', 'mooblipoisid-child' ); ?></h1>
		<p class="description">
			<?php esc_html_e( 'The one-off text shown on the homepage. Change anything here and the homepage updates straight away — no code needed.', 'mooblipoisid-child' ); ?>
		</p>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'mooblipoisid_homepage_settings_group' );
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
 * @param array $input Raw input.
 * @return array Clean values.
 */
function mooblipoisid_sanitize_homepage_settings( $input ) {
	$fields  = mooblipoisid_homepage_settings_fields();
	$clean   = array();
	$stored  = get_option( mooblipoisid_homepage_option_name(), array() );
	$stored  = is_array( $stored ) ? $stored : array();

	foreach ( $fields as $field_id => $field ) {
		$value = isset( $input[ $field_id ] ) ? $input[ $field_id ] : null;

		if ( null === $value ) {
			// Field not submitted: keep whatever is stored, or the default.
			if ( isset( $stored[ $field_id ] ) && '' !== $stored[ $field_id ] ) {
				$clean[ $field_id ] = $stored[ $field_id ];
			} else {
				$defaults           = mooblipoisid_homepage_settings_defaults();
				$clean[ $field_id ] = isset( $defaults[ $field_id ] ) ? $defaults[ $field_id ] : '';
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

	return $clean;
}