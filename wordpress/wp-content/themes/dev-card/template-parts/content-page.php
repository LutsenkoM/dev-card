<?php
/**
 * Template part for displaying full page content.
 *
 * @package Dev_Card
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<h1 class="entry-title"><?php the_title(); ?></h1>

	<div class="entry-content">
		<?php the_content(); ?>
	</div>
</article>
