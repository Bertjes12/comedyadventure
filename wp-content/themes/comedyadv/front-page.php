<?php
/**
 * Homepage template.
 */
get_header();

$contact_url   = comedyadv_url( 'contact' );
$aanbod_url    = comedyadv_url( 'aanbod' );
$workshops_url = comedyadv_archive_url( 'workshop' );
$locaties_url  = comedyadv_archive_url( 'locatie' );

// Show feed: up to 6 shows, upcoming first (asc), then recent past to fill.
$today = date( 'Y-m-d' );
$upcoming = get_posts( array(
	'post_type'      => 'show',
	'posts_per_page' => 6,
	'meta_key'       => '_comedyadv_show_date',
	'orderby'        => 'meta_value',
	'order'          => 'ASC',
	'meta_query'     => array(
		array( 'key' => '_comedyadv_show_date', 'value' => $today, 'compare' => '>=' ),
	),
) );
$shows = $upcoming;
if ( count( $shows ) < 6 ) {
	$past = get_posts( array(
		'post_type'      => 'show',
		'posts_per_page' => 6 - count( $shows ),
		'meta_key'       => '_comedyadv_show_date',
		'orderby'        => 'meta_value',
		'order'          => 'DESC',
		'meta_query'     => array(
			array( 'key' => '_comedyadv_show_date', 'value' => $today, 'compare' => '<' ),
		),
	) );
	$shows = array_merge( $shows, $past );
}

// Cities grid: query the locatie CPT.
$cities = get_posts( array( 'post_type' => 'locatie', 'posts_per_page' => 6, 'orderby' => 'menu_order title', 'order' => 'ASC' ) );

// Aanbod carousel: query the aanbod CPT.
$aanbod_items = get_posts( array( 'post_type' => 'aanbod', 'posts_per_page' => 8, 'orderby' => 'menu_order date', 'order' => 'ASC' ) );
?>

<!-- HERO -->
<section class="hero">
	<div class="container hero__content">
		<span class="hero__eyebrow">Live &bull; Stand-up &bull; Op maat</span>
		<h1 class="hero__title">Het ultieme entertainment <span>voor elk event.</span></h1>
		<p class="hero__lead">Bij ons zie je Stand up Comedy shows zoals die bedoeld zijn: Snel, scherp, en hilarisch!</p>
		<div class="hero__actions">
			<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--primary">Boek een show <span class="btn__arrow">&rarr;</span></a>
			<a href="<?php echo esc_url( $aanbod_url ); ?>" class="btn btn--ghost">Bekijk ons aanbod</a>
		</div>
	</div>
</section>

<!-- MARQUEE -->
<div class="marquee" aria-hidden="true">
	<div class="marquee__track">
		<span class="marquee__item">Bedrijfsfeesten</span>
		<span class="marquee__item">Diners</span>
		<span class="marquee__item">Personeelsuitjes</span>
		<span class="marquee__item">Bruiloften</span>
		<span class="marquee__item">Evenementen</span>
		<span class="marquee__item">Op maat</span>
	</div>
</div>

<!-- INTRO / STATS -->
<section class="section">
	<div class="container">
		<div class="feature-split feature-split--align-title">
			<div class="feature-split__media feature-split__media--natural reveal">
				<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/05/comedy-diner-Hoorn.png' ) ); ?>" alt="Comedy Adventure live op het podium" />
			</div>
			<div class="intro-block reveal">
				<h2>Comedy &amp; cabaret op maat voor ieder evenement</h2>

				<h2>Wie wij zijn</h2>
				<p>Comedy Adventure draait al jaren mee in de wereld van stand-up comedy en cabaret. Met scherpe comedians, sterke verhalen en precies de juiste dosis humor verzorgen wij avonden die het publiek nog lang bijblijven.</p>
				<p>Of het nu gaat om een personeelsfeest, evenement of compleet verzorgd comedy diner: wij brengen ervaren comedians en cabaretiers die sfeer maken en iedereen moeiteloos meenemen in een avond vol entertainment.</p>
				<p>Dankzij ons netwerk van Nederlandse topcomedians ben je verzekerd van humor, energie en kwaliteit.</p>

				<h3>Big Comedy Show</h3>
				<p>Onze <a href="<?php echo esc_url( home_url( '/aanbod/comedy-show/' ) ); ?>">Big Comedy Show</a> is perfect voor een avondvullend programma, met meerdere topcomedians en een professionele host die de avond aan elkaar praat.</p>

				<h3>Comedy Diner</h3>
				<p>Liever lachen &eacute;n lekker eten? Met het <a href="<?php echo esc_url( home_url( '/aanbod/comedy-dinershow/' ) ); ?>">Comedy Diner</a> combineer je een uitgebreid diner met een interactieve comedyshow vol humor.</p>

				<h3>Comedy op maat</h3>
				<p>Zoek je iets unieks voor jouw organisatie of groep, dan bieden wij ook comedy op maat: een <a href="<?php echo esc_url( home_url( '/aanbod/comedy-op-maat/' ) ); ?>">gepersonaliseerde show</a> met grappen en sketches speciaal geschreven op basis van jouw wensen en input.</p>

				<p>Neem contact met ons op en ontdek hoe wij van jouw evenement een hilarisch succes maken!</p>
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" class="btn btn--primary">Neem contact op</a>
			</div>
		</div>
	</div>
</section>

<!-- AANBOD CARDS -->
<section class="section section--grey">
	<div class="container">
		<div class="section-head">
			<span class="section-head__eyebrow">Ons aanbod</span>
			<h2>Voor elk event de perfecte show</h2>
			<p class="section-head__lead">Van een korte set tijdens het diner tot een volledig avondvullend programma. Kies het format dat bij jouw evenement past.</p>
		</div>
		<div class="card-carousel" data-card-carousel>
			<button type="button" class="card-carousel__btn card-carousel__btn--prev" data-carousel-prev aria-label="Vorige">&lsaquo;</button>
			<div class="card-carousel__viewport">
				<div class="card-carousel__track" data-carousel-track>
					<?php foreach ( $aanbod_items as $item ) :
						$item_id    = $item->ID;
						$image      = comedyadv_image_url( $item_id, 'medium_large' );
						$lead       = get_post_meta( $item_id, '_comedyadv_aanbod_lead', true );
						$card_extra = get_post_meta( $item_id, '_comedyadv_aanbod_card_extra', true );
					?>
						<a class="card reveal" href="<?php echo esc_url( get_permalink( $item ) ); ?>">
							<div class="card__media">
								<?php if ( $image ) : ?>
									<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $item->post_title ); ?>" />
								<?php endif; ?>
							</div>
							<div class="card__body">
								<?php if ( $card_extra ) : ?>
									<span class="card__tag"><?php echo esc_html( $card_extra ); ?></span>
								<?php endif; ?>
								<h3 class="card__title"><?php echo esc_html( $item->post_title ); ?></h3>
								<?php if ( $lead ) : ?>
									<p class="card__text"><?php echo esc_html( wp_trim_words( $lead, 24, '...' ) ); ?></p>
								<?php endif; ?>
								<span class="card__link">Meer info</span>
							</div>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
			<button type="button" class="card-carousel__btn card-carousel__btn--next" data-carousel-next aria-label="Volgende">&rsaquo;</button>
		</div>
	</div>
</section>

<!-- AGENDA / SHOWS -->
<section class="section">
	<div class="container">
		<div class="section-head">
			<span class="section-head__eyebrow">Agenda</span>
			<h2>Onze shows</h2>
			<p class="section-head__lead">Wil je live een show meemaken? Bekijk onze publieke voorstellingen door heel Nederland. Klik op een show voor de bijbehorende locatie.</p>
		</div>
		<?php if ( $shows ) : ?>
			<div class="agenda-list">
				<?php foreach ( $shows as $show ) :
					$sid       = $show->ID;
					$date      = get_post_meta( $sid, '_comedyadv_show_date', true );
					$venue     = get_post_meta( $sid, '_comedyadv_show_location', true );
					$time      = get_post_meta( $sid, '_comedyadv_show_time', true );
					$price     = get_post_meta( $sid, '_comedyadv_show_price', true );
					$city_id   = (int) get_post_meta( $sid, '_comedyadv_show_city', true );
					$city      = $city_id ? get_post( $city_id ) : null;
					$city_lbl  = $city ? $city->post_title : '';
					$city_url  = $city ? get_permalink( $city ) : '';
					$is_past   = $date < $today;
					$meta_parts = array_filter( array( $city_lbl ? $city_lbl : $venue, $time, $price ) );
				?>
					<article class="agenda-item reveal" <?php if ( $is_past ) echo 'style="opacity:0.7;"'; ?>>
						<div class="agenda-item__date">
							<div class="agenda-item__day"><?php echo esc_html( comedyadv_show_day( $date ) ); ?></div>
							<div class="agenda-item__month"><?php echo esc_html( comedyadv_show_month( $date ) ); ?></div>
						</div>
						<div class="agenda-item__info">
							<h4><?php if ( $city_url ) : ?>
								<a href="<?php echo esc_url( $city_url ); ?>" style="color:inherit;text-decoration:none;border-bottom:1px solid transparent;" onmouseover="this.style.borderColor='var(--orange)'" onmouseout="this.style.borderColor='transparent'"><?php echo esc_html( $show->post_title ); ?></a>
							<?php else : echo esc_html( $show->post_title ); endif; ?></h4>
							<?php if ( $meta_parts ) : ?>
								<p class="agenda-item__meta"><?php echo esc_html( implode( ' • ', $meta_parts ) ); ?><?php if ( $is_past ) echo ' &bull; geweest'; ?></p>
							<?php endif; ?>
						</div>
						<?php if ( $is_past ) : ?>
							<?php if ( $city_url ) : ?>
								<a href="<?php echo esc_url( $city_url ); ?>" class="btn btn--dark">Bekijk locatie</a>
							<?php endif; ?>
						<?php else : ?>
							<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--primary">Tickets</a>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<p>Nog geen shows toegevoegd.</p>
		<?php endif; ?>
	</div>
</section>

<!-- LOCATIES -->
<section class="section section--dark">
	<div class="container">
		<div class="section-head">
			<span class="section-head__eyebrow">Door heel Nederland</span>
			<h2>Comedy in jouw stad</h2>
			<p class="section-head__lead">Wij organiseren shows op locatie. Van Amsterdam tot Groningen &mdash; jij kiest, wij brengen de comedy.</p>
		</div>
		<?php if ( $cities ) : ?>
			<div class="city-grid">
				<?php foreach ( $cities as $city ) :
					$bg = comedyadv_image_url( $city->ID, 'large' );
				?>
					<a href="<?php echo esc_url( get_permalink( $city ) ); ?>" class="city-card">
						<?php if ( $bg ) : ?>
							<div class="city-card__bg" style="background-image:url('<?php echo esc_url( $bg ); ?>')"></div>
						<?php endif; ?>
						<div class="city-card__inner">
							<div class="city-card__name"><?php echo esc_html( $city->post_title ); ?></div>
							<div class="city-card__cta">Bekijk shows</div>
						</div>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>

<!-- CTA -->
<section class="section">
	<div class="container">
		<div class="cta-banner reveal">
			<h2>Klaar voor een onvergetelijke avond?</h2>
			<p>Vertel ons over je evenement en wij sturen binnen 24 uur een voorstel op maat.</p>
			<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--dark">Plan jouw show <span class="btn__arrow">&rarr;</span></a>
		</div>
	</div>
</section>

<?php get_footer(); ?>
