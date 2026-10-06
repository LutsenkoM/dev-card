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
	<div class="container">
		<?php if ( ! is_page( 'contacts' ) ) : ?>
			<div class="site-footer__cta reveal">
				<p class="site-footer__eyebrow">// got a project in mind?</p>
				<a class="site-footer__big-link" href="<?php echo esc_url( dev_card_page_url( 'contacts' ) ); ?>">
					Let's talk <span aria-hidden="true">&nearr;</span>
				</a>
			</div>
		<?php endif; ?>

		<div class="site-footer__bottom">
			<p class="site-footer__copy mb-0">
				&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>
				<?php if ( get_bloginfo( 'description' ) ) : ?>
					<span class="site-footer__sep">✦</span> <?php bloginfo( 'description' ); ?>
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

			<a class="back-to-top" href="#main">Back to top <span aria-hidden="true">&uarr;</span></a>
		</div>
	</div>

	<?php // Giant name across the bottom edge — decoration only. ?>
	<div class="site-footer__wordmark" aria-hidden="true" data-parallax="-0.04"><?php bloginfo( 'name' ); ?></div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
