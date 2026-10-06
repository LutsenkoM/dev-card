<?php
/**
 * Template for "404 Not Found" errors.
 *
 * @package Dev_Card
 */

get_header();
?>

<section class="error-404 container text-center py-5">
	<p class="error-404__code">404</p>
	<h1 class="h3 mb-3">Page not found<span class="text-accent">.</span></h1>
	<p class="text-secondary mb-4">The page you are looking for does not exist or has been moved.</p>
	<a class="btn btn-primary btn-lg" href="<?php echo esc_url( home_url( '/' ) ); ?>">Back to home</a>
</section>

<?php
get_footer();
