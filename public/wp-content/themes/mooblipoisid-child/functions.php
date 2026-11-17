<?php
/**
 * Mööblipoisid child theme functions.
 *
 * @package mooblipoisid-child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Homepage content model: post types, taxonomies, entry details and the
 * Homepage Settings screen. Kept in its own file so functions.php stays
 * focused on the front-end behaviour.
 */
require get_stylesheet_directory() . '/inc/content-model.php';

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
			wp_get_theme()->get( 'Version' )
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
 * Render the project enquiry form.
 */
function mooblipoisid_enquiry_form_shortcode() {
	$success = isset( $_GET['enquiry'] ) && 'sent' === $_GET['enquiry']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$error   = isset( $_GET['enquiry'] ) && 'error' === $_GET['enquiry']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	ob_start();
	?>
	<form class="mp-form" method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="mooblipoisid_enquiry" />
		<?php wp_nonce_field( 'mooblipoisid_enquiry', 'mooblipoisid_enquiry_nonce' ); ?>
		<div class="mp-form__hp" aria-hidden="true">
			<label>Leave this field empty<input type="text" name="mp_hp" tabindex="-1" autocomplete="off" /></label>
		</div>

		<div class="mp-form__panel">
		<?php if ( $success ) : ?>
			<p class="mp-form__success" role="status"><svg aria-hidden="true" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" style="flex:none"><path d="M20 6L9 17l-5-5"/></svg><?php esc_html_e( 'Thank you. Your enquiry has been routed to our project team. We will review your brief within 48 hours.', 'mooblipoisid-child' ); ?></p>
		<?php endif; ?>
		<?php if ( $error ) : ?>
			<p class="mp-form__error" role="alert"><?php esc_html_e( 'Something went wrong sending your enquiry. Please email marilyn@mooblipoisid.ee directly.', 'mooblipoisid-child' ); ?></p>
		<?php endif; ?>
		<div class="mp-form__row">
			<div>
				<label for="mp_name"><?php esc_html_e( 'Full Name *', 'mooblipoisid-child' ); ?></label>
				<input type="text" id="mp_name" name="mp_name" placeholder="e.g. Astrid Lindgren" required />
			</div>
			<div>
				<label for="mp_company"><?php esc_html_e( 'Company / Venue *', 'mooblipoisid-child' ); ?></label>
				<input type="text" id="mp_company" name="mp_company" placeholder="e.g. Café Nordlys OÜ" required />
			</div>
		</div>
		<div class="mp-form__row">
			<div>
				<label for="mp_email"><?php esc_html_e( 'Email Address *', 'mooblipoisid-child' ); ?></label>
				<input type="email" id="mp_email" name="mp_email" placeholder="name@company.com" required />
			</div>
			<div>
				<label for="mp_phone"><?php esc_html_e( 'Phone Number', 'mooblipoisid-child' ); ?></label>
				<input type="tel" id="mp_phone" name="mp_phone" placeholder="+372 5000 0000" />
			</div>
		</div>
		<div class="mp-form__row">
			<div>
				<label for="mp_project_type"><?php esc_html_e( 'Project Type *', 'mooblipoisid-child' ); ?></label>
				<select id="mp_project_type" name="mp_project_type" required>
					<option value=""><?php esc_html_e( 'Please choose…', 'mooblipoisid-child' ); ?></option>
					<option><?php esc_html_e( 'Café, Bar or Restaurant', 'mooblipoisid-child' ); ?></option>
					<option><?php esc_html_e( 'Retail Store / Shopfit', 'mooblipoisid-child' ); ?></option>
					<option><?php esc_html_e( 'Counters or Seating Only', 'mooblipoisid-child' ); ?></option>
					<option><?php esc_html_e( 'Multi-Site Roll-Out', 'mooblipoisid-child' ); ?></option>
					<option><?php esc_html_e( 'Private Kitchen', 'mooblipoisid-child' ); ?></option>
				</select>
			</div>
			<div>
				<label for="mp_window"><?php esc_html_e( 'Target Opening / Install Window', 'mooblipoisid-child' ); ?></label>
				<select id="mp_window" name="mp_window">
					<option value=""><?php esc_html_e( 'Please choose…', 'mooblipoisid-child' ); ?></option>
					<option><?php esc_html_e( 'Q1 2027', 'mooblipoisid-child' ); ?></option>
					<option><?php esc_html_e( 'Q2 2027', 'mooblipoisid-child' ); ?></option>
					<option><?php esc_html_e( 'Second Half of 2027', 'mooblipoisid-child' ); ?></option>
					<option><?php esc_html_e( 'Still Planning', 'mooblipoisid-child' ); ?></option>
				</select>
			</div>
		</div>
		<div>
			<label for="mp_location"><?php esc_html_e( 'Venue / Store Location', 'mooblipoisid-child' ); ?></label>
			<input type="text" id="mp_location" name="mp_location" placeholder="e.g. Tallinn, Stockholm, Helsinki, Riga" />
		</div>
		<div>
			<label for="mp_brief"><?php esc_html_e( 'Brief & Dimensions', 'mooblipoisid-child' ); ?></label>
			<textarea id="mp_brief" name="mp_brief" rows="4" placeholder="<?php esc_attr_e( 'Tell us what you need: e.g. 6 m bar with espresso station and under-counter fridges, 40 seats of banquette, branded checkout counter. Include budget range or architect details if you have them.', 'mooblipoisid-child' ); ?>"></textarea>
		</div>
		<div class="mp-form__upload">
			<svg aria-hidden="true" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><path d="M17 8l-5-5-5 5"/><path d="M12 3v12"/></svg>
			<label for="mp_files"><?php esc_html_e( 'Attach Floor Plans, Drawings or Concept Boards', 'mooblipoisid-child' ); ?></label>
			<input type="file" id="mp_files" name="mp_files[]" multiple accept=".pdf,.dwg,.png,.jpg,.jpeg,application/pdf,image/png,image/jpeg" />
			<p class="mp-form__upload-hint"><?php esc_html_e( 'PDF, DWG, PNG, JPEG up to 25MB', 'mooblipoisid-child' ); ?></p>
		</div>
		<div class="mp-form__submit">
			<button type="submit" class="mp-btn">
				<span><?php esc_html_e( 'Submit Project Enquiry', 'mooblipoisid-child' ); ?></span>
				<svg aria-hidden="true" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
			</button>
		</div>
		</div>
	</form>
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
		'window'       => sanitize_text_field( wp_unslash( $_POST['mp_window'] ?? '' ) ),
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
			$tmp = wp_tempnam( sanitize_file_name( $file_name ) );
			if ( ! $tmp || ! @move_uploaded_file( $_FILES['mp_files']['tmp_name'][ $i ], $tmp ) ) { // phpcs: ignore WordPress.PHP.NoSilencedErrors.Discouraged
				wp_safe_redirect( add_query_arg( 'enquiry', 'error', $redirect . '#consultation' ) );
				exit;
			}
			$attachments[] = $tmp;
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
		'Install window: ' . ( $fields['window'] ? $fields['window'] : '—' ),
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
