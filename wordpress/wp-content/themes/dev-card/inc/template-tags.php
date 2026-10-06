<?php
/**
 * Data helpers used by templates.
 *
 * @package Dev_Card
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get all skills grouped by skill group.
 *
 * Groups are ordered by creation (term ID), skills inside a group by the
 * "Order" field, then alphabetically. Uses one query for all skills instead
 * of one query per group.
 *
 * @return array<string, string[]> Group name => list of skill titles.
 */
function dev_card_get_skills_by_group() {
	$groups = get_terms(
		array(
			'taxonomy'   => 'dc_skill_group',
			'hide_empty' => true,
			'orderby'    => 'term_id',
			'order'      => 'ASC',
		)
	);

	if ( is_wp_error( $groups ) || empty( $groups ) ) {
		return array();
	}

	$skills = new WP_Query(
		array(
			'post_type'      => 'dc_skill',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
			'no_found_rows'  => true, // We don't paginate, so skip counting total rows.
		)
	);

	// Prepare an empty bucket per group, in the right order.
	$result = array();
	foreach ( $groups as $group ) {
		$result[ $group->term_id ] = array(
			'name'   => $group->name,
			'skills' => array(),
		);
	}

	// Put every skill into the bucket(s) of its group(s).
	foreach ( $skills->posts as $skill ) {
		$skill_groups = get_the_terms( $skill, 'dc_skill_group' );
		if ( ! $skill_groups || is_wp_error( $skill_groups ) ) {
			continue;
		}

		foreach ( $skill_groups as $group ) {
			if ( isset( $result[ $group->term_id ] ) ) {
				$result[ $group->term_id ]['skills'][] = get_the_title( $skill );
			}
		}
	}

	return wp_list_pluck( $result, 'skills', 'name' );
}

/**
 * Get published entries of a data post type (e.g. experience, education)
 * together with their custom fields.
 *
 * @param string $post_type Post type, e.g. 'dc_experience'.
 * @return array<int, array<string, string>> List of entries: 'title' + every meta field
 *                                            without the "_dc_" prefix ('company', 'period', ...).
 */
function dev_card_get_entries( $post_type ) {
	$config = dev_card_meta_box_config()[ $post_type ] ?? array( 'fields' => array() );

	$query = new WP_Query(
		array(
			'post_type'      => $post_type,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'DESC',
			),
			'no_found_rows'  => true,
		)
	);

	$entries = array();
	foreach ( $query->posts as $post ) {
		$entry = array( 'title' => get_the_title( $post ) );

		// Meta of all found posts is already cached by WP_Query, so this adds no queries.
		foreach ( array_keys( $config['fields'] ) as $key ) {
			$entry[ str_replace( '_dc_', '', $key ) ] = (string) get_post_meta( $post->ID, $key, true );
		}

		$entries[] = $entry;
	}

	return $entries;
}
