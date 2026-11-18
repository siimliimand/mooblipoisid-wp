<?php
/**
 * Homepage: product ranges ("What We Make").
 *
 * The 12 `range` entries are one ordered list: the first six are the
 * "Cafés, Bars & Restaurants" column, the last six the "Retail Shops" column.
 * The two column headings and their icons are the labels of the two columns
 * themselves and stay fixed; every list item (title + short description) comes
 * from the entries in menu_order ASC (the post date breaks ties). The `lang`
 * query var keeps the two columns in the active language, and adds the site
 * language as a fallback while a language has no ranges of its own yet.
 *
 * @package mooblipoisid-child
 */

defined( 'ABSPATH' ) || exit;

$mp_range_query = new WP_Query(
	array(
		'post_type'              => 'range',
		'post_status'            => 'publish',
		'posts_per_page'         => -1,
		'orderby'                => 'menu_order',
		'order'                  => 'ASC',
		'lang'                   => mooblipoisid_section_languages( 'range' ),
		'ignore_sticky_posts'    => true,
		'no_found_rows'          => true,
		'update_post_term_cache' => false,
		'update_post_meta_cache' => false,
	)
);

$mp_ranges = $mp_range_query->posts;
wp_reset_postdata();

if ( empty( $mp_ranges ) ) {
	return;
}

$mp_settings = function_exists( 'mooblipoisid_get_homepage_settings' ) ? mooblipoisid_get_homepage_settings() : array();

// First half of the ordered list is the hospitality column, second half retail.
$mp_range_split = (int) floor( count( $mp_ranges ) / 2 );

$mp_range_columns = array(
	array(
		'title'    => (string) ( $mp_settings['ranges_col_hospitality'] ?? '' ),
		'icon'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M18 8h2a2 2 0 0 1 0 4h-2"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/></svg>',
		'entries'  => array_slice( $mp_ranges, 0, $mp_range_split ),
	),
	array(
		'title'    => (string) ( $mp_settings['ranges_col_retail'] ?? '' ),
		'icon'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M3 9l1-5h16l1 5"/><path d="M4 9v11h16V9"/><path d="M9 20v-6h6v6"/></svg>',
		'entries'  => array_slice( $mp_ranges, $mp_range_split ),
	),
);
?>
<section class="mp-section mp-section--low" id="ranges">
  <div class="mp-container">
    <div class="mp-section-head">
      <div>
        <span class="mp-eyebrow"><?php echo esc_html( $mp_settings['ranges_eyebrow'] ?? '' ); ?></span>
        <h2 class="mp-headline-lg"><?php echo esc_html( $mp_settings['ranges_title'] ?? '' ); ?></h2>
      </div>
      <div class="mp-section-head__intro">
        <p class="mp-body"><?php echo esc_html( $mp_settings['ranges_intro'] ?? '' ); ?></p>
      </div>
    </div>
    <div class="mp-ranges">
      <?php foreach ( $mp_range_columns as $mp_range_column ) : ?>
      <div class="mp-range">
        <h3 class="mp-range__title"><?php echo $mp_range_column['icon']; // phpcs:ignore WordPress.Security.EscapeOutput -- static inline SVG icon ?> <?php echo $mp_range_column['title']; // phpcs:ignore WordPress.Security.EscapeOutput -- static column label with escaped ampersand entity ?></h3>
        <ul class="mp-range__list">
          <?php foreach ( $mp_range_column['entries'] as $mp_range_entry ) : ?>
          <li class="mp-range__item"><h4><?php echo esc_html( get_the_title( $mp_range_entry ) ); ?></h4><p><?php echo esc_html( get_the_excerpt( $mp_range_entry ) ); ?></p></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>