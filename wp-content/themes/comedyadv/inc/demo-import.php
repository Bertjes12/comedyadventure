<?php
/**
 * Demo content importer. Runs on theme activation; idempotent.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Run the full import sequence. Locaties first, then comedians/workshops, then shows
 * (which link to locaties + comedians).
 */
function comedyadv_import_demo_content() {
	comedyadv_import_demo_locaties();
	comedyadv_migrate_city_images_to_featured();      // legacy URL meta → Featured Image
	comedyadv_assign_locaties_to_category();
	comedyadv_import_demo_comedians();
	comedyadv_import_demo_workshops();
	comedyadv_migrate_workshop_images_to_featured();  // legacy URL meta → Featured Image
	comedyadv_import_demo_aanbod();
	comedyadv_migrate_aanbod_images_to_featured();    // legacy URL meta → Featured Image
	comedyadv_import_demo_shows();
	comedyadv_link_locatie_featured_shows();
}

/**
 * Migrate aanbod posts with legacy `_comedyadv_image_url` meta to Featured Image.
 * Idempotent. Skips posts that already have a Featured Image.
 */
function comedyadv_migrate_aanbod_images_to_featured() {
	$items = get_posts( array( 'post_type' => 'aanbod', 'posts_per_page' => -1 ) );
	foreach ( $items as $item ) {
		$url = get_post_meta( $item->ID, '_comedyadv_image_url', true );
		if ( ! $url ) {
			continue;
		}
		if ( ! has_post_thumbnail( $item->ID ) ) {
			if ( ! comedyadv_sideload_thumbnail( $item->ID, $url, $item->post_title ) ) {
				continue;
			}
		}
		delete_post_meta( $item->ID, '_comedyadv_image_url' );
	}
}

/**
 * Migrate workshop posts with legacy `_comedyadv_image_url` meta to Featured Image.
 * Idempotent. Skips workshops that already have a Featured Image.
 */
function comedyadv_migrate_workshop_images_to_featured() {
	$workshops = get_posts( array( 'post_type' => 'workshop', 'posts_per_page' => -1 ) );
	foreach ( $workshops as $w ) {
		$url = get_post_meta( $w->ID, '_comedyadv_image_url', true );
		if ( ! $url ) {
			continue;
		}
		if ( ! has_post_thumbnail( $w->ID ) ) {
			if ( ! comedyadv_sideload_thumbnail( $w->ID, $url, $w->post_title ) ) {
				continue; // leave meta in place for retry
			}
		}
		delete_post_meta( $w->ID, '_comedyadv_image_url' );
	}
}

/**
 * Ensure a "Locaties" category exists and every locatie post is in it.
 * Idempotent: safe to call repeatedly.
 */
function comedyadv_assign_locaties_to_category() {
	$term = get_term_by( 'name', 'Locaties', 'category' );
	if ( ! $term ) {
		$res = wp_insert_term( 'Locaties', 'category' );
		if ( is_wp_error( $res ) ) {
			return;
		}
		$term_id = (int) $res['term_id'];
	} else {
		$term_id = (int) $term->term_id;
	}

	$locaties = get_posts( array(
		'post_type'      => 'locatie',
		'posts_per_page' => -1,
		'post_status'    => 'any',
		'fields'         => 'ids',
	) );
	foreach ( $locaties as $post_id ) {
		wp_set_object_terms( $post_id, array( $term_id ), 'category', true ); // append
	}
}

/* ----------------------------------------------------------------------------
 * COMEDIANS
 * ------------------------------------------------------------------------- */

function comedyadv_demo_comedians() {
	return array(
		array( 'slug' => 'lars-de-vries',     'title' => 'Lars de Vries',     'tag' => 'Observatiehumor',  'image' => 'https://images.unsplash.com/photo-1543610892-0b1f7e6d8ac1?w=600', 'bio' => 'Scherp, droog en altijd raak. Lars heeft een neus voor de absurditeit van het dagelijks leven en weet elke zaal moeiteloos mee te krijgen.' ),
		array( 'slug' => 'sanne-bakker',      'title' => 'Sanne Bakker',      'tag' => 'Storytelling',     'image' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=600', 'bio' => 'Verhalend, persoonlijk en hilarisch. Sanne neemt het publiek mee in haar wereld en laat ze niet meer los.' ),
		array( 'slug' => 'karim-el-amrani',   'title' => 'Karim el Amrani',   'tag' => 'Stand-up',         'image' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=600', 'bio' => 'Energiek, snel en onvoorspelbaar. Karim weet als geen ander de zaal op te warmen met zijn pure stand-up energie.' ),
		array( 'slug' => 'iris-van-der-berg', 'title' => 'Iris van der Berg', 'tag' => 'Roast specialist', 'image' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=600', 'bio' => 'Niemand roast scherper dan Iris. Persoonlijk, gedurfd en altijd met een knipoog — perfect voor jubilea en afscheidsfeesten.' ),
		array( 'slug' => 'jeroen-visser',     'title' => 'Jeroen Visser',     'tag' => 'Improvisatie',     'image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=600', 'bio' => 'Geen show is hetzelfde. Jeroen leest het publiek, pakt momenten op en bouwt razendsnel sets op basis van wat er in de zaal gebeurt.' ),
		array( 'slug' => 'anouk-smit',        'title' => 'Anouk Smit',        'tag' => 'Theatraal',        'image' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=600', 'bio' => 'Theatrale comedy met een randje cabaret. Anouk combineert muziek, karakters en scherpe observaties tot een complete avond.' ),
		array( 'slug' => 'maarten-de-boer',   'title' => 'Maarten de Boer',   'tag' => 'Bedrijfscomedy',   'image' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?w=600', 'bio' => 'Specialist in zakelijke evenementen. Maarten doet uitgebreide research en weet jouw branche moeiteloos te roasten.' ),
		array( 'slug' => 'lisa-janssen',      'title' => 'Lisa Janssen',      'tag' => 'Newcomer',         'image' => 'https://images.unsplash.com/photo-1554151228-14d9def656e4?w=600', 'bio' => 'Een van de meest opvallende nieuwe stemmen in de Nederlandse comedy. Fris, eerlijk en verrassend slim.' ),
	);
}

function comedyadv_import_demo_comedians() {
	foreach ( comedyadv_demo_comedians() as $c ) {
		$existing = get_page_by_path( $c['slug'], OBJECT, 'comedian' );
		$id = $existing ? $existing->ID : 0;
		if ( ! $id ) {
			$id = wp_insert_post( array(
				'post_title'   => $c['title'],
				'post_name'    => $c['slug'],
				'post_status'  => 'publish',
				'post_type'    => 'comedian',
				'post_content' => $c['bio'],
			) );
			if ( is_wp_error( $id ) ) {
				continue;
			}
		}
		update_post_meta( $id, '_comedyadv_tag', $c['tag'] );
		if ( ! get_post_meta( $id, '_comedyadv_image_url', true ) ) {
			update_post_meta( $id, '_comedyadv_image_url', $c['image'] );
		}
	}
}

/* ----------------------------------------------------------------------------
 * LOCATIES
 * ------------------------------------------------------------------------- */

function comedyadv_demo_locaties() {
	return array(
		array(
			'slug'      => 'amsterdam',
			'title'     => 'Amsterdam',
			'image_url' => 'https://images.unsplash.com/photo-1576924542622-772579ea2049?w=800',
			'meta'      => array(
				'_comedyadv_city_title_html' => 'Comedyshow in <span>Amsterdam</span>',
				'_comedyadv_city_lead'       => 'De hoofdstad van Nederland is ook de hoofdstad van comedy. Wij brengen onze topcomedians naar jouw locatie in en rond Amsterdam — van de grachtenpanden tot de Zuidas.',
				'_comedyadv_city_occ_lead'   => 'Onze shows passen bij elk evenement in Amsterdam — van een intieme borrel tot een grootschalig bedrijfsfeest. We werken samen met de mooiste locaties van de stad.',
			),
		),
		array(
			'slug'      => 'rotterdam',
			'title'     => 'Rotterdam',
			'image_url' => 'https://images.unsplash.com/photo-1583756165039-30b8b94b1573?w=800',
			'meta'      => array(
				'_comedyadv_city_title_html' => 'Comedyshow in <span>Rotterdam</span>',
				'_comedyadv_city_lead'       => 'Stoere stad, stoere humor. In Rotterdam mag het scherp, eerlijk en zonder omhaal — precies zoals onze comedians het brengen. Van de Erasmusbrug tot Hoek van Holland.',
				'_comedyadv_city_occ_lead'   => 'Of je nu een havenfeest, een bedrijfsdiner aan de Maas of een teamuitje in een industriële loft organiseert — wij brengen comedy die past bij Rotterdam.',
			),
		),
		array(
			'slug'      => 'utrecht',
			'title'     => 'Utrecht',
			'image_url' => 'https://images.unsplash.com/photo-1592833167001-9ee05a7defc6?w=800',
			'meta'      => array(
				'_comedyadv_city_title_html' => 'Comedyshow in <span>Utrecht</span>',
				'_comedyadv_city_lead'       => 'Centraal in het land, centraal in de comedy. Utrecht heeft een levendige cultuurscene en de perfecte locaties voor een onvergetelijke avond. Wij brengen de comedians.',
				'_comedyadv_city_occ_lead'   => 'Van werfkelder tot moderne event-locatie — wij zorgen dat de comedy past bij je publiek en gelegenheid in Utrecht en omgeving.',
			),
		),
		array(
			'slug'      => 'den-haag',
			'title'     => 'Den Haag',
			'image_url' => 'https://images.unsplash.com/photo-1597212618440-5c0fc5f4bc26?w=800',
			'meta'      => array(
				'_comedyadv_city_title_html' => 'Comedyshow in <span>Den Haag</span>',
				'_comedyadv_city_lead'       => 'Hofstad, kuststad én comedystad. Of je nu een ambassadediner organiseert, een bedrijfsfeest in het centrum of een avond aan zee — wij brengen comedy met klasse én lef.',
				'_comedyadv_city_occ_lead'   => 'Onze comedians zijn ervaren in elk soort publiek — van zakelijke gasten tot een feestelijk gemêleerd Haags publiek.',
			),
		),
		array(
			'slug'      => 'eindhoven',
			'title'     => 'Eindhoven',
			'image_url' => 'https://images.unsplash.com/photo-1582719471384-894fbb16e074?w=800',
			'meta'      => array(
				'_comedyadv_city_title_html' => 'Comedyshow in <span>Eindhoven</span>',
				'_comedyadv_city_lead'       => 'Brainport, design én Bourgondisch. Eindhoven combineert innovatie met gastvrijheid — perfect voor een avond comedy die zowel scherp als hartelijk is.',
				'_comedyadv_city_occ_lead'   => 'Van techbedrijven op de High Tech Campus tot familiefeesten in een Bossche stadsbrouwerij — wij brengen comedy die past.',
			),
		),
		array(
			'slug'      => 'groningen',
			'title'     => 'Groningen',
			'image_url' => 'https://images.unsplash.com/photo-1565008447742-97f6f38c985c?w=800',
			'meta'      => array(
				'_comedyadv_city_title_html' => 'Comedyshow in <span>Groningen</span>',
				'_comedyadv_city_lead'       => 'Stad én ommeland. Groningen heeft een eigenzinnige cultuur en een publiek dat houdt van scherpe humor. Wij brengen onze comedians met plezier naar het noorden.',
				'_comedyadv_city_occ_lead'   => 'Van studentenfeesten tot zakelijke evenementen — onze comedians stemmen hun show af op het Groningse publiek.',
			),
		),
		// Dummy locaties (extra steden zonder gekoppelde shows).
		array(
			'slug'      => 'maastricht',
			'title'     => 'Maastricht',
			'image_url' => 'https://picsum.photos/seed/maastricht/800/1000',
			'meta'      => array(
				'_comedyadv_city_title_html' => 'Comedyshow in <span>Maastricht</span>',
				'_comedyadv_city_lead'       => 'Bourgondisch, internationaal en altijd in voor lol. Maastricht is de zuidelijkste comedy-stop op onze tour — met een publiek dat humor weet te waarderen onder een goed glas wijn.',
				'_comedyadv_city_occ_lead'   => 'Van studenteninitiaties tot zakelijke conferenties — onze comedians voelen zich thuis in elk Maastrichts gezelschap.',
			),
		),
		array(
			'slug'      => 'leeuwarden',
			'title'     => 'Leeuwarden',
			'image_url' => 'https://picsum.photos/seed/leeuwarden/800/1000',
			'meta'      => array(
				'_comedyadv_city_title_html' => 'Comedyshow in <span>Leeuwarden</span>',
				'_comedyadv_city_lead'       => 'Friese nuchterheid ontmoet Nederlandse comedy. In Leeuwarden lacht men droog en hard — precies zoals onze comedians het brengen.',
				'_comedyadv_city_occ_lead'   => 'Van bedrijfsuitjes in de Friese natuur tot grote zalen in het centrum — wij brengen lachsalvo\'s naar het noorden.',
			),
		),
		array(
			'slug'      => 'nijmegen',
			'title'     => 'Nijmegen',
			'image_url' => 'https://picsum.photos/seed/nijmegen/800/1000',
			'meta'      => array(
				'_comedyadv_city_title_html' => 'Comedyshow in <span>Nijmegen</span>',
				'_comedyadv_city_lead'       => 'Oudste stad van Nederland, jongste publiek van Comedy Adventure. Nijmegen heeft een levendige studentenscene en evenveel zin in een avond pure stand-up.',
				'_comedyadv_city_occ_lead'   => 'Van studie-feesten op de Heyendaal-campus tot bedrijfsavonden aan de Waal — onze comedians passen zich moeiteloos aan.',
			),
		),
		array(
			'slug'      => 'tilburg',
			'title'     => 'Tilburg',
			'image_url' => 'https://picsum.photos/seed/tilburg/800/1000',
			'meta'      => array(
				'_comedyadv_city_title_html' => 'Comedyshow in <span>Tilburg</span>',
				'_comedyadv_city_lead'       => 'Brabantse gastvrijheid, Brabantse humor. In Tilburg mag het direct, warm en vooral met veel gezelligheid — onze comedians voelen zich er als een vis in het water.',
				'_comedyadv_city_occ_lead'   => 'Van textielindustrie-jubilea tot moderne festival-edities op Hall of Fame — wij brengen comedy die past bij de Tilburgse vibe.',
			),
		),
		array(
			'slug'      => 'haarlem',
			'title'     => 'Haarlem',
			'image_url' => 'https://picsum.photos/seed/haarlem/800/1000',
			'meta'      => array(
				'_comedyadv_city_title_html' => 'Comedyshow in <span>Haarlem</span>',
				'_comedyadv_city_lead'       => 'Cultuurstad met een dorps karakter. Haarlem combineert intieme zalen met een beschaafd-maar-eigenzinnig publiek — perfect voor onze beste storytellers.',
				'_comedyadv_city_occ_lead'   => 'Van bedrijfsdiners aan de Grote Markt tot besloten feesten op locatie — wij brengen comedy die past bij Haarlems goede smaak.',
			),
		),
		array(
			'slug'      => 'comedy-diner-ouderkerk-aan-de-amstel',
			'title'     => 'Comedy Diner Ouderkerk aan de Amstel',
			'image_url' => 'https://picsum.photos/seed/ouderkerk/800/1000',
			'meta'      => array(
				'_comedyadv_city_title_html' => 'Comedy Diner in <span>Ouderkerk aan de Amstel</span>',
				'_comedyadv_city_lead'       => 'Vul hier de intro in via Admin → Locaties → Comedy Diner Ouderkerk aan de Amstel.',
				'_comedyadv_city_occ_lead'   => 'Vul hier de gelegenheden-tekst in via Admin → Locaties → Comedy Diner Ouderkerk aan de Amstel.',
			),
		),
	);
}

function comedyadv_import_demo_locaties() {
	foreach ( comedyadv_demo_locaties() as $loc ) {
		$existing = get_page_by_path( $loc['slug'], OBJECT, 'locatie' );
		$id       = $existing ? $existing->ID : 0;
		if ( ! $id ) {
			$id = wp_insert_post( array(
				'post_title'  => $loc['title'],
				'post_name'   => $loc['slug'],
				'post_status' => 'publish',
				'post_type'   => 'locatie',
			) );
			if ( is_wp_error( $id ) ) {
				continue;
			}
		}
		// Seed meta only if not already set (don't overwrite user edits).
		foreach ( $loc['meta'] as $k => $v ) {
			if ( ! get_post_meta( $id, $k, true ) ) {
				update_post_meta( $id, $k, $v );
			}
		}
		// Sideload the demo image as Featured Image, only when none is set yet.
		if ( ! has_post_thumbnail( $id ) && ! empty( $loc['image_url'] ) ) {
			comedyadv_sideload_thumbnail( $id, $loc['image_url'], $loc['title'] );
		}
	}
}

/**
 * Migrate existing locaties with the legacy `_comedyadv_city_image` meta to a
 * proper Featured Image. Idempotent: skips locaties that already have a thumbnail.
 */
function comedyadv_migrate_city_images_to_featured() {
	$locaties = get_posts( array( 'post_type' => 'locatie', 'posts_per_page' => -1 ) );
	foreach ( $locaties as $loc ) {
		$url = get_post_meta( $loc->ID, '_comedyadv_city_image', true );
		if ( ! $url ) {
			continue;
		}
		if ( ! has_post_thumbnail( $loc->ID ) ) {
			$ok = comedyadv_sideload_thumbnail( $loc->ID, $url, $loc->post_title );
			if ( ! $ok ) {
				continue; // leave the meta in place so we can retry next time
			}
		}
		delete_post_meta( $loc->ID, '_comedyadv_city_image' );
	}
}

/**
 * Download an external image, store it as an attachment on $post_id, and set it as Featured.
 * Returns true on success, false on any failure. Logs failures via error_log so
 * issues are visible in Local's "Tools → Logs" or wp-content/debug.log.
 */
function comedyadv_sideload_thumbnail( $post_id, $url, $desc = '' ) {
	if ( ! function_exists( 'media_sideload_image' ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
	}
	$att_id = media_sideload_image( $url, $post_id, $desc ?: null, 'id' );
	if ( is_wp_error( $att_id ) ) {
		error_log( sprintf( '[comedyadv] sideload failed for post %d (%s): %s', $post_id, $url, $att_id->get_error_message() ) );
		return false;
	}
	if ( ! $att_id ) {
		error_log( sprintf( '[comedyadv] sideload returned empty for post %d (%s)', $post_id, $url ) );
		return false;
	}
	set_post_thumbnail( $post_id, (int) $att_id );
	return true;
}

/* ----------------------------------------------------------------------------
 * WORKSHOPS
 * ------------------------------------------------------------------------- */

function comedyadv_demo_workshops() {
	return array(
		array(
			'slug'  => 'comedy-workshop',
			'title' => 'Comedy Workshop',
			'meta'  => array(
				'_comedyadv_breadcrumb' => 'Comedy Workshop',
				'_comedyadv_title_html' => 'Comedy <span>Workshop</span>',
				'_comedyadv_lead'       => 'De ultieme workshop voor wie altijd al eens stand-up wilde proberen. Van het eerste idee tot de eerste lach.',
				'_comedyadv_image_url'  => 'https://images.unsplash.com/photo-1525026198548-4baa812f1183?w=900',
				'_comedyadv_eyebrow1'   => 'Over deze workshop',
				'_comedyadv_h2_1'       => 'Sta zelf op het podium',
				'_comedyadv_price'      => '€595,-',
				'_comedyadv_price_sub'  => 'excl. BTW • basis tarief',
				'_comedyadv_specs'      => array(
					array( 'label' => 'Tijdsduur',         'value' => '2 uur' ),
					array( 'label' => 'Prijs per persoon', 'value' => '€30,-' ),
					array( 'label' => 'Groepsgrootte',     'value' => '8 – 25 personen' ),
					array( 'label' => 'Locatie',           'value' => 'Op jouw locatie' ),
					array( 'label' => 'Begeleiding',       'value' => '1 ervaren comedian' ),
					array( 'label' => 'Inclusief',         'value' => 'Mini-show afsluiting' ),
				),
				'_comedyadv_eyebrow2'   => 'Wat leer je?',
				'_comedyadv_h2_2'       => 'Het volledige stand-up proces',
				'_comedyadv_pills'      => array( 'Grappen schrijven', 'Set opbouwen', 'Timing & ritme', 'Podiumpresentatie', 'Omgaan met publiek', 'Eigen stem vinden' ),
				'_comedyadv_closing'    => 'Onze comedian past de inhoud aan op de groep. Of je nu een team developers, een familie of een groep vrienden bent — de oefeningen werken voor iedereen.',
				'_comedyadv_cta_h2'     => 'Klaar om je team het podium op te zetten?',
			),
			'content' => "In deze interactieve workshop leer je in twee uur tijd hoe je grappen schrijft, een set opbouwt en met zelfvertrouwen op het podium staat. Onze ervaren comedian neemt je mee door de basisprincipes van stand-up: timing, opbouw, observatie en de \"act-out\".\n\nJe gaat zelf aan de slag: korte schrijfopdrachten worden afgewisseld met praktische podiumoefeningen. Aan het eind van de workshop staat iedereen kort op het podium met een eigen mini-set — voor je collega's, vrienden of teamgenoten. Lachen gegarandeerd, en je leert je team van een hele andere kant kennen.\n\nPerfect voor teambuilding, personeelsuitjes, vrijgezellenfeesten of als verrassend onderdeel van een bedrijfsdag. Geen ervaring nodig — humor zit in iedereen.",
		),
		array(
			'slug'  => 'roast-workshop',
			'title' => 'Roast Workshop',
			'meta'  => array(
				'_comedyadv_breadcrumb' => 'Roast Workshop',
				'_comedyadv_title_html' => 'Roast <span>Workshop</span>',
				'_comedyadv_lead'       => 'Scherp, eerlijk, liefdevol — leer de kunst van het roasten en geef een collega, vriend of familielid een onvergetelijk afscheid, jubileum of verjaardag.',
				'_comedyadv_image_url'  => 'https://comedyadventure.nl/wp-content/uploads/2025/07/Roast-Workshop.png',
				'_comedyadv_eyebrow1'   => 'Over deze workshop',
				'_comedyadv_h2_1'       => 'De kunst van het liefdevol roasten',
				'_comedyadv_price'      => '€650,-',
				'_comedyadv_price_sub'  => 'excl. BTW • basis tarief',
				'_comedyadv_specs'      => array(
					array( 'label' => 'Tijdsduur',         'value' => '2 uur' ),
					array( 'label' => 'Prijs per persoon', 'value' => '€35,-' ),
					array( 'label' => 'Groepsgrootte',     'value' => '6 – 20 personen' ),
					array( 'label' => 'Locatie',           'value' => 'Op jouw locatie' ),
					array( 'label' => 'Begeleiding',       'value' => '1 ervaren roaster' ),
					array( 'label' => 'Inclusief',         'value' => 'Live roast-finale' ),
				),
				'_comedyadv_eyebrow2'   => 'Wat leer je?',
				'_comedyadv_h2_2'       => 'Roasten als een professional',
				'_comedyadv_pills'      => array( 'Observaties verzamelen', 'Roast-structuur', 'Liefdevol scherp zijn', 'Timing & opbouw', 'Podiumpresentatie', 'Reageren op publiek' ),
				'_comedyadv_closing'    => 'Speciaal geschikt voor afscheid, pensioen, een mijlpaal of een feest waarbij iemand centraal staat. Wij zorgen dat de scherpte altijd in dienst staat van de liefde voor de persoon.',
				'_comedyadv_cta_h2'     => 'Klaar om iemand liefdevol te roasten?',
			),
			'content' => "Roasten is meer dan iemand belachelijk maken. Het is een kunst: een persoon door de mangel halen op een manier waardoor diegene aan het einde van de avond zelf het hardst lacht. In deze workshop leer je hoe.\n\nOnder begeleiding van een ervaren comedian leer je hoe je observaties verzamelt, wat goede roast-grappen onderscheidt van flauwe sneren, hoe je structuur aanbrengt en hoe je het podium pakt. Je werkt aan een korte roast over een collega — die je vervolgens live presenteert tijdens de afsluiting.\n\nPerfect voor afscheidsfeesten, jubilea, verjaardagen of als gedurfd onderdeel van een bedrijfsuitje. Spannend, hilarisch en gegarandeerd memorabel.",
		),
		array(
			'slug'  => 'lama-workshop',
			'title' => 'Lama Workshop',
			'meta'  => array(
				'_comedyadv_breadcrumb' => 'Lama Workshop',
				'_comedyadv_title_html' => 'Lama <span>Workshop</span>',
				'_comedyadv_lead'       => 'De wereldberoemde "La la la" — improviseer in koppels een hilarisch lied opgebouwd uit niets meer dan luisteren, durven en lachen.',
				'_comedyadv_image_url'  => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=900',
				'_comedyadv_eyebrow1'   => 'Over deze workshop',
				'_comedyadv_h2_1'       => 'Geen tekst, geen plan, alleen lala',
				'_comedyadv_price'      => '€495,-',
				'_comedyadv_price_sub'  => 'excl. BTW • basis tarief',
				'_comedyadv_specs'      => array(
					array( 'label' => 'Tijdsduur',         'value' => '1,5 uur' ),
					array( 'label' => 'Prijs per persoon', 'value' => '€25,-' ),
					array( 'label' => 'Groepsgrootte',     'value' => '10 – 30 personen' ),
					array( 'label' => 'Locatie',           'value' => 'Op jouw locatie' ),
					array( 'label' => 'Begeleiding',       'value' => '1 comedian + muziek' ),
					array( 'label' => 'Voorkennis',        'value' => 'Geen vereist' ),
				),
				'_comedyadv_eyebrow2'   => 'Wat ga je doen?',
				'_comedyadv_h2_2'       => 'Improviseren, luisteren, lachen',
				'_comedyadv_pills'      => array( 'Muzikale improvisatie', "Samenwerken in duo's", 'Luistervaardigheid', 'Moment durven inzetten', 'Publiek meekrijgen', 'Loslaten' ),
				'_comedyadv_closing'    => 'Alle deelnemers krijgen de kans om zelf op het podium te staan. Onze comedian zorgt voor begeleidende muziek, structuur en heel veel humor onderweg.',
				'_comedyadv_cta_h2'     => 'Klaar voor de Lama?',
			),
			'content' => "Bekend uit De Lama's: in deze workshop staan deelnemers in koppels op het podium en improviseren een compleet lied — gebaseerd op een door het publiek aangereikt thema en met als enig hulpmiddel de woordjes \"la la la\". Het levert hilarische, ontroerende en soms verbluffende muzikale momenten op.\n\nOnder begeleiding van een ervaren comedian leer je luisteren, samenwerken op het podium, melodieën opbouwen en moment voor moment durven inzetten. Het mooie: niemand hoeft kunnen zingen. De lol zit in de durf.\n\nIdeaal als energieke teambuilding, ijsbreker bij events of feestelijke afsluiter. Een workshop die je collega's nooit meer vergeten.",
		),
		array(
			'slug'  => 'plat-amsterdams',
			'title' => 'Workshop Plat Amsterdams',
			'meta'  => array(
				'_comedyadv_breadcrumb' => 'Plat Amsterdams',
				'_comedyadv_title_html' => 'Workshop Plat <span>Amsterdams</span>',
				'_comedyadv_lead'       => 'Mokum, kakkerlak en kaaskoppen — leer praten als een echte Amsterdammer en ontdek de rijke tongval van de hoofdstad.',
				'_comedyadv_image_url'  => 'https://comedyadventure.nl/wp-content/uploads/2025/07/Workshop-Plat-Amsterdams.png',
				'_comedyadv_eyebrow1'   => 'Over deze workshop',
				'_comedyadv_h2_1'       => 'Een tongval met geschiedenis',
				'_comedyadv_price'      => '€495,-',
				'_comedyadv_price_sub'  => 'excl. BTW • basis tarief',
				'_comedyadv_specs'      => array(
					array( 'label' => 'Tijdsduur',         'value' => '1,5 uur' ),
					array( 'label' => 'Prijs per persoon', 'value' => '€25,-' ),
					array( 'label' => 'Groepsgrootte',     'value' => '8 – 25 personen' ),
					array( 'label' => 'Locatie',           'value' => 'Op jouw locatie' ),
					array( 'label' => 'Begeleiding',       'value' => 'Mokumse comedian' ),
					array( 'label' => 'Talen',             'value' => 'NL & expats welkom' ),
				),
				'_comedyadv_eyebrow2'   => 'Wat leer je?',
				'_comedyadv_h2_2'       => 'De échte taal van Mokum',
				'_comedyadv_pills'      => array( 'Uitspraak & klinkers', 'Mokums jargon', 'Klassieke uitdrukkingen', 'Amsterdamse one-liners', 'Attitude & lichaamstaal', 'Eindsketch' ),
				'_comedyadv_closing'    => 'Onze comedian past de inhoud aan op de groep — of je nu een born-and-bred Amsterdammer bent of expat die net is verhuisd, je leert iets nieuws.',
				'_comedyadv_cta_h2'     => 'Mokums leren in 90 minuten?',
			),
			'content' => "Plat Amsterdams (of Mokums) is meer dan een accent. Het is een attitude, een muziekje, een eigen woordenschat met Jiddische, Hollandse en straat-elementen. In deze workshop neemt onze in Mokum geboren comedian je mee in de échte taal van Amsterdam.\n\nJe leert de uitspraak (de befaamde \"harde G\" en de smelting van klinkers), het jargon (\"mazzel\", \"tof\", \"schorem\"), en hoe je een typisch Mokumse one-liner opbouwt. Je oefent met klassieke uitdrukkingen, doet rollenspelen en sluit af met een korte sketch waarin je je nieuwe Amsterdam-skills inzet.\n\nGeweldig voor bedrijven die in Amsterdam zitten, voor expats die de stad beter willen begrijpen, of als ludieke teambuilding voor iedereen die ooit op de Wallen heeft gestaan.",
		),
		array(
			'slug'  => 'plat-haags',
			'title' => 'Workshop Plat Haags',
			'meta'  => array(
				'_comedyadv_breadcrumb' => 'Plat Haags',
				'_comedyadv_title_html' => 'Workshop Plat <span>Haags</span>',
				'_comedyadv_lead'       => 'Hagenees of Hagenaar? Leer het verschil — én leer praten met de droge, lijzige humor van de Hofstad.',
				'_comedyadv_image_url'  => 'https://comedyadventure.nl/wp-content/uploads/2025/06/Workshop-Plat-Haags.jpg',
				'_comedyadv_eyebrow1'   => 'Over deze workshop',
				'_comedyadv_h2_1'       => 'Praat als een echte Hagenees',
				'_comedyadv_price'      => '€495,-',
				'_comedyadv_price_sub'  => 'excl. BTW • basis tarief',
				'_comedyadv_specs'      => array(
					array( 'label' => 'Tijdsduur',         'value' => '1,5 uur' ),
					array( 'label' => 'Prijs per persoon', 'value' => '€25,-' ),
					array( 'label' => 'Groepsgrootte',     'value' => '8 – 25 personen' ),
					array( 'label' => 'Locatie',           'value' => 'Op jouw locatie' ),
					array( 'label' => 'Begeleiding',       'value' => 'Echte Hagenees' ),
					array( 'label' => 'Talen',             'value' => 'NL & expats welkom' ),
				),
				'_comedyadv_eyebrow2'   => 'Wat leer je?',
				'_comedyadv_h2_2'       => 'De Hofstad-tongval',
				'_comedyadv_pills'      => array( 'Uitspraak & klinkerrek', 'Haags jargon', 'Hagenees vs. Hagenaar', 'Droge one-liners', 'Lichaamstaal', 'One-liner-battle' ),
				'_comedyadv_closing'    => 'Of je nu opgegroeid bent in Loosduinen of net verhuisd uit Stockholm — onze comedian zorgt dat iedereen de essentie meeneemt.',
				'_comedyadv_cta_h2'     => 'Hagenees worden in 90 minuten?',
			),
			'content' => "Den Haag heeft twee taalwerelden: het keurige Haags van de Hagenaar en het volkse Plat Haags van de Hagenees. In deze workshop duiken we in de tweede — de taal van Schilderswijk, Transvaal en het Zuiderpark. Lijzig, droog en heerlijk eigenwijs.\n\nOnder leiding van een echte Hagenees leer je de uitspraak (de uitgerekte \"ai\" en de korte \"ou\"), het jargon (\"hatsekiedee\", \"ouwehoere\", \"knaibel\"), en de befaamde Haagse droge humor. Je oefent in groepjes, doet kleine sketches en sluit af met een typisch Haagse one-liner-battle.\n\nPerfect voor Haagse bedrijven, expats die kennismaken met de stad, of als ludieke teambuilding waarbij iedereen wel een paar nieuwe woorden mee naar huis neemt.",
		),
		array(
			'slug'  => 'theatersport',
			'title' => 'Workshop Theatersport',
			'meta'  => array(
				'_comedyadv_breadcrumb' => 'Theatersport',
				'_comedyadv_title_html' => 'Workshop <span>Theatersport</span>',
				'_comedyadv_lead'       => 'Improviseren in teams, scènes opbouwen op basis van publieksinput en spelen volgens de gouden regel: ja, en…',
				'_comedyadv_image_url'  => 'https://images.unsplash.com/photo-1503095396549-807759245b35?w=900',
				'_comedyadv_eyebrow1'   => 'Over deze workshop',
				'_comedyadv_h2_1'       => 'De kunst van het samen improviseren',
				'_comedyadv_price'      => '€595,-',
				'_comedyadv_price_sub'  => 'excl. BTW • basis tarief',
				'_comedyadv_specs'      => array(
					array( 'label' => 'Tijdsduur',         'value' => '2 uur' ),
					array( 'label' => 'Prijs per persoon', 'value' => '€30,-' ),
					array( 'label' => 'Groepsgrootte',     'value' => '8 – 20 personen' ),
					array( 'label' => 'Locatie',           'value' => 'Op jouw locatie' ),
					array( 'label' => 'Begeleiding',       'value' => 'Improvisator' ),
					array( 'label' => 'Inclusief',         'value' => 'Mini-toernooi' ),
				),
				'_comedyadv_eyebrow2'   => 'Wat leer je?',
				'_comedyadv_h2_2'       => 'Improviseren als team',
				'_comedyadv_pills'      => array( 'Ja, en…', 'Luisteren onder druk', 'Scène opbouwen', 'Samenwerken', 'Risico durven nemen', 'Storytelling' ),
				'_comedyadv_closing'    => "Wereldwijd ingezet door bedrijven als leiderschapstraining — maar boven alles: gegarandeerd lachen om je collega's én jezelf.",
				'_comedyadv_cta_h2'     => 'Klaar voor een improvisatie-toernooi?',
			),
			'content' => "Theatersport is improvisatietheater in wedstrijdvorm. Twee teams spelen scènes op basis van suggesties uit het publiek — sneller, gekker en spannender dan je voor mogelijk houdt. In deze workshop leer je de basis: luisteren, accepteren, voortbouwen en samen verhalen creëren uit het niets.\n\nOnder begeleiding van een ervaren improvisator oefen je met klassieke spellen, leer je de regels van het impro-spel en sluit je af met een mini-toernooi waarin teams het tegen elkaar opnemen. De workshop is energiek, fysiek en bouwt razendsnel teamgevoel op.\n\nTheatersport wordt wereldwijd ingezet voor leiderschapstrainingen omdat het traint waar elk team beter in wil worden: luisteren, vertrouwen geven, samenwerken onder druk en risico durven nemen. Hilarisch én leerzaam.",
		),
	);
}

function comedyadv_import_demo_workshops() {
	foreach ( comedyadv_demo_workshops() as $w ) {
		$existing = get_page_by_path( $w['slug'], OBJECT, 'workshop' );
		$id = $existing ? $existing->ID : 0;
		if ( ! $id ) {
			$id = wp_insert_post( array(
				'post_title'   => $w['title'],
				'post_name'    => $w['slug'],
				'post_status'  => 'publish',
				'post_type'    => 'workshop',
				'post_content' => $w['content'],
			) );
			if ( is_wp_error( $id ) ) {
				continue;
			}
		}
		foreach ( $w['meta'] as $k => $v ) {
			if ( ! get_post_meta( $id, $k, true ) ) {
				update_post_meta( $id, $k, $v );
			}
		}
	}
}

/* ----------------------------------------------------------------------------
 * AANBOD
 * ------------------------------------------------------------------------- */

function comedyadv_demo_aanbod() {
	return array(
		array(
			'slug'         => 'comedy-dinershow',
			'title'        => 'Comedy Dinershow',
			'image'        => 'https://comedyadventure.nl/wp-content/uploads/2025/10/comedy-dinershow.png',
			'lead'         => 'De complete avond uit met een verzorgd 3-gangen diner en korte sets van vier Nederlandse topcomedians tussen de gangen door.',
			'duration'     => '3,5 uur',
			'price_pp'     => '€49,50',
			'min_personen' => '30',
			'content'      => "Tussen de gangen door brengen onze comedians korte, scherpe sets — afgewisseld met een culinair driegangen-menu en goedgekozen drankjes. Perfect voor bedrijfsfeesten, jubilea of een avondje uit met de hele afdeling.\n\nWij regelen alles: comedians, geluid, licht en de afstemming met de cateraar. Jij hoeft alleen maar te genieten.",
		),
		array(
			'slug'         => 'comedy-show',
			'title'        => 'Comedy Show',
			'image'        => 'https://comedyadventure.nl/wp-content/uploads/2025/08/banner1.png',
			'lead'         => 'Een avondvullende stand-up show met meerdere comedians — pure comedy zonder diner.',
			'duration'     => '1 uur',
			'price_pp'     => '€25,-',
			'min_personen' => '50',
			'content'      => "Klassieke stand-up: drie tot vier comedians die elk hun eigen set brengen, gevolgd door een pauze en een tweede helft. Geen diner, geen poespas — gewoon comedy in de meest pure vorm.\n\nIdeaal voor borrels, bedrijfsfeesten met externe catering, of als programma-onderdeel binnen een groter event.",
		),
		array(
			'slug'         => 'cabaret-diner',
			'title'        => 'Cabaret Diner',
			'image'        => 'https://comedyadventure.nl/wp-content/uploads/2025/10/comedy-dinershow.png',
			'lead'         => 'Diner gecombineerd met een cabaret-stijl programma — meer theatraal, met muziek en karakters.',
			'duration'     => '2,5 uur',
			'price_pp'     => '€55,-',
			'min_personen' => '25',
			'content'      => "Een wat ingetogener formaat dan onze Dinershow: in plaats van losse stand-up sets brengen onze cabaretiers een doorlopend programma met muziek, karakters en theatrale elementen. Een avond met meer ruimte voor sfeer en gesprek.\n\nGeschikt voor bedrijfsdiners, jubilea en gelegenheden waarbij gasten elkaar moeten kunnen verstaan tussen de comedy door.",
		),
		array(
			'slug'         => 'comedy-roast',
			'title'        => 'Comedy Roast',
			'image'        => 'https://comedyadventure.nl/wp-content/uploads/2025/10/Comedy-Roast.png',
			'lead'         => 'Een hilarische, persoonlijke roast op maat — perfect voor jubilea, afscheidsfeesten of een verjaardag met humor.',
			'duration'     => '30 minuten',
			'price_pp'     => 'op aanvraag',
			'min_personen' => '20',
			'content'      => "Onze comedian bereidt zich uitgebreid voor met een briefing en research om de hoofdpersoon (of het bedrijf) liefdevol te roasten. We zorgen dat de scherpte altijd in dienst staat van de liefde voor de persoon.\n\nIdeaal voor jubilea, afscheidsfeesten, pensioen, of een verjaardag waarbij iemand centraal mag staan.",
		),
		array(
			'slug'         => 'comedy-op-maat',
			'title'        => 'Comedy op Maat',
			'image'        => 'https://comedyadventure.nl/wp-content/uploads/2025/07/Op-Maat-Comedy-Show.png',
			'lead'         => 'Een volledig op jouw bedrijf of evenement afgestemde show — branding, content en props inclusief.',
			'duration'     => '30 minuten',
			'price_pp'     => 'op aanvraag',
			'min_personen' => '30',
			'content'      => "Wanneer een standaardshow niet genoeg is. Van briefing tot inhoud, van branding tot props — alles wordt afgestemd op jouw doelgroep en gelegenheid. Onze comedian doet uitgebreide research en stemt de set af op jouw publiek en branche.\n\nVraag een offerte aan en wij komen met een voorstel op maat.",
		),
	);
}

function comedyadv_import_demo_aanbod() {
	foreach ( comedyadv_demo_aanbod() as $a ) {
		$existing = get_page_by_path( $a['slug'], OBJECT, 'aanbod' );
		$id = $existing ? $existing->ID : 0;
		if ( ! $id ) {
			$id = wp_insert_post( array(
				'post_title'   => $a['title'],
				'post_name'    => $a['slug'],
				'post_status'  => 'publish',
				'post_type'    => 'aanbod',
				'post_content' => $a['content'],
			) );
			if ( is_wp_error( $id ) ) {
				continue;
			}
		}
		// Seed meta only if not already set (don't overwrite user edits).
		$meta = array(
			'_comedyadv_image_url'             => $a['image'],
			'_comedyadv_aanbod_lead'           => $a['lead'],
			'_comedyadv_aanbod_duration'       => $a['duration'],
			'_comedyadv_aanbod_price_pp'       => $a['price_pp'],
			'_comedyadv_aanbod_min_personen'   => $a['min_personen'],
		);
		foreach ( $meta as $k => $v ) {
			if ( ! get_post_meta( $id, $k, true ) ) {
				update_post_meta( $id, $k, $v );
			}
		}
	}
}

/* ----------------------------------------------------------------------------
 * SHOWS
 * ------------------------------------------------------------------------- */

function comedyadv_demo_shows() {
	return array(
		// Past shows.
		array(
			'slug'  => 'borrelshow-amsterdam-april',
			'title' => 'Borrelshow — Amsterdam',
			'date'  => '2026-04-12',
			'time'  => '17:00',
			'meta'  => array(
				'_comedyadv_show_duration' => '1,5 uur',
				'_comedyadv_show_price'    => '€18,-',
				'_comedyadv_show_location' => 'Bar Bukowski, Amsterdam',
				'_comedyadv_show_eyebrow'  => 'Live in Amsterdam',
				'_comedyadv_show_lead'     => 'Een ontspannen borrel-editie met drie comedians. De perfecte zaterdagmiddag.',
				'_comedyadv_image_url'     => 'https://comedyadventure.nl/wp-content/uploads/2025/08/banner1.png',
			),
			'city_slug'      => 'amsterdam',
			'comedian_slugs' => array( 'iris-van-der-berg', 'maarten-de-boer' ),
			'content'        => 'Een ontspannen borrel-editie met drie comedians.',
		),
		array(
			'slug'  => 'comedy-roast-erasmus',
			'title' => 'Comedy Roast — Erasmus Editie',
			'date'  => '2026-03-15',
			'time'  => '20:00',
			'meta'  => array(
				'_comedyadv_show_duration' => '2 uur',
				'_comedyadv_show_price'    => '€20,-',
				'_comedyadv_show_location' => 'BlueCity, Rotterdam',
				'_comedyadv_show_eyebrow'  => 'Live in Rotterdam',
				'_comedyadv_show_lead'     => 'Iris en Lars roasten de stad Rotterdam in een speciaal voor de Erasmus geschreven editie.',
				'_comedyadv_image_url'     => 'https://comedyadventure.nl/wp-content/uploads/2025/10/Comedy-Roast.png',
			),
			'city_slug'      => 'rotterdam',
			'comedian_slugs' => array( 'iris-van-der-berg', 'lars-de-vries' ),
			'content'        => 'Iris en Lars roasten de stad Rotterdam.',
		),
		array(
			'slug'  => 'stand-up-sundays-utrecht',
			'title' => 'Stand-Up Sundays',
			'date'  => '2026-02-08',
			'time'  => '15:00',
			'meta'  => array(
				'_comedyadv_show_duration' => '2 uur',
				'_comedyadv_show_price'    => '€16,-',
				'_comedyadv_show_location' => 'EKKO, Utrecht',
				'_comedyadv_show_eyebrow'  => 'Live in Utrecht',
				'_comedyadv_show_lead'     => 'Vier comedians, een zondagmiddag, een goedgevulde zaal — de wekelijkse traditie van Stand-Up Sundays.',
			),
			'city_slug'      => 'utrecht',
			'comedian_slugs' => array( 'jeroen-visser', 'lisa-janssen' ),
			'content'        => 'De wekelijkse zondagmiddag-traditie van Stand-Up Sundays.',
		),
		array(
			'slug'  => 'eindejaarsspecial-den-haag',
			'title' => 'Eindejaarsspecial — Den Haag',
			'date'  => '2025-12-21',
			'time'  => '20:30',
			'meta'  => array(
				'_comedyadv_show_duration' => '2,5 uur',
				'_comedyadv_show_price'    => '€32,50',
				'_comedyadv_show_location' => 'Paard, Den Haag',
				'_comedyadv_show_eyebrow'  => 'Live in Den Haag',
				'_comedyadv_show_lead'     => 'Het jaar uitluiden zoals het hoort — met een avond vol topcomedy in de Hofstad.',
				'_comedyadv_image_url'     => 'https://comedyadventure.nl/wp-content/uploads/2025/08/banner1.png',
			),
			'city_slug'      => 'den-haag',
			'comedian_slugs' => array( 'sanne-bakker', 'karim-el-amrani', 'anouk-smit' ),
			'content'        => 'Het jaar uitluiden met topcomedy in de Hofstad.',
		),
		array(
			'slug'  => 'solo-anouk-smit-eindhoven',
			'title' => 'Solo: Anouk Smit',
			'date'  => '2025-11-09',
			'time'  => '20:00',
			'meta'  => array(
				'_comedyadv_show_duration' => '1,5 uur',
				'_comedyadv_show_price'    => '€24,-',
				'_comedyadv_show_location' => 'Effenaar, Eindhoven',
				'_comedyadv_show_eyebrow'  => 'Live in Eindhoven',
				'_comedyadv_show_lead'     => 'Anouk Smit met haar theatrale solo-show vol muziek, karakters en scherpe observaties.',
			),
			'city_slug'      => 'eindhoven',
			'comedian_slugs' => array( 'anouk-smit' ),
			'content'        => 'Anouk Smit solo: theatrale comedy met muziek en karakters.',
		),
		// Upcoming shows.
		array(
			'slug'  => 'comedy-dinershow-lente-editie',
			'title' => 'Comedy Dinershow — Lente Editie',
			'date'  => '2026-05-12',
			'time'  => '19:00',
			'meta'  => array(
				'_comedyadv_show_duration' => '3,5 uur',
				'_comedyadv_show_price'    => 'Vanaf €49,50',
				'_comedyadv_show_location' => 'Café De Centrale, Vondelpark',
				'_comedyadv_show_eyebrow'  => 'Live in Amsterdam',
				'_comedyadv_show_lead'     => 'De ultieme avond uit. Drie culinaire gangen worden afgewisseld met korte sets van vier Nederlandse topcomedians. Geen avond eindigt zoals hij begon.',
				'_comedyadv_image_url'     => 'https://comedyadventure.nl/wp-content/uploads/2025/10/comedy-dinershow.png',
			),
			'city_slug' => 'amsterdam',
			'content'   => 'De ultieme avond uit. Drie culinaire gangen worden afgewisseld met korte sets van vier Nederlandse topcomedians.',
		),
		array(
			'slug'  => 'open-mic-night-lars',
			'title' => 'Open Mic Night met Lars de Vries',
			'date'  => '2026-05-19',
			'time'  => '20:30',
			'meta'  => array(
				'_comedyadv_show_duration' => '2 uur',
				'_comedyadv_show_price'    => '€15,-',
				'_comedyadv_show_location' => 'BlueCity, Rotterdam',
				'_comedyadv_show_eyebrow'  => 'Live in Rotterdam',
				'_comedyadv_show_lead'     => 'Een avond vol verrassende nieuwe stemmen, doorgewinterde namen en de meest pure vorm van stand-up — geleid door host en publiekslieveling Lars de Vries.',
				'_comedyadv_image_url'     => 'https://comedyadventure.nl/wp-content/uploads/2025/08/banner1.png',
			),
			'city_slug'      => 'rotterdam',
			'comedian_slugs' => array( 'lars-de-vries' ),
			'content'        => 'Een avond vol verrassende nieuwe stemmen, doorgewinterde namen en de meest pure vorm van stand-up.',
		),
		array(
			'slug'  => 'roast-special-sanne',
			'title' => 'Roast Special — Sanne Bakker',
			'date'  => '2026-05-26',
			'time'  => '20:00',
			'meta'  => array(
				'_comedyadv_show_duration' => '1,5 uur',
				'_comedyadv_show_price'    => '€22,50',
				'_comedyadv_show_location' => 'EKKO, Utrecht',
				'_comedyadv_show_eyebrow'  => 'Live in Utrecht',
				'_comedyadv_show_lead'     => 'Sanne neemt het publiek mee in haar wereld vol scherpe observaties en persoonlijke verhalen. Een avond waarin niemand zijn buik vasthoudt zonder te lachen.',
				'_comedyadv_image_url'     => 'https://comedyadventure.nl/wp-content/uploads/2025/10/Comedy-Roast.png',
			),
			'city_slug'      => 'utrecht',
			'comedian_slugs' => array( 'sanne-bakker' ),
			'content'        => 'Sanne neemt het publiek mee in haar wereld vol scherpe observaties en persoonlijke verhalen.',
		),
		array(
			'slug'  => 'comedy-adventure-live-den-haag',
			'title' => 'Comedy Adventure Live — Den Haag',
			'date'  => '2026-06-02',
			'time'  => '20:00',
			'meta'  => array(
				'_comedyadv_show_duration' => '2,5 uur',
				'_comedyadv_show_price'    => 'Vanaf €19,50',
				'_comedyadv_show_location' => 'Paard, Den Haag',
				'_comedyadv_show_eyebrow'  => 'Live in Den Haag',
				'_comedyadv_show_lead'     => 'De grootste editie van het seizoen. Vijf comedians, één avond, één doel: de Hofstad laten lachen tot \'s avonds laat. Inclusief speciale Haagse gastoptredens.',
				'_comedyadv_image_url'     => 'https://comedyadventure.nl/wp-content/uploads/2025/08/banner1.png',
			),
			'city_slug' => 'den-haag',
			'content'   => 'Vijf comedians, één avond, één doel: de Hofstad laten lachen.',
		),
		array(
			'slug'  => 'stand-up-showcase-eindhoven',
			'title' => 'Stand-Up Showcase — 5 comedians',
			'date'  => '2026-06-09',
			'time'  => '20:30',
			'meta'  => array(
				'_comedyadv_show_duration' => '2 uur',
				'_comedyadv_show_price'    => '€17,50',
				'_comedyadv_show_location' => 'Effenaar, Eindhoven',
				'_comedyadv_show_eyebrow'  => 'Live in Eindhoven',
				'_comedyadv_show_lead'     => 'Vijf comedians, vijf compleet verschillende stijlen, één onvergetelijke avond. Van scherpe observatiehumor tot pure absurditeit — een doorsnee van de Nederlandse stand-up scene.',
				'_comedyadv_image_url'     => 'https://comedyadventure.nl/wp-content/uploads/2025/07/Op-Maat-Comedy-Show.png',
			),
			'city_slug' => 'eindhoven',
			'content'   => 'Vijf comedians, vijf compleet verschillende stijlen, één onvergetelijke avond.',
		),
		array(
			'slug'  => 'karim-solo-groningen',
			'title' => 'Karim el Amrani — Solo',
			'date'  => '2026-06-16',
			'time'  => '20:00',
			'meta'  => array(
				'_comedyadv_show_duration' => '1,5 uur',
				'_comedyadv_show_price'    => '€25,-',
				'_comedyadv_show_location' => 'Vera, Groningen',
				'_comedyadv_show_eyebrow'  => 'Live in Groningen',
				'_comedyadv_show_lead'     => 'Energiek, snel en onvoorspelbaar. Karim brengt een complete avondvullende solo-show waarin hij het Groningse publiek meeneemt langs alle uithoeken van zijn humor.',
				'_comedyadv_image_url'     => 'https://comedyadventure.nl/wp-content/uploads/2025/07/Op-Maat-Comedy-Show.png',
			),
			'city_slug'      => 'groningen',
			'comedian_slugs' => array( 'karim-el-amrani' ),
			'content'        => 'Karim brengt een complete avondvullende solo-show in Groningen.',
		),
		array(
			'slug'  => 'zomeravond-comedy',
			'title' => 'Zomeravond Comedy',
			'date'  => '2026-06-23',
			'time'  => '19:30',
			'meta'  => array(
				'_comedyadv_show_duration' => '2 uur',
				'_comedyadv_show_price'    => 'Vanaf €29,-',
				'_comedyadv_show_location' => 'Amsterdam',
				'_comedyadv_show_eyebrow'  => 'Live in Amsterdam',
			),
			'city_slug' => 'amsterdam',
			'content'   => 'Zomereditie van Comedy Adventure in Amsterdam.',
		),
		array(
			'slug'  => 'best-of-comedy-adventure',
			'title' => 'Best Of Comedy Adventure',
			'date'  => '2026-06-30',
			'time'  => '20:00',
			'meta'  => array(
				'_comedyadv_show_duration' => '2 uur',
				'_comedyadv_show_price'    => '€22,50',
				'_comedyadv_show_location' => 'Rotterdam',
				'_comedyadv_show_eyebrow'  => 'Live in Rotterdam',
			),
			'city_slug' => 'rotterdam',
			'content'   => 'De grootste hits van Comedy Adventure in één avond.',
		),
	);
}

function comedyadv_import_demo_shows() {
	foreach ( comedyadv_demo_shows() as $s ) {
		$existing = get_page_by_path( $s['slug'], OBJECT, 'show' );
		$id = $existing ? $existing->ID : 0;
		if ( ! $id ) {
			$id = wp_insert_post( array(
				'post_title'   => $s['title'],
				'post_name'    => $s['slug'],
				'post_status'  => 'publish',
				'post_type'    => 'show',
				'post_content' => isset( $s['content'] ) ? $s['content'] : '',
			) );
			if ( is_wp_error( $id ) ) {
				continue;
			}
		}
		// Always refresh date/time + meta (cheap; ensures correct after migration).
		update_post_meta( $id, '_comedyadv_show_date', $s['date'] );
		update_post_meta( $id, '_comedyadv_show_time', $s['time'] );
		foreach ( $s['meta'] as $k => $v ) {
			update_post_meta( $id, $k, $v );
		}
		// Always refresh locatie reference (idempotent migration from page IDs to locatie CPT IDs).
		if ( ! empty( $s['city_slug'] ) ) {
			$loc = get_page_by_path( $s['city_slug'], OBJECT, 'locatie' );
			if ( $loc ) {
				update_post_meta( $id, '_comedyadv_show_city', (int) $loc->ID );
			}
		}
		// Always refresh comedian relationship.
		if ( ! empty( $s['comedian_slugs'] ) ) {
			$ids = array();
			foreach ( $s['comedian_slugs'] as $cslug ) {
				$comedian = get_page_by_path( $cslug, OBJECT, 'comedian' );
				if ( $comedian ) {
					$ids[] = (int) $comedian->ID;
				}
			}
			update_post_meta( $id, '_comedyadv_show_comedians', $ids );
		}
	}
}

/**
 * Set each locatie's featured show to the first upcoming show that links to it,
 * but only if the user hasn't already chosen one.
 */
function comedyadv_link_locatie_featured_shows() {
	$locaties = get_posts( array( 'post_type' => 'locatie', 'posts_per_page' => -1 ) );
	foreach ( $locaties as $loc ) {
		if ( get_post_meta( $loc->ID, '_comedyadv_featured_show', true ) ) {
			continue;
		}
		$next = get_posts( array(
			'post_type'      => 'show',
			'posts_per_page' => 1,
			'meta_key'       => '_comedyadv_show_date',
			'orderby'        => 'meta_value',
			'order'          => 'ASC',
			'meta_query'     => array(
				'relation' => 'AND',
				array( 'key' => '_comedyadv_show_city', 'value' => $loc->ID ),
				array( 'key' => '_comedyadv_show_date', 'value' => date( 'Y-m-d' ), 'compare' => '>=' ),
			),
		) );
		if ( $next ) {
			update_post_meta( $loc->ID, '_comedyadv_featured_show', (int) $next[0]->ID );
		}
	}
}

/**
 * Migrate old city pages (locaties + 6 sub-pages) into locatie CPT entries.
 * Runs before the demo import so meta is preserved.
 */
function comedyadv_migrate_cities_to_cpt() {
	$slugs = array( 'amsterdam', 'rotterdam', 'utrecht', 'den-haag', 'eindhoven', 'groningen' );
	$keys  = array( '_comedyadv_city_title_html', '_comedyadv_city_lead', '_comedyadv_city_occ_lead', '_comedyadv_featured_show' );

	foreach ( $slugs as $slug ) {
		$page = get_page_by_path( 'locaties/' . $slug );
		if ( ! $page ) {
			$page = get_page_by_path( $slug );
		}
		if ( ! $page || 'page' !== $page->post_type ) {
			continue;
		}

		$cpt = get_page_by_path( $slug, OBJECT, 'locatie' );
		if ( ! $cpt ) {
			$new_id = wp_insert_post( array(
				'post_title'  => $page->post_title,
				'post_name'   => $slug,
				'post_status' => 'publish',
				'post_type'   => 'locatie',
			) );
			if ( ! is_wp_error( $new_id ) ) {
				foreach ( $keys as $k ) {
					$v = get_post_meta( $page->ID, $k, true );
					if ( '' !== $v && null !== $v ) {
						update_post_meta( $new_id, $k, $v );
					}
				}
			}
		}
		wp_delete_post( $page->ID, true );
	}

	// Drop the parent /locaties/ page (replaced by locatie CPT archive).
	$loc_hub = get_page_by_path( 'locaties' );
	if ( $loc_hub && 'page' === $loc_hub->post_type ) {
		wp_delete_post( $loc_hub->ID, true );
	}
}
