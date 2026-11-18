<?php
/**
 * Homepage: ethos band ("#maker" — Ethos & Provenance).
 *
 * The eyebrow, quote, body copy and the workshop photo (with its alt text)
 * come from the Homepage Settings screen. The "In-House Production" badge and
 * the two ethos points are the fixed story around that copy. The attribution
 * block is intentionally not part of this section.
 *
 * @package mooblipoisid-child
 */

defined( 'ABSPATH' ) || exit;

$mp_settings    = function_exists( 'mooblipoisid_get_homepage_settings' ) ? mooblipoisid_get_homepage_settings() : array();
$mp_ethos_quote = isset( $mp_settings['ethos_quote'] ) ? (string) $mp_settings['ethos_quote'] : '';

$mp_ethos_image = isset( $mp_settings['ethos_image'] ) ? trim( (string) $mp_settings['ethos_image'] ) : '';
if ( '' === $mp_ethos_image ) {
	$mp_ethos_image = 'wp-content/uploads/2026/11/mp-3.jpg';
}
$mp_ethos_image_alt = isset( $mp_settings['ethos_image_alt'] ) ? trim( (string) $mp_settings['ethos_image_alt'] ) : '';
if ( '' === $mp_ethos_image_alt ) {
	$mp_ethos_image_alt = __( 'Meistrikoda Mööblipoisid töökojas (näidisfoto, ootab pärisfotograafiat)', 'mooblipoisid-child' );
}
?>
<section class="mp-section mp-section--dark" id="maker">
  <div class="mp-container">
    <div>
      <div>
        <div>
          <img src="<?php echo esc_url( mooblipoisid_image_src( $mp_ethos_image, 'large' ) ); ?>" alt="<?php echo esc_attr( $mp_ethos_image_alt ); ?>" loading="lazy" decoding="async">
        </div>
        <div class="mp-ethos__badge">
          <p><?php echo esc_html( $mp_settings['ethos_badge_title'] ?? '' ); ?></p>
          <p><?php echo esc_html( $mp_settings['ethos_badge_text'] ?? '' ); ?></p>
        </div>
      </div>
      <div>
        <div>
          <span class="mp-eyebrow"><?php echo esc_html( isset( $mp_settings['ethos_eyebrow'] ) ? (string) $mp_settings['ethos_eyebrow'] : '' ); ?></span>
          <blockquote><?php echo esc_html( wptexturize( html_entity_decode( $mp_ethos_quote, ENT_QUOTES, 'UTF-8' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- wptexturize() returns escaped entities ?></blockquote>
        </div>
        <p class="mp-body"><?php echo esc_html( isset( $mp_settings['ethos_body'] ) ? (string) $mp_settings['ethos_body'] : '' ); ?></p>
        <div class="mp-ethos__points">
          <div>
            <h4><?php echo esc_html( $mp_settings['ethos_point1_title'] ?? '' ); ?></h4>
            <p><?php echo esc_html( $mp_settings['ethos_point1_text'] ?? '' ); ?></p>
          </div>
          <div>
            <h4><?php echo esc_html( $mp_settings['ethos_point2_title'] ?? '' ); ?></h4>
            <p><?php echo esc_html( $mp_settings['ethos_point2_text'] ?? '' ); ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>