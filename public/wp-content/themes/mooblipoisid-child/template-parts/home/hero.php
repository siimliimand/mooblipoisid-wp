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

$mp_hero_image      = isset( $mp_settings['hero_image'] ) ? (string) $mp_settings['hero_image'] : '';
$mp_hero_image_alt  = isset( $mp_settings['hero_image_alt'] ) ? (string) $mp_settings['hero_image_alt'] : '';
$mp_hero_image_src  = mooblipoisid_image_src( $mp_hero_image, 'full' );

/*
 * Responsive hints for the LCP image (srcset + sizes + intrinsic dimensions).
 * Empty for non-attachment sources, in which case the single-src markup from
 * the static homepage is rendered unchanged.
 */
$mp_hero_image_attrs = function_exists( 'mooblipoisid_hero_image_attrs' ) ? mooblipoisid_hero_image_attrs( $mp_hero_image ) : array();

$mp_hero_image_extra = '';
if ( ! empty( $mp_hero_image_attrs['srcset'] ) ) {
	$mp_hero_image_extra .= ' srcset="' . esc_attr( $mp_hero_image_attrs['srcset'] ) . '" sizes="' . esc_attr( $mp_hero_image_attrs['sizes'] ) . '"';
}
if ( ! empty( $mp_hero_image_attrs['width'] ) && ! empty( $mp_hero_image_attrs['height'] ) ) {
	$mp_hero_image_extra .= ' width="' . (int) $mp_hero_image_attrs['width'] . '" height="' . (int) $mp_hero_image_attrs['height'] . '"';
}
?>
<section class="mp-hero">
  <img class="mp-hero__img" src="<?php echo esc_url( $mp_hero_image_src ); ?>" alt="<?php echo esc_attr( $mp_hero_image_alt ); ?>"<?php echo $mp_hero_image_extra; // phpcs:ignore WordPress.Security.EscapeOutput -- each attribute escaped above ?> fetchpriority="high" decoding="async">
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