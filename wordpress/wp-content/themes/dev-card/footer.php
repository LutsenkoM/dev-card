<?php
/**
 * The footer: closes the main content and the page.
 * Loaded by get_footer().
 *
 * @package Dev_Card
 */
?>
</main>

<footer class="site-footer">
	<div class="container d-flex flex-column flex-md-row align-items-center justify-content-between gap-3 py-4">
		<p class="site-footer__copy mb-0">
			&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>
			<?php if ( get_bloginfo( 'description' ) ) : ?>
				<span class="site-footer__sep">·</span> <?php bloginfo( 'description' ); ?>
			<?php endif; ?>
		</p>

		<?php if ( has_nav_menu( 'footer' ) ) : ?>
			<nav class="footer-nav" aria-label="Footer">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'nav footer-nav__list',
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
			</nav>
		<?php endif; ?>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
