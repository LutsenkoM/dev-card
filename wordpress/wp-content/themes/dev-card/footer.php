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
	<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?></p>
</footer>

<?php wp_footer(); ?>
</body>
</html>
