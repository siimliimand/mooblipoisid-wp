<?php
/**
 * Homepage: ethos band ("#maker" — Ethos & Provenance).
 *
 * The eyebrow, quote and body copy come from the Homepage Settings screen. The
 * workshop photo, the "In-House Production" badge and the two ethos points are
 * the fixed story around that copy. The attribution block is intentionally not
 * part of this section.
 *
 * @package mooblipoisid-child
 */

defined( 'ABSPATH' ) || exit;

$mp_settings    = function_exists( 'mooblipoisid_get_homepage_settings' ) ? mooblipoisid_get_homepage_settings() : array();
$mp_ethos_quote = isset( $mp_settings['ethos_quote'] ) ? (string) $mp_settings['ethos_quote'] : '';
?>
<section class="mp-section mp-section--dark" id="maker">
  <div class="mp-container">
    <div>
      <div>
        <div>
          <img src="https://mooblipoisid.ddev.site/wp-content/uploads/2026/11/mp-3.jpg" alt="<?php esc_attr_e( 'Meistrikoda Mööblipoisid töökojas (näidisfoto, ootab pärisfotograafiat)', 'mooblipoisid-child' ); ?>">
        </div>
        <div class="mp-ethos__badge">
          <p><?php esc_html_e( 'Oma tootmine', 'mooblipoisid-child' ); ?></p>
          <p><?php esc_html_e( 'Mööbel ja sisustus ühe katuse all Tallinnas', 'mooblipoisid-child' ); ?></p>
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
            <h4><?php esc_html_e( 'Valmis kandmiseks', 'mooblipoisid-child' ); ?></h4>
            <p><?php esc_html_e( 'Kuumus- ja kriipimiskindlad pealsed, tugevdatud liited ja äriklassi furnituur igapäevaseks kasutamiseks.', 'mooblipoisid-child' ); ?></p>
          </div>
          <div>
            <h4><?php esc_html_e( 'Üks meeskond, algusest lõpuni', 'mooblipoisid-child' ); ?></h4>
            <p><?php esc_html_e( 'Inimesed, kes teie esemed disainivad, valmistavad ja paigaldavad need ka ise, nii et üks kontaktisik vastutab kogu projekti eest.', 'mooblipoisid-child' ); ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>