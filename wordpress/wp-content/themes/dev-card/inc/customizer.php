<?php
/**
 * Customizer settings: Appearance -> Customize -> Contacts.
 *
 * Values are stored as theme mods (one option `theme_mods_dev-card` in wp_options)
 * and read in templates with get_theme_mod().
 *
 * @package Dev_Card
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Contact fields shown in the Customizer.
 *
 * Add a new contact here, and it gets a setting and a control automatically.
 *
 * @return array<string, array{label: string, type: string, sanitize: callable}>
 */
function dev_card_contact_fields() {
	return array(
		'email'    => array(
			'label'    => 'Email',
			'type'     => 'email',
			'sanitize' => 'sanitize_email',
		),
		'phone'    => array(
			'label'    => 'Phone',
			'type'     => 'tel',
			'sanitize' => 'dev_card_sanitize_phone',
		),
		'linkedin' => array(
			'label'    => 'LinkedIn URL',
			'type'     => 'url',
			'sanitize' => 'esc_url_raw',
		),
		'telegram' => array(
			'label'    => 'Telegram URL',
			'type'     => 'url',
			'sanitize' => 'esc_url_raw',
		),
		'location' => array(
			'label'    => 'Location',
			'type'     => 'text',
			'sanitize' => 'sanitize_text_field',
		),
	);
}

/**
 * Register the "Contacts" section, its settings and controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 */
function dev_card_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'dev_card_contacts',
		array(
			'title'       => 'Contacts',
			'description' => 'Shown on the Contacts page. Leave a field empty to hide it.',
			'priority'    => 30,
		)
	);

	foreach ( dev_card_contact_fields() as $key => $field ) {
		$setting_id = 'dev_card_contact_' . $key;

		// Setting = where and how the value is stored.
		$wp_customize->add_setting(
			$setting_id,
			array(
				'type'              => 'theme_mod',
				'default'           => '',
				'sanitize_callback' => $field['sanitize'],
				'transport'         => 'refresh',
			)
		);

		// Control = the input field the user sees.
		$wp_customize->add_control(
			$setting_id,
			array(
				'label'   => $field['label'],
				'section' => 'dev_card_contacts',
				'type'    => $field['type'],
			)
		);
	}
}
add_action( 'customize_register', 'dev_card_customize_register' );

/**
 * "SEO & Sharing" section: default image for link previews in social networks.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 */
function dev_card_customize_register_seo( $wp_customize ) {
	$wp_customize->add_section(
		'dev_card_seo',
		array(
			'title'       => 'SEO & Sharing',
			'description' => 'Image shown when a link to the site is shared (LinkedIn, Telegram, Slack...). Recommended size: 1200×630. Used when the page has no featured image.',
			'priority'    => 31,
		)
	);

	// Media control stores the attachment ID, so absint() is the right sanitizer.
	$wp_customize->add_setting(
		'dev_card_og_image',
		array(
			'type'              => 'theme_mod',
			'default'           => 0,
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'dev_card_og_image',
			array(
				'label'     => 'Sharing image',
				'section'   => 'dev_card_seo',
				'mime_type' => 'image',
			)
		)
	);
}
add_action( 'customize_register', 'dev_card_customize_register_seo' );

/**
 * Get a contact value saved in the Customizer.
 *
 * @param string $key Field key from dev_card_contact_fields(), e.g. 'email'.
 * @return string
 */
function dev_card_get_contact( $key ) {
	return (string) get_theme_mod( 'dev_card_contact_' . $key, '' );
}

/**
 * Sanitize a phone number: keep only digits, "+", spaces, dashes and parentheses.
 *
 * WordPress has no built-in sanitize_phone(), so we write our own callback.
 * Any callable that takes a value and returns the clean value works here.
 *
 * @param string $phone Raw input, e.g. "+48 (123) 456-789 <b>".
 * @return string Clean value, e.g. "+48 (123) 456-789".
 */
function dev_card_sanitize_phone( $phone ) {
	$phone = preg_replace( '/[^0-9+\-\s()]/', '', (string) $phone );

	return trim( preg_replace( '/\s+/', ' ', $phone ) );
}

/**
 * Build a value for a tel: link: "+48 (123) 456-789" -> "+48123456789".
 *
 * @param string $phone Phone number as saved in the Customizer.
 * @return string
 */
function dev_card_phone_href( $phone ) {
	return preg_replace( '/[^0-9+]/', '', $phone );
}

/**
 * Turn a URL into a short human-readable label: "https://www.github.com/user/" -> "github.com/user".
 *
 * @param string $url Full URL.
 * @return string
 */
function dev_card_pretty_url( $url ) {
	$url = preg_replace( '#^https?://(www\.)?#i', '', $url );

	return untrailingslashit( $url );
}
