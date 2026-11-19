<?php
/**
 * Homepage content model: post types, taxonomies, entry details and Homepage Settings.
 *
 * Everything a non-technical editor needs to maintain the homepage lives here:
 *
 *  - Content entries (custom post types)   -> project, testimonial, range, capability, step, trust_stat, mooblipoisid_faq
 *  - Grouping terms (taxonomies)           -> sector, project_tag   (on Projects)
 *  - Extra entry details (post meta)       -> mp_location_type, mp_role, mp_city, mp_rating, mp_week
 *  - One-off homepage text (single option) -> mp_homepage_settings (stored per language)
 *  - Page SEO (post meta on Pages)         -> _mooblipoisid_seo_title, _mooblipoisid_seo_description, _mooblipoisid_seo_social_image
 *  - Company info (single option)          -> mooblipoisid_company_info
 *
 * Multilingual (Polylang):
 *  - The six homepage content types and the two taxonomies are declared
 *    translatable by the theme (`mooblipoisid_polylang_translatable_content()`),
 *    so the settings are enforced in code: the content model stays complete on
 *    every deploy, and the entries are filtered by the active language
 *    automatically. The FAQ post type (`mooblipoisid_faq`) is registered
 *    translatable in the Polylang settings themselves (via wp-cli), not from
 *    this file — see the note on the post type below.
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
		/*
		 * The FAQ entries of the homepage FAQ section. NOTE: `mooblipoisid_faq`
		 * is deliberately NOT listed in `mooblipoisid_translatable_content()`
		 * below (that list is mirrored into the `polylang` option on admin_init).
		 * Polylang's translatable post types are extended for it outside this
		 * file (via wp-cli), so nothing here writes to the Polylang settings.
		 */
		'mooblipoisid_faq' => array(
			'label'       => __( 'FAQ', 'mooblipoisid-child' ),
			'singular'    => __( 'FAQ', 'mooblipoisid-child' ),
			'desc'        => __( 'The questions and answers shown in the FAQ section of the homepage. Put the question in the title and the answer in the main text box.', 'mooblipoisid-child' ),
			'menu_icon'   => 'dashicons-editor-help',
			'supports'    => array( 'title', 'editor', 'page-attributes' ),
			'menu_position' => 27,
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
 * The published FAQ entries of the active language, in homepage order.
 *
 * `mooblipoisid_faq` entries sort like every other content type: the
 * "Order on Homepage" page attribute (`menu_order`) ascending, with the post
 * date breaking ties. Polylang filters the query by the active language on the
 * frontend (the post type is registered translatable in the Polylang settings,
 * see the note on the post type above), so no `lang` argument is passed here —
 * the same way the homepage section queries rely on the language filter.
 *
 * The question and the answer are returned raw: the consumer escapes or
 * sanitizes them for the context it renders.
 *
 * @return array[] A list of `array( 'id' => int, 'question' => string, 'answer' => string )`.
 */
function mooblipoisid_get_faqs() {
	if ( ! post_type_exists( 'mooblipoisid_faq' ) ) {
		return array();
	}

	$posts = get_posts(
		array(
			'post_type'              => 'mooblipoisid_faq',
			'post_status'            => 'publish',
			'posts_per_page'         => -1,
			'orderby'                => 'menu_order',
			'order'                  => 'ASC',
			'ignore_sticky_posts'    => true,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
		)
	);

	$faqs = array();
	foreach ( (array) $posts as $post ) {
		$faqs[] = array(
			'id'       => (int) $post->ID,
			'question' => (string) $post->post_title,
			'answer'   => (string) $post->post_content,
		);
	}

	return $faqs;
}

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
		'mooblipoisid_faq' => array(
			'title'  => __( 'Question', 'mooblipoisid-child' ),
			'editor' => __( 'Answer', 'mooblipoisid-child' ),
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
		<?php mooblipoisid_render_media_picker_field( 'sector_image', 'sector_image', '' ); ?>
		<p class="description"><?php esc_html_e( 'The photo shown on this sector card. Pick it from the Media Library.', 'mooblipoisid-child' ); ?></p>
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
			<?php mooblipoisid_render_media_picker_field( 'sector_image', 'sector_image', $image ); ?>
			<p class="description"><?php esc_html_e( 'The photo shown on this sector card. Pick it from the Media Library.', 'mooblipoisid-child' ); ?></p>
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
		'ethos_image'      => 'wp-content/uploads/2026/11/mp-3.jpg',
		'ethos_image_alt'  => __( 'Meistrikoda Mööblipoisid töökojas (näidisfoto, ootab pärisfotograafiat)', 'mooblipoisid-child' ),
		// Consultation band.
		'consult_eyebrow'  => __( 'Start a Dialogue', 'mooblipoisid-child' ),
		'consult_headline' => __( 'Let’s plan your venue or store.', 'mooblipoisid-child' ),
		'consult_intro'    => __( 'Send us your floor plans, architect drawings or concept boards. We respond with initial feedback and a budget direction within 48 hours.', 'mooblipoisid-child' ),

		/*
		 * Section heads and fixed copy. The Estonian strings below are the
		 * source copy (the .po files used to carry these; they now live here so
		 * every language is editable on this screen).
		 */
		// Sectors ("Who We Build For").
		'sectors_eyebrow'           => 'Kellele me ehitame',
		'sectors_title'             => 'Mööbel äripindade sisekujunduseks',
		'sectors_intro'             => 'Iga koht on erinev, kuid nõudmised on samad: pikad lahtiolekuajad, pidev kasutus ja bränd, mis peab juba esimesest päevast õige välja nägema.',
		'sector_title_hospitality'  => 'Kohvikud, baarid ja restoranid',
		'sector_title_retail'       => 'Jaemüük ja kaubanduslik sisekujundus',
		'sector_title_private'      => 'Eraköögid',
		'sector_alt_hospitality'    => 'Kohviku ja baari interjöör pika puidust letiga',
		'sector_alt_retail'         => 'Kaupluse vitriinriiulid ja kassalauk',
		'sector_alt_private'        => 'Erilahendusega tammepuidust köök',
		'sectors_link_hospitality'  => 'Vaata toitlustusvalikut',
		'sectors_link_retail'       => 'Vaata jaemüügivalikut',
		'sectors_link_private'      => 'Küsi pakkumist',
		// Ranges ("What We Make").
		'ranges_eyebrow'            => 'Mida me valmistame',
		'ranges_title'              => 'Täielik äriklassi valik',
		'ranges_intro'              => 'Valmistatud teie jooniste järgi või koostöös teie arhitektiga ning toodetud täieliku paketina, nii et kõik sobib omavahel kokku paigalduspäeval.',
		'ranges_col_hospitality'    => 'Kohvikud, baarid ja restoranid',
		'ranges_col_retail'         => 'Jaekauplused',
		// Projects ("Selected Commissions").
		'projects_eyebrow'          => 'Valitud tööd',
		'projects_title'            => 'Hiljutised äri- ja jaemüügiprojektid',
		'projects_card_link'        => 'Arutage sarnast projekti',
		// Capabilities.
		'caps_eyebrow'              => 'Võimekused',
		'caps_title'                => 'Üks partner. Kogu teie ruumi sisustus.',
		'caps_intro'                => 'Baar ei ole harva ainult puit. Kuna mööbel ja sisustuselemendid valmistatakse meie enda töökojas, saabuvad osad viimistletuna ja paigaldusvalmisena, ilma tarnijate vahel jooksmiseta.',
		// Process.
		'process_eyebrow'           => 'Protsess',
		'process_title'             => 'Briefist avamispäevani',
		'process_intro'             => 'Töötame teie siseviimistluse graafiku järgi, koordineerime teie arhitekti ja töövõtjatega ning paigaldame teie avamiskuupäeva ümber.',
		// Stories (testimonials).
		'stories_eyebrow'           => 'Klientide lood',
		'stories_title'             => 'Mida meie kliendid ütlevad',
		// Ethos fixed copy (badge + the two points under the quote).
		'ethos_badge_title'         => 'Oma tootmine',
		'ethos_badge_text'          => 'Mööbel ja sisustus ühe katuse all Tallinnas',
		'ethos_point1_title'        => 'Valmis kandmiseks',
		'ethos_point1_text'         => 'Kuumus- ja kriipimiskindlad pealsed, tugevdatud liited ja äriklassi furnituur igapäevaseks kasutamiseks.',
		'ethos_point2_title'        => 'Üks meeskond, algusest lõpuni',
		'ethos_point2_text'         => 'Inimesed, kes teie esemed disainivad, valmistavad ja paigaldavad need ka ise, nii et üks kontaktisik vastutab kogu projekti eest.',
		// Consultation contacts.
		'consult_contact1_role'     => 'Tootmisjuht — Andre Merila',
		'consult_contact2_role'     => 'Projektijuht — Marilyn Salu',
		'consult_visit_title'       => 'Töökoja ja näidiste külastus',
		'consult_visit_text'        => 'Tutvuge materjalide, viimistluste ja töös olevate projektidega meie töökojas aadressil Võidu 75, Tallinn, kokkuleppel.',
		// Enquiry form.
		'form_honeypot_label'       => 'Jätke see väli tühjaks',
		'form_success'              => 'Aitäh. Teie päring on edastatud meie projektitiimile. Vaatame teie briefi üle 48 tunni jooksul.',
		'form_error'                => 'Teie päringu saatmisel läks midagi valesti. Palun kirjutage otse aadressil marilyn@mooblipoisid.ee.',
		'form_label_name'           => 'Täisnimi *',
		'form_placeholder_name'     => 'nt Astrid Lindgren',
		'form_label_company'        => 'Ettevõte / koht *',
		'form_placeholder_company'  => 'nt Kohvik Nordlys OÜ',
		'form_label_email'          => 'E-posti aadress *',
		'form_label_phone'          => 'Telefoninumber',
		'form_label_project_type'   => 'Projekti tüüp *',
		'form_option_pick'          => 'Palun valige…',
		'form_opt_type_cafe'        => 'Kohvik, baar või restoran',
		'form_opt_type_retail'      => 'Jaemüügipood / kaubanduslik sisekujundus',
		'form_opt_type_counters'    => 'Ainult letid või istekohad',
		'form_opt_type_multi'       => 'Mitme objekti lahendus',
		'form_opt_type_private'     => 'Eraköök',
		'form_label_location'       => 'Koha / poe asukoht',
		'form_placeholder_location' => 'nt Tallinn, Stockholm, Helsinki, Riia',
		'form_label_brief'          => 'Brief ja mõõdud',
		'form_placeholder_brief'    => 'Rääkige meile, mida vajate: nt 6 m baar espresso-jaama ja letialuste külmikutega, 40 istekohta pingiistmetel, bränditud kassalauk. Lisage eelarvevahemik või arhitekti andmed, kui need on olemas.',
		'form_upload_label'         => 'Lisage plaanid, joonised või kontseptsioonitahvlid',
		'form_upload_hint'          => 'PDF, DWG, PNG, JPEG kuni 25MB',
		'form_file_remove'          => 'Eemalda fail',
		'form_submit'               => 'Saada projektipäring',
		// Header & buttons.
		'header_cta_label'          => 'Küsi pakkumist',
		'backtotop_label'           => 'Tagasi üles',
		// Footer.
		'footer_tagline'            => 'Erilahendusega mööbel ja sisustus kohvikutele, baaridele, restoranidele ja jaemüügile, lisaks eraköögid, valmistatud kestva põhjalikkusega.',
		'footer_label_workshop'     => 'Töökoda',
		'footer_label_discover'     => 'Avasta',
		'footer_label_searches'     => 'Otsingud',
		'footer_searches_text'      => 'Äripindade siseviimistluse, jaemüügikettide laiendamise ja arhitektide koostöö jaoks saatke oma brief otse meie projektide lauale.',
		'footer_cta_label'          => 'Küsi pakkumist',
		'footer_copyright'          => 'Kõik õigused kaitstud.',
		'footer_legal_commission'   => 'Komisjonitingimused',
		'footer_legal_materials'    => 'Materjalide päritolu',
		'footer_legal_privacy'      => 'Privaatsuspoliitika',
		// FAQ section heading (empty: rendered once the editor fills it in — the
		// questions and answers themselves live under the FAQ menu).
		'faq_heading'               => '',
		'faq_sub'                   => '',
		// SEO & Sharing (empty until an editor fills them in).
		'seo_meta_title'            => '',
		'seo_meta_description'      => '',
		'seo_social_image'          => '',
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
			'description' => __( 'The large photo behind the hero. Click “Select image” to upload or choose one from the media library, or paste an image address (URL).', 'mooblipoisid-child' ),
			'type'        => 'image',
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
		'ethos_image'      => array(
			'label'       => __( 'Ethos Image Address', 'mooblipoisid-child' ),
			'description' => __( 'The workshop photo in the dark band. Click “Select image” to upload or choose one from the media library, or paste an image address (URL).', 'mooblipoisid-child' ),
			'type'        => 'image',
			'section'     => 'ethos',
		),
		'ethos_image_alt'  => array(
			'label'       => __( 'Ethos Image Description', 'mooblipoisid-child' ),
			'description' => __( 'A short description of the workshop photo, used by screen readers and search engines.', 'mooblipoisid-child' ),
			'type'        => 'text',
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

		// "Who We Build For" section head + cards.
		'sectors_eyebrow'          => array(
			'label'       => __( 'Small Heading', 'mooblipoisid-child' ),
			'description' => __( 'The small uppercase label above the section heading.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'sectors',
		),
		'sectors_title'            => array(
			'label'       => __( 'Section Heading', 'mooblipoisid-child' ),
			'description' => __( 'The big heading of the section.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'sectors',
		),
		'sectors_intro'            => array(
			'label'       => __( 'Intro Text', 'mooblipoisid-child' ),
			'description' => __( 'The paragraph under the heading.', 'mooblipoisid-child' ),
			'type'        => 'textarea',
			'section'     => 'sectors',
		),
		'sector_title_hospitality' => array(
			'label'       => __( 'Card 1 (Cafés & Bars) Title', 'mooblipoisid-child' ),
			'description' => __( 'The headline shown on the first sector card.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'sectors',
		),
		'sector_title_retail'      => array(
			'label'       => __( 'Card 2 (Retail) Title', 'mooblipoisid-child' ),
			'description' => __( 'The headline shown on the second sector card.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'sectors',
		),
		'sector_title_private'     => array(
			'label'       => __( 'Card 3 (Private Dining) Title', 'mooblipoisid-child' ),
			'description' => __( 'The headline shown on the third sector card.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'sectors',
		),
		'sector_alt_hospitality'   => array(
			'label'       => __( 'Card 1 Photo Description', 'mooblipoisid-child' ),
			'description' => __( 'A short description of the first card photo, used by screen readers.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'sectors',
		),
		'sector_alt_retail'        => array(
			'label'       => __( 'Card 2 Photo Description', 'mooblipoisid-child' ),
			'description' => __( 'A short description of the second card photo, used by screen readers.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'sectors',
		),
		'sector_alt_private'       => array(
			'label'       => __( 'Card 3 Photo Description', 'mooblipoisid-child' ),
			'description' => __( 'A short description of the third card photo, used by screen readers.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'sectors',
		),
		'sectors_link_hospitality' => array(
			'label'       => __( 'Card 1 Link Text', 'mooblipoisid-child' ),
			'description' => __( 'The link text at the bottom of the first card.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'sectors',
		),
		'sectors_link_retail'      => array(
			'label'       => __( 'Card 2 Link Text', 'mooblipoisid-child' ),
			'description' => __( 'The link text at the bottom of the second card.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'sectors',
		),
		'sectors_link_private'     => array(
			'label'       => __( 'Card 3 Link Text', 'mooblipoisid-child' ),
			'description' => __( 'The link text at the bottom of the third card.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'sectors',
		),

		// "What We Make" section.
		'ranges_eyebrow'         => array(
			'label'       => __( 'Small Heading', 'mooblipoisid-child' ),
			'description' => __( 'The small uppercase label above the section heading.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'ranges',
		),
		'ranges_title'           => array(
			'label'       => __( 'Section Heading', 'mooblipoisid-child' ),
			'description' => __( 'The big heading of the section.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'ranges',
		),
		'ranges_intro'           => array(
			'label'       => __( 'Intro Text', 'mooblipoisid-child' ),
			'description' => __( 'The paragraph under the heading.', 'mooblipoisid-child' ),
			'type'        => 'textarea',
			'section'     => 'ranges',
		),
		'ranges_col_hospitality' => array(
			'label'       => __( 'Left Column Title (Hospitality)', 'mooblipoisid-child' ),
			'description' => __( 'The title of the first product-range column.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'ranges',
		),
		'ranges_col_retail'      => array(
			'label'       => __( 'Right Column Title (Retail)', 'mooblipoisid-child' ),
			'description' => __( 'The title of the second product-range column.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'ranges',
		),

		// "Selected Commissions" section.
		'projects_eyebrow'   => array(
			'label'       => __( 'Small Heading', 'mooblipoisid-child' ),
			'description' => __( 'The small uppercase label above the section heading.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'projects',
		),
		'projects_title'     => array(
			'label'       => __( 'Section Heading', 'mooblipoisid-child' ),
			'description' => __( 'The big heading of the section.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'projects',
		),
		'projects_card_link' => array(
			'label'       => __( 'Card Link Text', 'mooblipoisid-child' ),
			'description' => __( 'The link text at the bottom of every project card.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'projects',
		),

		// Capabilities section.
		'caps_eyebrow' => array(
			'label'       => __( 'Small Heading', 'mooblipoisid-child' ),
			'description' => __( 'The small uppercase label above the section heading.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'capabilities',
		),
		'caps_title'   => array(
			'label'       => __( 'Section Heading', 'mooblipoisid-child' ),
			'description' => __( 'The big heading of the section.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'capabilities',
		),
		'caps_intro'   => array(
			'label'       => __( 'Intro Text', 'mooblipoisid-child' ),
			'description' => __( 'The paragraph under the heading.', 'mooblipoisid-child' ),
			'type'        => 'textarea',
			'section'     => 'capabilities',
		),

		// Process section.
		'process_eyebrow' => array(
			'label'       => __( 'Small Heading', 'mooblipoisid-child' ),
			'description' => __( 'The small uppercase label above the section heading.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'process',
		),
		'process_title'   => array(
			'label'       => __( 'Section Heading', 'mooblipoisid-child' ),
			'description' => __( 'The big heading of the section.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'process',
		),
		'process_intro'   => array(
			'label'       => __( 'Intro Text', 'mooblipoisid-child' ),
			'description' => __( 'The paragraph under the heading.', 'mooblipoisid-child' ),
			'type'        => 'textarea',
			'section'     => 'process',
		),

		// Stories section.
		'stories_eyebrow' => array(
			'label'       => __( 'Small Heading', 'mooblipoisid-child' ),
			'description' => __( 'The small uppercase label above the section heading.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'stories',
		),
		'stories_title'   => array(
			'label'       => __( 'Section Heading', 'mooblipoisid-child' ),
			'description' => __( 'The big heading of the section.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'stories',
		),

		// Ethos fixed copy (badge + points).
		'ethos_badge_title' => array(
			'label'       => __( 'Badge Title', 'mooblipoisid-child' ),
			'description' => __( 'The short label on the badge overlapping the workshop photo.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'ethos',
		),
		'ethos_badge_text'  => array(
			'label'       => __( 'Badge Text', 'mooblipoisid-child' ),
			'description' => __( 'The sentence under the badge title.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'ethos',
		),
		'ethos_point1_title' => array(
			'label'       => __( 'Point 1 Title', 'mooblipoisid-child' ),
			'description' => __( 'The heading of the first point under the quote (e.g. durability).', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'ethos',
		),
		'ethos_point1_text'  => array(
			'label'       => __( 'Point 1 Text', 'mooblipoisid-child' ),
			'description' => __( 'The sentence under the first point heading.', 'mooblipoisid-child' ),
			'type'        => 'textarea',
			'section'     => 'ethos',
		),
		'ethos_point2_title' => array(
			'label'       => __( 'Point 2 Title', 'mooblipoisid-child' ),
			'description' => __( 'The heading of the second point (e.g. one team).', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'ethos',
		),
		'ethos_point2_text'  => array(
			'label'       => __( 'Point 2 Text', 'mooblipoisid-child' ),
			'description' => __( 'The sentence under the second point heading.', 'mooblipoisid-child' ),
			'type'        => 'textarea',
			'section'     => 'ethos',
		),

		// Consultation contacts.
		'consult_contact1_role' => array(
			'label'       => __( 'Contact 1 Role (Production Manager)', 'mooblipoisid-child' ),
			'description' => __( 'The role line above the first phone/e-mail contact.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'consult',
		),
		'consult_contact2_role' => array(
			'label'       => __( 'Contact 2 Role (Project Manager)', 'mooblipoisid-child' ),
			'description' => __( 'The role line above the second e-mail/phone contact.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'consult',
		),
		'consult_visit_title'   => array(
			'label'       => __( 'Workshop Visit Title', 'mooblipoisid-child' ),
			'description' => __( 'The heading of the workshop-visit contact row.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'consult',
		),
		'consult_visit_text'    => array(
			'label'       => __( 'Workshop Visit Text', 'mooblipoisid-child' ),
			'description' => __( 'The sentence about visiting the workshop.', 'mooblipoisid-child' ),
			'type'        => 'textarea',
			'section'     => 'consult',
		),

		// Enquiry form.
		'form_honeypot_label'       => array(
			'label'       => __( 'Spam-protection Label (hidden)', 'mooblipoisid-child' ),
			'description' => __( 'Hidden from visitors; leave as is.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'form',
		),
		'form_success'              => array(
			'label'       => __( 'Success Message', 'mooblipoisid-child' ),
			'description' => __( 'Shown after the form is sent successfully.', 'mooblipoisid-child' ),
			'type'        => 'textarea',
			'section'     => 'form',
		),
		'form_error'                => array(
			'label'       => __( 'Error Message', 'mooblipoisid-child' ),
			'description' => __( 'Shown if sending fails.', 'mooblipoisid-child' ),
			'type'        => 'textarea',
			'section'     => 'form',
		),
		'form_label_name'           => array(
			'label'       => __( 'Full Name Label', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'form',
		),
		'form_placeholder_name'     => array(
			'label'       => __( 'Full Name Example Text', 'mooblipoisid-child' ),
			'description' => __( 'The grey example text inside the field.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'form',
		),
		'form_label_company'        => array(
			'label'       => __( 'Company / Venue Label', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'form',
		),
		'form_placeholder_company'  => array(
			'label'       => __( 'Company / Venue Example Text', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'form',
		),
		'form_label_email'          => array(
			'label'       => __( 'E-mail Label', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'form',
		),
		'form_label_phone'          => array(
			'label'       => __( 'Phone Label', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'form',
		),
		'form_label_project_type'   => array(
			'label'       => __( 'Project Type Label', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'form',
		),
		'form_option_pick'          => array(
			'label'       => __( 'Dropdown “Please Choose” Text', 'mooblipoisid-child' ),
			'description' => __( 'Shown in both dropdowns before a choice is made.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'form',
		),
		'form_opt_type_cafe'        => array(
			'label'       => __( 'Project Type: Cafés & Bars', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'form',
		),
		'form_opt_type_retail'      => array(
			'label'       => __( 'Project Type: Retail', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'form',
		),
		'form_opt_type_counters'    => array(
			'label'       => __( 'Project Type: Counters or Seating Only', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'form',
		),
		'form_opt_type_multi'       => array(
			'label'       => __( 'Project Type: Multi-location', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'form',
		),
		'form_opt_type_private'     => array(
			'label'       => __( 'Project Type: Private Kitchen', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'form',
		),
		'form_label_location'       => array(
			'label'       => __( 'Location Label', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'form',
		),
		'form_placeholder_location' => array(
			'label'       => __( 'Location Example Text', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'form',
		),
		'form_label_brief'          => array(
			'label'       => __( 'Brief & Measurements Label', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'form',
		),
		'form_placeholder_brief'    => array(
			'label'       => __( 'Brief Example Text', 'mooblipoisid-child' ),
			'description' => __( 'The grey example text inside the brief box.', 'mooblipoisid-child' ),
			'type'        => 'textarea',
			'section'     => 'form',
		),
		'form_upload_label'         => array(
			'label'       => __( 'File Upload Label', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'form',
		),
		'form_upload_hint'          => array(
			'label'       => __( 'File Upload Hint', 'mooblipoisid-child' ),
			'description' => __( 'The small text about allowed file types and size.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'form',
		),
		'form_file_remove'          => array(
			'label'       => __( 'Remove Attached File Label', 'mooblipoisid-child' ),
			'description' => __( 'Read aloud by screen readers on the “×” button next to an attached file; not visible on the screen.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'form',
		),
		'form_submit'               => array(
			'label'       => __( 'Submit Button Text', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'form',
		),

		// Header & buttons.
		'header_cta_label' => array(
			'label'       => __( 'Header Button Text', 'mooblipoisid-child' ),
			'description' => __( 'The button shown in the site header on every page.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'header',
		),
		'backtotop_label'  => array(
			'label'       => __( 'Back-to-top Button Label', 'mooblipoisid-child' ),
			'description' => __( 'Read aloud by screen readers; not visible on the screen.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'header',
		),

		// Footer.
		'footer_tagline'          => array(
			'label'       => __( 'Brand Tagline', 'mooblipoisid-child' ),
			'description' => __( 'The sentence under the logo in the footer.', 'mooblipoisid-child' ),
			'type'        => 'textarea',
			'section'     => 'footer',
		),
		'footer_label_workshop'   => array(
			'label'       => __( 'Column Title: Workshop', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'footer',
		),
		'footer_label_discover'   => array(
			'label'       => __( 'Column Title: Explore (links)', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'footer',
		),
		'footer_label_searches'   => array(
			'label'       => __( 'Column Title: Enquiries', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'footer',
		),
		'footer_searches_text'    => array(
			'label'       => __( 'Enquiries Text', 'mooblipoisid-child' ),
			'description' => __( 'The sentence inviting briefs in the footer.', 'mooblipoisid-child' ),
			'type'        => 'textarea',
			'section'     => 'footer',
		),
		'footer_cta_label'        => array(
			'label'       => __( 'Footer Button Text', 'mooblipoisid-child' ),
			'description' => __( 'The button text in the footer.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'footer',
		),
		'footer_copyright'        => array(
			'label'       => __( 'Copyright Text', 'mooblipoisid-child' ),
			'description' => __( 'The text after “© year name OÜ”.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'footer',
		),
		'footer_legal_commission' => array(
			'label'       => __( 'Legal Link: Commission Terms', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'footer',
		),
		'footer_legal_materials'  => array(
			'label'       => __( 'Legal Link: Material Provenance', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'footer',
		),
		'footer_legal_privacy'    => array(
			'label'       => __( 'Legal Link: Privacy Policy', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'footer',
		),

		// FAQ section head.
		'faq_heading'             => array(
			'label'       => __( 'FAQ Section Heading', 'mooblipoisid-child' ),
			'description' => __( 'The heading above the homepage FAQ section. The questions and answers themselves are managed under the FAQ menu.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'faq',
		),
		'faq_sub'                 => array(
			'label'       => __( 'FAQ Section Subline', 'mooblipoisid-child' ),
			'description' => __( 'The short paragraph under the FAQ heading.', 'mooblipoisid-child' ),
			'type'        => 'textarea',
			'section'     => 'faq',
		),

		// SEO & Sharing.
		'seo_meta_title'          => array(
			'label'       => __( 'SEO Meta Title', 'mooblipoisid-child' ),
			'description' => __( 'The browser-tab and search-result title. Keep it around 60 characters.', 'mooblipoisid-child' ),
			'type'        => 'text',
			'section'     => 'seo',
		),
		'seo_meta_description'    => array(
			'label'       => __( 'SEO Meta Description', 'mooblipoisid-child' ),
			'description' => __( 'The search-result snippet under the title. Keep it around 155 characters.', 'mooblipoisid-child' ),
			'type'        => 'textarea',
			'section'     => 'seo',
		),
		'seo_social_image'        => array(
			'label'       => __( 'Social Sharing Image', 'mooblipoisid-child' ),
			'description' => __( 'The image shown when the homepage is shared on social media. Click “Select image” to upload or choose one from the media library, or paste an image address (URL).', 'mooblipoisid-child' ),
			'type'        => 'image',
			'section'     => 'seo',
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
		'hero'         => array(
			'title'       => __( 'Hero (the top of the homepage)', 'mooblipoisid-child' ),
			'description' => __( 'The large opening panel with the headline, two buttons and the background photo.', 'mooblipoisid-child' ),
		),
		'sectors'      => array(
			'title'       => __( '“Who We Build For” Section', 'mooblipoisid-child' ),
			'description' => __( 'The heading and the three sector cards (Cafés & Bars, Retail, Private dining) with their photos and links.', 'mooblipoisid-child' ),
		),
		'ranges'       => array(
			'title'       => __( '“What We Make” Section', 'mooblipoisid-child' ),
			'description' => __( 'The heading and the two product-range column titles.', 'mooblipoisid-child' ),
		),
		'projects'     => array(
			'title'       => __( '“Selected Commissions” Section', 'mooblipoisid-child' ),
			'description' => __( 'The heading above the project cards and the link text on each card.', 'mooblipoisid-child' ),
		),
		'capabilities' => array(
			'title'       => __( '“Capabilities” Section', 'mooblipoisid-child' ),
			'description' => __( 'The heading and intro above the four capability boxes.', 'mooblipoisid-child' ),
		),
		'process'      => array(
			'title'       => __( '“The Process” Section', 'mooblipoisid-child' ),
			'description' => __( 'The heading and intro above the numbered process steps.', 'mooblipoisid-child' ),
		),
		'stories'      => array(
			'title'       => __( '“Client Stories” Section', 'mooblipoisid-child' ),
			'description' => __( 'The heading above the client testimonials.', 'mooblipoisid-child' ),
		),
		'ethos'        => array(
			'title'       => __( 'Ethos Band (the dark workshop band)', 'mooblipoisid-child' ),
			'description' => __( 'The dark band that explains how and why we build: quote, badge and the two points.', 'mooblipoisid-child' ),
		),
		'consult'      => array(
			'title'       => __( 'Consultation Band & Contacts', 'mooblipoisid-child' ),
			'description' => __( 'The introduction and the contact people above the enquiry form.', 'mooblipoisid-child' ),
		),
		'form'         => array(
			'title'       => __( 'Enquiry Form', 'mooblipoisid-child' ),
			'description' => __( 'Every label, placeholder (example text), dropdown option and message of the enquiry form.', 'mooblipoisid-child' ),
		),
		'header'       => array(
			'title'       => __( 'Header & Buttons', 'mooblipoisid-child' ),
			'description' => __( 'The button in the site header and the back-to-top button label.', 'mooblipoisid-child' ),
		),
		'footer'       => array(
			'title'       => __( 'Footer', 'mooblipoisid-child' ),
			'description' => __( 'The text pieces of the site footer: tagline, column labels, links, copyright and legal links.', 'mooblipoisid-child' ),
		),
		'faq'          => array(
			'title'       => __( 'FAQ Section', 'mooblipoisid-child' ),
			'description' => __( 'The heading of the homepage FAQ section. The questions and answers themselves are managed under the FAQ menu.', 'mooblipoisid-child' ),
		),
		'seo'          => array(
			'title'       => __( 'SEO & Sharing', 'mooblipoisid-child' ),
			'description' => __( 'How the homepage appears in search engines and when it is shared on social media.', 'mooblipoisid-child' ),
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
 * Load the media library scripts and styles for screens with media-picker
 * fields: the Homepage Settings screen, the sector term screens (the
 * "Sector Image" field) and the page edit screen (the page "SEO" metabox).
 *
 * The inline script binds every `.mp-media-field` block on the screen to the
 * standard WordPress media modal (live preview, Select image, Clear image).
 *
 * @param string $hook_suffix Current admin page.
 */
function mooblipoisid_admin_media_picker_assets( $hook_suffix ) {
	$is_homepage_settings = ( 'settings_page_mooblipoisid_homepage_settings' === $hook_suffix );

	$is_sector_screen = in_array( $hook_suffix, array( 'edit-tags.php', 'term.php' ), true )
		&& isset( $_GET['taxonomy'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		&& 'sector' === sanitize_key( wp_unslash( $_GET['taxonomy'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	$is_page_editor = in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true );
	if ( $is_page_editor ) {
		$editor_post_type = '';
		if ( isset( $_GET['post'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$editor_post_type = (string) get_post_type( absint( $_GET['post'] ) );
		} elseif ( isset( $_GET['post_type'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$editor_post_type = sanitize_key( wp_unslash( $_GET['post_type'] ) );
		}
		$is_page_editor = ( 'page' === $editor_post_type );
	}

	if ( ! $is_homepage_settings && ! $is_sector_screen && ! $is_page_editor ) {
		return;
	}

	wp_enqueue_media();

	wp_register_script( 'mooblipoisid-homepage-settings-media', '', array( 'media-editor' ), null, true );
	wp_enqueue_script( 'mooblipoisid-homepage-settings-media' );

	wp_localize_script(
		'mooblipoisid-homepage-settings-media',
		'mpHomepageMedia',
		array(
			'modalTitle'  => __( 'Choose an image', 'mooblipoisid-child' ),
			'modalButton' => __( 'Use this image', 'mooblipoisid-child' ),
		)
	);

	$script = <<<'JS'
/* MP-MEDIA-JS-START */
(function () {
	'use strict';

	function ready(fn) {
		if (document.readyState !== 'loading') { fn(); } else { document.addEventListener('DOMContentLoaded', fn); }
	}

	ready(function () {
		var i18n = window.mpHomepageMedia || { modalTitle: 'Choose an image', modalButton: 'Use this image' };

		Array.prototype.forEach.call(document.querySelectorAll('.mp-media-field'), function (wrap) {
			var input = wrap.querySelector('.mp-media-field__input');
			var preview = wrap.querySelector('.mp-media-field__preview');
			var empty = wrap.querySelector('.mp-media-field__empty');
			var choose = wrap.querySelector('.mp-media-field__choose');
			var clear = wrap.querySelector('.mp-media-field__clear');
			var frame = null;

			function sync() {
				var url = input.value.replace(/\s+/g, '');
				if (url) {
					preview.src = url;
					preview.hidden = false;
					empty.hidden = true;
					clear.hidden = false;
				} else {
					preview.hidden = true;
					preview.removeAttribute('src');
					empty.hidden = false;
					clear.hidden = true;
				}
			}

			choose.addEventListener('click', function (event) {
				event.preventDefault();

				if (!window.wp || !wp.media) { return; }

				if (frame) { frame.open(); return; }

				frame = wp.media({
					title: i18n.modalTitle,
					button: { text: i18n.modalButton },
					library: { type: 'image' },
					multiple: false
				});

				frame.on('select', function () {
					var attachment = frame.state().get('selection').first();
					if (!attachment) { return; }
					input.value = attachment.get('url');
					sync();
				});

				frame.open();
			});

			if (clear) {
				clear.addEventListener('click', function (event) {
					event.preventDefault();
					input.value = '';
					sync();
				});
			}

			input.addEventListener('change', sync);
			input.addEventListener('paste', function () { window.setTimeout(sync, 0); });
			sync();
		});

		// The "Add New Sector" form saves over AJAX and WordPress then empties
		// the inputs without firing change events — re-sync the previews.
		if (window.jQuery) {
			jQuery(document).ajaxComplete(function (event, xhr, settings) {
				if (settings && settings.data && String(settings.data).indexOf('action=add-tag') !== -1) {
					Array.prototype.forEach.call(document.querySelectorAll('.mp-media-field'), function (wrap) {
						var input = wrap.querySelector('.mp-media-field__input');
						if (input) { input.dispatchEvent(new Event('change')); }
					});
				}
			});
		}
	});
})();
/* MP-MEDIA-JS-END */
JS;
	wp_add_inline_script( 'mooblipoisid-homepage-settings-media', $script );

	wp_register_style( 'mooblipoisid-homepage-settings-media', '', array(), null );
	wp_enqueue_style( 'mooblipoisid-homepage-settings-media' );
	wp_add_inline_style(
		'mooblipoisid-homepage-settings-media',
		'.mp-media-field__preview{max-width:320px;max-height:160px;width:auto;height:auto;padding:4px;border:1px solid #dcdcde;border-radius:4px;background:#f0f0f1;object-fit:contain;}'
		. '.mp-media-field__empty{margin:0 0 8px;color:#646970;font-style:italic;}'
		. '.mp-media-field__buttons{margin:8px 0 0;}'
		. '.mp-media-field .button-link-delete{margin-left:8px;}'
	);
}
add_action( 'admin_enqueue_scripts', 'mooblipoisid_admin_media_picker_assets' );

/**
 * Render the shared media-picker field markup: live preview, URL input and
 * Select/Clear buttons. The behaviour is wired up by the inline media script
 * (see mooblipoisid_admin_media_picker_assets()), which binds every
 * `.mp-media-field` block on the screen to the WordPress media modal.
 *
 * Used by the Homepage Settings "image" fields, the "Sector Image" field on
 * the sector term screens and the page "SEO" metabox.
 *
 * @param string $name        Input name attribute (the submitted key).
 * @param string $field_id    Input id attribute.
 * @param string $value       Current image URL.
 * @param string $input_class Extra classes for the input (e.g. large-text).
 */
function mooblipoisid_render_media_picker_field( $name, $field_id, $value, $input_class = '' ) {
	$value = is_scalar( $value ) ? (string) $value : '';
	?>
	<div class="mp-media-field">
		<img class="mp-media-field__preview" src="<?php echo esc_url( $value ); ?>" alt="" <?php echo '' === $value ? 'hidden' : ''; ?> />
		<p class="mp-media-field__empty" <?php echo '' === $value ? '' : 'hidden'; ?>><?php esc_html_e( 'No image selected yet.', 'mooblipoisid-child' ); ?></p>
		<input type="text" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $field_id ); ?>" value="<?php echo esc_attr( $value ); ?>" class="mp-media-field__input <?php echo esc_attr( $input_class ); ?>" />
		<p class="mp-media-field__buttons">
			<button type="button" class="button mp-media-field__choose"><?php esc_html_e( 'Select image', 'mooblipoisid-child' ); ?></button>
			<button type="button" class="button button-link-delete mp-media-field__clear" <?php echo '' === $value ? 'hidden' : ''; ?>><?php esc_html_e( 'Clear image', 'mooblipoisid-child' ); ?></button>
		</p>
	</div>
	<?php
}

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

	if ( 'image' === $field['type'] ) {
		mooblipoisid_render_media_picker_field( $name, $field_id, $value, 'large-text' );
	} elseif ( 'textarea' === $field['type'] ) {
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

	/*
	 * The admin form submits the fields nested under the language being edited
	 * (mp_homepage_settings[ru][field_id]), so unwrap that level. A flat array
	 * (the fields themselves) is accepted too, for programmatic callers.
	 */
	if ( isset( $input[ $language ] ) && is_array( $input[ $language ] ) ) {
		$input = $input[ $language ];
	}

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

		if ( 'url' === $field['type'] || 'image' === $field['type'] ) {
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

/* ==========================================================================
 * 4. Page SEO (regular Pages)
 * ========================================================================== */

/**
 * Field configuration for the "SEO" meta box on regular Pages.
 *
 * Every page is one language's post, so these fields are single values — not
 * stored per language like the Homepage Settings.
 *
 * @return array[]
 */
function mooblipoisid_page_seo_meta_fields() {
	return array(
		'_mooblipoisid_seo_title' => array(
			'label'       => __( 'SEO Title', 'mooblipoisid-child' ),
			'description' => __( 'The browser-tab and search-result title of this page. Keep it around 60 characters.', 'mooblipoisid-child' ),
			'type'        => 'text',
		),
		'_mooblipoisid_seo_description' => array(
			'label'       => __( 'SEO Description', 'mooblipoisid-child' ),
			'description' => __( 'The search-result snippet under the title. Keep it around 155 characters.', 'mooblipoisid-child' ),
			'type'        => 'textarea',
		),
		'_mooblipoisid_seo_social_image' => array(
			'label'       => __( 'Social Sharing Image', 'mooblipoisid-child' ),
			'description' => __( 'The image shown when this page is shared on social media. Pick it from the Media Library.', 'mooblipoisid-child' ),
			'type'        => 'image',
		),
	);
}

/**
 * Add the "SEO" meta box to regular Pages.
 */
function mooblipoisid_add_page_seo_meta_box() {
	add_meta_box(
		'mooblipoisid_page_seo',
		__( 'SEO', 'mooblipoisid-child' ),
		'mooblipoisid_render_page_seo_meta_box',
		'page',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'mooblipoisid_add_page_seo_meta_box' );

/**
 * Render the "SEO" meta box for a page.
 *
 * @param WP_Post $post Post being edited.
 */
function mooblipoisid_render_page_seo_meta_box( $post ) {
	wp_nonce_field( 'mooblipoisid_page_seo', 'mooblipoisid_page_seo_nonce' );
	?>
	<p class="description"><?php esc_html_e( 'How this page appears in search engines and when it is shared on social media. Everything here is optional.', 'mooblipoisid-child' ); ?></p>
	<table class="form-table" role="presentation">
		<tbody>
		<?php foreach ( mooblipoisid_page_seo_meta_fields() as $key => $field ) : ?>
			<?php
			$value = get_post_meta( $post->ID, $key, true );
			$value = is_scalar( $value ) ? (string) $value : '';
			?>
			<tr>
				<th scope="row">
					<label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
				</th>
				<td>
					<?php if ( 'image' === $field['type'] ) : ?>
						<?php mooblipoisid_render_media_picker_field( $key, $key, $value ); ?>
					<?php elseif ( 'textarea' === $field['type'] ) : ?>
						<textarea name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>" rows="4" class="large-text code"><?php echo esc_textarea( $value ); ?></textarea>
					<?php else : ?>
						<input type="text" class="large-text" name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $value ); ?>" />
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
 * Save the page SEO meta with nonce, capability and sanitization checks.
 *
 * Runs only for pages: the nonce below is only present on the page edit screen,
 * and the post type is checked again before anything is stored, so revisions,
 * autosaves and other post types are left untouched.
 *
 * @param int $post_id Post being saved.
 */
function mooblipoisid_save_page_seo_meta( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}
	if ( ! isset( $_POST['mooblipoisid_page_seo_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mooblipoisid_page_seo_nonce'] ) ), 'mooblipoisid_page_seo' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( 'page' !== get_post_type( $post_id ) ) {
		return;
	}

	foreach ( mooblipoisid_page_seo_meta_fields() as $key => $field ) {
		if ( ! isset( $_POST[ $key ] ) ) {
			continue;
		}

		$raw = wp_unslash( $_POST[ $key ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput

		if ( 'image' === $field['type'] ) {
			update_post_meta( $post_id, $key, esc_url_raw( $raw ) );
			continue;
		}

		if ( 'textarea' === $field['type'] ) {
			update_post_meta( $post_id, $key, sanitize_textarea_field( $raw ) );
			continue;
		}

		update_post_meta( $post_id, $key, sanitize_text_field( $raw ) );
	}
}
add_action( 'save_post', 'mooblipoisid_save_page_seo_meta' );

/* ==========================================================================
 * 5. Company info (Settings → Mööblipoisid: Company info)
 * ========================================================================== */

/**
 * The single option holding the company contact details.
 */
function mooblipoisid_company_info_option_name() {
	return 'mooblipoisid_company_info';
}

/**
 * Field configuration for the Company info screen.
 *
 * The address is stored once per language (`address_et`, `address_ru`,
 * `address_en`); every other field is a single value.
 *
 * @return array[]
 */
function mooblipoisid_company_info_fields() {
	return array(
		'legal_name' => array(
			'label'       => __( 'Legal Name', 'mooblipoisid-child' ),
			'description' => __( 'The official company name, e.g. as it appears on invoices.', 'mooblipoisid-child' ),
			'type'        => 'text',
		),
		'reg_code'   => array(
			'label'       => __( 'Registration Code', 'mooblipoisid-child' ),
			'description' => __( 'The company registration number.', 'mooblipoisid-child' ),
			'type'        => 'text',
		),
		'phone'      => array(
			'label' => __( 'Phone', 'mooblipoisid-child' ),
			'type'  => 'text',
		),
		'phone_alt'  => array(
			'label' => __( 'Second Phone', 'mooblipoisid-child' ),
			'type'  => 'text',
		),
		'email'      => array(
			'label' => __( 'E-mail', 'mooblipoisid-child' ),
			'type'  => 'text',
		),
		'email_alt'  => array(
			'label' => __( 'Second E-mail', 'mooblipoisid-child' ),
			'type'  => 'text',
		),
		'facebook'   => array(
			'label' => __( 'Facebook URL', 'mooblipoisid-child' ),
			'type'  => 'url',
		),
		'instagram'  => array(
			'label' => __( 'Instagram URL', 'mooblipoisid-child' ),
			'type'  => 'url',
		),
		'linkedin'   => array(
			'label' => __( 'LinkedIn URL', 'mooblipoisid-child' ),
			'type'  => 'url',
		),
		'address_et' => array(
			'label' => __( 'Address (Estonian)', 'mooblipoisid-child' ),
			'type'  => 'textarea',
		),
		'address_ru' => array(
			'label' => __( 'Address (Russian)', 'mooblipoisid-child' ),
			'type'  => 'textarea',
		),
		'address_en' => array(
			'label' => __( 'Address (English)', 'mooblipoisid-child' ),
			'type'  => 'textarea',
		),
	);
}

/**
 * The address field of each language: language slug => field id.
 *
 * @return array
 */
function mooblipoisid_company_info_address_fields() {
	return array(
		'et' => 'address_et',
		'ru' => 'address_ru',
		'en' => 'address_en',
	);
}

/**
 * Read the company contact details, with the address of the active language.
 *
 * Every field is a plain string (empty until it is filled in on the screen).
 * On top of the stored fields the array carries a resolved `address` key: the
 * address text of the language being viewed, or an empty string when that
 * language has no address of its own (no cross-language fallback).
 *
 * @return array
 */
function mooblipoisid_get_company_info() {
	$stored = get_option( mooblipoisid_company_info_option_name(), array() );
	$stored = is_array( $stored ) ? $stored : array();

	$info = array();
	foreach ( mooblipoisid_company_info_fields() as $field_id => $field ) {
		$value = isset( $stored[ $field_id ] ) ? $stored[ $field_id ] : '';
		$info[ $field_id ] = is_scalar( $value ) ? (string) $value : '';
	}

	$addresses = mooblipoisid_company_info_address_fields();
	$language  = mooblipoisid_current_language_code();

	$info['address'] = isset( $addresses[ $language ] ) ? $info[ $addresses[ $language ] ] : '';

	return $info;
}

/**
 * Sanitize the company info before it is stored.
 *
 * Text fields are stripped to single-line plain text, URL fields are stored as
 * raw URLs and the address textareas keep their line breaks.
 *
 * @param array $input Raw input (one key per field).
 * @return array Sanitized value to store.
 */
function mooblipoisid_sanitize_company_info( $input ) {
	$fields = mooblipoisid_company_info_fields();
	$input  = is_array( $input ) ? $input : array();
	$clean  = array();

	foreach ( $fields as $field_id => $field ) {
		$value = isset( $input[ $field_id ] ) ? $input[ $field_id ] : '';
		$value = is_scalar( $value ) ? (string) $value : '';

		if ( 'textarea' === $field['type'] ) {
			$clean[ $field_id ] = sanitize_textarea_field( $value );
			continue;
		}

		$value = sanitize_text_field( $value );

		if ( 'url' === $field['type'] ) {
			$value = esc_url_raw( $value );
		}

		$clean[ $field_id ] = $value;
	}

	return $clean;
}

/**
 * Register the Company info settings (Settings → Mööblipoisid: Company info).
 */
function mooblipoisid_register_company_info_settings() {
	register_setting(
		'mooblipoisid_company_info_group',
		mooblipoisid_company_info_option_name(),
		array(
			'type'              => 'array',
			'sanitize_callback' => 'mooblipoisid_sanitize_company_info',
			'default'           => array(),
		)
	);

	add_settings_section(
		'mooblipoisid_company_info_main',
		__( 'Company Details', 'mooblipoisid-child' ),
		function () {
			echo '<p>' . esc_html__( 'The company contact details used across the site. The address is stored once per language; the site shows the address of the language being viewed.', 'mooblipoisid-child' ) . '</p>';
		},
		'mooblipoisid_company_info'
	);

	foreach ( mooblipoisid_company_info_fields() as $field_id => $field ) {
		add_settings_field(
			$field_id,
			$field['label'],
			'mooblipoisid_render_company_info_field',
			'mooblipoisid_company_info',
			'mooblipoisid_company_info_main',
			array(
				'label_for' => $field_id,
				'field_id'  => $field_id,
				'field'     => $field,
			)
		);
	}
}
add_action( 'admin_init', 'mooblipoisid_register_company_info_settings' );

/**
 * Add the Company info screen under Settings.
 */
function mooblipoisid_add_company_info_page() {
	add_options_page(
		__( 'Mööblipoisid: Company info', 'mooblipoisid-child' ),
		__( 'Mööblipoisid: Company info', 'mooblipoisid-child' ),
		'manage_options',
		'mooblipoisid_company_info',
		'mooblipoisid_render_company_info_page'
	);
}
add_action( 'admin_menu', 'mooblipoisid_add_company_info_page' );

/**
 * Render one Company info field.
 *
 * @param array $args Field arguments passed from add_settings_field().
 */
function mooblipoisid_render_company_info_field( $args ) {
	$field_id    = $args['field_id'];
	$field       = $args['field'];
	$option_name = mooblipoisid_company_info_option_name();

	$options = get_option( $option_name, array() );
	$options = is_array( $options ) ? $options : array();

	$value = isset( $options[ $field_id ] ) ? $options[ $field_id ] : '';
	$value = is_scalar( $value ) ? (string) $value : '';

	$name = $option_name . '[' . $field_id . ']';

	if ( 'textarea' === $field['type'] ) {
		printf(
			'<textarea name="%1$s" id="%2$s" rows="3" class="large-text code">%3$s</textarea>',
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
 * Render the Company info screen.
 */
function mooblipoisid_render_company_info_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Mööblipoisid: Company info', 'mooblipoisid-child' ); ?></h1>
		<p class="description">
			<?php esc_html_e( 'The company contact details used across the site. Nothing here is shown on the site until it is filled in.', 'mooblipoisid-child' ); ?>
		</p>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'mooblipoisid_company_info_group' );
			do_settings_sections( 'mooblipoisid_company_info' );
			submit_button( __( 'Save Company Info', 'mooblipoisid-child' ) );
			?>
		</form>
	</div>
	<?php
}