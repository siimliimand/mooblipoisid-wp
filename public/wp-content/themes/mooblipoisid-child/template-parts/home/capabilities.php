<?php
/**
 * Homepage: capability cards ("One Workshop. Every Material Your Venue Needs.").
 *
 * Every card is a `capability` entry: the title is the card name and the short
 * description is the card text. Entries render in menu_order ASC (the post date
 * breaks ties). The card icons are fixed artwork and do not come from the
 * entries. The `lang` query var keeps the cards in the active language, and adds
 * the site language as a fallback while a language has no cards of its own yet.
 *
 * @package mooblipoisid-child
 */

defined( 'ABSPATH' ) || exit;

$mp_capability_query = new WP_Query(
	array(
		'post_type'              => 'capability',
		'post_status'            => 'publish',
		'posts_per_page'         => -1,
		'orderby'                => 'menu_order',
		'order'                  => 'ASC',
		'lang'                   => mooblipoisid_section_languages( 'capability' ),
		'ignore_sticky_posts'    => true,
		'no_found_rows'          => true,
		'update_post_term_cache' => false,
		'update_post_meta_cache' => false,
	)
);

if ( ! $mp_capability_query->have_posts() ) {
	wp_reset_postdata();
	return;
}

// The four card icons, matched to the capability order on the homepage.
$mp_capability_icons = array(
	'<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3M4.9 4.9l2.1 2.1M17 17l2.1 2.1M19.1 4.9L17 7M7 17l-2.1 2.1"/></svg>',
	'<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M14.7 6.3a4.5 4.5 0 0 0-6 6L3 18l3 3 5.7-5.7a4.5 4.5 0 0 0 6-6L14 13l-3-3 3.7-3.7z"/></svg>',
	'<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>',
	'<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
);

$mp_capability_index = 0;
$mp_settings         = function_exists( 'mooblipoisid_get_homepage_settings' ) ? mooblipoisid_get_homepage_settings() : array();
?>
<section class="mp-section mp-section--low" id="capabilities">
  <div class="mp-container">
    <div class="mp-section-head">
      <div>
        <span class="mp-eyebrow"><?php echo esc_html( $mp_settings['caps_eyebrow'] ?? '' ); ?></span>
        <h2 class="mp-headline-lg"><?php echo esc_html( $mp_settings['caps_title'] ?? '' ); ?></h2>
      </div>
      <div class="mp-section-head__intro">
        <p class="mp-body"><?php echo esc_html( $mp_settings['caps_intro'] ?? '' ); ?></p>
      </div>
    </div>
    <div class="mp-caps">
      <?php while ( $mp_capability_query->have_posts() ) : ?>
        <?php
        $mp_capability_query->the_post();
        $mp_capability_icon = isset( $mp_capability_icons[ $mp_capability_index ] ) ? $mp_capability_icons[ $mp_capability_index ] : '';
        ?>
      <div class="mp-cap">
        <div class="mp-cap__icon"><?php echo $mp_capability_icon; // phpcs:ignore WordPress.Security.EscapeOutput -- static inline SVG icon ?></div>
        <h3 class="mp-headline-sm"><?php echo esc_html( get_the_title() ); ?></h3>
        <p class="mp-body"><?php echo esc_html( get_the_excerpt() ); ?></p>
      </div>
        <?php $mp_capability_index++; ?>
      <?php endwhile; ?>
    </div>
  </div>
</section>
<?php
wp_reset_postdata();