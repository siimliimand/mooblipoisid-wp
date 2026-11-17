<?php
/**
 * Homepage: client stories ("What Our Clients Say").
 *
 * Every quote is a `testimonial` entry: the main text is the quote body, the
 * `mp_rating` entry detail (1–5) is the star row, `mp_role` is the person's
 * role and `mp_city` the city shown next to it. Entries render in menu_order
 * ASC (with the post date as the natural tie-break).
 *
 * @package mooblipoisid-child
 */

defined( 'ABSPATH' ) || exit;

$mp_story_query = new WP_Query(
	array(
		'post_type'              => 'testimonial',
		'post_status'            => 'publish',
		'posts_per_page'         => -1,
		'orderby'                => 'menu_order',
		'order'                  => 'ASC',
		'ignore_sticky_posts'    => true,
		'no_found_rows'          => true,
		'update_post_term_cache' => false,
		'update_post_meta_cache' => true,
	)
);

if ( ! $mp_story_query->have_posts() ) {
	wp_reset_postdata();
	return;
}
?>
<section class="mp-section mp-section--surface" id="stories">
  <div class="mp-container">
    <div class="mp-section-head">
      <div>
        <span class="mp-eyebrow">Client Stories</span>
        <h2 class="mp-headline-lg">What Our Clients Say</h2>
      </div>
    </div>
    <div class="mp-testimonials">
      <?php while ( $mp_story_query->have_posts() ) : ?>
        <?php
        $mp_story_query->the_post();
        $mp_story_quote = (string) get_post_field( 'post_content', get_the_ID() );
        $mp_story_role  = (string) get_post_meta( get_the_ID(), 'mp_role', true );
        $mp_story_city  = (string) get_post_meta( get_the_ID(), 'mp_city', true );
        $mp_story_stars = (int) get_post_meta( get_the_ID(), 'mp_rating', true );
        ?>
      <figure class="mp-quote">
        <?php if ( $mp_story_stars > 0 ) : ?>
        <div class="mp-quote__stars"><?php echo str_repeat( '<span>&#9733;</span>', min( 5, $mp_story_stars ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- static star character entities ?></div>
        <?php endif; ?>
        <blockquote><?php echo esc_html( wptexturize( html_entity_decode( $mp_story_quote, ENT_QUOTES, 'UTF-8' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- wptexturize() returns escaped entities ?></blockquote>
        <figcaption class="mp-quote__meta">
          <div>
            <?php if ( '' !== $mp_story_role ) : ?>
            <p class="mp-quote__role"><?php echo esc_html( $mp_story_role ); ?></p>
            <?php endif; ?>
            <?php if ( '' !== $mp_story_city ) : ?>
            <p class="mp-quote__city"><?php echo esc_html( $mp_story_city ); ?></p>
            <?php endif; ?>
          </div>
        </figcaption>
      </figure>
      <?php endwhile; ?>
    </div>
  </div>
</section>
<?php
wp_reset_postdata();