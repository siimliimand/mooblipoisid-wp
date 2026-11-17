<?php
/**
 * Front page (homepage) template.
 *
 * Renders the homepage dynamically from the structured content model:
 *
 *  - Hero + consultation copy -> Homepage Settings (mooblipoisid_get_homepage_settings())
 *  - Trust facts, sectors, product ranges, projects, capabilities, process steps
 *    and client stories -> the `trust_stat`, `sector`, `range`, `project`,
 *    `capability`, `step` and `testimonial` entries.
 *
 * Every section is a template part in template-parts/home/ so each one can be
 * reused or overridden on its own. The markup, class names and nesting are kept
 * byte-identical to the static homepage markup (docs/homepage-content.html) so
 * the design does not change at all.
 *
 * @package mooblipoisid-child
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<main id="inner-wrap" class="wrap kt-clear" role="main">
	<div id="primary" class="content-area">
		<div class="content-container site-container">
			<div id="main" class="site-main">
				<div class="content-wrap">
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<div class="entry-content-wrap">
						<div class="entry-content single-content">
							<?php
							$mooblipoisid_home_sections = array(
								'hero',
								'trust',
								'sectors',
								'ranges',
								'projects',
								'capabilities',
								'process',
								'ethos',
								'stories',
								'consultation',
							);

							/**
							 * Filter the homepage sections rendered by front-page.php.
							 *
							 * @param string[] $sections Template part names, in render order.
							 */
							$mooblipoisid_home_sections = apply_filters( 'mooblipoisid_homepage_sections', $mooblipoisid_home_sections );

							foreach ( (array) $mooblipoisid_home_sections as $mooblipoisid_home_section ) {
								$mooblipoisid_home_section = sanitize_key( $mooblipoisid_home_section );
								if ( '' === $mooblipoisid_home_section ) {
									continue;
								}
								get_template_part( 'template-parts/home/' . $mooblipoisid_home_section );
							}
							?>
						</div>
						</div>
					</article>
				</div>
			</div>
		</div>
	</div>
</main>

<?php
get_footer();