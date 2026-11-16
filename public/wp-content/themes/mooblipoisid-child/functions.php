<?php
/**
 * Mööblipoisid child theme functions.
 *
 * @package mooblipoisid-child
 */

defined( 'ABSPATH' ) || exit;

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

	if ( $success ) {
		echo '<p class="mp-form__success" role="status"><svg aria-hidden="true" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" style="flex:none;margin-top:2px"><path d="M20 6L9 17l-5-5"/></svg>' .
			esc_html__( 'Thank you. Your enquiry has been routed to our project team. We will review your brief within 48 hours.', 'mooblipoisid-child' ) .
			'</p>';
	}
	if ( $error ) {
		echo '<p class="mp-form__error" role="alert">' .
			esc_html__( 'Something went wrong sending your enquiry. Please email marilyn@mooblipoisid.ee directly.', 'mooblipoisid-child' ) .
			'</p>';
	}
	?>
	<form class="mp-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="mooblipoisid_enquiry" />
		<?php wp_nonce_field( 'mooblipoisid_enquiry', 'mooblipoisid_enquiry_nonce' ); ?>
		<div class="mp-form__hp" aria-hidden="true">
			<label>Leave this field empty<input type="text" name="mp_hp" tabindex="-1" autocomplete="off" /></label>
		</div>

		<div>
			<label for="mp_name"><?php esc_html_e( 'Full Name *', 'mooblipoisid-child' ); ?></label>
			<input type="text" id="mp_name" name="mp_name" required />
		</div>
		<div>
			<label for="mp_company"><?php esc_html_e( 'Company / Venue *', 'mooblipoisid-child' ); ?></label>
			<input type="text" id="mp_company" name="mp_company" required />
		</div>
		<div>
			<label for="mp_email"><?php esc_html_e( 'Email Address *', 'mooblipoisid-child' ); ?></label>
			<input type="email" id="mp_email" name="mp_email" required />
		</div>
		<div>
			<label for="mp_phone"><?php esc_html_e( 'Phone Number', 'mooblipoisid-child' ); ?></label>
			<input type="tel" id="mp_phone" name="mp_phone" />
		</div>
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
		<div class="mp-form__full">
			<label for="mp_location"><?php esc_html_e( 'Venue / Store Location', 'mooblipoisid-child' ); ?></label>
			<input type="text" id="mp_location" name="mp_location" />
		</div>
		<div class="mp-form__full">
			<label for="mp_brief"><?php esc_html_e( 'Brief & Dimensions', 'mooblipoisid-child' ); ?></label>
			<textarea id="mp_brief" name="mp_brief"></textarea>
		</div>
		<p class="mp-form__hint">
			<svg aria-hidden="true" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" style="flex:none"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><path d="M17 8l-5-5-5 5"/><path d="M12 3v12"/></svg>
			<?php esc_html_e( 'Attach floor plans, drawings or concept boards (PDF, DWG, PNG, JPEG up to 25MB) by forwarding them after our first reply.', 'mooblipoisid-child' ); ?>
		</p>
		<p class="mp-form__note"><?php esc_html_e( 'We respond with initial feedback and a budget direction within 48 hours.', 'mooblipoisid-child' ); ?></p>
		<button type="submit" class="mp-btn"><?php esc_html_e( 'Submit Project Enquiry', 'mooblipoisid-child' ); ?></button>
	</form>
	<?php

	return ob_get_clean();
}
add_shortcode( 'mooblipoisid_enquiry_form', 'mooblipoisid_enquiry_form_shortcode' );

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

	$sent = wp_mail( mooblipoisid_enquiry_recipients(), $subject, implode( "\n", $lines ), $headers );

	wp_safe_redirect( add_query_arg( 'enquiry', $sent ? 'sent' : 'error', $redirect . '#consultation' ) );
	exit;
}
add_action( 'admin_post_mooblipoisid_enquiry', 'mooblipoisid_handle_enquiry' );
add_action( 'admin_post_nopriv_mooblipoisid_enquiry', 'mooblipoisid_handle_enquiry' );
