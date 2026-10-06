<?php
/**
 * Dev Card theme functions and definitions.
 *
 * @package Dev_Card
 */

// Prevent direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register theme features.
 *
 * Runs on the `after_setup_theme` hook, right after the theme is loaded
 * and before WordPress initializes the rest of the request.
 */
function dev_card_setup() {
	// Let WordPress manage the <title> tag in <head>.
	add_theme_support( 'title-tag' );

	// Enable featured images for posts and pages.
	add_theme_support( 'post-thumbnails' );

	// Output valid HTML5 markup for core elements.
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);

	// Allow uploading a logo in Appearance -> Customize -> Site Identity.
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 64,
			'width'       => 64,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	// Register menu locations. Menus are assigned to them in Appearance -> Menus.
	register_nav_menus(
		array(
			'primary' => 'Primary Menu',
			'footer'  => 'Footer Menu',
		)
	);
}
add_action( 'after_setup_theme', 'dev_card_setup' );

/**
 * Enqueue theme styles and scripts.
 */
function dev_card_enqueue_assets() {
	$theme_uri       = get_template_directory_uri();
	$theme_dir       = get_template_directory();
	$bootstrap_ver   = '5.3.8';
	$main_css_path   = '/assets/css/main.css';

	// Google Fonts: Inter for text, JetBrains Mono for "code" accents.
	wp_enqueue_style(
		'dev-card-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap',
		array(),
		null // No ?ver= — Google Fonts URLs must stay as is.
	);

	// Bootstrap CSS (bundled locally in the theme).
	wp_enqueue_style(
		'bootstrap',
		$theme_uri . '/assets/vendor/bootstrap/bootstrap.min.css',
		array(),
		$bootstrap_ver
	);

	// Theme styles. Depend on Bootstrap so they load after it and can override it.
	// filemtime() changes the version on every file save, so the browser never serves a stale copy.
	wp_enqueue_style(
		'dev-card-main',
		$theme_uri . $main_css_path,
		array( 'bootstrap', 'dev-card-fonts' ),
		filemtime( $theme_dir . $main_css_path )
	);

	// Bootstrap JS bundle (includes Popper). Needed for the mobile navbar toggle.
	wp_enqueue_script(
		'bootstrap',
		$theme_uri . '/assets/vendor/bootstrap/bootstrap.bundle.min.js',
		array(),
		$bootstrap_ver,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'dev_card_enqueue_assets' );

/**
 * Add Bootstrap's `nav-item` class to menu <li> elements.
 *
 * @param string[] $classes CSS classes of the menu item.
 * @param WP_Post  $item    Menu item object.
 * @param stdClass $args    wp_nav_menu() arguments.
 * @return string[]
 */
function dev_card_nav_menu_item_class( $classes, $item, $args ) {
	if ( in_array( $args->theme_location, array( 'primary', 'footer' ), true ) ) {
		$classes[] = 'nav-item';
	}

	return $classes;
}
add_filter( 'nav_menu_css_class', 'dev_card_nav_menu_item_class', 10, 3 );

/**
 * Add Bootstrap's `nav-link` (and `active`) classes to menu links.
 *
 * @param array    $atts HTML attributes of the <a> tag.
 * @param WP_Post  $item Menu item object.
 * @param stdClass $args wp_nav_menu() arguments.
 * @return array
 */
function dev_card_nav_menu_link_attributes( $atts, $item, $args ) {
	if ( in_array( $args->theme_location, array( 'primary', 'footer' ), true ) ) {
		$atts['class'] = 'nav-link' . ( $item->current ? ' active' : '' );
	}

	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'dev_card_nav_menu_link_attributes', 10, 3 );

/**
 * Get the URL of a page by its slug.
 *
 * Templates link to each other through this helper instead of hardcoded URLs,
 * so links keep working if the domain or permalink structure changes.
 *
 * @param string $slug Page slug, e.g. 'profile'.
 * @return string Page URL, or the home URL if the page does not exist.
 */
function dev_card_page_url( $slug ) {
	$page = get_page_by_path( $slug );

	return $page ? get_permalink( $page ) : home_url( '/' );
}

/**
 * Theme modules.
 */
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/post-types.php';
require get_template_directory() . '/inc/template-tags.php';
