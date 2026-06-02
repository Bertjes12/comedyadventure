<?php
/**
 * Homepage — Gen Z redesign.
 */
get_header();

$contact_url   = comedyadv_url( 'contact' );
$aanbod_url    = comedyadv_url( 'aanbod' );
$workshops_url = comedyadv_archive_url( 'workshop' );
$locaties_url  = comedyadv_archive_url( 'locatie' );

$today = date( 'Y-m-d' );
$upcoming = get_posts( array(
	'post_type'      => 'show',
	'posts_per_page' => 5,
	'meta_key'       => '_comedyadv_show_date',
	'orderby'        => 'meta_value',
	'order'          => 'ASC',
	'meta_query'     => array(
		array( 'key' => '_comedyadv_show_date', 'value' => $today, 'compare' => '>=' ),
	),
) );
$shows = $upcoming;
if ( count( $shows ) < 5 ) {
	$past = get_posts( array(
		'post_type'      => 'show',
		'posts_per_page' => 5 - count( $shows ),
		'meta_key'       => '_comedyadv_show_date',
		'orderby'        => 'meta_value',
		'order'          => 'DESC',
		'meta_query'     => array(
			array( 'key' => '_comedyadv_show_date', 'value' => $today, 'compare' => '<' ),
		),
	) );
	$shows = array_merge( $shows, $past );
}

$cities       = get_posts( array( 'post_type' => 'locatie', 'posts_per_page' => 6, 'orderby' => 'menu_order title', 'order' => 'ASC' ) );
$aanbod_items = get_posts( array( 'post_type' => 'aanbod', 'posts_per_page' => 6, 'orderby' => 'menu_order date', 'order' => 'ASC' ) );
$comedians    = get_posts( array( 'post_type' => 'comedian', 'posts_per_page' => 4, 'orderby' => 'menu_order title', 'order' => 'ASC' ) );
?>

<!-- HERO -->
<?php
$hero_show  = ! empty( $shows ) ? $shows[0] : null;
$hero_date  = $hero_show ? get_post_meta( $hero_show->ID, '_comedyadv_show_date', true ) : '';
$hero_venue = $hero_show ? get_post_meta( $hero_show->ID, '_comedyadv_show_location', true ) : '';
$hero_city_id = $hero_show ? (int) get_post_meta( $hero_show->ID, '_comedyadv_show_city', true ) : 0;
$hero_city    = $hero_city_id ? get_post( $hero_city_id ) : null;
$hero_place   = $hero_city ? $hero_city->post_title : $hero_venue;
?>
<section class="hp-hero" id="hp-hero">
	<div class="hp-hero__orb hp-hero__orb--1" aria-hidden="true"></div>
	<div class="hp-hero__orb hp-hero__orb--2" aria-hidden="true"></div>
	<div class="hp-hero__noise"               aria-hidden="true"></div>

	<div class="container hp-hero__inner">

		<!-- Left: copy -->
		<div class="hp-hero__body">
			<div class="hp-hero__live-badge">
				<span class="hp-hero__live-dot" aria-hidden="true"></span>
				Live shows &bull; Door heel Nederland
			</div>

			<h1 class="hp-hero__title">
				Het ultieme<br>
				<em class="hp-hero__title-grad">entertainment</em><br>
				voor elk event.
			</h1>

			<p class="hp-hero__lead">Scherpe comedians, sterke verhalen en precies de juiste dosis humor — voor personeelsfeesten, diners, bruiloften en meer.</p>

			<div class="hp-hero__actions">
				<a href="<?php echo esc_url( home_url( '/boeken/' ) ); ?>" class="hp-hero__btn hp-hero__btn--primary js-magnetic">
					Boek een show
					<svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
						<path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</a>
				<a href="<?php echo esc_url( $aanbod_url ); ?>" class="hp-hero__btn hp-hero__btn--ghost js-magnetic">
					Bekijk ons aanbod
				</a>
			</div>

		</div>


	</div>
</section>


<!-- INTRO SPLIT -->
<section class="hp-intro">
	<div class="container hp-intro__inner">
		<div class="hp-intro__media reveal">
			<div class="hp-intro__image-wrap">
				<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/05/comedy-diner-Hoorn.png' ) ); ?>" alt="Comedy Adventure live op het podium" />
			</div>
		</div>
		<div class="hp-intro__text reveal">
			<span class="workshop-learn__eyebrow">Wie wij zijn</span>
			<h2 class="hp-intro__title">Comedy &amp; cabaret op maat</h2>
			<p>Met scherpe comedians, sterke verhalen en precies de juiste dosis humor verzorgen wij avonden die het publiek nog lang bijblijven.</p>
			<p>Of het nu gaat om een personeelsfeest, evenement of compleet verzorgd comedy diner — wij brengen ervaren comedians en cabaretiers die iedereen moeiteloos meenemen in een avond vol entertainment.</p>
			<div class="hp-intro__pills">
				<?php
				$pills_exclude = array( 'Wrap Up', 'Cabaret Diner' );
				foreach ( $aanbod_items as $item ) :
					if ( in_array( $item->post_title, $pills_exclude, true ) ) continue;
				?>
				<a class="workshop-pill" href="<?php echo esc_url( get_permalink( $item->ID ) ); ?>"><?php echo esc_html( $item->post_title ); ?></a>
				<?php endforeach; ?>
			</div>
			<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--primary" style="margin-top:8px;">
				Neem contact op <span class="btn__arrow">&rarr;</span>
			</a>
		</div>
	</div>
</section>

<!-- HOE WERKT HET -->
<section class="hp-how">
	<div class="container">
		<div class="hp-section-head">
			<div>
				<span class="section-head__eyebrow">Simpel &amp; snel</span>
				<h2 class="hp-section-head__title">Van idee naar<br>onvergetelijke avond</h2>
			</div>
		</div>
		<div class="hp-how__steps">
			<div class="hp-how__step reveal">
				<div class="hp-how__step-num">01</div>
				<div class="hp-how__step-body">
					<h3>Vertel ons over je evenement</h3>
					<p>Vul het contactformulier in — datum, locatie, aantal gasten en welk format je in gedachten hebt. Duurt minder dan 2 minuten.</p>
				</div>
			</div>
			<div class="hp-how__step reveal">
				<div class="hp-how__step-num">02</div>
				<div class="hp-how__step-body">
					<h3>Ontvang een voorstel op maat</h3>
					<p>Binnen 24 uur sturen wij een vrijblijvend voorstel — inclusief comedian, format, prijs en beschikbaarheid.</p>
				</div>
			</div>
			<div class="hp-how__step reveal">
				<div class="hp-how__step-num">03</div>
				<div class="hp-how__step-body">
					<h3>Geniet van de show</h3>
					<p>Wij regelen alles van A tot Z. Jij hoeft alleen maar te genieten samen met je gasten.</p>
				</div>
			</div>
		</div>
		<div style="text-align:center;margin-top:40px;">
			<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--primary">Start jouw aanvraag <span class="btn__arrow">&rarr;</span></a>
		</div>
	</div>
</section>

<!-- AANBOD -->
<?php if ( $aanbod_items ) : ?>
<section class="hp-aanbod">
	<div class="container">
		<div class="hp-section-head">
			<div>
				<span class="section-head__eyebrow">Ons aanbod</span>
				<h2 class="hp-section-head__title">Voor elk event<br>de perfecte show</h2>
			</div>
			<a href="<?php echo esc_url( $aanbod_url ); ?>" class="btn btn--ghost">Alle formats &rarr;</a>
		</div>
		<div class="workshops-grid">
			<?php foreach ( $aanbod_items as $item ) :
				$iid      = $item->ID;
				$image    = comedyadv_image_url( $iid, 'large' );
				$lead     = get_post_meta( $iid, '_comedyadv_aanbod_lead', true );
				$price_pp = get_post_meta( $iid, '_comedyadv_aanbod_price_pp', true );
			?>
			<a class="workshop-card reveal" href="<?php echo esc_url( get_permalink( $item ) ); ?>">
				<?php if ( $image ) : ?>
				<div class="workshop-card__image">
					<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $item->post_title ); ?>" />
				</div>
				<?php endif; ?>
				<div class="workshop-card__body">
					<h3 class="workshop-card__title"><?php echo esc_html( $item->post_title ); ?></h3>
					<?php if ( $lead ) : ?>
						<p class="workshop-card__lead"><?php echo esc_html( wp_trim_words( $lead, 16, '…' ) ); ?></p>
					<?php endif; ?>
					<?php if ( $price_pp ) : ?>
					<div class="workshop-card__footer">
						<span class="workshop-card__price"><?php echo esc_html( $price_pp ); ?><small>p.p.</small></span>
					</div>
					<?php endif; ?>
				</div>
			</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<!-- SHOWS AGENDA -->
<?php if ( $shows ) : ?>
<section class="hp-shows">
	<div class="container">
		<div class="hp-section-head">
			<div>
				<span class="section-head__eyebrow" style="color:#FF5500;">Agenda</span>
				<h2 class="hp-section-head__title" style="color:#fff;">Aankomende shows</h2>
			</div>
			<a href="<?php echo esc_url( home_url( '/agenda/' ) ); ?>" class="btn btn--ghost" style="border-color:rgba(255,255,255,0.25);color:#fff;">Volledige agenda &rarr;</a>
		</div>
		<div class="agenda-lineup">
			<?php foreach ( $shows as $show ) :
				$sid      = $show->ID;
				$date     = get_post_meta( $sid, '_comedyadv_show_date', true );
				$time     = get_post_meta( $sid, '_comedyadv_show_time', true );
				$venue    = get_post_meta( $sid, '_comedyadv_show_location', true );
				$price    = get_post_meta( $sid, '_comedyadv_show_price', true );
				$city_id  = (int) get_post_meta( $sid, '_comedyadv_show_city', true );
				$city     = $city_id ? get_post( $city_id ) : null;
				$city_lbl = $city ? $city->post_title : '';
				$city_url = $city ? get_permalink( $city ) : '';
				$is_past  = $date < $today;
				$meta     = array_filter( array( $city_lbl ?: $venue, $time, $is_past ? '' : $price ) );
			?>
			<article class="lineup-card reveal<?php echo $is_past ? ' lineup-card--past' : ''; ?>">
				<div class="lineup-card__date">
					<span class="lineup-card__day"><?php echo esc_html( comedyadv_show_day( $date ) ); ?></span>
					<span class="lineup-card__month"><?php echo esc_html( comedyadv_show_month( $date ) ); ?></span>
				</div>
				<div class="lineup-card__info">
					<h3 class="lineup-card__title">
						<?php if ( $city_url ) : ?>
							<a href="<?php echo esc_url( $city_url ); ?>"><?php echo esc_html( $show->post_title ); ?></a>
						<?php else : echo esc_html( $show->post_title ); endif; ?>
					</h3>
					<?php if ( $meta ) : ?>
						<p class="lineup-card__meta"><?php echo esc_html( implode( ' · ', $meta ) ); ?><?php if ( $is_past ) echo ' · geweest'; ?></p>
					<?php endif; ?>
				</div>
				<?php if ( ! $is_past ) : ?>
					<a href="<?php echo esc_url( $city_url ?: home_url( '/boeken/' ) ); ?>" class="btn btn--primary lineup-card__btn">Tickets</a>
				<?php elseif ( $city_url ) : ?>
					<a href="<?php echo esc_url( $city_url ); ?>" class="btn btn--ghost lineup-card__btn">Bekijk</a>
				<?php endif; ?>
			</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<!-- LOCATIES -->
<?php if ( $cities ) : ?>
<section class="hp-locaties">
	<div class="container">
		<div class="hp-section-head">
			<div>
				<span class="section-head__eyebrow">Door heel Nederland</span>
				<h2 class="hp-section-head__title">Comedy in<br>jouw stad</h2>
			</div>
			<a href="<?php echo esc_url( $locaties_url ); ?>" class="btn btn--ghost">Alle steden &rarr;</a>
		</div>
		<div class="city-grid">
			<?php foreach ( $cities as $city ) :
				$bg = comedyadv_image_url( $city->ID, 'large' );
			?>
			<a href="<?php echo esc_url( get_permalink( $city ) ); ?>" class="city-card reveal">
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
	</div>
</section>
<?php endif; ?>

<!-- REVIEWS -->
<?php
$hp_reviews = (array) get_post_meta( get_the_ID(), '_comedyadv_page_reviews', true );
$hp_reviews = array_values( array_filter( $hp_reviews, function( $r ) { return ! empty( $r['text'] ); } ) );
if ( $hp_reviews ) :
?>
<section class="hp-reviews">
	<div class="container">
		<div class="hp-reviews__grid">
			<?php foreach ( $hp_reviews as $review ) : ?>
			<div class="hp-review reveal">
				<div class="hp-review__stars">★★★★★</div>
				<p>"<?php echo esc_html( $review['text'] ); ?>"</p>
				<?php if ( ! empty( $review['author'] ) ) : ?>
				<span class="hp-review__author"><?php echo esc_html( $review['author'] ); ?></span>
				<?php endif; ?>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

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
