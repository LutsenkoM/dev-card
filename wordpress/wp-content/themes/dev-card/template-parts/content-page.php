<?php
/**
 * Template part for displaying full page content.
 *
 * @package Dev_Card
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="page-hero">
		<div class="container">
			<p class="page-hero__path">~/<?php echo esc_html( get_post_field( 'post_name' ) ); ?></p>
			<h1 class="page-hero__title"><?php the_title(); ?><span class="text-accent">.</span></h1>
		</div>
	</header>

	<div class="container py-5">
		<div class="entry-content">
			<?php the_content(); ?>
		</div>
	</div>
</article>
