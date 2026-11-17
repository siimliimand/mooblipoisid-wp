<?php
/**
 * Homepage: trust strip (the four short facts under the hero).
 *
 * Every fact is a `trust_stat` entry: the title is the big value
 * (.mp-trust__value) and the short description is the caption (.mp-trust__text).
 * Entries render in menu_order ASC (with the post date as the natural tie-break).
 * The `lang` query var keeps the facts in the active language, and adds the site
 * language as a fallback while a language has no facts of its own yet.
 *
 * @package mooblipoisid-child
 */

defined( 'ABSPATH' ) || exit;

$mp_trust_query = new WP_Query(
	array(
		'post_type'              => 'trust_stat',
		'post_status'            => 'publish',
		'posts_per_page'         => -1,
		'orderby'                => 'menu_order',
		'order'                  => 'ASC',
		'lang'                   => mooblipoisid_section_languages( 'trust_stat' ),
		'ignore_sticky_posts'    => true,
		'no_found_rows'          => true,
		'update_post_term_cache' => false,
		'update_post_meta_cache' => false,
	)
);

if ( ! $mp_trust_query->have_posts() ) {
	wp_reset_postdata();
	return;
}
?>
<section class="mp-trust">
  <div class="mp-container">
    <div class="mp-trust__grid">
      <?php while ( $mp_trust_query->have_posts() ) : ?>
        <?php $mp_trust_query->the_post(); ?>
      <div>
        <div class="mp-trust__value"><?php echo esc_html( get_the_title() ); ?></div>
        <div class="mp-trust__text"><?php echo esc_html( get_the_excerpt() ); ?></div>
      </div>
      <?php endwhile; ?>
    </div>
  </div>
</section>
<?php
wp_reset_postdata();