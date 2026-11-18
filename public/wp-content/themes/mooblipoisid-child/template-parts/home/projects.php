<?php
/**
 * Homepage: project cards ("Recent Venue & Retail Projects").
 *
 * Every card is a `project` entry: the featured image is the card photo, the
 * Materials & Features terms are the card tags, the `mp_location_type` entry
 * detail is the small meta line, the title is the card name and the short
 * description is the card text. Entries render in menu_order ASC (the post date
 * breaks ties), so the first entry renders first in the grid. The
 * `lang` query var keeps the cards in the active language, and adds the site
 * language as a fallback while a language has no projects of its own yet.
 *
 * @package mooblipoisid-child
 */

defined( 'ABSPATH' ) || exit;

$mp_project_query = new WP_Query(
	array(
		'post_type'              => 'project',
		'post_status'            => 'publish',
		'posts_per_page'         => -1,
		'orderby'                => 'menu_order',
		'order'                  => 'ASC',
		'lang'                   => mooblipoisid_section_languages( 'project' ),
		'ignore_sticky_posts'    => true,
		'no_found_rows'          => true,
		'update_post_term_cache' => true,
		'update_post_meta_cache' => true,
	)
);

if ( ! $mp_project_query->have_posts() ) {
	wp_reset_postdata();
	return;
}

$mp_settings = function_exists( 'mooblipoisid_get_homepage_settings' ) ? mooblipoisid_get_homepage_settings() : array();

?>
<section class="mp-section mp-section--surface" id="projects">
  <div class="mp-container">
    <div class="mp-section-head">
      <div>
        <span class="mp-eyebrow"><?php echo esc_html( $mp_settings['projects_eyebrow'] ?? '' ); ?></span>
        <h2 class="mp-headline-lg"><?php echo esc_html( $mp_settings['projects_title'] ?? '' ); ?></h2>
      </div>
    </div>
    <div class="mp-projects">
      <?php while ( $mp_project_query->have_posts() ) : ?>
        <?php
        $mp_project_query->the_post();

		// Tags show in the order they were added to the project: the term
        // relationship id is the creation order, the term name breaks ties.
        // The relationships are read directly (`get_the_terms()` sorts by term
        // id, and the ids of terms created later — e.g. translations — are not
        // in creation order).
        global $wpdb;

        $mp_project_tags = array();
        $mp_project_tag_rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT tt.term_id, tr.term_taxonomy_id AS tt_id FROM {$wpdb->term_relationships} AS tr
                 INNER JOIN {$wpdb->term_taxonomy} AS tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
                 WHERE tr.object_id = %d AND tt.taxonomy = 'project_tag' ORDER BY tr.term_taxonomy_id ASC",
                get_the_ID()
            )
        );

        foreach ( (array) $mp_project_tag_rows as $mp_project_tag_row ) {
            $mp_project_tag = get_term( (int) $mp_project_tag_row->term_id, 'project_tag' );
            if ( ! $mp_project_tag instanceof WP_Term ) {
                continue;
            }
            $mp_project_tag->mp_sort_order = (int) $mp_project_tag_row->tt_id;
            $mp_project_tags[] = $mp_project_tag;
        }
        $mp_project_tags = wp_list_sort( $mp_project_tags, array( 'mp_sort_order' => 'ASC', 'name' => 'ASC' ) );
        ?>
      <article class="mp-project">
        <div class="mp-project__media">
          <?php if ( has_post_thumbnail() ) : ?>
          <?php
          $mp_project_thumb_id  = (int) get_post_thumbnail_id( get_the_ID() );
          $mp_project_thumb_src = get_the_post_thumbnail_url( get_the_ID(), 'large' );
          if ( ! $mp_project_thumb_src ) {
              $mp_project_thumb_src = get_the_post_thumbnail_url( get_the_ID(), 'full' );
          }
          ?>
          <img src="<?php echo esc_url( $mp_project_thumb_src ); ?>" alt="<?php echo esc_attr( get_post_meta( $mp_project_thumb_id, '_wp_attachment_image_alt', true ) ); ?>" loading="lazy" decoding="async">
          <?php endif; ?>
          <?php if ( $mp_project_tags ) : ?>
          <div class="mp-project__tags">
            <?php foreach ( $mp_project_tags as $mp_project_tag ) : ?>
            <span class="mp-project__tag"><?php echo esc_html( $mp_project_tag->name ); ?></span>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </div>
        <div class="mp-project__body">
          <span class="mp-project__meta"><?php echo esc_html( get_post_meta( get_the_ID(), 'mp_location_type', true ) ); ?></span>
          <h3 class="mp-headline-md"><?php echo esc_html( get_the_title() ); ?></h3>
          <p class="mp-body"><?php echo esc_html( get_the_excerpt() ); ?></p>
          <a class="mp-project__link" href="#consultation"><?php echo esc_html( $mp_settings['projects_card_link'] ?? '' ); ?></a>
        </div>
      </article>
      <?php endwhile; ?>
    </div>
  </div>
</section>
<?php
wp_reset_postdata();