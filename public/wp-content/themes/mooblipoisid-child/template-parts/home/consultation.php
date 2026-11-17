<?php
/**
 * Homepage: consultation band ("#consultation" — Start a Dialogue).
 *
 * The eyebrow, headline and intro come from the Homepage Settings screen. The
 * contact rows are fixed contact details (they keep their inline SVG icons),
 * and the enquiry form is rendered through the
 * [mooblipoisid_enquiry_form] shortcode as the second column of the
 * `.mp-consult` grid.
 *
 * @package mooblipoisid-child
 */

defined( 'ABSPATH' ) || exit;

$mp_settings = function_exists( 'mooblipoisid_get_homepage_settings' ) ? mooblipoisid_get_homepage_settings() : array();

/*
 * The headline is stored with a curly apostrophe, while the design shows the
 * HTML entity for it.  `&apos;` is not in esc_html()'s entity allow-list (it
 * would be escaped to `&amp;apos;`), so the apostrophe is emitted as the
 * numeric character reference `&#39;`, which renders as the same character and
 * survives escaping unchanged.
 */
$mp_settings['consult_headline'] = str_replace( "\u{2019}", '&#39;', (string) ( $mp_settings['consult_headline'] ?? '' ) );

$mp_contact_icon_phone = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.9.32 1.79.59 2.66a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.42-1.16a2 2 0 0 1 2.11-.45c.87.27 1.76.47 2.66.59A2 2 0 0 1 22 16.92z"/></svg>';
$mp_contact_icon_mail  = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><path d="M22 6l-10 7L2 6"/></svg>';
$mp_contact_icon_place = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/><path d="M9 21v-6h6v6"/></svg>';
?>
<section class="mp-section mp-section--inverse" id="consultation">
  <div class="mp-container mp-consult">
    <div class="mp-consult__left">
      <div class="mp-consult__intro">
        <div class="mp-consult__head">
          <span class="mp-eyebrow"><?php echo esc_html( isset( $mp_settings['consult_eyebrow'] ) ? (string) $mp_settings['consult_eyebrow'] : '' ); ?></span>
          <h2 class="mp-display"><?php echo esc_html( $mp_settings['consult_headline'] ); ?></h2>
        </div>
        <p class="mp-body-lg"><?php echo esc_html( isset( $mp_settings['consult_intro'] ) ? (string) $mp_settings['consult_intro'] : '' ); ?></p>
      </div>
      <div class="mp-contact-list">
        <div class="mp-contact-row">
          <div class="mp-contact-row__icon"><?php echo $mp_contact_icon_phone; // phpcs:ignore WordPress.Security.EscapeOutput -- static inline SVG icon ?></div>
          <div>
            <p class="mp-contact-row__role">Production Manager — Andre Merila</p>
            <p class="mp-contact-row__primary"><a href="tel:+3725153229">+372 515 3229</a></p>
            <p class="mp-contact-row__secondary"><a href="mailto:andre@mooblipoisid.ee">andre@mooblipoisid.ee</a></p>
          </div>
        </div>
        <div class="mp-contact-row">
          <div class="mp-contact-row__icon"><?php echo $mp_contact_icon_mail; // phpcs:ignore WordPress.Security.EscapeOutput -- static inline SVG icon ?></div>
          <div>
            <p class="mp-contact-row__role">Project Manager — Marilyn Salu</p>
            <p class="mp-contact-row__primary"><a href="mailto:marilyn@mooblipoisid.ee">marilyn@mooblipoisid.ee</a></p>
            <p class="mp-contact-row__secondary"><a href="tel:+37255910872">+372 55 910 872</a></p>
          </div>
        </div>
        <div class="mp-contact-row">
          <div class="mp-contact-row__icon"><?php echo $mp_contact_icon_place; // phpcs:ignore WordPress.Security.EscapeOutput -- static inline SVG icon ?></div>
          <div>
            <p class="mp-contact-row__role">Workshop &amp; Sample Visits</p>
            <p class="mp-contact-row__secondary">See materials, finishes and work in progress at our workshop at Võidu 75, Tallinn, by appointment.</p>
          </div>
        </div>
      </div>
    </div>
    <?php echo do_shortcode( '[mooblipoisid_enquiry_form]' ); // phpcs:ignore WordPress.Security.EscapeOutput -- the enquiry form template ?>
  </div>
</section>