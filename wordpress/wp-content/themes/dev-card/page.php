<?php
/**
 * Template for static pages.
 * Used for every page unless a more specific template exists
 * (e.g. page-{slug}.php or front-page.php).
 *
 * @package Dev_Card
 */

get_header();

while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/content', 'page' );
endwhile;

get_footer();
