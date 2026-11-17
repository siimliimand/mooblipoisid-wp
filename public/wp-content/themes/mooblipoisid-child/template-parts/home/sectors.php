<?php
/**
 * Homepage: sectors ("Who We Build For").
 *
 * The three cards are the `sector` terms assigned to Projects. The term
 * description is the card text, the `sector_image` term meta is the card photo
 * and `sector_order` decides the order. The section headline is fixed copy.
 *
 * @package mooblipoisid-child
 */

defined( 'ABSPATH' ) || exit;

$mp_sectors = get_terms(
	array(
		'taxonomy'   => 'sector',
		'hide_empty' => false,
	)
);

if ( is_wp_error( $mp_sectors ) || empty( $mp_sectors ) ) {
	return;
}

foreach ( $mp_sectors as $mp_sector ) {
	$mp_sector->mp_sort_order = (int) get_term_meta( $mp_sector->term_id, 'sector_order', true );
}

$mp_sectors = wp_list_sort( $mp_sectors, array( 'mp_sort_order' => 'ASC', 'name' => 'ASC' ) );

$mp_sector_links = array(
	'hospitality' => array(
		'href'  => '#ranges',
		'label' => 'See the Hospitality Range',
	),
	'retail'      => array(
		'href'  => '#ranges',
		'label' => 'See the Retail Range',
	),
	'private'     => array(
		'href'  => '#consultation',
		'label' => 'Enquire',
	),
);

/*
 * The card headline is the marketing name of the card (the "Hospitality"
 * sector is the "Cafés, Bars & Restaurants" card) and the photo alt text is
 * the descriptive sentence shown on the static cards.  Neither of those has a
 * field in the content model yet, so they live here next to the card copy.
 */
$mp_sector_titles = array(
	'hospitality' => 'Cafés, Bars & Restaurants',
	'retail'      => 'Retail & Shopfitting',
	'private'     => 'Private Kitchens',
);

$mp_sector_alts = array(
	'hospitality' => 'Café and bar interior with long timber counter',
	'retail'      => 'Retail store display shelving and checkout counter',
	'private'     => 'Bespoke oak kitchen',
);

$mp_sector_arrow = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';
?>
<section class="mp-section mp-section--surface" id="hospitality">
  <div class="mp-container">
    <div class="mp-section-head">
      <div>
        <span class="mp-eyebrow">Who We Build For</span>
        <h2 class="mp-headline-lg">Furniture Made for Commercial Interiors</h2>
      </div>
      <div class="mp-section-head__intro">
        <p class="mp-body">Every venue is different, but the demands are the same: long opening hours, constant use and a brand that has to look right from day one.</p>
      </div>
    </div>
    <div class="mp-sectors">
      <?php $mp_sector_index = 0; ?>
      <?php foreach ( $mp_sectors as $mp_sector ) : ?>
        <?php
        $mp_sector_link  = isset( $mp_sector_links[ $mp_sector->slug ] ) ? $mp_sector_links[ $mp_sector->slug ] : array(
            'href'  => '#consultation',
            'label' => 'Enquire',
        );
        $mp_sector_img   = (string) get_term_meta( $mp_sector->term_id, 'sector_image', true );
        $mp_sector_alt   = isset( $mp_sector_alts[ $mp_sector->slug ] ) ? $mp_sector_alts[ $mp_sector->slug ] : wp_strip_all_tags( $mp_sector->name );
        $mp_sector_title = isset( $mp_sector_titles[ $mp_sector->slug ] ) ? $mp_sector_titles[ $mp_sector->slug ] : wp_strip_all_tags( $mp_sector->name );
        ?>
      <a class="mp-sector<?php echo 0 === $mp_sector_index ? ' mp-sector--lead' : ''; ?>" href="<?php echo esc_url( $mp_sector_link['href'] ); ?>">
        <div class="mp-sector__media">
          <img src="<?php echo esc_url( $mp_sector_img ); ?>" alt="<?php echo esc_attr( $mp_sector_alt ); ?>">
          <span class="mp-sector__tag"><?php echo esc_html( wp_strip_all_tags( $mp_sector->name ) . ' • ' . str_pad( (string) ( $mp_sector_index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
        </div>
        <div class="mp-sector__body">
          <h3 class="mp-headline-md"><?php echo esc_html( $mp_sector_title ); ?></h3>
          <p class="mp-body"><?php echo esc_html( $mp_sector->description ); ?></p>
          <span class="mp-sector__link"><?php echo esc_html( $mp_sector_link['label'] ); ?> <?php echo $mp_sector_arrow; // phpcs:ignore WordPress.Security.EscapeOutput -- static inline SVG icon ?></span>
        </div>
      </a>
        <?php $mp_sector_index++; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>