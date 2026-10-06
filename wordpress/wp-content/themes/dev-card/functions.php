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
}
add_action( 'after_setup_theme', 'dev_card_setup' );

/**
 * Enqueue theme styles and scripts.
 */
function dev_card_enqueue_assets() {
	$theme_version = wp_get_theme()->get( 'Version' );

	wp_enqueue_style(
		'dev-card-style',       // Unique handle.
		get_stylesheet_uri(),   // URL of the theme's style.css.
		array(),                // Dependencies.
		$theme_version          // Version for cache busting (?ver=0.1.0).
	);
}
add_action( 'wp_enqueue_scripts', 'dev_card_enqueue_assets' );
