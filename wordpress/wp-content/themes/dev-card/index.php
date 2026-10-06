<?php
/**
 * Main (fallback) template of the theme.
 * WordPress uses it when no more specific template is found.
 *
 * @package Dev_Card
 */

get_header();
?>

<?php if ( have_posts() ) : ?>
	<?php while ( have_posts() ) : the_post(); ?>
		<?php get_template_part( 'template-parts/content' ); ?>
	<?php endwhile; ?>

	<?php the_posts_pagination(); ?>
<?php else : ?>
	<p>Nothing found.</p>
<?php endif; ?>

<?php
get_footer();
