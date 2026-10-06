<?php
/**
 * Template for "404 Not Found" errors.
 *
 * @package Dev_Card
 */

get_header();
?>

<section class="error-404">
	<h1>404</h1>
	<p>The page you are looking for does not exist.</p>
	<p><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Back to home</a></p>
</section>

<?php
get_footer();
