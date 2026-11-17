<?php
/**
 * Homepage: project cards ("Recent Venue & Retail Projects").
 *
 * Every card is a `project` entry: the featured image is the card photo, the
 * Materials & Features terms are the card tags, the `mp_location_type` entry
 * detail is the small meta line, the title is the card name and the short
 * description is the card text. Entries render in menu_order ASC (the post date
 * breaks ties), so the first entry is the lead card (.mp-project--lead).
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

$mp_project_index = 0;
?>
<section class="mp-section mp-section--surface" id="projects">
  <div class="mp-container">
    <div class="mp-section-head">
      <div>
        <span class="mp-eyebrow">Selected Commissions</span>
        <h2 class="mp-headline-lg">Recent Venue &amp; Retail Projects</h2>
      </div>
    </div>
    <div class="mp-projects">
      <?php while ( $mp_project_query->have_posts() ) : ?>
        <?php
        $mp_project_query->the_post();

        $mp_project_tags = wp_get_post_terms( get_the_ID(), 'project_tag' );
        if ( is_wp_error( $mp_project_tags ) ) {
            $mp_project_tags = array();
        }

        // Tags show in the order they were added to the project: the term
        // relationship id is the creation order, the term name breaks ties.
        foreach ( $mp_project_tags as $mp_project_tag ) {
            $mp_project_tag->mp_sort_order = (int) $mp_project_tag->term_taxonomy_id;
        }
        $mp_project_tags = wp_list_sort( $mp_project_tags, array( 'mp_sort_order' => 'ASC', 'name' => 'ASC' ) );
        ?>
      <article class="mp-project<?php echo 0 === $mp_project_index ? ' mp-project--lead' : ''; ?>">
        <div class="mp-project__media">
          <?php if ( has_post_thumbnail() ) : ?>
          <img src="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'full' ) ); ?>" alt="<?php echo esc_attr( get_post_meta( (int) get_post_thumbnail_id( get_the_ID() ), '_wp_attachment_image_alt', true ) ); ?>">
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
          <a class="mp-project__link" href="#consultation">Discuss a Similar Project</a>
        </div>
      </article>
        <?php $mp_project_index++; ?>
      <?php endwhile; ?>
    </div>
  </div>
</section>
<?php
wp_reset_postdata();