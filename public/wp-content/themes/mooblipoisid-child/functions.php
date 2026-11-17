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
 * Translate the footer widget / legal-bar text (stored as plain HTML in the
 * database) through the theme's gettext domain, so each language shows its own
 * copy. The stored strings are the Estonian source; the .mo files hold the
 * Russian and English translations.
 *
 * @param string $content Widget HTML.
 * @return string Translated widget HTML.
 */
function mooblipoisid_translate_footer_html( $content ) {
	if ( ! is_string( $content ) || '' === $content ) {
		return $content;
	}
	return __( $content, 'mooblipoisid-child' );
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
			<label><?php esc_html_e( 'Jätke see väli tühjaks', 'mooblipoisid-child' ); ?><input type="text" name="mp_hp" tabindex="-1" autocomplete="off" /></label>
		</div>

		<div class="mp-form__panel">
		<?php if ( $success ) : ?>
			<p class="mp-form__success" role="status"><svg aria-hidden="true" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" style="flex:none"><path d="M20 6L9 17l-5-5"/></svg><?php esc_html_e( 'Aitäh. Teie päring on edastatud meie projektitiimile. Vaatame teie briefi üle 48 tunni jooksul.', 'mooblipoisid-child' ); ?></p>
		<?php endif; ?>
		<?php if ( $error ) : ?>
			<p class="mp-form__error" role="alert"><?php esc_html_e( 'Teie päringu saatmisel läks midagi valesti. Palun kirjutage otse aadressil marilyn@mooblipoisid.ee.', 'mooblipoisid-child' ); ?></p>
		<?php endif; ?>
		<div class="mp-form__row">
			<div>
				<label for="mp_name"><?php esc_html_e( 'Täisnimi *', 'mooblipoisid-child' ); ?></label>
				<input type="text" id="mp_name" name="mp_name" placeholder="<?php esc_attr_e( 'nt Astrid Lindgren', 'mooblipoisid-child' ); ?>" required />
			</div>
			<div>
				<label for="mp_company"><?php esc_html_e( 'Ettevõte / koht *', 'mooblipoisid-child' ); ?></label>
				<input type="text" id="mp_company" name="mp_company" placeholder="<?php esc_attr_e( 'nt Kohvik Nordlys OÜ', 'mooblipoisid-child' ); ?>" required />
			</div>
		</div>
		<div class="mp-form__row">
			<div>
				<label for="mp_email"><?php esc_html_e( 'E-posti aadress *', 'mooblipoisid-child' ); ?></label>
				<input type="email" id="mp_email" name="mp_email" placeholder="nimi@ettevote.ee" required />
			</div>
			<div>
				<label for="mp_phone"><?php esc_html_e( 'Telefoninumber', 'mooblipoisid-child' ); ?></label>
				<input type="tel" id="mp_phone" name="mp_phone" placeholder="+372 5000 0000" />
			</div>
		</div>
		<div class="mp-form__row">
			<div>
				<label for="mp_project_type"><?php esc_html_e( 'Projekti tüüp *', 'mooblipoisid-child' ); ?></label>
				<select id="mp_project_type" name="mp_project_type" required>
					<option value=""><?php esc_html_e( 'Palun valige…', 'mooblipoisid-child' ); ?></option>
					<option><?php esc_html_e( 'Kohvik, baar või restoran', 'mooblipoisid-child' ); ?></option>
					<option><?php esc_html_e( 'Jaemüügipood / kaubanduslik sisekujundus', 'mooblipoisid-child' ); ?></option>
					<option><?php esc_html_e( 'Ainult letid või istekohad', 'mooblipoisid-child' ); ?></option>
					<option><?php esc_html_e( 'Mitme objekti lahendus', 'mooblipoisid-child' ); ?></option>
					<option><?php esc_html_e( 'Eraköök', 'mooblipoisid-child' ); ?></option>
				</select>
			</div>
			<div>
				<label for="mp_window"><?php esc_html_e( 'Soovitud avamis- / paigaldusaeg', 'mooblipoisid-child' ); ?></label>
				<select id="mp_window" name="mp_window">
					<option value=""><?php esc_html_e( 'Palun valige…', 'mooblipoisid-child' ); ?></option>
					<option><?php esc_html_e( '2027. aasta 1. kvartal', 'mooblipoisid-child' ); ?></option>
					<option><?php esc_html_e( '2027. aasta 2. kvartal', 'mooblipoisid-child' ); ?></option>
					<option><?php esc_html_e( '2027. aasta teine poolaasta', 'mooblipoisid-child' ); ?></option>
					<option><?php esc_html_e( 'Veel planeerimisel', 'mooblipoisid-child' ); ?></option>
				</select>
			</div>
		</div>
		<div>
			<label for="mp_location"><?php esc_html_e( 'Koha / poe asukoht', 'mooblipoisid-child' ); ?></label>
			<input type="text" id="mp_location" name="mp_location" placeholder="<?php esc_attr_e( 'nt Tallinn, Stockholm, Helsinki, Riia', 'mooblipoisid-child' ); ?>" />
		</div>
		<div>
			<label for="mp_brief"><?php esc_html_e( 'Brief ja mõõdud', 'mooblipoisid-child' ); ?></label>
			<textarea id="mp_brief" name="mp_brief" rows="4" placeholder="<?php esc_attr_e( 'Rääkige meile, mida vajate: nt 6 m baar espresso-jaama ja letialuste külmikutega, 40 istekohta pingiistmetel, bränditud kassalauk. Lisage eelarvevahemik või arhitekti andmed, kui need on olemas.', 'mooblipoisid-child' ); ?>"></textarea>
		</div>
		<div class="mp-form__upload">
			<svg aria-hidden="true" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><path d="M17 8l-5-5-5 5"/><path d="M12 3v12"/></svg>
			<label for="mp_files"><?php esc_html_e( 'Lisage plaanid, joonised või kontseptsioonitahvlid', 'mooblipoisid-child' ); ?></label>
			<input type="file" id="mp_files" name="mp_files[]" multiple accept=".pdf,.dwg,.png,.jpg,.jpeg,application/pdf,image/png,image/jpeg" />
			<p class="mp-form__upload-hint"><?php esc_html_e( 'PDF, DWG, PNG, JPEG kuni 25MB', 'mooblipoisid-child' ); ?></p>
		</div>
		<div class="mp-form__submit">
			<button type="submit" class="mp-btn">
				<span><?php esc_html_e( 'Saada projektipäring', 'mooblipoisid-child' ); ?></span>
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
