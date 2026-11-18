<?php
/**
 * Homepage: hero band (full-bleed image, headline, subline and two calls to action).
 *
 * The copy and the image come from the Homepage Settings screen. The markup is
 * identical to the static homepage (docs/homepage-content.html).
 *
 * @package mooblipoisid-child
 */

defined( 'ABSPATH' ) || exit;

$mp_settings = function_exists( 'mooblipoisid_get_homepage_settings' ) ? mooblipoisid_get_homepage_settings() : array();

$mp_hero_image     = isset( $mp_settings['hero_image'] ) ? (string) $mp_settings['hero_image'] : '';
$mp_hero_image_alt = isset( $mp_settings['hero_image_alt'] ) ? (string) $mp_settings['hero_image_alt'] : '';
?>
<section class="mp-hero">
  <img class="mp-hero__img" src="<?php echo esc_url( mooblipoisid_image_src( $mp_hero_image, 'full' ) ); ?>" alt="<?php echo esc_attr( $mp_hero_image_alt ); ?>" fetchpriority="high" decoding="async">
  <div class="mp-hero__shade-top"></div>
  <div class="mp-hero__shade-side"></div>
  <div class="mp-hero__content">
    <h1 class="mp-hero__title"><?php echo esc_html( isset( $mp_settings['hero_headline'] ) ? (string) $mp_settings['hero_headline'] : '' ); ?></h1>
    <p class="mp-hero__sub"><?php echo esc_html( isset( $mp_settings['hero_sub'] ) ? (string) $mp_settings['hero_sub'] : '' ); ?></p>
    <div class="mp-hero__actions">
      <a class="mp-btn" href="<?php echo esc_url( isset( $mp_settings['hero_cta1_href'] ) ? (string) $mp_settings['hero_cta1_href'] : '' ); ?>"><?php echo esc_html( isset( $mp_settings['hero_cta1_label'] ) ? (string) $mp_settings['hero_cta1_label'] : '' ); ?></a>
      <a class="mp-btn mp-btn--ghost" style="border-color:rgba(251,249,246,0.3);color:#fbf9f6" href="<?php echo esc_url( isset( $mp_settings['hero_cta2_href'] ) ? (string) $mp_settings['hero_cta2_href'] : '' ); ?>"><?php echo esc_html( isset( $mp_settings['hero_cta2_label'] ) ? (string) $mp_settings['hero_cta2_label'] : '' ); ?></a>
    </div>
  </div>
</section>