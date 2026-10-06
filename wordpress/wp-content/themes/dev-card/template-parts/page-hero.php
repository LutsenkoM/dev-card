<?php
/**
 * Template part: page title area ("hero") shared by all inner pages.
 * Must be called inside The Loop.
 *
 * @package Dev_Card
 */
?>
<header class="page-hero">
	<div class="container">
		<p class="page-hero__path">~/<?php echo esc_html( get_post_field( 'post_name' ) ); ?></p>
		<h1 class="page-hero__title"><?php the_title(); ?><span class="text-accent">.</span></h1>
	</div>
</header>
