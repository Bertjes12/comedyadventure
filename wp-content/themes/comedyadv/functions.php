<?php
/**
 * Comedy Adventure theme — main bootstrap.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'COMEDYADV_VERSION', '1.2.1' );
define( 'COMEDYADV_DEMO_VERSION', '1.9.0' ); // bump to trigger demo re-import on next admin load
define( 'COMEDYADV_MENU_VERSION', '2' );     // bump to rebuild the primary nav menu on next admin load
define( 'COMEDYADV_DIR', get_template_directory() );

require_once COMEDYADV_DIR . '/inc/cpt.php';
require_once COMEDYADV_DIR . '/inc/meta-boxes.php';
require_once COMEDYADV_DIR . '/inc/demo-import.php';

/**
 * Theme setup.
 */
function comedyadv_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );

	register_nav_menus( array(
		'primary' => __( 'Hoofdnavigatie', 'comedyadv' ),
		'footer'  => __( 'Footer menu', 'comedyadv' ),
	) );
}
add_action( 'after_setup_theme', 'comedyadv_setup' );

/**
 * Enqueue.
 */
function comedyadv_enqueue_assets() {
	wp_enqueue_style( 'comedyadv-fonts', 'https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700&family=Cormorant+Garamond:ital,wght@0,600;0,700;1,600;1,700&family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,600;12..96,700;12..96,800&display=swap', array(), null );
	wp_enqueue_style( 'comedyadv-styles', get_theme_file_uri( 'assets/css/styles.css' ), array( 'comedyadv-fonts' ), COMEDYADV_VERSION );
	wp_enqueue_style( 'comedyadv-style',  get_stylesheet_uri(), array( 'comedyadv-styles' ), COMEDYADV_VERSION );
	wp_enqueue_script( 'comedyadv-main',  get_theme_file_uri( 'assets/js/main.js' ), array(), COMEDYADV_VERSION, true );
	wp_localize_script( 'comedyadv-main', 'comedyadv_ajax', array(
		'url'   => admin_url( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce( 'comedyadv_contact' ),
	) );
}
add_action( 'wp_enqueue_scripts', 'comedyadv_enqueue_assets' );

function comedyadv_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'comedyadv_resource_hints', 10, 2 );

/**
 * Static-only pages we still create.
 */
function comedyadv_static_pages() {
	return array(
		array( 'slug' => 'home',    'title' => 'Home' ),
		array( 'slug' => 'agenda',  'title' => 'Agenda' ),
		array( 'slug' => 'contact', 'title' => 'Contact' ),
	);
}

/**
 * Slugs of pages from previous theme versions that conflict with CPT URLs.
 */
function comedyadv_obsolete_page_slugs() {
	return array(
		'comedians', 'workshops', 'aanbod',
		'comedy-workshop', 'roast-workshop', 'lama-workshop',
		'plat-amsterdams', 'plat-haags', 'theatersport',
	);
}

/**
 * Activation flow.
 *
 * `after_switch_theme` fires on `setup_theme` — before `init` — so the CPT
 * registration that's hooked to `init` hasn't run yet. We register them
 * manually first so the migration and import can use them right away.
 */
function comedyadv_after_switch_theme() {
	if ( function_exists( 'comedyadv_register_cpts' ) ) {
		comedyadv_register_cpts();
	}
	comedyadv_remove_obsolete_pages();
	comedyadv_migrate_cities_to_cpt(); // Defined in inc/demo-import.php.
	$created = comedyadv_create_static_pages();
	comedyadv_set_front_page( $created );
	comedyadv_create_primary_menu();
	comedyadv_import_demo_content();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'comedyadv_after_switch_theme' );

/**
 * Auto-import demo content when the demo version changes. Runs on the next admin
 * page load after a theme update. Idempotent — only adds posts whose slugs are
 * missing — so it won't disturb user-added or user-edited content.
 */
function comedyadv_maybe_import_demo() {
	if ( ! is_admin() ) {
		return;
	}
	if ( get_option( 'comedyadv_demo_version' ) === COMEDYADV_DEMO_VERSION ) {
		return;
	}
	comedyadv_remove_obsolete_pages(); // delete pages that conflict with newer CPT URLs
	comedyadv_create_static_pages();   // ensure pages exist (idempotent)
	comedyadv_import_demo_content();   // ensure CPT posts exist (idempotent)
	flush_rewrite_rules();
	update_option( 'comedyadv_demo_version', COMEDYADV_DEMO_VERSION );
}
add_action( 'admin_init', 'comedyadv_maybe_import_demo' );

/**
 * Retry pending image migrations on every admin load, regardless of demo version.
 * Cheap probe-query first; only runs the actual migration when there's work to do.
 */
function comedyadv_ensure_image_migrations() {
	if ( ! is_admin() ) {
		return;
	}

	$pending_workshops = get_posts( array(
		'post_type'      => 'workshop',
		'posts_per_page' => 1,
		'meta_key'       => '_comedyadv_image_url',
		'fields'         => 'ids',
	) );
	if ( $pending_workshops ) {
		comedyadv_migrate_workshop_images_to_featured();
	}

	$pending_locaties = get_posts( array(
		'post_type'      => 'locatie',
		'posts_per_page' => 1,
		'meta_key'       => '_comedyadv_city_image',
		'fields'         => 'ids',
	) );
	if ( $pending_locaties ) {
		comedyadv_migrate_city_images_to_featured();
	}

	$pending_aanbod = get_posts( array(
		'post_type'      => 'aanbod',
		'posts_per_page' => 1,
		'meta_key'       => '_comedyadv_image_url',
		'fields'         => 'ids',
	) );
	if ( $pending_aanbod ) {
		comedyadv_migrate_aanbod_images_to_featured();
	}
}
add_action( 'admin_init', 'comedyadv_ensure_image_migrations', 99 );

/**
 * One-time: seed locatie + placeholder show for Ouderkerk aan de Amstel.
 */
function comedyadv_seed_ouderkerk_locatie() {
	if ( get_option( 'comedyadv_ouderkerk_seeded_v2' ) ) {
		return;
	}

	// Create or find the locatie post.
	$loc = get_page_by_path( 'comedy-diner-ouderkerk-aan-de-amstel', OBJECT, 'locatie' );
	if ( ! $loc ) {
		$loc_id = wp_insert_post( array(
			'post_title'  => 'Comedy Diner Ouderkerk aan de Amstel',
			'post_name'   => 'comedy-diner-ouderkerk-aan-de-amstel',
			'post_status' => 'publish',
			'post_type'   => 'locatie',
		) );
	} else {
		$loc_id = $loc->ID;
	}
	if ( ! $loc_id || is_wp_error( $loc_id ) ) {
		return;
	}

	// Set locatie meta.
	update_post_meta( $loc_id, '_comedyadv_city_title_html', 'Comedy Diner in <span>Ouderkerk aan de Amstel</span>' );
	if ( ! get_post_meta( $loc_id, '_comedyadv_city_lead', true ) ) {
		update_post_meta( $loc_id, '_comedyadv_city_lead', 'Vul hier de intro-tekst in via Admin → Locaties.' );
	}
	if ( ! get_post_meta( $loc_id, '_comedyadv_city_occ_lead', true ) ) {
		update_post_meta( $loc_id, '_comedyadv_city_occ_lead', 'Vul hier de gelegenheden-tekst in via Admin → Locaties.' );
	}

	// Create or find a placeholder show linked to this locatie.
	$show = get_page_by_path( 'comedy-diner-ouderkerk-placeholder', OBJECT, 'show' );
	if ( ! $show ) {
		$show_id = wp_insert_post( array(
			'post_title'   => 'Comedy Diner — Ouderkerk aan de Amstel',
			'post_name'    => 'comedy-diner-ouderkerk-placeholder',
			'post_status'  => 'publish',
			'post_type'    => 'show',
			'post_content' => 'Vul hier de showbeschrijving in.',
		) );
	} else {
		$show_id = $show->ID;
	}
	if ( $show_id && ! is_wp_error( $show_id ) ) {
		update_post_meta( $show_id, '_comedyadv_show_date',     '2026-12-31' );
		update_post_meta( $show_id, '_comedyadv_show_time',     '19:00' );
		update_post_meta( $show_id, '_comedyadv_show_duration', 'Vul in' );
		update_post_meta( $show_id, '_comedyadv_show_price',    'Op aanvraag' );
		update_post_meta( $show_id, '_comedyadv_show_location', 'Ouderkerk aan de Amstel' );
		update_post_meta( $show_id, '_comedyadv_show_eyebrow',  'Live in Ouderkerk aan de Amstel' );
		update_post_meta( $show_id, '_comedyadv_show_lead',     'Vul hier de show-omschrijving in via Admin → Shows.' );
		update_post_meta( $show_id, '_comedyadv_show_city',     $loc_id );
		update_post_meta( $loc_id,  '_comedyadv_featured_show', $show_id );
	}

	update_option( 'comedyadv_ouderkerk_seeded_v2', '1' );
}
add_action( 'init', 'comedyadv_seed_ouderkerk_locatie', 20 );

/**
 * Rebuild the primary nav menu when its version changes. Bump COMEDYADV_MENU_VERSION
 * after editing the nav_items list in comedyadv_create_primary_menu().
 *
 * Note: this fully rebuilds the "Hoofdnavigatie" menu, replacing any manual edits.
 */
function comedyadv_maybe_rebuild_menu() {
	if ( ! is_admin() ) {
		return;
	}
	if ( get_option( 'comedyadv_menu_version' ) === COMEDYADV_MENU_VERSION ) {
		return;
	}
	comedyadv_create_primary_menu();
	update_option( 'comedyadv_menu_version', COMEDYADV_MENU_VERSION );
}
add_action( 'admin_init', 'comedyadv_maybe_rebuild_menu', 50 );

function comedyadv_remove_obsolete_pages() {
	foreach ( comedyadv_obsolete_page_slugs() as $slug ) {
		$page = get_page_by_path( $slug );
		if ( $page && 'page' === $page->post_type ) {
			wp_delete_post( $page->ID, true );
		}
		$nested = get_page_by_path( 'workshops/' . $slug );
		if ( $nested && 'page' === $nested->post_type ) {
			wp_delete_post( $nested->ID, true );
		}
	}
}

function comedyadv_create_static_pages() {
	$created = array();
	foreach ( comedyadv_static_pages() as $page ) {
		$existing = get_page_by_path( $page['slug'] );
		if ( $existing ) {
			$created[ $page['slug'] ] = $existing->ID;
			continue;
		}
		$id = wp_insert_post( array(
			'post_title'  => $page['title'],
			'post_name'   => $page['slug'],
			'post_status' => 'publish',
			'post_type'   => 'page',
		) );
		if ( ! is_wp_error( $id ) ) {
			$created[ $page['slug'] ] = $id;
		}
	}
	return $created;
}

function comedyadv_set_front_page( $created ) {
	$home_id = isset( $created['home'] ) ? (int) $created['home'] : 0;
	if ( ! $home_id ) {
		$home    = get_page_by_path( 'home' );
		$home_id = $home ? (int) $home->ID : 0;
	}
	if ( $home_id ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home_id );
	}
}

function comedyadv_create_primary_menu() {
	$menu_name = 'Hoofdnavigatie';
	$menu      = wp_get_nav_menu_object( $menu_name );

	if ( ! $menu ) {
		$menu_id = wp_create_nav_menu( $menu_name );
	} else {
		$menu_id = (int) $menu->term_id;
		$items   = wp_get_nav_menu_items( $menu_id );
		if ( $items ) {
			foreach ( $items as $item ) {
				wp_delete_post( $item->ID, true );
			}
		}
	}

	$nav_items = array(
		array( 'type' => 'archive', 'object' => 'comedian', 'label' => 'Comedians' ),
		array( 'type' => 'archive', 'object' => 'aanbod',   'label' => 'Aanbod' ),
		array( 'type' => 'archive', 'object' => 'workshop', 'label' => 'Workshops' ),
		array( 'type' => 'page',    'slug'   => 'agenda',   'label' => 'Agenda' ),
		array( 'type' => 'archive', 'object' => 'locatie',  'label' => 'Locaties' ),
		array( 'type' => 'page',    'slug'   => 'contact',  'label' => 'Contact' ),
	);

	foreach ( $nav_items as $item ) {
		$args = array( 'menu-item-title' => $item['label'], 'menu-item-status' => 'publish' );
		if ( 'page' === $item['type'] ) {
			$page = get_page_by_path( $item['slug'] );
			if ( ! $page ) {
				continue;
			}
			$args['menu-item-object']    = 'page';
			$args['menu-item-object-id'] = $page->ID;
			$args['menu-item-type']      = 'post_type';
		} else {
			$args['menu-item-url']  = get_post_type_archive_link( $item['object'] );
			$args['menu-item-type'] = 'custom';
		}
		wp_update_nav_menu_item( $menu_id, 0, $args );
	}

	$contact = get_page_by_path( 'contact' );
	if ( $contact ) {
		wp_update_nav_menu_item( $menu_id, 0, array(
			'menu-item-title'     => 'Boek een show',
			'menu-item-object'    => 'page',
			'menu-item-object-id' => $contact->ID,
			'menu-item-type'      => 'post_type',
			'menu-item-status'    => 'publish',
			'menu-item-classes'   => 'nav__cta-item',
		) );
	}

	$locations            = get_theme_mod( 'nav_menu_locations', array() );
	$locations['primary'] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );
}

function comedyadv_primary_nav() {
	if ( has_nav_menu( 'primary' ) ) {
		wp_nav_menu( array(
			'theme_location' => 'primary',
			'container'      => false,
			'menu_class'     => 'nav__list',
			'walker'         => new Comedyadv_Nav_Walker(),
			'fallback_cb'    => 'comedyadv_primary_nav_fallback',
		) );
		return;
	}
	comedyadv_primary_nav_fallback();
}

function comedyadv_primary_nav_fallback() {
	$current_url = is_singular() ? get_permalink() : '';

	$items = array(
		array( 'label' => 'Comedians', 'url' => get_post_type_archive_link( 'comedian' ) ),
		array( 'label' => 'Aanbod',    'url' => get_post_type_archive_link( 'aanbod' ) ),
		array( 'label' => 'Workshops', 'url' => get_post_type_archive_link( 'workshop' ) ),
		array( 'label' => 'Agenda',    'url' => comedyadv_url( 'agenda' ) ),
		array( 'label' => 'Locaties',  'url' => get_post_type_archive_link( 'locatie' ) ),
		array( 'label' => 'Contact',   'url' => comedyadv_url( 'contact' ) ),
	);

	echo '<ul class="nav__list">';
	foreach ( $items as $item ) {
		if ( ! $item['url'] ) {
			continue;
		}
		$class = 'nav__link';
		if ( $current_url && trailingslashit( $current_url ) === trailingslashit( $item['url'] ) ) {
			$class .= ' is-active';
		}
		printf( '<li><a class="%1$s" href="%2$s">%3$s</a></li>', esc_attr( $class ), esc_url( $item['url'] ), esc_html( $item['label'] ) );
	}
	printf( '<li><a class="nav__link nav__cta" href="%1$s">Boek een show</a></li>', esc_url( home_url( '/boeken/' ) ) );
	echo '</ul>';
}

class Comedyadv_Nav_Walker extends Walker_Nav_Menu {
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '<ul class="nav__sub">';
	}
	public function end_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '</ul>';
	}
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$classes  = array( 'nav__link' );
		$item_cls = empty( $item->classes ) ? array() : (array) $item->classes;
		$is_cta = in_array( 'nav__cta-item', $item_cls, true );
		if ( $is_cta ) {
			$classes[] = 'nav__cta';
		}
		if ( in_array( 'current-menu-item', $item_cls, true ) || in_array( 'current_page_item', $item_cls, true ) ) {
			$classes[] = 'is-active';
		}
		$url   = $is_cta ? home_url( '/boeken/' ) : $item->url;
		$attrs = sprintf( ' href="%s" class="%s"', esc_url( $url ), esc_attr( implode( ' ', $classes ) ) );
		$output .= '<li>';
		$output .= '<a' . $attrs . '>' . esc_html( $item->title ) . '</a>';
	}
	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		$output .= '</li>';
	}
}

/**
 * URL helper. Looks up by slug across pages, locatie CPT, and known archive shortcuts.
 */
function comedyadv_url( $slug ) {
	$page = get_page_by_path( $slug );
	if ( $page ) {
		return get_permalink( $page );
	}
	$loc = get_page_by_path( $slug, OBJECT, 'locatie' );
	if ( $loc ) {
		return get_permalink( $loc );
	}
	$archive_map = array(
		'locaties'  => 'locatie',
		'comedians' => 'comedian',
		'workshops' => 'workshop',
		'aanbod'    => 'aanbod',
	);
	if ( isset( $archive_map[ $slug ] ) ) {
		$url = get_post_type_archive_link( $archive_map[ $slug ] );
		if ( $url ) {
			return $url;
		}
	}
	return home_url( '/' . $slug . '/' );
}

function comedyadv_breadcrumbs( $crumbs ) {
	$out = array();
	foreach ( $crumbs as $crumb ) {
		if ( is_array( $crumb ) ) {
			$out[] = '<a href="' . esc_url( $crumb[1] ) . '">' . esc_html( $crumb[0] ) . '</a>';
		} else {
			$out[] = esc_html( $crumb );
		}
	}
	return implode( ' / ', $out );
}

function comedyadv_meta( $post_id, $key, $default = '' ) {
	$v = get_post_meta( $post_id, $key, true );
	return ( '' === $v || null === $v ) ? $default : $v;
}

function comedyadv_show_day( $date ) {
	$ts = strtotime( $date );
	return $ts ? gmdate( 'd', $ts ) : '';
}

function comedyadv_show_month( $date ) {
	$months = array( 1=>'Jan',2=>'Feb',3=>'Mrt',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Dec' );
	$ts = strtotime( $date );
	return $ts ? $months[ (int) gmdate( 'n', $ts ) ] : '';
}

function comedyadv_archive_url( $post_type ) {
	$url = get_post_type_archive_link( $post_type );
	return $url ? $url : home_url( '/' );
}

/**
 * Format YYYY-MM-DD as "12 mei 2026".
 */
function comedyadv_show_long_date( $date ) {
	$ts = strtotime( $date );
	if ( ! $ts ) {
		return '';
	}
	$months = array( 1=>'januari',2=>'februari',3=>'maart',4=>'april',5=>'mei',6=>'juni',7=>'juli',8=>'augustus',9=>'september',10=>'oktober',11=>'november',12=>'december' );
	return (int) gmdate( 'j', $ts ) . ' ' . $months[ (int) gmdate( 'n', $ts ) ] . ' ' . gmdate( 'Y', $ts );
}

/**
 * AJAX handler for the contact/booking form.
 * Accessible for logged-in and logged-out visitors alike.
 */
function comedyadv_handle_contact_form() {
	check_ajax_referer( 'comedyadv_contact', 'nonce' );

	$naam     = sanitize_text_field( wp_unslash( $_POST['naam']     ?? '' ) );
	$bedrijf  = sanitize_text_field( wp_unslash( $_POST['bedrijf']  ?? '' ) );
	$email    = sanitize_email(       wp_unslash( $_POST['email']    ?? '' ) );
	$telefoon = sanitize_text_field( wp_unslash( $_POST['telefoon'] ?? '' ) );
	$type     = sanitize_text_field( wp_unslash( $_POST['type']     ?? '' ) );
	$datum    = sanitize_text_field( wp_unslash( $_POST['datum']    ?? '' ) );
	$gasten   = sanitize_text_field( wp_unslash( $_POST['gasten']   ?? '' ) );
	$stad     = sanitize_text_field( wp_unslash( $_POST['stad']     ?? '' ) );
	$format   = sanitize_text_field( wp_unslash( $_POST['format']   ?? '' ) );
	$bericht  = sanitize_textarea_field( wp_unslash( $_POST['bericht'] ?? '' ) );

	if ( ! $naam || ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => 'Vul je naam en een geldig e-mailadres in.' ) );
	}

	$to      = 'info@comedyadventure.nl';
	$subject = 'Nieuwe boekingsaanvraag van ' . $naam;

	$body  = "Naam:             {$naam}\n";
	if ( $bedrijf )  $body .= "Bedrijf:          {$bedrijf}\n";
	$body .= "E-mail:           {$email}\n";
	if ( $telefoon ) $body .= "Telefoon:         {$telefoon}\n";
	if ( $type )     $body .= "Type evenement:   {$type}\n";
	if ( $datum )    $body .= "Gewenste datum:   {$datum}\n";
	if ( $gasten )   $body .= "Aantal gasten:    {$gasten}\n";
	if ( $stad )     $body .= "Stad / locatie:   {$stad}\n";
	if ( $format )   $body .= "Gewenst format:   {$format}\n";
	if ( $bericht )  $body .= "\nBericht:\n{$bericht}\n";

	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		'Reply-To: ' . $naam . ' <' . $email . '>',
	);

	$sent = wp_mail( $to, $subject, $body, $headers );

	if ( $sent ) {
		wp_send_json_success( array( 'message' => 'Bedankt! We nemen binnen 24 uur contact met je op.' ) );
	} else {
		wp_send_json_error( array( 'message' => 'Er ging iets mis. Bel ons op 020-700 94 39 of mail naar info@comedyadventure.nl.' ) );
	}
}
add_action( 'wp_ajax_comedyadv_contact',        'comedyadv_handle_contact_form' );
add_action( 'wp_ajax_nopriv_comedyadv_contact', 'comedyadv_handle_contact_form' );

/**
 * SEO: betere paginatitel voor locatie-pagina's.
 */
add_filter( 'document_title_parts', 'comedyadv_locatie_title_parts' );
function comedyadv_locatie_title_parts( $title ) {
	if ( ! is_singular( 'locatie' ) ) {
		return $title;
	}
	$title['title'] = 'Comedyshow in ' . get_the_title() . ' — Tickets & Reserveringen';
	return $title;
}

/**
 * SEO: meta description, canonical, Open Graph en Twitter Card voor locatie-pagina's.
 */
add_action( 'wp_head', 'comedyadv_locatie_seo_meta', 1 );
function comedyadv_locatie_seo_meta() {
	if ( ! is_singular( 'locatie' ) ) {
		return;
	}

	$id        = get_queried_object_id();
	$city_name = get_the_title( $id );
	$lead      = get_post_meta( $id, '_comedyadv_city_lead', true );
	$image_url = comedyadv_image_url( $id, 'large' );

	// Zoek upcoming show voor beschrijving / afbeelding
	$show_id = (int) get_post_meta( $id, '_comedyadv_featured_show', true );
	if ( ! $show_id ) {
		$auto = get_posts( array(
			'post_type'      => 'show',
			'posts_per_page' => 1,
			'meta_key'       => '_comedyadv_show_date',
			'orderby'        => 'meta_value',
			'order'          => 'ASC',
			'meta_query'     => array(
				'relation' => 'AND',
				array( 'key' => '_comedyadv_show_city', 'value' => $id ),
				array( 'key' => '_comedyadv_show_date', 'value' => gmdate( 'Y-m-d' ), 'compare' => '>=' ),
			),
		) );
		if ( $auto ) {
			$show_id = (int) $auto[0]->ID;
		}
	}

	// Beschrijving opbouwen (max 155 tekens)
	$description = $lead;
	if ( ! $description && $show_id ) {
		$description = get_post_meta( $show_id, '_comedyadv_show_lead', true );
	}
	if ( ! $description ) {
		$description = 'Beleef een onvergetelijke comedyshow in ' . $city_name . '. Reserveer je plekken via Comedy Adventure — professionele comedy op locatie.';
	}
	$description = mb_substr( wp_strip_all_tags( $description ), 0, 155 );

	// Afbeelding: locatie → show als fallback
	if ( ! $image_url && $show_id ) {
		$image_url = comedyadv_image_url( $show_id, 'large' );
	}

	$page_url  = get_permalink( $id );
	$site_name = get_bloginfo( 'name' );
	$og_title  = 'Comedyshow in ' . $city_name . ' | ' . $site_name;

	echo '<meta name="description" content="' . esc_attr( $description ) . '" />' . "\n";
	echo '<link rel="canonical" href="' . esc_url( $page_url ) . '" />' . "\n";
	echo '<meta property="og:type" content="website" />' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $og_title ) . '" />' . "\n";
	echo '<meta property="og:description" content="' . esc_attr( $description ) . '" />' . "\n";
	echo '<meta property="og:url" content="' . esc_url( $page_url ) . '" />' . "\n";
	echo '<meta property="og:site_name" content="' . esc_attr( $site_name ) . '" />' . "\n";
	echo '<meta property="og:locale" content="nl_NL" />' . "\n";
	if ( $image_url ) {
		echo '<meta property="og:image" content="' . esc_url( $image_url ) . '" />' . "\n";
		echo '<meta property="og:image:alt" content="' . esc_attr( 'Comedyshow in ' . $city_name ) . '" />' . "\n";
	}
	echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
	echo '<meta name="twitter:title" content="' . esc_attr( $og_title ) . '" />' . "\n";
	echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '" />' . "\n";
	if ( $image_url ) {
		echo '<meta name="twitter:image" content="' . esc_url( $image_url ) . '" />' . "\n";
	}
}
