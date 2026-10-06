<?php
/**
 * Template part for displaying a post in a list (blog, archive, search).
 *
 * @package Dev_Card
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<h2 class="entry-title">
		<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
	</h2>

	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div>
</article>
