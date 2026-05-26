<?php
/**
 * Native meta boxes for the CPTs.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const COMEDYADV_NONCE = 'comedyadv_meta_nonce';

/**
 * Register meta boxes.
 */
function comedyadv_register_meta_boxes() {
	add_meta_box( 'comedyadv_comedian', 'Comedian-gegevens', 'comedyadv_render_comedian_box', 'comedian', 'normal', 'high' );
	add_meta_box( 'comedyadv_show',     'Show-gegevens',     'comedyadv_render_show_box',     'show',     'normal', 'high' );
	add_meta_box( 'comedyadv_workshop', 'Workshop-gegevens', 'comedyadv_render_workshop_box', 'workshop', 'normal', 'high' );
	add_meta_box( 'comedyadv_locatie',  'Locatie-gegevens',  'comedyadv_render_locatie_box',  'locatie',  'normal', 'high' );
	add_meta_box( 'comedyadv_aanbod',   'Aanbod-gegevens',   'comedyadv_render_aanbod_box',   'aanbod',   'normal', 'high' );
}
add_action( 'add_meta_boxes', 'comedyadv_register_meta_boxes' );

function comedyadv_field_text( $post_id, $key, $label, $placeholder = '' ) {
	$value = esc_attr( get_post_meta( $post_id, $key, true ) );
	printf(
		'<p><label style="display:block;font-weight:600;margin-bottom:4px;">%1$s</label><input type="text" name="%2$s" value="%3$s" placeholder="%4$s" style="width:100%%;" /></p>',
		esc_html( $label ),
		esc_attr( $key ),
		$value,
		esc_attr( $placeholder )
	);
}

function comedyadv_field_textarea( $post_id, $key, $label, $rows = 3 ) {
	$value = esc_textarea( get_post_meta( $post_id, $key, true ) );
	printf(
		'<p><label style="display:block;font-weight:600;margin-bottom:4px;">%1$s</label><textarea name="%2$s" rows="%3$d" style="width:100%%;">%4$s</textarea></p>',
		esc_html( $label ),
		esc_attr( $key ),
		(int) $rows,
		$value
	);
}

function comedyadv_render_comedian_box( $post ) {
	wp_nonce_field( COMEDYADV_NONCE, COMEDYADV_NONCE );
	echo '<p class="description">Tip: post content (boven) wordt gebruikt als de bio. Naam = post-titel. Foto: Featured Image of plak een externe URL hieronder.</p>';
	comedyadv_field_text( $post->ID, '_comedyadv_tag', 'Tag / specialiteit', 'bv. Observatiehumor' );
	comedyadv_field_text( $post->ID, '_comedyadv_image_url', 'Afbeelding URL (optioneel — anders Featured Image)', 'https://...' );
}

function comedyadv_render_show_box( $post ) {
	wp_nonce_field( COMEDYADV_NONCE, COMEDYADV_NONCE );
	echo '<p class="description">Show-titel = post-titel. Lange beschrijving = post content (boven). Korte lead = veld hieronder. Shows hebben geen eigen pagina — ze worden alleen getoond op de gekoppelde locatie-pagina en op de homepage.</p>';

	comedyadv_field_text( $post->ID, '_comedyadv_show_date', 'Datum (YYYY-MM-DD)', '2026-05-12' );
	comedyadv_field_text( $post->ID, '_comedyadv_show_time', 'Tijd', '19:00' );
	comedyadv_field_text( $post->ID, '_comedyadv_show_duration', 'Tijdsduur', '3,5 uur' );
	comedyadv_field_text( $post->ID, '_comedyadv_show_price', 'Prijs', 'Vanaf €49,50' );
	comedyadv_field_text( $post->ID, '_comedyadv_show_location', 'Locatie (zaal/venue)', 'Café De Centrale, Vondelpark' );
	comedyadv_field_text( $post->ID, '_comedyadv_show_eyebrow', 'Eyebrow (op stadspagina)', 'Live in Amsterdam' );
	comedyadv_field_textarea( $post->ID, '_comedyadv_show_lead', 'Korte lead (1-2 zinnen)' );
	comedyadv_field_text( $post->ID, '_comedyadv_image_url', 'Afbeelding URL (optioneel — anders Featured Image)', 'https://...' );

	// Locatie relationship.
	$current  = (int) get_post_meta( $post->ID, '_comedyadv_show_city', true );
	$locaties = get_posts( array( 'post_type' => 'locatie', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
	echo '<p><label style="display:block;font-weight:600;margin-bottom:4px;">Locatie (stad)</label><select name="_comedyadv_show_city" style="width:100%;"><option value="">— geen —</option>';
	foreach ( $locaties as $loc ) {
		printf( '<option value="%d"%s>%s</option>', (int) $loc->ID, selected( $current, $loc->ID, false ), esc_html( $loc->post_title ) );
	}
	echo '</select></p>';

	// Comedians multi-select.
	$comedians = get_posts( array( 'post_type' => 'comedian', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
	$selected  = (array) get_post_meta( $post->ID, '_comedyadv_show_comedians', true );
	$selected  = array_map( 'intval', $selected );
	echo '<p><label style="display:block;font-weight:600;margin-bottom:4px;">Optredende comedians</label><select name="_comedyadv_show_comedians[]" multiple size="6" style="width:100%;">';
	foreach ( $comedians as $c ) {
		printf( '<option value="%d"%s>%s</option>', (int) $c->ID, in_array( (int) $c->ID, $selected, true ) ? ' selected' : '', esc_html( $c->post_title ) );
	}
	echo '</select><span class="description">Houd Ctrl/Cmd ingedrukt voor meerdere selecties.</span></p>';
}

function comedyadv_render_workshop_box( $post ) {
	wp_nonce_field( COMEDYADV_NONCE, COMEDYADV_NONCE );
	echo '<p class="description">Workshopnaam = post-titel. Hero-afbeelding = de <strong>Featured Image</strong> rechtsboven. De drie introductie-paragrafen zet je in post content (boven). De rest hieronder.</p>';

	comedyadv_field_text( $post->ID, '_comedyadv_breadcrumb', 'Breadcrumb-label', 'Comedy Workshop' );
	comedyadv_field_text( $post->ID, '_comedyadv_title_html', 'Hero-titel (HTML; gebruik <span> voor accent)', 'Comedy <span>Workshop</span>' );
	comedyadv_field_textarea( $post->ID, '_comedyadv_lead', 'Hero lead (korte intro onder de titel)' );
	comedyadv_field_text( $post->ID, '_comedyadv_eyebrow1', 'Eyebrow links (boven content)', 'Over deze workshop' );
	comedyadv_field_text( $post->ID, '_comedyadv_h2_1', 'H2 links', 'Sta zelf op het podium' );

	echo '<hr><h4 style="margin:16px 0 8px;">Specs-paneel</h4>';
	comedyadv_field_text( $post->ID, '_comedyadv_price', 'Prijs per persoon', '€595,-' );
	comedyadv_field_text( $post->ID, '_comedyadv_price_sub', 'Prijs onderschrift', 'excl. BTW • basis tarief' );

	$specs = (array) get_post_meta( $post->ID, '_comedyadv_specs', true );
	echo '<table style="width:100%;margin:8px 0;"><thead><tr><th style="text-align:left;">Label</th><th style="text-align:left;">Waarde</th></tr></thead><tbody>';
	for ( $i = 0; $i < 6; $i++ ) {
		$label = isset( $specs[ $i ]['label'] ) ? esc_attr( $specs[ $i ]['label'] ) : '';
		$value = isset( $specs[ $i ]['value'] ) ? esc_attr( $specs[ $i ]['value'] ) : '';
		printf(
			'<tr><td><input type="text" name="_comedyadv_specs[%1$d][label]" value="%2$s" style="width:100%%;" /></td><td><input type="text" name="_comedyadv_specs[%1$d][value]" value="%3$s" style="width:100%%;" /></td></tr>',
			$i, $label, $value
		);
	}
	echo '</tbody></table>';

	echo '<hr><h4 style="margin:16px 0 8px;">Wat-leer-je-paneel</h4>';
	comedyadv_field_text( $post->ID, '_comedyadv_eyebrow2', 'Eyebrow rechts', 'Wat leer je?' );
	comedyadv_field_text( $post->ID, '_comedyadv_h2_2', 'H2 rechts', 'Het volledige stand-up proces' );

	$pills = (array) get_post_meta( $post->ID, '_comedyadv_pills', true );
	echo '<p><label style="display:block;font-weight:600;margin-bottom:4px;">Pills (max 6)</label></p>';
	for ( $i = 0; $i < 6; $i++ ) {
		$pill = isset( $pills[ $i ] ) ? esc_attr( $pills[ $i ] ) : '';
		printf(
			'<p><input type="text" name="_comedyadv_pills[%1$d]" value="%2$s" placeholder="bv. Grappen schrijven" style="width:100%%;" /></p>',
			$i, $pill
		);
	}

	comedyadv_field_textarea( $post->ID, '_comedyadv_closing', 'Afsluitende paragraaf (rechts onder pills)' );
	comedyadv_field_text( $post->ID, '_comedyadv_cta_h2', 'CTA-banner heading (onderaan pagina)', 'Klaar om je team het podium op te zetten?' );
}

function comedyadv_render_aanbod_box( $post ) {
	wp_nonce_field( COMEDYADV_NONCE, COMEDYADV_NONCE );
	echo '<p class="description">Naam = post-titel. Afbeelding op de card en pagina = de <strong>Featured Image</strong> rechtsboven. Hoofdtekst zet je in post content (boven).</p>';
	comedyadv_field_textarea( $post->ID, '_comedyadv_aanbod_lead', 'Korte intro / hero-lead (1-2 zinnen)', 2 );
	comedyadv_field_text( $post->ID, '_comedyadv_aanbod_card_extra', 'Extra tekst op card (oranje, boven de titel)', 'bv. Vanaf 30 personen • Op maat' );

	echo '<hr><h4 style="margin:16px 0 8px;">Info-blok</h4>';
	comedyadv_field_text( $post->ID, '_comedyadv_aanbod_duration',     'Tijdsduur',                'bv. 3,5 uur' );
	comedyadv_field_text( $post->ID, '_comedyadv_aanbod_price_pp',     'Prijs per persoon',        'bv. €49,50' );
	comedyadv_field_text( $post->ID, '_comedyadv_aanbod_min_personen', 'Minimum aantal personen',  'bv. 30' );
}

function comedyadv_render_locatie_box( $post ) {
	wp_nonce_field( COMEDYADV_NONCE, COMEDYADV_NONCE );
	echo '<p class="description">Stadnaam = post-titel. Achtergrondafbeelding op het /locaties/ archief = de <strong>Featured Image</strong> rechtsboven. Hoofd-content optioneel.</p>';

	comedyadv_field_text( $post->ID, '_comedyadv_city_title_html', 'Hero-titel (HTML)', 'Comedyshow in <span>Amsterdam</span>' );
	comedyadv_field_textarea( $post->ID, '_comedyadv_city_lead', 'Hero lead' );
	comedyadv_field_textarea( $post->ID, '_comedyadv_city_occ_lead', 'Lead onder "Voor welke gelegenheden?"' );

	// Featured show relationship.
	$shows    = get_posts( array( 'post_type' => 'show', 'posts_per_page' => -1, 'orderby' => 'meta_value', 'meta_key' => '_comedyadv_show_date', 'order' => 'ASC' ) );
	$current  = (int) get_post_meta( $post->ID, '_comedyadv_featured_show', true );
	echo '<p><label style="display:block;font-weight:600;margin-bottom:4px;">Featured show</label><select name="_comedyadv_featured_show" style="width:100%;"><option value="">— automatisch (eerstvolgende show in deze stad) —</option>';
	foreach ( $shows as $show ) {
		$date = get_post_meta( $show->ID, '_comedyadv_show_date', true );
		printf(
			'<option value="%d"%s>%s — %s</option>',
			(int) $show->ID,
			selected( $current, $show->ID, false ),
			esc_html( $show->post_title ),
			esc_html( $date )
		);
	}
	echo '</select></p>';
}

/**
 * Save handler.
 */
function comedyadv_save_post( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! isset( $_POST[ COMEDYADV_NONCE ] ) || ! wp_verify_nonce( $_POST[ COMEDYADV_NONCE ], COMEDYADV_NONCE ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$post_type = get_post_type( $post_id );

	$text_keys     = array();
	$textarea_keys = array();
	$arrayed_keys  = array();
	$pill_keys     = array();
	$specs_keys    = array();
	$select_keys   = array();
	$multi_keys    = array();

	switch ( $post_type ) {
		case 'comedian':
			$text_keys = array( '_comedyadv_tag', '_comedyadv_image_url' );
			break;

		case 'show':
			$text_keys = array(
				'_comedyadv_show_date', '_comedyadv_show_time', '_comedyadv_show_duration',
				'_comedyadv_show_price', '_comedyadv_show_location', '_comedyadv_show_eyebrow',
				'_comedyadv_image_url',
			);
			$textarea_keys = array( '_comedyadv_show_lead' );
			$select_keys   = array( '_comedyadv_show_city' );
			$multi_keys    = array( '_comedyadv_show_comedians' );
			break;

		case 'workshop':
			$text_keys = array(
				'_comedyadv_breadcrumb', '_comedyadv_title_html',
				'_comedyadv_eyebrow1', '_comedyadv_h2_1', '_comedyadv_price', '_comedyadv_price_sub',
				'_comedyadv_eyebrow2', '_comedyadv_h2_2', '_comedyadv_cta_h2',
			);
			$textarea_keys = array( '_comedyadv_lead', '_comedyadv_closing' );
			$pill_keys     = array( '_comedyadv_pills' );
			$specs_keys    = array( '_comedyadv_specs' );
			break;

		case 'locatie':
			$text_keys     = array( '_comedyadv_city_title_html' );
			$textarea_keys = array( '_comedyadv_city_lead', '_comedyadv_city_occ_lead' );
			$select_keys   = array( '_comedyadv_featured_show' );
			break;

		case 'aanbod':
			$text_keys     = array(
				'_comedyadv_aanbod_duration',
				'_comedyadv_aanbod_price_pp',
				'_comedyadv_aanbod_min_personen',
				'_comedyadv_aanbod_card_extra',
			);
			$textarea_keys = array( '_comedyadv_aanbod_lead' );
			break;
	}

	foreach ( $text_keys as $k ) {
		if ( isset( $_POST[ $k ] ) ) {
			update_post_meta( $post_id, $k, sanitize_text_field( wp_unslash( $_POST[ $k ] ) ) );
		}
	}
	foreach ( $textarea_keys as $k ) {
		if ( isset( $_POST[ $k ] ) ) {
			update_post_meta( $post_id, $k, wp_kses_post( wp_unslash( $_POST[ $k ] ) ) );
		}
	}
	foreach ( $select_keys as $k ) {
		if ( isset( $_POST[ $k ] ) ) {
			update_post_meta( $post_id, $k, (int) $_POST[ $k ] );
		}
	}
	foreach ( $multi_keys as $k ) {
		if ( isset( $_POST[ $k ] ) ) {
			$ids = array_map( 'intval', (array) $_POST[ $k ] );
			$ids = array_filter( $ids );
			update_post_meta( $post_id, $k, $ids );
		} else {
			update_post_meta( $post_id, $k, array() );
		}
	}
	foreach ( $pill_keys as $k ) {
		if ( isset( $_POST[ $k ] ) ) {
			$pills = array();
			foreach ( (array) $_POST[ $k ] as $pill ) {
				$pill = sanitize_text_field( wp_unslash( $pill ) );
				if ( '' !== $pill ) {
					$pills[] = $pill;
				}
			}
			update_post_meta( $post_id, $k, $pills );
		}
	}
	foreach ( $specs_keys as $k ) {
		if ( isset( $_POST[ $k ] ) && is_array( $_POST[ $k ] ) ) {
			$rows = array();
			foreach ( $_POST[ $k ] as $row ) {
				$label = isset( $row['label'] ) ? sanitize_text_field( wp_unslash( $row['label'] ) ) : '';
				$value = isset( $row['value'] ) ? sanitize_text_field( wp_unslash( $row['value'] ) ) : '';
				if ( '' !== $label || '' !== $value ) {
					$rows[] = array( 'label' => $label, 'value' => $value );
				}
			}
			update_post_meta( $post_id, $k, $rows );
		}
	}
}
add_action( 'save_post', 'comedyadv_save_post' );

/**
 * Resolve image URL for a post (meta override > featured image > '').
 */
function comedyadv_image_url( $post_id, $size = 'large' ) {
	$url = get_post_meta( $post_id, '_comedyadv_image_url', true );
	if ( $url ) {
		return $url;
	}
	if ( has_post_thumbnail( $post_id ) ) {
		$src = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $size );
		if ( $src ) {
			return $src[0];
		}
	}
	return '';
}
