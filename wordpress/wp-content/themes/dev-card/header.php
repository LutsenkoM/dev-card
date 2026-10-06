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

<a class="skip-link" href="#main">Skip to content</a>

<?php // Decorative background layers: gradient blobs, grid, film grain. Purely visual. ?>
<div class="bg-decor" aria-hidden="true">
	<div class="bg-blob bg-blob--1" data-parallax="0.12"></div>
	<div class="bg-blob bg-blob--2" data-parallax="-0.08"></div>
	<div class="bg-blob bg-blob--3" data-parallax="0.05"></div>
	<div class="bg-grid"></div>
	<div class="bg-noise"></div>
</div>
<div class="cursor-glow" aria-hidden="true"></div>
<div class="scroll-progress" aria-hidden="true"></div>

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
					<a class="btn btn-primary btn-sm site-header__cta ms-md-3" href="<?php echo esc_url( dev_card_page_url( 'contacts' ) ); ?>" data-magnetic>
						Let's talk <span aria-hidden="true">&nearr;</span>
					</a>
				</div>
			<?php endif; ?>
		</div>
	</nav>
</header>

<main class="site-main" id="main">
