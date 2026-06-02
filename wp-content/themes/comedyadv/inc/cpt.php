<?php
/**
 * Custom Post Types: comedian, show, workshop, locatie.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function comedyadv_register_cpts() {
	register_post_type( 'comedian', array(
		'label'         => 'Comedians',
		'labels'        => array(
			'name'          => 'Comedians',
			'singular_name' => 'Comedian',
			'add_new'       => 'Nieuwe comedian',
			'add_new_item'  => 'Nieuwe comedian toevoegen',
			'edit_item'     => 'Comedian bewerken',
			'menu_name'     => 'Comedians',
		),
		'public'        => true,
		'show_in_rest'  => true,
		'menu_icon'     => 'dashicons-microphone',
		'menu_position' => 20,
		'has_archive'   => 'comedians',
		'rewrite'       => array( 'slug' => 'comedians', 'with_front' => false ),
		'supports'      => array( 'title', 'editor', 'thumbnail' ),
	) );

	// Show: data-only — visible in admin, no public URL.
	register_post_type( 'show', array(
		'label'              => 'Shows',
		'labels'             => array(
			'name'          => 'Shows',
			'singular_name' => 'Show',
			'add_new'       => 'Nieuwe show',
			'add_new_item'  => 'Nieuwe show toevoegen',
			'edit_item'     => 'Show bewerken',
			'menu_name'     => 'Shows',
		),
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'show_in_admin_bar'  => true,
		'show_in_rest'       => true,
		'menu_icon'          => 'dashicons-calendar-alt',
		'menu_position'      => 21,
		'has_archive'        => false,
		'rewrite'            => false,
		'supports'           => array( 'title', 'editor', 'thumbnail' ),
	) );

	register_post_type( 'workshop', array(
		'label'         => 'Workshops',
		'labels'        => array(
			'name'          => 'Workshops',
			'singular_name' => 'Workshop',
			'add_new'       => 'Nieuwe workshop',
			'add_new_item'  => 'Nieuwe workshop toevoegen',
			'edit_item'     => 'Workshop bewerken',
			'menu_name'     => 'Workshops',
		),
		'public'        => true,
		'show_in_rest'  => true,
		'menu_icon'     => 'dashicons-groups',
		'menu_position' => 22,
		'has_archive'   => 'workshops',
		'rewrite'       => array( 'slug' => 'workshops', 'with_front' => false ),
		'supports'      => array( 'title', 'editor', 'thumbnail' ),
	) );

	register_post_type( 'aanbod', array(
		'label'         => 'Aanbod',
		'labels'        => array(
			'name'          => 'Aanbod',
			'singular_name' => 'Aanbod-item',
			'add_new'       => 'Nieuw aanbod-item',
			'add_new_item'  => 'Nieuw aanbod-item toevoegen',
			'edit_item'     => 'Aanbod-item bewerken',
			'menu_name'     => 'Aanbod',
		),
		'public'        => true,
		'show_in_rest'  => true,
		'menu_icon'     => 'dashicons-megaphone',
		'menu_position' => 24,
		'has_archive'   => 'aanbod',
		'rewrite'       => array( 'slug' => 'aanbod', 'with_front' => false ),
		'supports'      => array( 'title', 'editor', 'thumbnail' ),
	) );

	register_post_type( 'locatie', array(
		'label'         => 'Locaties',
		'labels'        => array(
			'name'          => 'Locaties',
			'singular_name' => 'Locatie',
			'add_new'       => 'Nieuwe locatie',
			'add_new_item'  => 'Nieuwe locatie toevoegen',
			'edit_item'     => 'Locatie bewerken',
			'menu_name'     => 'Locaties',
		),
		'public'        => true,
		'show_in_rest'  => true,
		'menu_icon'     => 'dashicons-location',
		'menu_position' => 23,
		'has_archive'   => 'locaties',
		'rewrite'       => array( 'slug' => 'locaties', 'with_front' => false ),
		'supports'      => array( 'title', 'editor', 'thumbnail' ),
		'taxonomies'    => array( 'category' ),
	) );

	// Make the built-in `category` taxonomy available on the locatie CPT (also via REST/admin UI).
	register_taxonomy_for_object_type( 'category', 'locatie' );
}
add_action( 'init', 'comedyadv_register_cpts' );

/**
 * Shows are data-only — use the Classic Editor so all meta fields are visible without scrolling.
 */
add_filter( 'use_block_editor_for_post_type', function( $use, $post_type ) {
	return ( 'show' === $post_type ) ? false : $use;
}, 10, 2 );

/**
 * Order archive queries.
 */
function comedyadv_archive_order( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( $query->is_post_type_archive( 'comedian' ) || $query->is_post_type_archive( 'workshop' ) || $query->is_post_type_archive( 'locatie' ) || $query->is_post_type_archive( 'aanbod' ) ) {
		$query->set( 'orderby', 'menu_order date' );
		$query->set( 'order', 'ASC' );
		$query->set( 'posts_per_page', 50 );
	}
}
add_action( 'pre_get_posts', 'comedyadv_archive_order' );
