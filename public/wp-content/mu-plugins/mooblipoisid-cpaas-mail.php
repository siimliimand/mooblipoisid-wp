<?php
/**
 * Plugin Name: Mööblipoisid – Zoho CPaaS transactional mail
 * Description: Sends every wp_mail() through the Zoho CPaaS (formerly ZeptoMail) REST API. Lives in mu-plugins so it loads before pluggable.php and cannot be deactivated from the admin.
 * Version:     1.0.0
 *
 * CONFIGURATION (add to wp-config.php — keep the key out of git):
 *
 *   define( 'MOOBLIPOISID_CPAAS_API_KEY', 'Zoho-enczapikey-…' );          // Send Mail token from the Mail Agent (Setup Info → API). Empty = local PHP mail() fallback.
 *   define( 'MOOBLIPOISID_CPAAS_ENDPOINT', 'https://cpaas.zoho.com/v1.1/email' ); // US datacenter. EU account: https://cpaas.zoho.eu/v1.1/email
 *   define( 'MOOBLIPOISID_MAIL_FROM', 'marilyn@mooblipoisid.ee' );        // Must live on the domain verified in the CPaaS console.
 *   define( 'MOOBLIPOISID_MAIL_FROM_NAME', 'Mööblipoisid' );
 *
 * Notes:
 * - Zoho CPaaS limits total message size to 10 MB including attachments; the
 *   enquiry form allows 25 MB uploads, so very large attachments fail the send
 *   (the form then shows its "please write to us directly" notice).
 * - Dynamic Reply-To IS supported through CPaaS (unlike plain Zoho Mail SMTP),
 *   so the enquiry form's customer reply-to keeps working.
 *
 * @package mooblipoisid
 */

defined( 'ABSPATH' ) || exit;

if ( function_exists( 'wp_mail' ) ) {
	return; // Something already declared it (mu-plugins load first, so this stays ours).
}

/**
 * Default endpoint (US datacenter) when MOOBLIPOISID_CPAAS_ENDPOINT is unset.
 *
 * @return string
 */
function mooblipoisid_cpaas_endpoint() {
	return defined( 'MOOBLIPOISID_CPAAS_ENDPOINT' ) && MOOBLIPOISID_CPAAS_ENDPOINT
		? MOOBLIPOISID_CPAAS_ENDPOINT
		: 'https://cpaas.zoho.com/v1.1/email';
}

/**
 * Configured sender API key. Empty means "not wired up yet" → PHP mail() fallback.
 * Accepts the raw token or the full header value ("Zoho-enczapikey …") pasted
 * straight from the console — the prefix is normalized away here.
 *
 * @return string
 */
function mooblipoisid_cpaas_api_key() {
	$key = defined( 'MOOBLIPOISID_CPAAS_API_KEY' ) ? trim( (string) MOOBLIPOISID_CPAAS_API_KEY ) : '';

	if ( 0 === stripos( $key, 'Zoho-enczapikey ' ) ) {
		$key = trim( substr( $key, strlen( 'Zoho-enczapikey ' ) ) );
	}

	return $key;
}

/**
 * Parse an email header block (string with newlines, numeric array of
 * "Name: value" strings, or name => value array) into name => value pairs.
 *
 * @param string|string[]|array $headers Raw headers.
 * @return array Lower-cased header names => values.
 */
function mooblipoisid_cpaas_normalize_headers( $headers ) {
	$pairs = array();

	if ( '' === $headers || null === $headers ) {
		return $pairs;
	}

	if ( ! is_array( $headers ) ) {
		$headers = explode( "\n", str_replace( array( "\r\n", "\r" ), "\n", (string) $headers ) );
	}

	foreach ( $headers as $name => $content ) {
		$content = trim( (string) $content );

		if ( '' === $content ) {
			continue;
		}

		// "Name: value" element.
		if ( preg_match( '/^([A-Za-z0-9-]+):\s*(.*)$/', $content, $m ) ) {
			$pairs[ strtolower( $m[1] ) ] = $m[2];
			continue;
		}

		// Associative element: valid header name as key.
		if ( is_string( $name ) && preg_match( '/^[A-Za-z0-9-]+$/', $name ) ) {
			$pairs[ strtolower( $name ) ] = $content;
		}
	}

	return $pairs;
}

/**
 * Parse recipient value(s) into [ [address, name], … ]. Accepts the shapes
 * core wp_mail() accepts: "a@b.c", "Name <a@b.c>", comma lists, plain arrays
 * of those, or associative arrays (address => name).
 *
 * @param mixed $value Recipient(s).
 * @return array List of [address, name] pairs. Invalid entries are skipped.
 */
function mooblipoisid_cpaas_parse_recipients( $value ) {
	$out     = array();
	$entries = array();

	if ( empty( $value ) ) {
		return $out;
	}

	if ( is_string( $value ) ) {
		$entries = explode( ',', $value );
	} elseif ( is_array( $value ) ) {
		foreach ( $value as $key => $item ) {
			if ( is_string( $key ) && is_email( $key ) ) {
				$out[] = array( $key, (string) $item ); // "address" => "Name".
			} elseif ( is_string( $item ) ) {
				$entries = array_merge( $entries, explode( ',', $item ) );
			}
		}
	}

	foreach ( $entries as $entry ) {
		$entry  = trim( (string) $entry );
		$addr   = '';
		$name   = '';

		if ( preg_match( '/^(.*)<\s*([^<>\s]+@[^\s>]+)\s*>\s*$/', $entry, $m ) ) {
			$name = trim( $m[1], " \t\"'" );
			$addr = $m[2];
		} else {
			$addr = $entry;
		}

		$addr = trim( $addr );

		if ( is_email( $addr ) ) {
			$out[] = array( $addr, $name );
		}
	}

	return $out;
}

/**
 * Turn one [address, name] pair into CPaaS's nested object shape (to/cc/bcc
 * wrap it in `email_address`; `from` and `reply_to` use it directly).
 *
 * @param array $pair [address, name].
 * @return array
 */
function mooblipoisid_cpaas_address_object( $pair ) {
	$object = array( 'address' => (string) $pair[0] );

	if ( '' !== (string) $pair[1] ) {
		$object['name'] = (string) $pair[1];
	}

	return $object;
}

/**
 * Recipient object for to/cc/bcc: the address object nested under
 * `email_address` per the CPaaS API schema.
 *
 * @param array $pair [address, name].
 * @return array
 */
function mooblipoisid_cpaas_recipient_object( $pair ) {
	return array( 'email_address' => mooblipoisid_cpaas_address_object( $pair ) );
}

/**
 * Local file path(s) / URL(s) → CPaaS attachment objects (base64 content).
 * URLs are downloaded to a temp copy first. Oversized or unreadable files are
 * skipped with an error_log entry rather than failing the whole message.
 *
 * @param mixed $attachments As accepted by wp_mail().
 * @return array
 */
function mooblipoisid_cpaas_build_attachments( $attachments ) {
	$out     = array();
	$max_len = 10 * 1024 * 1024; // ~10 MB message cap on CPaaS; leave headroom.

	if ( empty( $attachments ) ) {
		return $out;
	}

	$paths = is_array( $attachments ) ? $attachments : explode( "\n", (string) $attachments );

	foreach ( $paths as $path ) {
		$path = trim( (string) $path );

		if ( '' === $path ) {
			continue;
		}

		$tmp = null;

		if ( preg_match( '#^https?://#i', $path ) ) {
			$tmp = download_url( $path, 20 );

			if ( is_wp_error( $tmp ) ) {
				error_log( 'Mooblipoisid CPaaS mail: attachment download failed (' . $path . '): ' . $tmp->get_error_message() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				continue;
			}

			$path = $tmp;
		}

		if ( ! is_string( $path ) || '' === $path || ! is_file( $path ) || ! is_readable( $path ) ) {
			if ( null !== $tmp ) {
				wp_delete_file( $tmp );
			}
			continue;
		}

		$size = (int) filesize( $path );
		$name = wp_basename( $path );

		if ( $size > $max_len ) {
			error_log( 'Mooblipoisid CPaaS mail: attachment skipped (too large, ~' . round( $size / 1048576 ) . ' MB): ' . $name ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			if ( null !== $tmp ) {
				wp_delete_file( $tmp );
			}
			continue;
		}

		$mime = mime_content_type( $path );
		if ( ! is_string( $mime ) || '' === $mime ) {
			$mime = 'application/octet-stream';
		}

		$out[] = array(
			'name'      => $name,
			'mime_type' => $mime,
			'content'   => base64_encode( (string) file_get_contents( $path ) ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		);

		if ( null !== $tmp ) {
			wp_delete_file( $tmp );
		}
	}

	return $out;
}

/**
 * Build the CPaaS JSON payload from wp_mail()-style arguments.
 *
 * Public so it can be exercised from CLI tests without sending anything.
 *
 * @param string|array       $to          Recipient(s).
 * @param string             $subject     Subject.
 * @param string|array       $message     Body (arrays joined with newlines).
 * @param string|array       $headers     Headers.
 * @param string|array       $attachments Attachment paths/URLs.
 * @param string|array|null  $from        Override sender [address, name] (defaults to the configured constants).
 * @return array|WP_Error Payload array, or WP_Error on missing recipients/content.
 */
function mooblipoisid_cpaas_build_payload( $to, $subject, $message, $headers = '', $attachments = array(), $from = null ) {
	$recipients = mooblipoisid_cpaas_parse_recipients( $to );

	if ( empty( $recipients ) ) {
		return new WP_Error( 'mooblipoisid_cpaas_no_recipients', 'No valid recipient address.' );
	}

	if ( is_array( $message ) ) {
		$message = implode( "\n", $message );
	}

	$message   = (string) $message;
	$headers   = mooblipoisid_cpaas_normalize_headers( $headers );
	$is_html   = isset( $headers['content-type'] ) && false !== stripos( (string) $headers['content-type'], 'text/html' );
	$from_pair = is_array( $from ) ? $from : array(
		defined( 'MOOBLIPOISID_MAIL_FROM' ) && MOOBLIPOISID_MAIL_FROM ? MOOBLIPOISID_MAIL_FROM : 'wordpress@' . preg_replace( '#^www\.#', '', (string) wp_parse_url( home_url(), PHP_URL_HOST ) ),
		defined( 'MOOBLIPOISID_MAIL_FROM_NAME' ) && MOOBLIPOISID_MAIL_FROM_NAME ? MOOBLIPOISID_MAIL_FROM_NAME : (string) get_bloginfo( 'name' ),
	);

	if ( ! is_email( (string) $from_pair[0] ) ) {
		return new WP_Error( 'mooblipoisid_cpaas_bad_from', 'Configured sender address is not valid: ' . (string) $from_pair[0] );
	}

	$payload = array(
		'from'    => mooblipoisid_cpaas_address_object( $from_pair ),
		'to'      => array_map( 'mooblipoisid_cpaas_recipient_object', $recipients ),
		'subject' => (string) $subject,
	);

	if ( $is_html ) {
		$payload['htmlbody'] = $message;
	} else {
		$payload['textbody'] = $message;
	}

	foreach ( array( 'reply-to' => 'reply_to', 'cc' => 'cc', 'bcc' => 'bcc' ) as $header => $field ) {
		if ( isset( $headers[ $header ] ) ) {
			$parsed = mooblipoisid_cpaas_parse_recipients( $headers[ $header ] );
			if ( ! empty( $parsed ) ) {
				// reply_to is flat; cc/bcc are nested under email_address.
				$wrapper           = 'reply_to' === $field ? 'mooblipoisid_cpaas_address_object' : 'mooblipoisid_cpaas_recipient_object';
				$payload[ $field ] = array_map( $wrapper, $parsed );
			}
		}
	}

	$files = mooblipoisid_cpaas_build_attachments( $attachments );
	if ( ! empty( $files ) ) {
		$payload['attachments'] = $files;
	}

	return $payload;
}

/**
 * POST the payload to the CPaaS API. Returns true on HTTP 200, logs failures.
 *
 * @param array  $payload Payload from mooblipoisid_cpaas_build_payload().
 * @param string $api_key Overrides the configured key (used by CLI tests).
 * @return bool
 */
function mooblipoisid_cpaas_send( $payload, $api_key = null ) {
	$key = null === $api_key ? mooblipoisid_cpaas_api_key() : (string) $api_key;

	$response = wp_remote_post(
		mooblipoisid_cpaas_endpoint(),
		array(
			'timeout' => 20,
			'headers' => array(
				'Accept'        => 'application/json',
				'Content-Type'  => 'application/json',
				'Authorization' => 'Zoho-enczapikey ' . $key,
			),
			'body'    => wp_json_encode( $payload ),
		)
	);

	if ( is_wp_error( $response ) ) {
		error_log( 'Mooblipoisid CPaaS mail: request failed: ' . $response->get_error_message() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		return false;
	}

	$code = (int) wp_remote_retrieve_response_code( $response );

	if ( 200 !== $code ) {
		error_log( 'Mooblipoisid CPaaS mail: API ' . $code . ': ' . substr( (string) wp_remote_retrieve_body( $response ), 0, 500 ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		return false;
	}

	return true;
}

/**
 * Fallback sender (used when no CPaaS key is configured): plain PHP mail()
 * through PHPMailer, mirroring what core's wp_mail() does out of the box.
 * On DDEV this lands in Mailpit; on a server it uses the host MTA.
 *
 * @param string|array $to          Recipient(s).
 * @param string       $subject     Subject.
 * @param string       $message     Body (already flattened to a string).
 * @param array        $headers     Normalized header pairs.
 * @param string|array $attachments Attachment paths.
 * @return bool
 */
function mooblipoisid_cpaas_fallback_send( $to, $subject, $message, $headers, $attachments ) {
	global $phpmailer;

	require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
	require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
	require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';

	$phpmailer = new \PHPMailer\PHPMailer\PHPMailer( true );
	$phpmailer->isMail();
	$phpmailer->CharSet = get_bloginfo( 'charset' );

	$host = (string) wp_parse_url( home_url(), PHP_URL_HOST );
	$phpmailer->setFrom( 'wordpress@' . preg_replace( '#^www\.#', '', $host ), get_bloginfo( 'name' ) );

	foreach ( mooblipoisid_cpaas_parse_recipients( $to ) as $recipient ) {
		$phpmailer->addAddress( $recipient[0], $recipient[1] );
	}

	if ( isset( $headers['reply-to'] ) ) {
		foreach ( mooblipoisid_cpaas_parse_recipients( $headers['reply-to'] ) as $pair ) {
			$phpmailer->addReplyTo( $pair[0], $pair[1] );
		}
	}

	if ( isset( $headers['cc'] ) ) {
		foreach ( mooblipoisid_cpaas_parse_recipients( $headers['cc'] ) as $pair ) {
			$phpmailer->addCc( $pair[0], $pair[1] );
		}
	}

	if ( isset( $headers['bcc'] ) ) {
		foreach ( mooblipoisid_cpaas_parse_recipients( $headers['bcc'] ) as $pair ) {
			$phpmailer->addBcc( $pair[0], $pair[1] );
		}
	}

	$phpmailer->Subject = (string) $subject;

	if ( isset( $headers['content-type'] ) && false !== stripos( (string) $headers['content-type'], 'text/html' ) ) {
		$phpmailer->isHTML( true );
		$phpmailer->Body    = $message;
		$phpmailer->AltBody = wp_strip_all_tags( $message );
	} else {
		$phpmailer->Body = $message;
	}

	if ( ! empty( $attachments ) ) {
		$paths = is_array( $attachments ) ? $attachments : explode( "\n", (string) $attachments );
		foreach ( $paths as $path ) {
			$path = trim( (string) $path );
			if ( '' !== $path && is_file( $path ) ) {
				$phpmailer->addAttachment( $path );
			}
		}
	}

	try {
		return (bool) $phpmailer->send();
	} catch ( \PHPMailer\PHPMailer\Exception $e ) {
		error_log( 'Mooblipoisid CPaaS mail: fallback send failed: ' . $e->getMessage() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		return false;
	}
}

/**
 * wp_mail() replacement (pluggable): routes through CPaaS when an API key is
 * configured, otherwise falls back to plain PHP mail().
 *
 * @param string|array $to          Recipient(s).
 * @param string       $subject     Subject.
 * @param string|array $message     Message body.
 * @param string|array $headers     Additional headers.
 * @param string|array $attachments Files to attach.
 * @return bool Whether the email was sent successfully.
 */
function wp_mail( $to, $subject, $message, $headers = '', $attachments = array() ) {
	if ( '' === mooblipoisid_cpaas_api_key() ) {
		if ( is_array( $message ) ) {
			$message = implode( "\n", $message );
		}

		return mooblipoisid_cpaas_fallback_send( $to, $subject, (string) $message, mooblipoisid_cpaas_normalize_headers( $headers ), $attachments );
	}

	$payload = mooblipoisid_cpaas_build_payload( $to, $subject, $message, $headers, $attachments );

	if ( is_wp_error( $payload ) ) {
		error_log( 'Mooblipoisid CPaaS mail: ' . $payload->get_error_message() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		return false;
	}

	return mooblipoisid_cpaas_send( $payload );
}
