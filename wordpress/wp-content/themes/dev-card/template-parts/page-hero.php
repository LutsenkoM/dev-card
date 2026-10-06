<?php
/**
 * Template part: page title area ("hero") shared by all inner pages.
 * Must be called inside The Loop.
 *
 * @package Dev_Card
 */
?>
<header class="page-hero">
	<?php // Huge outlined title in the background, moves slower than the page (parallax). ?>
	<span class="page-hero__bg-word" aria-hidden="true" data-parallax="0.25"><?php the_title(); ?></span>

	<div class="container">
		<p class="page-hero__path reveal">~/<?php echo esc_html( get_post_field( 'post_name' ) ); ?></p>
		<h1 class="page-hero__title reveal"><?php the_title(); ?><span class="text-accent">.</span></h1>
		<?php if ( has_excerpt() ) : ?>
			<p class="page-hero__lead reveal"><?php echo esc_html( get_the_excerpt() ); ?></p>
		<?php endif; ?>
	</div>
</header>
