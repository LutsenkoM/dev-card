<?php
/**
 * Custom fields (post meta) and the meta boxes to edit them.
 *
 * @package Dev_Card
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Custom fields per post type.
 *
 * Meta keys start with "_" so WordPress treats them as protected:
 * they are hidden from the generic "Custom Fields" panel and only editable via our meta box.
 *
 * @return array<string, array{title: string, fields: array<string, array{label: string, type: string, placeholder?: string}>}>
 */
function dev_card_meta_box_config() {
	return array(
		'dc_experience' => array(
			'title'  => 'Job details',
			'fields' => array(
				'_dc_company'     => array(
					'label'       => 'Company',
					'type'        => 'text',
					'placeholder' => 'eKreative',
				),
				'_dc_period'      => array(
					'label'       => 'Period',
					'type'        => 'text',
					'placeholder' => '06/2019 — 04/2026',
				),
				'_dc_description' => array(
					'label' => 'Description',
					'type'  => 'textarea',
				),
			),
		),
		'dc_education'  => array(
			'title'  => 'Degree details',
			'fields' => array(
				'_dc_school' => array(
					'label'       => 'School',
					'type'        => 'text',
					'placeholder' => 'University name, city',
				),
				'_dc_period' => array(
					'label'       => 'Period',
					'type'        => 'text',
					'placeholder' => '09/2009 — 01/2014',
				),
			),
		),
		'page'          => array(
			'title'     => 'Profile details',
			// Show this box only on the Profile page, not on every page.
			'condition' => static function ( $post ) {
				return 'profile' === $post->post_name;
			},
			'fields'    => array(
				'_dc_facts'     => array(
					'label'       => 'Facts (sidebar card)',
					'type'        => 'pairs',
					'placeholder' => "Location: Krakow, Poland\nExperience: 8+ years",
				),
				'_dc_languages' => array(
					'label'       => 'Languages',
					'type'        => 'pairs',
					'placeholder' => "English: B2\nUkrainian: Native",
				),
			),
		),
	);
}

/**
 * Check whether a meta box config applies to the given post.
 *
 * @param array   $box  One entry of dev_card_meta_box_config().
 * @param WP_Post $post Post being edited or saved.
 * @return bool
 */
function dev_card_meta_box_applies( $box, $post ) {
	return ! isset( $box['condition'] ) || call_user_func( $box['condition'], $post );
}

/**
 * Parse "Label: Value" lines into an array.
 *
 * Splits each line at the first colon, so values may contain colons too.
 * Lines without a colon or with an empty part are skipped.
 *
 * @param string $text Multiline text, e.g. "English: B2\nPolish: A2".
 * @return array<string, string> Label => value, e.g. [ 'English' => 'B2', 'Polish' => 'A2' ].
 */
function dev_card_parse_pairs( $text ) {
	$pairs = array();

	foreach ( preg_split( '/\R/', (string) $text ) as $line ) {
		$parts = array_map( 'trim', explode( ':', $line, 2 ) );

		if ( 2 === count( $parts ) && '' !== $parts[0] && '' !== $parts[1] ) {
			$pairs[ $parts[0] ] = $parts[1];
		}
	}

	return $pairs;
}

/**
 * Register the meta keys, so WordPress knows their type and how to sanitize them.
 * `show_in_rest` also exposes them to the block editor and the REST API.
 */
function dev_card_register_meta() {
	foreach ( dev_card_meta_box_config() as $post_type => $box ) {
		foreach ( $box['fields'] as $key => $field ) {
			register_post_meta(
				$post_type,
				$key,
				array(
					'type'              => 'string',
					'single'            => true,
					'default'           => '',
					'show_in_rest'      => true,
					// Multiline fields keep line breaks, single-line fields don't.
					'sanitize_callback' => 'text' === $field['type'] ? 'sanitize_text_field' : 'sanitize_textarea_field',
					// Protected ("_") keys need an explicit permission check to be editable via REST.
					'auth_callback'     => static function () {
						return current_user_can( 'edit_posts' );
					},
				)
			);
		}
	}
}
add_action( 'init', 'dev_card_register_meta' );

/**
 * Add a meta box to every post type from the config.
 *
 * @param string  $post_type Post type of the edited post.
 * @param WP_Post $post      Edited post.
 */
function dev_card_add_meta_boxes( $post_type, $post ) {
	foreach ( dev_card_meta_box_config() as $box_post_type => $box ) {
		if ( $box_post_type !== $post_type || ! dev_card_meta_box_applies( $box, $post ) ) {
			continue;
		}

		add_meta_box(
			'dev_card_details',          // HTML id of the box.
			$box['title'],               // Box title.
			'dev_card_render_meta_box',  // Callback that prints the fields.
			$post_type,                  // Screen (post type).
			'normal',                    // Context: main column.
			'high',                      // Priority: right after the title.
			array( 'fields' => $box['fields'] ) // Passed to the callback as $box['args'].
		);
	}
}
add_action( 'add_meta_boxes', 'dev_card_add_meta_boxes', 10, 2 );

/**
 * Print the meta box fields.
 *
 * @param WP_Post $post Current post.
 * @param array   $box  Meta box data; our fields are in $box['args']['fields'].
 */
function dev_card_render_meta_box( $post, $box ) {
	// Nonce: proves the form was submitted from this admin screen (protection against CSRF).
	wp_nonce_field( 'dev_card_save_meta', 'dev_card_meta_nonce' );

	echo '<div class="dev-card-meta">';

	foreach ( $box['args']['fields'] as $key => $field ) {
		$value       = get_post_meta( $post->ID, $key, true );
		$placeholder = $field['placeholder'] ?? '';

		printf( '<p><label for="%1$s"><strong>%2$s</strong></label><br>', esc_attr( $key ), esc_html( $field['label'] ) );

		if ( 'pairs' === $field['type'] ) {
			// "Label: Value" per line — a simple repeater without JavaScript.
			printf(
				'<textarea id="%1$s" name="%1$s" rows="6" class="widefat code" placeholder="%3$s">%2$s</textarea>
				<span class="description">One item per line: <code>Label: Value</code></span>',
				esc_attr( $key ),
				esc_textarea( $value ),
				esc_attr( $placeholder )
			);
		} elseif ( 'textarea' === $field['type'] ) {
			printf(
				'<textarea id="%1$s" name="%1$s" rows="4" class="widefat">%2$s</textarea>',
				esc_attr( $key ),
				esc_textarea( $value )
			);
		} else {
			printf(
				'<input type="text" id="%1$s" name="%1$s" value="%2$s" placeholder="%3$s" class="widefat">',
				esc_attr( $key ),
				esc_attr( $value ),
				esc_attr( $placeholder )
			);
		}

		echo '</p>';
	}

	echo '</div>';
}

/**
 * Save the meta box fields.
 *
 * @param int $post_id Post ID.
 */
function dev_card_save_meta_box( $post_id ) {
	// 1. The request must come from our form, with a valid nonce.
	if ( ! isset( $_POST['dev_card_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['dev_card_meta_nonce'] ), 'dev_card_save_meta' ) ) {
		return;
	}

	// 2. Skip autosaves: they don't send our fields, and we would wipe the values.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// 3. The current user must be allowed to edit this post.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$config = dev_card_meta_box_config()[ get_post_type( $post_id ) ] ?? null;
	if ( ! $config || ! dev_card_meta_box_applies( $config, get_post( $post_id ) ) ) {
		return;
	}

	foreach ( array_keys( $config['fields'] ) as $key ) {
		if ( ! isset( $_POST[ $key ] ) ) {
			continue;
		}

		// 4. wp_unslash() removes the slashes WordPress adds to $_POST;
		// sanitizing happens in the sanitize_callback from register_post_meta().
		$value = wp_unslash( $_POST[ $key ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		if ( '' === trim( $value ) ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}
}
add_action( 'save_post_dc_experience', 'dev_card_save_meta_box' );
add_action( 'save_post_dc_education', 'dev_card_save_meta_box' );
add_action( 'save_post_page', 'dev_card_save_meta_box' );
