<?php
/**
 * Single locatie (city/event) page — Eventbrite-style layout.
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
	$loc_image  = comedyadv_image_url( $id, 'large' );

	// Featured show.
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

	// All shows for this locatie.
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
	$today          = date( 'Y-m-d' );
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

	// Show data.
	$show_title    = '';
	$show_date     = '';
	$show_time     = '';
	$show_duration  = '';
	$show_price     = '';
	$show_inclusive = '';
	$show_doors     = '';
	$show_venue        = '';
	$show_venue_url    = '';
	$show_address_l1   = '';
	$show_address_l2   = '';
	$show_eyebrow  = '';
	$show_lead     = '';
	$show_content  = '';
	$show_image    = '';
	$show_comedians = array();
	$date_long     = '';

	if ( $show_id ) {
		$show_post     = get_post( $show_id );
		$show_title    = $show_post ? $show_post->post_title : '';
		$show_content  = $show_post ? $show_post->post_content : '';
		$show_date     = get_post_meta( $show_id, '_comedyadv_show_date', true );
		$show_time     = get_post_meta( $show_id, '_comedyadv_show_time', true );
		$show_duration  = get_post_meta( $show_id, '_comedyadv_show_duration', true );
		$show_price     = get_post_meta( $show_id, '_comedyadv_show_price', true );
		$show_inclusive = get_post_meta( $show_id, '_comedyadv_show_inclusive', true );
		$show_schedule  = (array) get_post_meta( $show_id, '_comedyadv_show_schedule', true );
		$show_schedule  = array_values( array_filter( $show_schedule, function( $r ) { return ! empty( $r['time'] ) || ! empty( $r['title'] ); } ) );
		$show_faq       = (array) get_post_meta( $show_id, '_comedyadv_show_faq', true );
		$show_faq       = array_values( array_filter( $show_faq, function( $r ) { return ! empty( $r['q'] ); } ) );
		$show_doors     = get_post_meta( $show_id, '_comedyadv_show_doors', true );
		$show_venue        = get_post_meta( $show_id, '_comedyadv_show_location', true );
		$show_venue_url    = get_post_meta( $show_id, '_comedyadv_show_venue_url', true );
		$show_address_l1   = get_post_meta( $show_id, '_comedyadv_show_address_line1', true );
		$show_address_l2   = get_post_meta( $show_id, '_comedyadv_show_address_line2', true );
		$show_eyebrow  = get_post_meta( $show_id, '_comedyadv_show_eyebrow', true );
		$show_lead     = get_post_meta( $show_id, '_comedyadv_show_lead', true );
		$show_image    = comedyadv_image_url( $show_id, 'large' );
		if ( ! $show_image ) {
			$show_image = $loc_image;
		}
		$comedian_ids   = (array) get_post_meta( $show_id, '_comedyadv_show_comedians', true );
		$comedian_ids   = array_filter( array_map( 'intval', $comedian_ids ) );
		if ( $comedian_ids ) {
			$show_comedians = get_posts( array( 'post_type' => 'comedian', 'post__in' => $comedian_ids, 'posts_per_page' => -1, 'orderby' => 'post__in' ) );
		}
		$date_long = comedyadv_show_long_date( $show_date );
	}

	$hero_image  = $show_image ?: $loc_image;
	$gallery = array();
	for ( $i = 1; $i <= 4; $i++ ) {
		$att_id = (int) get_post_meta( $id, '_comedyadv_gallery_' . $i, true );
		if ( $att_id ) {
			$src = wp_get_attachment_image_src( $att_id, 'large' );
			if ( $src ) {
				$gallery[] = $src[0];
			}
		}
	}
?>

<!-- BREADCRUMBS -->
<div class="workshop-breadcrumbs">
	<div class="container">
		<?php echo comedyadv_breadcrumbs( array( array( 'Home', $home_url ), array( 'Locaties', $loc_url ), $city_name ) ); ?>
	</div>
</div>

<!-- HERO SPLIT: titel links, foto rechts (zelfde als workshop) -->
<div class="workshop-hero locatie-hero">
	<div class="container">
		<div class="workshop-hero__inner">

			<!-- LINKS: eyebrow + titel + info + button -->
			<div class="workshop-hero__body">
				<?php if ( $show_eyebrow ) : ?>
					<span class="workshop-hero__tag"><?php echo esc_html( $show_eyebrow ); ?></span>
				<?php endif; ?>
				<h1 class="workshop-hero__title"><?php echo wp_kses_post( $title_html ); ?></h1>
				<?php if ( $lead ) : ?>
					<p class="workshop-hero__lead"><?php echo esc_html( $lead ); ?></p>
				<?php endif; ?>

				<!-- Datum / tijd / locatie als inline meta -->
				<?php if ( $show_date || $show_time || $show_venue ) : ?>
				<div class="locatie-hero__meta">
					<?php if ( $show_date ) : ?>
						<span class="locatie-hero__meta-item">📅 <?php echo esc_html( $date_long ); ?></span>
					<?php endif; ?>
					<?php if ( $show_time ) : ?>
						<span class="locatie-hero__meta-item">🕐 <?php echo esc_html( $show_time ); ?></span>
					<?php endif; ?>
					<?php if ( $show_venue ) : ?>
						<span class="locatie-hero__meta-item">📍 <?php echo esc_html( $show_venue ); ?></span>
					<?php endif; ?>
				</div>
				<?php endif; ?>

				<div class="locatie-hero__actions">
					<button type="button" class="btn btn--primary workshop-hero__cta" data-reserveer-open
						data-show="<?php echo esc_attr( $show_title ); ?>"
						data-stad="<?php echo esc_attr( $city_name ); ?>"
						data-datum="<?php echo esc_attr( $date_long ); ?>"
						data-tijd="<?php echo esc_attr( $show_time ); ?>">
						Reserveer nu <span class="btn__arrow">&rarr;</span>
					</button>
					<button class="btn-share" onclick="if(navigator.share){navigator.share({title:document.title,url:location.href});}else{navigator.clipboard.writeText(location.href);this.textContent='Link gekopieerd!';}">
						<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M18 16c-.8 0-1.5.3-2 .8l-7.3-4.2c.1-.2.1-.4.1-.6s0-.4-.1-.6L16 7.2c.5.5 1.2.8 2 .8 1.7 0 3-1.3 3-3s-1.3-3-3-3-3 1.3-3 3c0 .2 0 .4.1.6L7.8 9.8C7.3 9.3 6.6 9 5.8 9c-1.7 0-3 1.3-3 3s1.3 3 3 3c.8 0 1.5-.3 2-.8l7.3 4.2c-.1.2-.1.3-.1.5 0 1.6 1.3 2.9 2.9 2.9s2.9-1.3 2.9-2.9-1.3-2.9-2.9-2.9z"/></svg>
						Deel dit evenement
					</button>
				</div>
			</div>

			<!-- RECHTS: foto slider -->
			<?php
			$slider_images = array();
			if ( $hero_image ) $slider_images[] = $hero_image;
			foreach ( $gallery as $g ) {
				if ( $g !== $hero_image ) $slider_images[] = $g;
			}
			?>
			<?php if ( $slider_images ) : ?>
			<div class="workshop-hero__photo reveal">
				<div class="locatie-slider">
					<div class="locatie-slider__track" data-slider-track>
						<?php foreach ( $slider_images as $img ) : ?>
						<div class="locatie-slider__slide">
							<img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( 'Comedyshow in ' . $city_name . ( $show_title ? ' — ' . $show_title : '' ) ); ?>" />
						</div>
						<?php endforeach; ?>
					</div>
					<?php if ( count( $slider_images ) > 1 ) : ?>
					<button class="locatie-slider__btn locatie-slider__btn--prev" data-slider-prev aria-label="Vorige foto">&#8592;</button>
					<button class="locatie-slider__btn locatie-slider__btn--next" data-slider-next aria-label="Volgende foto">&#8594;</button>
					<div class="locatie-slider__dots" data-slider-dots>
						<?php foreach ( $slider_images as $i => $img ) : ?>
						<button class="locatie-slider__dot<?php echo $i === 0 ? ' is-active' : ''; ?>" data-slider-dot="<?php echo $i; ?>"></button>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>
				</div>
			</div>
			<?php endif; ?>

		</div>
	</div>
</div>

<!-- MAIN: content + sidebar -->
<div class="event-main">
	<div class="container">
		<div class="event-main__inner">

			<!-- LINKER KOLOM: alle content -->
			<div class="event-main__body">

				<!-- Beschrijving -->
				<?php
				$loc_content = get_the_content();
				if ( $show_lead || $loc_content ) : ?>
				<div class="event-main__section">
					<?php if ( $show_lead ) : ?><p><?php echo esc_html( $show_lead ); ?></p><?php endif; ?>
					<?php if ( $loc_content ) : ?>
						<div class="event-content"><?php echo wp_kses_post( apply_filters( 'the_content', $loc_content ) ); ?></div>
					<?php elseif ( $show_content ) : ?>
						<div class="event-content"><?php echo wp_kses_post( wpautop( $show_content ) ); ?></div>
					<?php endif; ?>
				</div>
				<?php endif; ?>

				<!-- Verloop van de avond -->
				<?php if ( $show_schedule ) : ?>
				<div class="event-main__section">
					<h2 class="event-section__title">Verloop van de avond</h2>
					<div class="show-timeline">
						<?php foreach ( $show_schedule as $i => $step ) : ?>
						<div class="show-timeline__item">
							<div class="show-timeline__time"><?php echo esc_html( $step['time'] ); ?></div>
							<div class="show-timeline__marker">
								<span class="show-timeline__dot"></span>
								<?php if ( $i < count( $show_schedule ) - 1 ) : ?><span class="show-timeline__line"></span><?php endif; ?>
							</div>
							<div class="show-timeline__body">
								<strong class="show-timeline__title"><?php echo esc_html( $step['title'] ); ?></strong>
								<?php if ( ! empty( $step['desc'] ) ) : ?>
								<p class="show-timeline__desc"><?php echo esc_html( $step['desc'] ); ?></p>
								<?php endif; ?>
							</div>
						</div>
						<?php endforeach; ?>
					</div>
				</div>

					<div class="timeline-notice">
						<span class="timeline-notice__icon">⏰</span>
						<div>
							<strong class="timeline-notice__heading">Op tijd aanwezig? Dan zit je goed.</strong>
							<p>Onze shows beginnen stipt volgens planning.</p>
							<p>Heb je een comedydiner geboekt? Zorg dan dat je op tijd aanwezig bent. Loopt het diner ondanks je tijdige aankomst uit, dan wachten wij uiteraard op je.</p>
						</div>
					</div>
				<?php endif; ?>

				<!-- Menu -->
				<?php
				$show_menu = $show_id ? (array) get_post_meta( $show_id, '_comedyadv_show_menu', true ) : array();
				$show_menu = array_values( array_filter( $show_menu, function( $row ) { return ! empty( $row['name'] ); } ) );
				if ( $show_menu ) : ?>
				<div class="event-main__section">
					<h2 class="event-section__title">Menu</h2>
					<div class="menu-card">
						<div class="menu-card__inner">
							<?php foreach ( $show_menu as $i => $course ) : ?>
								<?php if ( $i > 0 ) : ?><div class="menu-card__divider"><span></span></div><?php endif; ?>
								<div class="menu-course">
									<span class="menu-course__name"><?php echo esc_html( $course['name'] ); ?></span>
									<?php if ( ! empty( $course['items'] ) ) :
										$dishes = array_filter( array_map( 'trim', explode( "\n", $course['items'] ) ) );
									?>
									<ul class="menu-course__items">
										<?php foreach ( $dishes as $dish ) : ?>
											<li><?php echo esc_html( $dish ); ?></li>
										<?php endforeach; ?>
									</ul>
									<?php endif; ?>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
				<?php endif; ?>

				<!-- Meer shows op deze locatie -->
				<?php
				$other_shows = array_filter( $all_city_shows, function( $s ) use ( $show_id ) {
					return (int) $s->ID !== (int) $show_id;
				} );
				if ( $other_shows ) : ?>
				<div class="event-main__section">
					<h2 class="event-section__title">Meer shows op deze locatie</h2>
					<div class="agenda-list">
						<?php foreach ( $other_shows as $s ) :
							$d    = get_post_meta( $s->ID, '_comedyadv_show_date', true );
							$t    = get_post_meta( $s->ID, '_comedyadv_show_time', true );
							$p    = get_post_meta( $s->ID, '_comedyadv_show_price', true );
							$v    = get_post_meta( $s->ID, '_comedyadv_show_location', true );
							$is_p = $d < $today;
							$meta = array_filter( array( $v, $t, ( $is_p ? '' : $p ) ) );
						?>
						<article class="agenda-item" <?php if ( $is_p ) echo 'style="opacity:0.7;"'; ?>>
							<div class="agenda-item__date">
								<div class="agenda-item__day"><?php echo esc_html( comedyadv_show_day( $d ) ); ?></div>
								<div class="agenda-item__month"><?php echo esc_html( comedyadv_show_month( $d ) ); ?></div>
							</div>
							<div class="agenda-item__info">
								<h4><?php echo esc_html( $s->post_title ); ?></h4>
								<?php if ( $meta ) : ?>
									<p class="agenda-item__meta"><?php echo esc_html( implode( ' • ', $meta ) ); ?><?php if ( $is_p ) echo ' &bull; geweest'; ?></p>
								<?php endif; ?>
							</div>
							<?php if ( ! $is_p ) : ?>
								<button type="button" class="btn btn--primary" data-reserveer-open
									data-show="<?php echo esc_attr( $s->post_title ); ?>"
									data-stad="<?php echo esc_attr( $city_name ); ?>"
									data-datum="<?php echo esc_attr( comedyadv_show_long_date( $d ) ); ?>"
									data-tijd="<?php echo esc_attr( $t ); ?>">Tickets</button>
							<?php endif; ?>
						</article>
						<?php endforeach; ?>
					</div>
				</div>
				<?php endif; ?>

				<!-- Locatie / Maps -->
				<?php if ( $show_address_l1 || $show_address_l2 ) :
					$maps_q = urlencode( trim( $show_venue . ' ' . $show_address_l1 . ' ' . $show_address_l2 ) );
				?>
				<div class="event-main__section">
					<h2 class="event-section__title">Locatie</h2>
					<div class="location-widget">
						<?php if ( $show_venue ) : ?><p class="location-widget__venue"><?php echo esc_html( $show_venue ); ?></p><?php endif; ?>
						<?php if ( $show_address_l1 ) : ?><p class="location-widget__addr"><?php echo esc_html( $show_address_l1 ); ?></p><?php endif; ?>
						<?php if ( $show_address_l2 ) : ?><p class="location-widget__addr"><?php echo esc_html( $show_address_l2 ); ?></p><?php endif; ?>
						<p class="location-widget__label">Hoe wil je er komen?</p>
						<div class="location-widget__modes">
							<a class="travel-mode" href="https://www.google.com/maps/dir/?api=1&destination=<?php echo $maps_q; ?>&travelmode=driving" target="_blank" rel="noopener"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.9 7c-.2-.6-.8-1-1.4-1H6.5c-.7 0-1.2.4-1.5 1L3 13v8c0 .6.4 1 1 1h1c.6 0 1-.4 1-1v-1h12v1c0 .6.4 1 1 1h1c.6 0 1-.4 1-1v-8l-2.1-6zM6.5 17c-.8 0-1.5-.7-1.5-1.5S5.7 14 6.5 14s1.5.7 1.5 1.5S7.3 17 6.5 17zm11 0c-.8 0-1.5-.7-1.5-1.5s.7-1.5 1.5-1.5 1.5.7 1.5 1.5-.7 1.5-1.5 1.5zM5 12l1.5-4.5h11L19 12H5z"/></svg>Auto</a>
							<a class="travel-mode" href="https://www.google.com/maps/dir/?api=1&destination=<?php echo $maps_q; ?>&travelmode=transit" target="_blank" rel="noopener"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2c-4.4 0-8 .5-8 4v9.5C4 17.4 5.6 19 7.5 19L6 20.5v.5h12v-.5L16.5 19c1.9 0 3.5-1.6 3.5-3.5V6c0-3.5-3.6-4-8-4zm0 14c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm6-7H6V6h12v3z"/></svg>OV</a>
							<a class="travel-mode" href="https://www.google.com/maps/dir/?api=1&destination=<?php echo $maps_q; ?>&travelmode=bicycling" target="_blank" rel="noopener"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M15.5 5.5c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zM5 12c-2.8 0-5 2.2-5 5s2.2 5 5 5 5-2.2 5-5-2.2-5-5-5zm0 8.5c-1.9 0-3.5-1.6-3.5-3.5S3.1 13.5 5 13.5s3.5 1.6 3.5 3.5S6.9 20.5 5 20.5zm5.8-10l2.4-2.4.8.8c1.3 1.3 3 2.1 5.1 2.1V11c-1.5 0-2.7-.6-3.6-1.5l-1.9-1.9c-.5-.4-1-.6-1.6-.6s-1.1.2-1.4.6L7.8 10c-.4.4-.6.9-.6 1.4 0 .6.2 1.1.6 1.4L11 15v5h2v-6.5l-2.2-2zM19 12c-2.8 0-5 2.2-5 5s2.2 5 5 5 5-2.2 5-5-2.2-5-5-5zm0 8.5c-1.9 0-3.5-1.6-3.5-3.5s1.6-3.5 3.5-3.5 3.5 1.6 3.5 3.5-1.6 3.5-3.5 3.5z"/></svg>Fiets</a>
							<a class="travel-mode" href="https://www.google.com/maps/dir/?api=1&destination=<?php echo $maps_q; ?>&travelmode=walking" target="_blank" rel="noopener"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M13.5 5.5c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zM9.8 8.9L7 23h2.1l1.8-8 2.1 2v6h2v-7.5l-2.1-2 .6-3C14.8 12 16.8 13 19 13v-2c-1.9 0-3.5-1-4.3-2.4l-1-1.6c-.4-.6-1-1-1.7-1-.3 0-.5.1-.8.1L6 8.3V13h2V9.6l1.8-.7"/></svg>Lopen</a>
						</div>
						<div class="location-widget__map">
							<iframe loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://www.google.com/maps?q=<?php echo $maps_q; ?>&output=embed" allowfullscreen></iframe>
						</div>
					</div>
				</div>
				<?php endif; ?>

				<!-- FAQ -->
				<?php if ( $show_faq ) : ?>
				<div class="event-main__section">
					<h2 class="event-section__title">Veelgestelde vragen</h2>
					<div class="show-faq">
						<?php foreach ( $show_faq as $faq_item ) : ?>
						<details class="faq-item">
							<summary class="faq-item__question"><?php echo esc_html( $faq_item['q'] ); ?></summary>
							<div class="faq-item__answer"><?php echo wp_kses_post( wpautop( $faq_item['a'] ) ); ?></div>
						</details>
						<?php endforeach; ?>
					</div>
				</div>
				<?php endif; ?>

			</div><!-- /.event-main__body -->

			<!-- RECHTER KOLOM: sticky ticket + info -->
			<div class="event-main__sidebar">

				<!-- Ticket kaart -->
				<div class="event-ticket-card">
					<?php if ( $show_date ) :
						$is_past = $show_date < $today; ?>
						<div class="ticket-status <?php echo $is_past ? 'ticket-status--past' : 'ticket-status--available'; ?>">
							<?php if ( $is_past ) : ?>
								<span>&#10005;</span> Uitverkocht / verlopen
							<?php else : ?>
								<span>&#9679;</span> Aanvraag mogelijk
							<?php endif; ?>
						</div>
					<?php endif; ?>
					<?php if ( $show_price ) : ?>
						<div class="event-ticket-card__price"><?php echo esc_html( $show_price ); ?></div>
					<?php endif; ?>
					<ul class="event-info-list">
						<?php if ( $show_date ) : ?>
						<li class="event-info-list__item">
							<svg class="event-info-list__icon" viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/></svg>
							<div><span class="event-info-list__label">Datum</span><span class="event-info-list__value"><?php echo esc_html( $date_long ); ?></span></div>
						</li>
						<?php endif; ?>
						<?php if ( $show_time ) : ?>
						<li class="event-info-list__item">
							<svg class="event-info-list__icon" viewBox="0 0 24 24"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67V7z"/></svg>
							<div><span class="event-info-list__label">Aanvang</span><span class="event-info-list__value"><?php echo esc_html( $show_time ); ?></span></div>
						</li>
						<?php endif; ?>
						<?php if ( $show_doors ) : ?>
						<li class="event-info-list__item">
							<svg class="event-info-list__icon" viewBox="0 0 24 24"><path d="M19 19V4.5L14.5 0H6c-1.1 0-2 .9-2 2v17H1v2h22v-2h-4zM14 2l4 4h-4V2zM6 2h6v5h5v12H6V2z"/></svg>
							<div><span class="event-info-list__label">Deuren open</span><span class="event-info-list__value"><?php echo esc_html( $show_doors ); ?></span></div>
						</li>
						<?php endif; ?>
						<?php if ( $show_duration ) : ?>
						<li class="event-info-list__item">
							<svg class="event-info-list__icon" viewBox="0 0 24 24"><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm4.2 14.2L11 13V7h1.5v5.2l4.5 2.7-.8 1.3z"/></svg>
							<div><span class="event-info-list__label">Tijdsduur</span><span class="event-info-list__value"><?php echo esc_html( $show_duration ); ?></span></div>
						</li>
						<?php endif; ?>
						<?php if ( $show_venue ) : ?>
						<li class="event-info-list__item">
							<svg class="event-info-list__icon" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
							<div><span class="event-info-list__label">Zaal</span><span class="event-info-list__value"><?php echo esc_html( $show_venue ); ?></span></div>
						</li>
						<?php endif; ?>
						<?php if ( $show_inclusive ) :
							$inclusive_items = array_filter( array_map( 'trim', explode( "\n", $show_inclusive ) ) );
						?>
						<li class="event-info-list__item event-info-list__item--inclusive">
							<div>
								<span class="event-info-list__label">Inclusief</span>
								<ul class="event-inclusive-list">
									<?php foreach ( $inclusive_items as $inc_item ) : ?>
									<li>
										<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>
										<?php echo esc_html( $inc_item ); ?>
									</li>
									<?php endforeach; ?>
								</ul>
							</div>
						</li>
						<?php endif; ?>
					</ul>
					<button type="button" class="btn btn--primary" style="width:100%;justify-content:center;margin-top:20px;" data-reserveer-open
						data-show="<?php echo esc_attr( $show_title ); ?>"
						data-stad="<?php echo esc_attr( $city_name ); ?>"
						data-datum="<?php echo esc_attr( $date_long ); ?>"
						data-tijd="<?php echo esc_attr( $show_time ); ?>">
						Reserveer nu <span class="btn__arrow">&rarr;</span>
					</button>
					<p class="ticket-no-obligation">✓ Vrijblijvende aanvraag — geen verplichtingen</p>
					<button class="btn-share" onclick="if(navigator.share){navigator.share({title:document.title,url:location.href});}else{navigator.clipboard.writeText(location.href);this.textContent='Link gekopieerd!';}">
						<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M18 16c-.8 0-1.5.3-2 .8l-7.3-4.2c.1-.2.1-.4.1-.6s0-.4-.1-.6L16 7.2c.5.5 1.2.8 2 .8 1.7 0 3-1.3 3-3s-1.3-3-3-3-3 1.3-3 3c0 .2 0 .4.1.6L7.8 9.8C7.3 9.3 6.6 9 5.8 9c-1.7 0-3 1.3-3 3s1.3 3 3 3c.8 0 1.5-.3 2-.8l7.3 4.2c-.1.2-.1.3-.1.5 0 1.6 1.3 2.9 2.9 2.9s2.9-1.3 2.9-2.9-1.3-2.9-2.9-2.9z"/></svg>
						Deel dit evenement
					</button>
				</div>

				<!-- Optredende comedians -->
				<?php if ( $show_comedians ) : ?>
				<div class="event-sidebar-card">
					<h3 class="event-sidebar-card__title">Optredende comedians</h3>
					<div class="event-comedians">
						<?php foreach ( $show_comedians as $comedian ) :
							$ctag   = get_post_meta( $comedian->ID, '_comedyadv_tag', true );
							$cimage = comedyadv_image_url( $comedian->ID, 'thumbnail' );
						?>
						<a class="event-comedian" href="<?php echo esc_url( get_permalink( $comedian->ID ) ); ?>">
							<?php if ( $cimage ) : ?>
								<img class="event-comedian__img" src="<?php echo esc_url( $cimage ); ?>" alt="<?php echo esc_attr( $comedian->post_title ); ?>" />
							<?php endif; ?>
							<div class="event-comedian__info">
								<strong><?php echo esc_html( $comedian->post_title ); ?></strong>
								<?php if ( $ctag ) : ?><span><?php echo esc_html( $ctag ); ?></span><?php endif; ?>
							</div>
							<svg class="event-comedian__arrow" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
						</a>
						<?php endforeach; ?>
					</div>
				</div>
				<?php endif; ?>

			</div><!-- /.event-main__sidebar -->

		</div><!-- /.event-main__inner -->
	</div><!-- /.container -->
</div><!-- /.event-main -->

<!-- REVIEWS -->
<?php
$locatie_reviews = (array) get_post_meta( $id, '_comedyadv_locatie_reviews', true );
$locatie_reviews = array_values( array_filter( $locatie_reviews, function( $r ) { return ! empty( $r['text'] ); } ) );
if ( $locatie_reviews ) :
?>
<section class="locatie-reviews">
	<div class="container">
		<div class="locatie-reviews__head">
			<span class="section-head__eyebrow">Ervaringen</span>
			<h2>Wat bezoekers zeggen</h2>
		</div>
		<div class="locatie-reviews__grid">
			<?php foreach ( $locatie_reviews as $review ) : ?>
			<div class="locatie-review reveal">
				<div class="locatie-review__stars">★★★★★</div>
				<p>"<?php echo esc_html( $review['text'] ); ?>"</p>
				<?php if ( ! empty( $review['author'] ) ) : ?>
				<span class="locatie-review__author">— <?php echo esc_html( $review['author'] ); ?></span>
				<?php endif; ?>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<!-- ANDERE SHOWS -->
<?php
$other_city_shows = get_posts( array(
	'post_type'      => 'show',
	'posts_per_page' => 4,
	'meta_key'       => '_comedyadv_show_date',
	'orderby'        => 'meta_value',
	'order'          => 'ASC',
	'meta_query'     => array(
		'relation' => 'AND',
		array( 'key' => '_comedyadv_show_date', 'value' => $today, 'compare' => '>=' ),
		array( 'key' => '_comedyadv_show_city', 'value' => $id, 'compare' => '!=' ),
	),
) );
if ( $other_city_shows ) : ?>
<section class="section section--grey">
	<div class="container">
		<div class="section-head">
			<span class="section-head__eyebrow">Meer comedy</span>
			<h2>Meer shows</h2>
		</div>
		<div class="city-grid">
			<?php foreach ( $other_city_shows as $os ) :
				$os_date     = get_post_meta( $os->ID, '_comedyadv_show_date', true );
				$os_time     = get_post_meta( $os->ID, '_comedyadv_show_time', true );
				$os_price    = get_post_meta( $os->ID, '_comedyadv_show_price', true );
				$os_city_id  = (int) get_post_meta( $os->ID, '_comedyadv_show_city', true );
				$os_city     = $os_city_id ? get_the_title( $os_city_id ) : '';
				$os_city_url = $os_city_id ? get_permalink( $os_city_id ) : '';
				$os_date_fmt = $os_date ? comedyadv_show_long_date( $os_date ) : '';
				$os_img      = comedyadv_image_url( $os->ID, 'large' );
				if ( ! $os_img && $os_city_id ) {
					$os_img = comedyadv_image_url( $os_city_id, 'large' );
				}
			?>
			<a href="<?php echo esc_url( $os_city_url ?: '#' ); ?>" class="city-card reveal">
				<?php if ( $os_img ) : ?>
					<div class="city-card__bg" style="background-image:url('<?php echo esc_url( $os_img ); ?>')"></div>
				<?php endif; ?>
				<div class="city-card__inner">
					<?php if ( $os_city ) : ?>
						<div class="city-card__name"><?php echo esc_html( $os_city ); ?></div>
					<?php endif; ?>
					<?php if ( $os_date_fmt || $os_time ) : ?>
						<div class="city-card__sub"><?php echo esc_html( implode( ' · ', array_filter( array( $os_date_fmt, $os_time ) ) ) ); ?></div>
					<?php endif; ?>
					<div class="city-card__cta">Bekijk show</div>
				</div>
			</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<!-- CTA -->
<section class="section">
	<div class="container">
		<div class="cta-banner">
			<h2>Vragen over een show in <?php echo esc_html( $city_name ); ?>?</h2>
			<p>Neem contact op.</p>
			<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--dark">Contact <span class="btn__arrow">&rarr;</span></a>
		</div>
	</div>
</section>

<?php
// ── JSON-LD Structured Data ──────────────────────────────────────────────────
$schema_items = array();

// BreadcrumbList
$schema_items[] = array(
	'@type'           => 'BreadcrumbList',
	'itemListElement' => array(
		array( '@type' => 'ListItem', 'position' => 1, 'name' => 'Home',     'item' => home_url( '/' ) ),
		array( '@type' => 'ListItem', 'position' => 2, 'name' => 'Locaties', 'item' => $loc_url ),
		array( '@type' => 'ListItem', 'position' => 3, 'name' => $city_name, 'item' => get_permalink() ),
	),
);

// Event schema
if ( $show_id && $show_date ) {
	$start_dt = $show_date . ( $show_time ? 'T' . $show_time . ':00' : '' );
	$event = array(
		'@type'               => 'Event',
		'name'                => $show_title ?: ( 'Comedyshow in ' . $city_name ),
		'startDate'           => $start_dt,
		'eventStatus'         => 'https://schema.org/EventScheduled',
		'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
		'organizer'           => array(
			'@type' => 'Organization',
			'name'  => 'Comedy Adventure',
			'url'   => home_url( '/' ),
		),
	);

	$desc_raw = $show_lead ?: $lead ?: wp_strip_all_tags( get_the_content() );
	if ( $desc_raw ) {
		$event['description'] = mb_substr( wp_strip_all_tags( $desc_raw ), 0, 300 );
	}
	if ( $hero_image ) {
		$event['image'] = $hero_image;
	}
	if ( $show_venue || $show_address_l1 ) {
		$place = array( '@type' => 'Place' );
		if ( $show_venue ) {
			$place['name'] = $show_venue;
		}
		$addr = array( '@type' => 'PostalAddress', 'addressCountry' => 'NL' );
		if ( $show_address_l1 ) $addr['streetAddress']   = $show_address_l1;
		if ( $show_address_l2 ) $addr['addressLocality'] = $show_address_l2;
		$place['address'] = $addr;
		$event['location'] = $place;
	}
	if ( $show_price ) {
		$price_clean = str_replace( ',', '.', preg_replace( '/[^0-9,.]/', '', $show_price ) );
		$event['offers'] = array(
			'@type'         => 'Offer',
			'price'         => $price_clean ?: $show_price,
			'priceCurrency' => 'EUR',
			'availability'  => ( $show_date >= $today )
				? 'https://schema.org/InStock'
				: 'https://schema.org/SoldOut',
			'url'           => get_permalink(),
		);
	}
	if ( $show_comedians ) {
		$performers = array();
		foreach ( $show_comedians as $comedian ) {
			$performers[] = array(
				'@type' => 'Person',
				'name'  => $comedian->post_title,
				'url'   => get_permalink( $comedian->ID ),
			);
		}
		$event['performer'] = count( $performers ) === 1 ? $performers[0] : $performers;
	}
	$schema_items[] = $event;
}

// FAQPage schema
if ( ! empty( $show_faq ) ) {
	$faq_entities = array();
	foreach ( $show_faq as $faq_item ) {
		if ( ! empty( $faq_item['q'] ) && ! empty( $faq_item['a'] ) ) {
			$faq_entities[] = array(
				'@type'          => 'Question',
				'name'           => $faq_item['q'],
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => wp_strip_all_tags( $faq_item['a'] ),
				),
			);
		}
	}
	if ( $faq_entities ) {
		$schema_items[] = array(
			'@type'      => 'FAQPage',
			'mainEntity' => $faq_entities,
		);
	}
}
?>
<script type="application/ld+json">
<?php echo wp_json_encode(
	array( '@context' => 'https://schema.org', '@graph' => $schema_items ),
	JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
); ?>
</script>

<?php endwhile; ?>
<?php get_footer(); ?>
