<?php
/**
 * The header: <head> section and the top of the page.
 * Loaded by get_header().
 *
 * @package Dev_Card
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> data-bs-theme="dark">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header sticky-top">
	<nav class="navbar navbar-expand-md" aria-label="Primary">
		<div class="container">
			<a class="navbar-brand site-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<?php if ( has_custom_logo() ) : ?>
					<?php
					// Output the logo image only: the brand link is already here.
					echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'full', false, array( 'class' => 'site-brand__logo' ) );
					?>
				<?php else : ?>
					<span class="site-brand__mark" aria-hidden="true">&lt;/&gt;</span>
				<?php endif; ?>
				<span class="site-brand__name"><?php bloginfo( 'name' ); ?></span>
			</a>

			<?php if ( has_nav_menu( 'primary' ) ) : ?>
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#primary-nav" aria-controls="primary-nav" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>

				<div class="collapse navbar-collapse" id="primary-nav">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'container'      => false,
							'menu_class'     => 'navbar-nav ms-auto site-nav__list',
							'depth'          => 1,
							'fallback_cb'    => false,
						)
					);
					?>
				</div>
			<?php endif; ?>
		</div>
	</nav>
</header>

<main class="site-main">
