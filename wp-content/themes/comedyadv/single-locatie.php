<?php
/**
 * Single locatie (city) page.
 */
get_header();

$contact_url = comedyadv_url( 'contact' );
$home_url    = home_url( '/' );
$loc_url     = get_post_type_archive_link( 'locatie' );

while ( have_posts() ) : the_post();
	$id         = get_the_ID();
	$city_name  = get_the_title();
	$title_html = comedyadv_meta( $id, '_comedyadv_city_title_html', 'Comedyshow in <span>' . esc_html( $city_name ) . '</span>' );
	$lead       = comedyadv_meta( $id, '_comedyadv_city_lead' );
	$occ_lead   = comedyadv_meta( $id, '_comedyadv_city_occ_lead' );

	// Featured show: explicit choice, else first upcoming for this city.
	$show_id = (int) comedyadv_meta( $id, '_comedyadv_featured_show' );
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
				array( 'key' => '_comedyadv_show_date', 'value' => date( 'Y-m-d' ), 'compare' => '>=' ),
			),
		) );
		if ( $auto ) {
			$show_id = (int) $auto[0]->ID;
		}
	}

	// All shows for this city, sorted by date asc.
	$all_city_shows = get_posts( array(
		'post_type'      => 'show',
		'posts_per_page' => -1,
		'meta_key'       => '_comedyadv_show_date',
		'orderby'        => 'meta_value',
		'order'          => 'ASC',
		'meta_query'     => array(
			array( 'key' => '_comedyadv_show_city', 'value' => $id ),
		),
	) );
	$today    = date( 'Y-m-d' );
	$upcoming_shows = array();
	$past_shows     = array();
	foreach ( $all_city_shows as $s ) {
		$d = get_post_meta( $s->ID, '_comedyadv_show_date', true );
		if ( $d >= $today ) {
			$upcoming_shows[] = $s;
		} else {
			$past_shows[] = $s;
		}
	}
?>

<section class="page-hero">
	<div class="container page-hero__inner">
		<div class="page-hero__breadcrumbs"><?php echo comedyadv_breadcrumbs( array( array( 'Home', $home_url ), array( 'Locaties', $loc_url ), $city_name ) ); ?></div>
		<h1 class="page-hero__title"><?php echo wp_kses_post( $title_html ); ?></h1>
		<?php if ( $lead ) : ?>
			<p class="page-hero__lead"><?php echo esc_html( $lead ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php if ( $show_id ) :
	$date      = get_post_meta( $show_id, '_comedyadv_show_date', true );
	$time      = get_post_meta( $show_id, '_comedyadv_show_time', true );
	$duration  = get_post_meta( $show_id, '_comedyadv_show_duration', true );
	$price     = get_post_meta( $show_id, '_comedyadv_show_price', true );
	$venue     = get_post_meta( $show_id, '_comedyadv_show_location', true );
	$eyebrow   = get_post_meta( $show_id, '_comedyadv_show_eyebrow', true );
	$show_lead = get_post_meta( $show_id, '_comedyadv_show_lead', true );
	$image_url = comedyadv_image_url( $show_id, 'large' );
	$show_post = get_post( $show_id );
	$show_title = $show_post ? $show_post->post_title : '';

	$date_display = comedyadv_show_long_date( $date );
	if ( $time ) {
		$date_display .= ' • ' . $time;
	}
?>
<section class="section section--grey">
	<div class="container">
		<div class="show-feature">
			<div class="show-feature__media reveal">
				<span class="show-feature__badge">Aankomende show</span>
				<div class="show-feature__date-stamp">
					<span class="show-feature__date-day"><?php echo esc_html( comedyadv_show_day( $date ) ); ?></span>
					<span class="show-feature__date-month"><?php echo esc_html( comedyadv_show_month( $date ) ); ?></span>
				</div>
				<?php if ( $image_url ) : ?>
					<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $show_title ); ?>" />
				<?php endif; ?>
			</div>
			<div class="reveal">
				<?php if ( $eyebrow ) : ?>
					<span class="show-feature__eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
				<?php endif; ?>
				<h2 class="show-feature__title"><?php echo esc_html( $show_title ); ?></h2>
				<?php if ( $show_lead ) : ?>
					<p class="show-feature__lead"><?php echo esc_html( $show_lead ); ?></p>
				<?php endif; ?>
				<div class="show-info">
					<div class="show-info__item">
						<span class="show-info__label">
							<svg class="show-info__icon" viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/></svg>
							Datum
						</span>
						<span class="show-info__value"><?php echo esc_html( $date_display ); ?></span>
					</div>
					<div class="show-info__item">
						<span class="show-info__label">
							<svg class="show-info__icon" viewBox="0 0 24 24"><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm4.2 14.2L11 13V7h1.5v5.2l4.5 2.7-.8 1.3z"/></svg>
							Tijdsduur
						</span>
						<span class="show-info__value"><?php echo esc_html( $duration ); ?></span>
					</div>
					<div class="show-info__item">
						<span class="show-info__label">
							<svg class="show-info__icon" viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16C7.36 5.58 5.8 6.84 5.8 8.77c0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1H5.6c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
							Prijs
						</span>
						<span class="show-info__value"><?php echo esc_html( $price ); ?></span>
					</div>
					<div class="show-info__item">
						<span class="show-info__label">
							<svg class="show-info__icon" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
							Locatie
						</span>
						<span class="show-info__value"><?php echo esc_html( $venue ); ?></span>
					</div>
				</div>
				<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--primary">Reserveer tickets <span class="btn__arrow">&rarr;</span></a>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>

<?php if ( count( $upcoming_shows ) > 1 || $past_shows ) : ?>
<section class="section">
	<div class="container">
		<div class="section-head">
			<span class="section-head__eyebrow">Alle shows in <?php echo esc_html( $city_name ); ?></span>
			<h2>Wat staat er op de planning?</h2>
		</div>

		<?php if ( count( $upcoming_shows ) > 1 ) :
			// Skip the featured show in the list (avoid duplication).
			$other_upcoming = array_filter( $upcoming_shows, function ( $s ) use ( $show_id ) {
				return (int) $s->ID !== (int) $show_id;
			} );
			if ( $other_upcoming ) : ?>
				<h3 style="font-family:'Inter',sans-serif;text-transform:none;font-weight:700;font-size:1.2rem;margin:24px 0 16px;">Aankomende shows</h3>
				<div class="agenda-list">
					<?php foreach ( $other_upcoming as $s ) :
						$d = get_post_meta( $s->ID, '_comedyadv_show_date', true );
						$t = get_post_meta( $s->ID, '_comedyadv_show_time', true );
						$p = get_post_meta( $s->ID, '_comedyadv_show_price', true );
						$v = get_post_meta( $s->ID, '_comedyadv_show_location', true );
						$meta = array_filter( array( $v, $t, $p ) );
					?>
						<article class="agenda-item">
							<div class="agenda-item__date">
								<div class="agenda-item__day"><?php echo esc_html( comedyadv_show_day( $d ) ); ?></div>
								<div class="agenda-item__month"><?php echo esc_html( comedyadv_show_month( $d ) ); ?></div>
							</div>
							<div class="agenda-item__info">
								<h4><?php echo esc_html( $s->post_title ); ?></h4>
								<?php if ( $meta ) : ?>
									<p class="agenda-item__meta"><?php echo esc_html( implode( ' • ', $meta ) ); ?></p>
								<?php endif; ?>
							</div>
							<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--primary">Tickets</a>
						</article>
					<?php endforeach; ?>
				</div>
			<?php endif;
		endif; ?>

		<?php if ( $past_shows ) :
			$past_shows = array_reverse( $past_shows ); // most recent first
		?>
			<h3 style="font-family:'Inter',sans-serif;text-transform:none;font-weight:700;font-size:1.2rem;margin:40px 0 16px;color:var(--grey-500);">Eerdere shows</h3>
			<div class="agenda-list">
				<?php foreach ( $past_shows as $s ) :
					$d = get_post_meta( $s->ID, '_comedyadv_show_date', true );
					$t = get_post_meta( $s->ID, '_comedyadv_show_time', true );
					$v = get_post_meta( $s->ID, '_comedyadv_show_location', true );
					$meta = array_filter( array( $v, $t ) );
				?>
					<article class="agenda-item" style="opacity:0.7;">
						<div class="agenda-item__date">
							<div class="agenda-item__day"><?php echo esc_html( comedyadv_show_day( $d ) ); ?></div>
							<div class="agenda-item__month"><?php echo esc_html( comedyadv_show_month( $d ) ); ?></div>
						</div>
						<div class="agenda-item__info">
							<h4><?php echo esc_html( $s->post_title ); ?></h4>
							<?php if ( $meta ) : ?>
								<p class="agenda-item__meta"><?php echo esc_html( implode( ' • ', $meta ) ); ?> &bull; geweest</p>
							<?php endif; ?>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
<?php endif; ?>

<section class="section section--grey">
	<div class="container">
		<div class="section-head">
			<span class="section-head__eyebrow">Comedy in <?php echo esc_html( $city_name ); ?></span>
			<h2>Voor welke gelegenheden?</h2>
			<?php if ( $occ_lead ) : ?>
				<p class="section-head__lead"><?php echo esc_html( $occ_lead ); ?></p>
			<?php endif; ?>
		</div>
		<ul class="pill-list">
			<li class="pill">Bedrijfsfeesten</li>
			<li class="pill">Diners</li>
			<li class="pill">Personeelsuitjes</li>
			<li class="pill">Bruiloften</li>
			<li class="pill">Jubilea</li>
			<li class="pill">Productlanceringen</li>
			<li class="pill">Borrels</li>
			<li class="pill">Afscheidsfeesten</li>
		</ul>
	</div>
</section>

<section class="section">
	<div class="container">
		<div class="section-head">
			<span class="section-head__eyebrow">Hoe het werkt</span>
			<h2>Van eerste contact tot lachsalvo</h2>
			<p class="section-head__lead">In vier stappen regelen wij een onvergetelijke comedyshow in <?php echo esc_html( $city_name ); ?>.</p>
		</div>
		<div class="steps">
			<div class="step reveal">
				<h4>Aanvraag</h4>
				<p>Vul het formulier in of bel ons. Vertel over je evenement, datum, locatie en aantal gasten.</p>
			</div>
			<div class="step reveal">
				<h4>Voorstel op maat</h4>
				<p>Binnen 24 uur ontvang je een voorstel met passende comedians, format en prijsindicatie.</p>
			</div>
			<div class="step reveal">
				<h4>Briefing &amp; voorbereiding</h4>
				<p>Onze comedian bereidt zich voor met een uitgebreide briefing over je publiek en gelegenheid.</p>
			</div>
			<div class="step reveal">
				<h4>Showtime in <?php echo esc_html( $city_name ); ?></h4>
				<p>Wij regelen techniek, opbouw en uitvoering. Jij en je gasten kunnen ontspannen genieten.</p>
			</div>
		</div>
	</div>
</section>

<section class="section">
	<div class="container">
		<div class="cta-banner">
			<h2>Klaar voor jouw show in <?php echo esc_html( $city_name ); ?>?</h2>
			<p>Neem contact op voor een vrijblijvend voorstel op maat. Wij reageren binnen 24 uur.</p>
			<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--dark">Plan jouw show in <?php echo esc_html( $city_name ); ?> <span class="btn__arrow">&rarr;</span></a>
		</div>
	</div>
</section>

<?php endwhile; ?>

<?php get_footer(); ?>
