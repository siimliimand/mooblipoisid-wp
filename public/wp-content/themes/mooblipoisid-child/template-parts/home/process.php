<?php
/**
 * Homepage: process steps ("From Brief to Opening Day").
 *
 * Every step is a `step` entry: the title is the step name, the short
 * description is the step text and the `mp_week` entry detail is the timeline
 * label. Entries render in menu_order ASC (the post date breaks ties), and the
 * step number is derived from the position (01, 02, 03, ...). The `lang` query
 * var keeps the steps in the active language, and adds the site language as a
 * fallback while a language has no steps of its own yet.
 *
 * @package mooblipoisid-child
 */

defined( 'ABSPATH' ) || exit;

$mp_step_query = new WP_Query(
	array(
		'post_type'              => 'step',
		'post_status'            => 'publish',
		'posts_per_page'         => -1,
		'orderby'                => 'menu_order',
		'order'                  => 'ASC',
		'lang'                   => mooblipoisid_section_languages( 'step' ),
		'ignore_sticky_posts'    => true,
		'no_found_rows'          => true,
		'update_post_term_cache' => false,
		'update_post_meta_cache' => true,
	)
);

if ( ! $mp_step_query->have_posts() ) {
	wp_reset_postdata();
	return;
}

$mp_step_index = 0;
$mp_settings   = function_exists( 'mooblipoisid_get_homepage_settings' ) ? mooblipoisid_get_homepage_settings() : array();
?>
<section class="mp-section mp-section--surface" id="process">
  <div class="mp-container">
    <div class="mp-section-head">
      <div>
        <span class="mp-eyebrow"><?php echo esc_html( $mp_settings['process_eyebrow'] ?? '' ); ?></span>
        <h2 class="mp-headline-lg"><?php echo esc_html( $mp_settings['process_title'] ?? '' ); ?></h2>
      </div>
      <div class="mp-section-head__intro">
        <p class="mp-body"><?php echo esc_html( $mp_settings['process_intro'] ?? '' ); ?></p>
      </div>
    </div>
    <div class="mp-process">
      <?php while ( $mp_step_query->have_posts() ) : ?>
        <?php
        $mp_step_query->the_post();
        $mp_step_index++;
        ?>
      <div class="mp-step">
        <div class="mp-step__num"><?php echo esc_html( str_pad( (string) $mp_step_index, 2, '0', STR_PAD_LEFT ) ); ?></div>
        <h3 class="mp-headline-sm"><?php echo esc_html( get_the_title() ); ?></h3>
        <p class="mp-body"><?php echo esc_html( get_the_excerpt() ); ?></p>
        <span class="mp-step__meta"><?php echo esc_html( get_post_meta( get_the_ID(), 'mp_week', true ) ); ?></span>
      </div>
      <?php endwhile; ?>
    </div>
  </div>
</section>
<?php
wp_reset_postdata();