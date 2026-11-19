<?php
/**
 * Homepage: FAQ.
 *
 * Every item is a `mooblipoisid_faq` entry fetched through
 * mooblipoisid_get_faqs() (the content model's getter, per language). Items
 * render as native <details>/<summary> pairs — no JavaScript needed, and the
 * questions and answers stay in the markup for crawlers and AI engines. The
 * heading and intro come from the Homepage Settings screen (faq_heading,
 * faq_sub). When no items exist for the active language (or the getter is not
 * available yet), nothing at all is rendered.
 *
 * @package mooblipoisid-child
 */

defined( 'ABSPATH' ) || exit;

$mp_faqs = function_exists( 'mooblipoisid_get_faqs' ) ? (array) mooblipoisid_get_faqs() : array();

if ( empty( $mp_faqs ) ) {
	return;
}

$mp_settings = function_exists( 'mooblipoisid_get_homepage_settings' ) ? mooblipoisid_get_homepage_settings() : array();
?>
<section class="mp-section mp-section--low mp-faq" id="faq">
  <div class="mp-container">
    <div class="mp-section-head">
      <div>
        <h2 class="mp-headline-lg"><?php echo esc_html( $mp_settings['faq_heading'] ?? '' ); ?></h2>
      </div>
      <div class="mp-section-head__intro">
        <p class="mp-body"><?php echo esc_html( $mp_settings['faq_sub'] ?? '' ); ?></p>
      </div>
    </div>
    <div class="mp-faq__list">
      <?php foreach ( $mp_faqs as $mp_faq ) : ?>
      <?php
      $mp_faq_question = (string) ( isset( $mp_faq['question'] ) ? $mp_faq['question'] : '' );
      $mp_faq_answer   = (string) ( isset( $mp_faq['answer'] ) ? $mp_faq['answer'] : '' );

      // Gutenberg block comments (`<!-- wp:paragraph -->`) are editor plumbing,
      // not content — strip them so they never leak into the page markup.
      $mp_faq_answer = trim( (string) preg_replace( '/<!--.*?-->/s', '', $mp_faq_answer ) );
      ?>
      <details class="mp-faq__item">
        <summary class="mp-faq__q"><?php echo esc_html( $mp_faq_question ); ?></summary>
        <div class="mp-faq__a"><?php echo wp_kses_post( wpautop( $mp_faq_answer ) ); ?></div>
      </details>
      <?php endforeach; ?>
    </div>
  </div>
</section>
