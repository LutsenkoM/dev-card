<?php
/**
 * Custom post types and taxonomies.
 *
 * @package Dev_Card
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build the admin labels for a post type.
 *
 * @param string $singular Singular name, e.g. 'Skill'.
 * @param string $plural   Plural name, e.g. 'Skills'.
 * @return array<string, string>
 */
function dev_card_post_type_labels( $singular, $plural ) {
	return array(
		'name'               => $plural,
		'singular_name'      => $singular,
		'menu_name'          => $plural,
		'add_new'            => 'Add ' . $singular,
		'add_new_item'       => 'Add New ' . $singular,
		'edit_item'          => 'Edit ' . $singular,
		'new_item'           => 'New ' . $singular,
		'search_items'       => 'Search ' . $plural,
		'not_found'          => 'No ' . strtolower( $plural ) . ' found.',
		'not_found_in_trash' => 'No ' . strtolower( $plural ) . ' found in Trash.',
		'all_items'          => 'All ' . $plural,
	);
}

/**
 * Shared arguments for data-only post types: editable in the admin,
 * but without own URLs on the site.
 *
 * @return array<string, mixed>
 */
function dev_card_data_post_type_args() {
	return array(
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_rest'        => true, // Required for the block editor.
		// 'page-attributes' adds the "Order" field (stored in menu_order).
		'supports'            => array( 'title', 'page-attributes' ),
		'has_archive'         => false,
		'rewrite'             => false,
		'exclude_from_search' => true,
	);
}

/**
 * Register post types and taxonomies.
 *
 * Runs on `init`: post types and taxonomies must be registered on every request,
 * WordPress does not store them in the database.
 */
function dev_card_register_post_types() {
	register_post_type(
		'dc_skill',
		array_merge(
			dev_card_data_post_type_args(),
			array(
				'labels'        => dev_card_post_type_labels( 'Skill', 'Skills' ),
				'menu_position' => 21,
				'menu_icon'     => 'dashicons-awards',
			)
		)
	);

	register_post_type(
		'dc_experience',
		array_merge(
			dev_card_data_post_type_args(),
			array(
				'labels'        => dev_card_post_type_labels( 'Job', 'Experience' ),
				'menu_position' => 22,
				'menu_icon'     => 'dashicons-portfolio',
			)
		)
	);

	register_post_type(
		'dc_education',
		array_merge(
			dev_card_data_post_type_args(),
			array(
				'labels'        => dev_card_post_type_labels( 'Degree', 'Education' ),
				'menu_position' => 23,
				'menu_icon'     => 'dashicons-welcome-learn-more',
			)
		)
	);

	register_taxonomy(
		'dc_skill_group',
		'dc_skill',
		array(
			'labels'            => array(
				'name'          => 'Skill Groups',
				'singular_name' => 'Skill Group',
				'menu_name'     => 'Groups',
				'all_items'     => 'All Groups',
				'edit_item'     => 'Edit Group',
				'add_new_item'  => 'Add New Group',
				'search_items'  => 'Search Groups',
				'not_found'     => 'No groups found.',
			),
			// Hierarchical = category-like UI with checkboxes (non-hierarchical = tag-like UI).
			'hierarchical'      => true,
			'public'            => false,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true, // "Skill Groups" column in the skills list.
			'rewrite'           => false,
		)
	);
}
add_action( 'init', 'dev_card_register_post_types' );
