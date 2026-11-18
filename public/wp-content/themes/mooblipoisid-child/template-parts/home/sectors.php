<?php
/**
 * Homepage: sectors ("Who We Build For").
 *
 * The three cards are the `sector` terms assigned to Projects. The term
 * description is the card text, the `sector_image` term meta is the card photo
 * and `sector_order` decides the order. The section headline is fixed copy.
 * The `lang` argument keeps the sector names and descriptions in the active
 * language, and adds the site language as a fallback while a language has no
 * sectors of its own yet.
 *
 * @package mooblipoisid-child
 */

defined( 'ABSPATH' ) || exit;

$mp_sectors = get_terms(
	array(
		'taxonomy'   => 'sector',
		'hide_empty' => false,
		'lang'       => mooblipoisid_section_languages( 'sector' ),
	)
);

if ( is_wp_error( $mp_sectors ) || empty( $mp_sectors ) ) {
	return;
}

// Per-entry fallback: drop a default-language sector whose translation exists.
$mp_sectors = mooblipoisid_drop_translated_original_terms( $mp_sectors );

/*
 * The card headline, link and photo alt text are keyed by the Estonian
 * (default) slug below, so every language resolves them through the slug of
 * its term's original. Resolve each term to its Estonian original via Polylang
 * so a translated sector still maps to the stable default-language key.
 */
foreach ( $mp_sectors as $mp_sector ) {
	$mp_sector->mp_sort_order = (int) get_term_meta( $mp_sector->term_id, 'sector_order', true );

	$mp_origin_id = (int) $mp_sector->term_id;
	if ( function_exists( 'pll_get_term_translations' ) ) {
		$mp_translations = pll_get_term_translations( (int) $mp_sector->term_id );
		if ( is_array( $mp_translations ) && ! empty( $mp_translations['et'] ) ) {
			$mp_origin_id = (int) $mp_translations['et'];
		}
	}
	$mp_origin = $mp_origin_id === (int) $mp_sector->term_id ? $mp_sector : get_term( $mp_origin_id, 'sector' );
	$mp_sector->mp_origin_slug = ( $mp_origin instanceof WP_Term ) ? $mp_origin->slug : $mp_sector->slug;
}

$mp_sectors = wp_list_sort( $mp_sectors, array( 'mp_sort_order' => 'ASC', 'name' => 'ASC' ) );

$mp_settings = function_exists( 'mooblipoisid_get_homepage_settings' ) ? mooblipoisid_get_homepage_settings() : array();

$mp_sector_links = array(
	'hospitality' => array(
		'href'  => '#ranges',
		'label' => (string) ( $mp_settings['sectors_link_hospitality'] ?? '' ),
	),
	'retail'      => array(
		'href'  => '#ranges',
		'label' => (string) ( $mp_settings['sectors_link_retail'] ?? '' ),
	),
	'private'     => array(
		'href'  => '#consultation',
		'label' => (string) ( $mp_settings['sectors_link_private'] ?? '' ),
	),
);

/*
 * The card headline is the marketing name of the card (the "Hospitality"
 * sector is the "Cafés, Bars & Restaurants" card) and the photo alt text is
 * the descriptive sentence shown on the cards. Both are per-language fields on
 * the Homepage Settings screen, keyed by the Estonian (default) term slug so a
 * translated sector still resolves to the stable default-language key.
 */
$mp_sector_titles = array(
	'hospitality' => (string) ( $mp_settings['sector_title_hospitality'] ?? '' ),
	'retail'      => (string) ( $mp_settings['sector_title_retail'] ?? '' ),
	'private'     => (string) ( $mp_settings['sector_title_private'] ?? '' ),
);

$mp_sector_alts = array(
	'hospitality' => (string) ( $mp_settings['sector_alt_hospitality'] ?? '' ),
	'retail'      => (string) ( $mp_settings['sector_alt_retail'] ?? '' ),
	'private'     => (string) ( $mp_settings['sector_alt_private'] ?? '' ),
);

$mp_sector_arrow = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';
?>
<section class="mp-section mp-section--surface" id="hospitality">
  <div class="mp-container">
    <div class="mp-section-head">
      <div>
        <span class="mp-eyebrow"><?php echo esc_html( $mp_settings['sectors_eyebrow'] ?? '' ); ?></span>
        <h2 class="mp-headline-lg"><?php echo esc_html( $mp_settings['sectors_title'] ?? '' ); ?></h2>
      </div>
      <div class="mp-section-head__intro">
        <p class="mp-body"><?php echo esc_html( $mp_settings['sectors_intro'] ?? '' ); ?></p>
      </div>
    </div>
    <div class="mp-sectors">
      <?php $mp_sector_index = 0; ?>
      <?php foreach ( $mp_sectors as $mp_sector ) : ?>
        <?php
        $mp_sector_key   = isset( $mp_sector->mp_origin_slug ) ? (string) $mp_sector->mp_origin_slug : (string) $mp_sector->slug;
        $mp_sector_link  = isset( $mp_sector_links[ $mp_sector_key ] ) ? $mp_sector_links[ $mp_sector_key ] : array(
            'href'  => '#consultation',
            'label' => (string) ( $mp_settings['sectors_link_private'] ?? '' ),
        );
        $mp_sector_img   = (string) get_term_meta( $mp_sector->term_id, 'sector_image', true );
        $mp_sector_alt   = isset( $mp_sector_alts[ $mp_sector_key ] ) ? $mp_sector_alts[ $mp_sector_key ] : wp_strip_all_tags( $mp_sector->name );
        $mp_sector_title = isset( $mp_sector_titles[ $mp_sector_key ] ) ? $mp_sector_titles[ $mp_sector_key ] : wp_strip_all_tags( $mp_sector->name );
        ?>
      <a class="mp-sector" href="<?php echo esc_url( $mp_sector_link['href'] ); ?>">
        <div class="mp-sector__media">
          <img src="<?php echo esc_url( mooblipoisid_image_src( $mp_sector_img, 'large' ) ); ?>" alt="<?php echo esc_attr( $mp_sector_alt ); ?>" loading="lazy" decoding="async">
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