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
 * Register the "Skill" post type and the "Skill group" taxonomy.
 *
 * Runs on `init`: post types and taxonomies must be registered on every request,
 * WordPress does not store them in the database.
 */
function dev_card_register_skills() {
	register_post_type(
		'dc_skill',
		array(
			'labels'              => array(
				'name'               => 'Skills',
				'singular_name'      => 'Skill',
				'menu_name'          => 'Skills',
				'add_new'            => 'Add Skill',
				'add_new_item'       => 'Add New Skill',
				'edit_item'          => 'Edit Skill',
				'new_item'           => 'New Skill',
				'search_items'       => 'Search Skills',
				'not_found'          => 'No skills found.',
				'not_found_in_trash' => 'No skills found in Trash.',
				'all_items'          => 'All Skills',
			),
			// Data-only type: managed in the admin, but has no own URLs on the site.
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => true, // Required for the block editor.
			'menu_position'       => 21,
			'menu_icon'           => 'dashicons-awards',
			// 'page-attributes' adds the "Order" field (stored in menu_order).
			'supports'            => array( 'title', 'page-attributes' ),
			'has_archive'         => false,
			'rewrite'             => false,
			'exclude_from_search' => true,
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
add_action( 'init', 'dev_card_register_skills' );
