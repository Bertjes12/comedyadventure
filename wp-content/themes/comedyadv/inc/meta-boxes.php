<?php
/**
 * Native meta boxes for the CPTs.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const COMEDYADV_NONCE = 'comedyadv_meta_nonce';

/**
 * Enqueue media uploader scripts on locatie edit screens.
 */
function comedyadv_enqueue_media_scripts( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( $screen && 'locatie' === $screen->post_type ) {
		wp_enqueue_media();
		wp_add_inline_script( 'jquery-core', "
jQuery(function($){
	$(document).on('click', '.comedyadv-img-upload', function(e){
		e.preventDefault();
		var btn     = $(this);
		var wrap    = btn.closest('.comedyadv-img-field');
		var input   = wrap.find('.comedyadv-img-id');
		var preview = wrap.find('.comedyadv-img-preview');
		var remove  = wrap.find('.comedyadv-img-remove');
		var frame = wp.media({ title: 'Kies een afbeelding', button:{ text:'Gebruik afbeelding' }, multiple:false });
		frame.on('select', function(){
			var att = frame.state().get('selection').first().toJSON();
			input.val(att.id);
			preview.attr('src', att.sizes && att.sizes.medium ? att.sizes.medium.url : att.url).show();
			remove.show();
			btn.text('Wijzig afbeelding');
		});
		frame.open();
	});
	$(document).on('click', '.comedyadv-img-remove', function(e){
		e.preventDefault();
		var wrap = $(this).closest('.comedyadv-img-field');
		wrap.find('.comedyadv-img-id').val('');
		wrap.find('.comedyadv-img-preview').hide().attr('src','');
		$(this).hide();
		wrap.find('.comedyadv-img-upload').text('Afbeelding kiezen');
	});
});
		" );
	}
}
add_action( 'admin_enqueue_scripts', 'comedyadv_enqueue_media_scripts' );

/**
 * Render an image-picker field backed by the media library.
 */
function comedyadv_field_image( $post_id, $key, $label ) {
	$att_id = (int) get_post_meta( $post_id, $key, true );
	$src    = '';
	if ( $att_id ) {
		$img = wp_get_attachment_image_src( $att_id, 'medium' );
		if ( $img ) {
			$src = $img[0];
		}
	}
	?>
	<p><label style="display:block;font-weight:600;margin-bottom:6px;"><?php echo esc_html( $label ); ?></label></p>
	<div class="comedyadv-img-field" style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
		<input type="hidden" name="<?php echo esc_attr( $key ); ?>" class="comedyadv-img-id" value="<?php echo esc_attr( $att_id ?: '' ); ?>" />
		<?php if ( $src ) : ?>
			<img class="comedyadv-img-preview" src="<?php echo esc_url( $src ); ?>" style="width:80px;height:60px;object-fit:cover;border-radius:6px;border:1px solid #ddd;" />
		<?php else : ?>
			<img class="comedyadv-img-preview" src="" style="width:80px;height:60px;object-fit:cover;border-radius:6px;border:1px solid #ddd;display:none;" />
		<?php endif; ?>
		<button type="button" class="button comedyadv-img-upload"><?php echo $src ? 'Wijzig afbeelding' : 'Afbeelding kiezen'; ?></button>
		<button type="button" class="button-link-delete comedyadv-img-remove" style="<?php echo $src ? '' : 'display:none;'; ?>">Verwijderen</button>
	</div>
	<?php
}

/**
 * Register meta boxes.
 */
function comedyadv_register_meta_boxes() {
	add_meta_box( 'comedyadv_comedian', 'Comedian-gegevens', 'comedyadv_render_comedian_box', 'comedian', 'normal', 'high' );
	add_meta_box( 'comedyadv_show',     'Show-gegevens',     'comedyadv_render_show_box',     'show',     'normal', 'high' );
	add_meta_box( 'comedyadv_workshop', 'Workshop-gegevens', 'comedyadv_render_workshop_box', 'workshop', 'normal', 'high' );
	add_meta_box( 'comedyadv_locatie',  'Locatie-gegevens',  'comedyadv_render_locatie_box',  'locatie',  'normal', 'high' );
	add_meta_box( 'comedyadv_aanbod',   'Aanbod-gegevens',   'comedyadv_render_aanbod_box',   'aanbod',   'normal', 'high' );
	add_meta_box( 'comedyadv_reviews',  'Reviews',           'comedyadv_render_reviews_box',  'page',     'normal', 'low' );
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
	comedyadv_field_text( $post->ID, '_comedyadv_show_doors', 'Deuren open', '18:30' );
	comedyadv_field_text( $post->ID, '_comedyadv_show_duration', 'Tijdsduur', '3,5 uur' );
	comedyadv_field_text( $post->ID, '_comedyadv_show_price', 'Prijs', '€49,50' );
	comedyadv_field_textarea( $post->ID, '_comedyadv_show_inclusive', 'Inclusief (één item per regel)', 4 );
	comedyadv_field_text( $post->ID, '_comedyadv_show_location', 'Locatie (zaal/venue)', 'Café De Centrale, Vondelpark' );
	comedyadv_field_text( $post->ID, '_comedyadv_show_venue_url', 'Link naar de locatie (URL)', 'https://...' );
	comedyadv_field_text( $post->ID, '_comedyadv_show_address_line1', 'Straat + huisnummer', 'bv. Veembroederhof 100' );
	comedyadv_field_text( $post->ID, '_comedyadv_show_address_line2', 'Postcode + stad', 'bv. 1019 HC Amsterdam' );
	comedyadv_field_text( $post->ID, '_comedyadv_show_eyebrow', 'Eyebrow (op stadspagina)', 'Live in Amsterdam' );
	comedyadv_field_textarea( $post->ID, '_comedyadv_show_lead', 'Korte lead (1-2 zinnen)' );
	comedyadv_field_text( $post->ID, '_comedyadv_image_url', 'Afbeelding URL (optioneel — anders Featured Image)', 'https://...' );

	echo '<hr><h4 style="margin:16px 0 8px;">Menu (optioneel — alleen invullen bij diner-shows)</h4>';
	echo '<p class="description" style="margin-bottom:8px;">Laat gangen leeg om geen menu te tonen op de pagina.</p>';
	$menu = (array) get_post_meta( $post->ID, '_comedyadv_show_menu', true );
	echo '<p class="description" style="margin-bottom:12px;">Voer per gang de gerechten in — één gerecht per regel.</p>';
	for ( $i = 0; $i < 6; $i++ ) {
		$name  = isset( $menu[ $i ]['name'] ) ? esc_attr( $menu[ $i ]['name'] ) : '';
		$items = isset( $menu[ $i ]['items'] ) ? esc_textarea( $menu[ $i ]['items'] ) : '';
		printf(
			'<div style="margin-bottom:16px;padding:12px;background:#f9f9f9;border:1px solid #e5e5e5;border-radius:4px;">
				<input type="text" name="_comedyadv_show_menu[%1$d][name]" value="%2$s" placeholder="Gangnaam — bv. Voorgerecht" style="width:100%%;font-weight:600;margin-bottom:6px;" />
				<textarea name="_comedyadv_show_menu[%1$d][items]" rows="3" placeholder="Één gerecht per regel&#10;bv. Huisgemaakte tomatensoep&#10;Salade met geitenkaas" style="width:100%%;margin-top:4px;">%3$s</textarea>
			</div>',
			$i, $name, $items
		);
	}

	// Verloop van de avond.
	echo '<hr><h4 style="margin:16px 0 8px;">Verloop van de avond (optioneel)</h4>';
	echo '<p class="description" style="margin-bottom:12px;">Vul het tijdschema in — laat leeg om het blok te verbergen.</p>';
	$schedule = (array) get_post_meta( $post->ID, '_comedyadv_show_schedule', true );
	for ( $i = 0; $i < 8; $i++ ) :
		$time  = isset( $schedule[ $i ]['time'] )  ? esc_attr( $schedule[ $i ]['time'] )  : '';
		$title = isset( $schedule[ $i ]['title'] ) ? esc_attr( $schedule[ $i ]['title'] ) : '';
		$desc  = isset( $schedule[ $i ]['desc'] )  ? esc_attr( $schedule[ $i ]['desc'] )  : '';
		printf(
			'<div style="display:flex;gap:8px;margin-bottom:8px;align-items:flex-start;">
				<input type="text" name="_comedyadv_show_schedule[%1$d][time]"  value="%2$s" placeholder="18:30" style="width:70px;flex-shrink:0;" />
				<input type="text" name="_comedyadv_show_schedule[%1$d][title]" value="%3$s" placeholder="Deuren open" style="flex:1;" />
				<input type="text" name="_comedyadv_show_schedule[%1$d][desc]"  value="%4$s" placeholder="Omschrijving (optioneel)" style="flex:1;" />
			</div>',
			$i, $time, $title, $desc
		);
	endfor;

	// FAQ.
	echo '<hr><h4 style="margin:16px 0 8px;">Veelgestelde vragen (optioneel)</h4>';
	echo '<p class="description" style="margin-bottom:12px;">Laat leeg om het FAQ-blok te verbergen.</p>';
	$faq = (array) get_post_meta( $post->ID, '_comedyadv_show_faq', true );
	for ( $i = 0; $i < 6; $i++ ) :
		$q = isset( $faq[ $i ]['q'] ) ? esc_attr( $faq[ $i ]['q'] ) : '';
		$a = isset( $faq[ $i ]['a'] ) ? esc_textarea( $faq[ $i ]['a'] ) : '';
		printf(
			'<div style="margin-bottom:12px;padding:12px;background:#f9f9f9;border:1px solid #e5e5e5;border-radius:4px;">
				<input type="text" name="_comedyadv_show_faq[%1$d][q]" value="%2$s" placeholder="Vraag — bv. Is er parkeergelegenheid?" style="width:100%%;font-weight:600;margin-bottom:6px;" />
				<textarea name="_comedyadv_show_faq[%1$d][a]" rows="2" placeholder="Antwoord" style="width:100%%;">%3$s</textarea>
			</div>',
			$i, $q, $a
		);
	endfor;

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
	echo '<p class="description">Stadnaam = post-titel. Hoofdfoto (groot links) = de <strong>Featured Image</strong> rechtsboven. Hoofd-content optioneel.</p>';

	comedyadv_field_text( $post->ID, '_comedyadv_city_title_html', 'Hero-titel (HTML)', 'Comedyshow in <span>Amsterdam</span>' );
	comedyadv_field_textarea( $post->ID, '_comedyadv_city_lead', 'Hero lead' );
	comedyadv_field_textarea( $post->ID, '_comedyadv_city_occ_lead', 'Lead onder "Voor welke gelegenheden?"' );

	echo '<hr><h4 style="margin:16px 0 8px;">Fotogalerij — 4 kleine foto\'s rechts</h4>';
	for ( $i = 1; $i <= 4; $i++ ) {
		comedyadv_field_image( $post->ID, '_comedyadv_gallery_' . $i, 'Foto ' . $i );
	}

	// Reviews.
	echo '<hr><h4 style="margin:16px 0 8px;">Reviews (optioneel — max 3)</h4>';
	echo '<p class="description" style="margin-bottom:12px;">Laat leeg om het reviews-blok te verbergen.</p>';
	$reviews = (array) get_post_meta( $post->ID, '_comedyadv_locatie_reviews', true );
	for ( $i = 0; $i < 3; $i++ ) :
		$text   = isset( $reviews[ $i ]['text'] )   ? esc_textarea( $reviews[ $i ]['text'] )   : '';
		$author = isset( $reviews[ $i ]['author'] ) ? esc_attr( $reviews[ $i ]['author'] )     : '';
		printf(
			'<div style="margin-bottom:14px;padding:12px;background:#f9f9f9;border:1px solid #e5e5e5;border-radius:4px;">
				<textarea name="_comedyadv_locatie_reviews[%1$d][text]" rows="2" placeholder="Reviewtekst..." style="width:100%%;margin-bottom:6px;">%2$s</textarea>
				<input type="text" name="_comedyadv_locatie_reviews[%1$d][author]" value="%3$s" placeholder="Naam — functie/bedrijf" style="width:100%%;" />
			</div>',
			$i, $text, $author
		);
	endfor;

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

function comedyadv_render_reviews_box( $post ) {
	wp_nonce_field( COMEDYADV_NONCE, COMEDYADV_NONCE );
	echo '<p class="description" style="margin-bottom:12px;">Laat leeg om het reviews-blok te verbergen op deze pagina.</p>';
	$reviews = (array) get_post_meta( $post->ID, '_comedyadv_page_reviews', true );
	for ( $i = 0; $i < 3; $i++ ) :
		$text   = isset( $reviews[ $i ]['text'] )   ? esc_textarea( $reviews[ $i ]['text'] )   : '';
		$author = isset( $reviews[ $i ]['author'] ) ? esc_attr( $reviews[ $i ]['author'] )     : '';
		printf(
			'<div style="margin-bottom:14px;padding:12px;background:#f9f9f9;border:1px solid #e5e5e5;border-radius:4px;">
				<textarea name="_comedyadv_page_reviews[%1$d][text]" rows="2" placeholder="Reviewtekst..." style="width:100%%;margin-bottom:6px;">%2$s</textarea>
				<input type="text" name="_comedyadv_page_reviews[%1$d][author]" value="%3$s" placeholder="Naam — functie/bedrijf" style="width:100%%;" />
			</div>',
			$i, $text, $author
		);
	endfor;
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
				'_comedyadv_show_date', '_comedyadv_show_time', '_comedyadv_show_doors',
				'_comedyadv_show_duration', '_comedyadv_show_price',
				'_comedyadv_show_location', '_comedyadv_show_venue_url', '_comedyadv_show_eyebrow',
				'_comedyadv_show_address_line1', '_comedyadv_show_address_line2',
				'_comedyadv_image_url',
			);
			$textarea_keys = array( '_comedyadv_show_lead', '_comedyadv_show_inclusive' );
			$select_keys   = array( '_comedyadv_show_city' );
			$multi_keys    = array( '_comedyadv_show_comedians' );
			// Menu opslaan.
			if ( isset( $_POST['_comedyadv_show_menu'] ) && is_array( $_POST['_comedyadv_show_menu'] ) ) {
				$courses = array();
				foreach ( $_POST['_comedyadv_show_menu'] as $row ) {
					$name  = sanitize_text_field( wp_unslash( $row['name'] ?? '' ) );
					$items = sanitize_textarea_field( wp_unslash( $row['items'] ?? '' ) );
					if ( '' !== $name ) {
						$courses[] = array( 'name' => $name, 'items' => $items );
					}
				}
				update_post_meta( $post_id, '_comedyadv_show_menu', $courses );
			} else {
				update_post_meta( $post_id, '_comedyadv_show_menu', array() );
			}
			// FAQ opslaan.
			if ( isset( $_POST['_comedyadv_show_faq'] ) && is_array( $_POST['_comedyadv_show_faq'] ) ) {
				$faqs = array();
				foreach ( $_POST['_comedyadv_show_faq'] as $row ) {
					$q = sanitize_text_field( wp_unslash( $row['q'] ?? '' ) );
					$a = sanitize_textarea_field( wp_unslash( $row['a'] ?? '' ) );
					if ( '' !== $q ) {
						$faqs[] = array( 'q' => $q, 'a' => $a );
					}
				}
				update_post_meta( $post_id, '_comedyadv_show_faq', $faqs );
			} else {
				update_post_meta( $post_id, '_comedyadv_show_faq', array() );
			}
			// Schema opslaan.
			if ( isset( $_POST['_comedyadv_show_schedule'] ) && is_array( $_POST['_comedyadv_show_schedule'] ) ) {
				$steps = array();
				foreach ( $_POST['_comedyadv_show_schedule'] as $row ) {
					$time  = sanitize_text_field( wp_unslash( $row['time']  ?? '' ) );
					$title = sanitize_text_field( wp_unslash( $row['title'] ?? '' ) );
					$desc  = sanitize_text_field( wp_unslash( $row['desc']  ?? '' ) );
					if ( '' !== $time || '' !== $title ) {
						$steps[] = array( 'time' => $time, 'title' => $title, 'desc' => $desc );
					}
				}
				update_post_meta( $post_id, '_comedyadv_show_schedule', $steps );
			} else {
				update_post_meta( $post_id, '_comedyadv_show_schedule', array() );
			}
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
			$select_keys   = array( '_comedyadv_featured_show', '_comedyadv_gallery_1', '_comedyadv_gallery_2', '_comedyadv_gallery_3', '_comedyadv_gallery_4' );
			// Reviews opslaan.
			if ( isset( $_POST['_comedyadv_locatie_reviews'] ) && is_array( $_POST['_comedyadv_locatie_reviews'] ) ) {
				$reviews = array();
				foreach ( $_POST['_comedyadv_locatie_reviews'] as $row ) {
					$text   = sanitize_textarea_field( wp_unslash( $row['text']   ?? '' ) );
					$author = sanitize_text_field( wp_unslash( $row['author'] ?? '' ) );
					if ( '' !== $text ) {
						$reviews[] = array( 'text' => $text, 'author' => $author );
					}
				}
				update_post_meta( $post_id, '_comedyadv_locatie_reviews', $reviews );
			} else {
				update_post_meta( $post_id, '_comedyadv_locatie_reviews', array() );
			}
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

		case 'page':
			if ( isset( $_POST['_comedyadv_page_reviews'] ) && is_array( $_POST['_comedyadv_page_reviews'] ) ) {
				$reviews = array();
				foreach ( $_POST['_comedyadv_page_reviews'] as $row ) {
					$text   = sanitize_textarea_field( wp_unslash( $row['text']   ?? '' ) );
					$author = sanitize_text_field( wp_unslash( $row['author'] ?? '' ) );
					if ( '' !== $text ) {
						$reviews[] = array( 'text' => $text, 'author' => $author );
					}
				}
				update_post_meta( $post_id, '_comedyadv_page_reviews', $reviews );
			} else {
				update_post_meta( $post_id, '_comedyadv_page_reviews', array() );
			}
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
