<?php
/**
 * Template part for displaying a post in a list (blog, archive, search).
 *
 * @package Dev_Card
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'card card-dev h-100' ); ?>>
	<div class="card-body">
		<p class="card-dev__meta"><?php echo esc_html( get_the_date() ); ?></p>

		<h2 class="card-title h4">
			<a class="stretched-link" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h2>

		<div class="card-text">
			<?php the_excerpt(); ?>
		</div>
	</div>
</article>
