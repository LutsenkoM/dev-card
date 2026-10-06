<?php
/**
 * Main (fallback) template of the theme.
 * WordPress uses it when no more specific template is found.
 *
 * @package Dev_Card
 */

get_header();
?>

<div class="container py-5">
	<?php if ( have_posts() ) : ?>
		<div class="row g-4">
			<?php while ( have_posts() ) : the_post(); ?>
				<div class="col-md-6 col-lg-4">
					<?php get_template_part( 'template-parts/content' ); ?>
				</div>
			<?php endwhile; ?>
		</div>

		<?php the_posts_pagination( array( 'class' => 'mt-5' ) ); ?>
	<?php else : ?>
		<p>Nothing found.</p>
	<?php endif; ?>
</div>

<?php
get_footer();
