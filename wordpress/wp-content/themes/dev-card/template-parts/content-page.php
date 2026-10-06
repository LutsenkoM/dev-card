<?php
/**
 * Template part for displaying full page content.
 *
 * @package Dev_Card
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php get_template_part( 'template-parts/page-hero' ); ?>

	<div class="container py-5">
		<div class="entry-content">
			<?php the_content(); ?>
		</div>
	</div>
</article>
