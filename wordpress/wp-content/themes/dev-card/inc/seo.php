<?php
/**
 * SEO and social sharing: meta description, Open Graph, Twitter cards,
 * JSON-LD structured data, sitemap and author-archive hardening.
 *
 * If an SEO plugin (Yoast, Rank Math, SEOPress) is active, it handles the meta tags
 * and this module stays silent to avoid duplicates.
 *
 * @package Dev_Card
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether a dedicated SEO plugin is active.
 *
 * @return bool
 */
function dev_card_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'SEOPRESS_VERSION' );
}

/**
 * Meta description for the current request (max ~160 characters).
 *
 * Priority: page excerpt → trimmed page content → site tagline.
 *
 * @return string
 */
function dev_card_meta_description() {
	$text = '';

	if ( is_singular() ) {
		$post = get_queried_object();
		$text = has_excerpt( $post )
			? get_the_excerpt( $post )
			: wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 30, '…' );
	}

	if ( '' === trim( $text ) ) {
		$text = get_bloginfo( 'description' );
	}

	$text = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $text ) ) );

	return mb_strlen( $text ) > 160 ? rtrim( mb_substr( $text, 0, 157 ) ) . '…' : $text;
}

/**
 * Image for social sharing.
 *
 * Priority: featured image of the current page → Profile photo → "Sharing image" from the Customizer.
 *
 * @return array{url: string, width: int, height: int, alt: string}|null
 */
function dev_card_og_image() {
	$candidates = array();

	if ( is_singular() && has_post_thumbnail() ) {
		$candidates[] = get_post_thumbnail_id();
	}

	$profile = get_page_by_path( 'profile' );
	if ( $profile && has_post_thumbnail( $profile ) ) {
		$candidates[] = get_post_thumbnail_id( $profile );
	}

	$candidates[] = (int) get_theme_mod( 'dev_card_og_image', 0 );

	foreach ( array_filter( $candidates ) as $attachment_id ) {
		$image = wp_get_attachment_image_src( $attachment_id, 'large' );
		if ( $image ) {
			$alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );

			return array(
				'url'    => $image[0],
				'width'  => (int) $image[1],
				'height' => (int) $image[2],
				'alt'    => $alt ? $alt : get_bloginfo( 'name' ),
			);
		}
	}

	return null;
}

/**
 * Print meta description, Open Graph and Twitter Card tags in <head>.
 */
function dev_card_print_meta_tags() {
	if ( dev_card_seo_plugin_active() ) {
		return;
	}

	$description = dev_card_meta_description();
	$url         = is_singular() ? get_permalink() : home_url( add_query_arg( array() ) );
	$image       = dev_card_og_image();

	// property => content. Empty values are skipped.
	$og = array(
		'og:type'        => is_front_page() ? 'profile' : 'website',
		'og:site_name'   => get_bloginfo( 'name' ),
		'og:title'       => wp_get_document_title(),
		'og:description' => $description,
		'og:url'         => $url,
		'og:locale'      => get_locale(),
		'og:image'       => $image['url'] ?? '',
		'og:image:width' => $image['width'] ?? '',
		'og:image:height' => $image['height'] ?? '',
		'og:image:alt'   => $image['alt'] ?? '',
	);

	echo "\n<!-- Dev Card SEO -->\n";
	printf( '<meta name="description" content="%s">' . "\n", esc_attr( $description ) );

	foreach ( array_filter( $og ) as $property => $content ) {
		printf( '<meta property="%s" content="%s">' . "\n", esc_attr( $property ), esc_attr( $content ) );
	}

	printf( '<meta name="twitter:card" content="%s">' . "\n", $image ? 'summary_large_image' : 'summary' );
}
add_action( 'wp_head', 'dev_card_print_meta_tags', 1 );

/**
 * JSON-LD "Person" structured data on the front page and the Profile page.
 * Helps Google understand who the site is about (name, job title, profiles).
 */
function dev_card_print_person_schema() {
	if ( dev_card_seo_plugin_active() || ! ( is_front_page() || is_page( 'profile' ) ) ) {
		return;
	}

	$skills = dev_card_get_skills_by_group();
	$facts  = dev_card_get_profile_facts();
	$image  = dev_card_og_image();

	$person = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'Person',
		'name'        => get_bloginfo( 'name' ),
		'jobTitle'    => get_bloginfo( 'description' ),
		'url'         => home_url( '/' ),
		'description' => dev_card_meta_description(),
		'knowsAbout'  => array_values( (array) reset( $skills ) ),
		'sameAs'      => array_values( array_filter( array( dev_card_get_contact( 'linkedin' ), dev_card_get_contact( 'telegram' ) ) ) ),
	);

	$location = dev_card_get_contact( 'location' ) ? dev_card_get_contact( 'location' ) : ( $facts['Location'] ?? '' );
	if ( $location ) {
		$person['address'] = array(
			'@type'           => 'PostalAddress',
			'addressLocality' => $location,
		);
	}

	if ( $image ) {
		$person['image'] = $image['url'];
	}

	wp_print_inline_script_tag(
		wp_json_encode( array_filter( $person ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ),
		array( 'type' => 'application/ld+json' )
	);
}
add_action( 'wp_head', 'dev_card_print_person_schema', 2 );

/**
 * Remove the users sitemap: it lists author archives (/author/{login}/)
 * and reveals the admin login name to anyone.
 *
 * @param WP_Sitemaps_Provider $provider Sitemap provider.
 * @param string               $name     Provider name: 'posts', 'taxonomies' or 'users'.
 * @return WP_Sitemaps_Provider|false
 */
function dev_card_disable_users_sitemap( $provider, $name ) {
	return 'users' === $name ? false : $provider;
}
add_filter( 'wp_sitemaps_add_provider', 'dev_card_disable_users_sitemap', 10, 2 );

/**
 * Author archives make no sense on a one-person business card and leak the login name
 * (/?author=1 redirects to /author/{login}/). Send them to the front page instead.
 */
function dev_card_redirect_author_archives() {
	if ( is_author() || isset( $_GET['author'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only redirect.
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
}
// Priority 1: run before core's redirect_canonical() (priority 10), which would
// first redirect /?author=1 to /author/{login}/ and reveal the login.
add_action( 'template_redirect', 'dev_card_redirect_author_archives', 1 );
