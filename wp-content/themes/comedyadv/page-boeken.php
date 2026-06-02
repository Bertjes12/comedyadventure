<?php
/**
 * Boekingspagina — kies een show uit de agenda.
 */
get_header();

$home_url    = home_url( '/' );
$contact_url = comedyadv_url( 'contact' );
$today       = date( 'Y-m-d' );

$shows = get_posts( array(
	'post_type'      => 'show',
	'posts_per_page' => -1,
	'meta_key'       => '_comedyadv_show_date',
	'orderby'        => 'meta_value',
	'order'          => 'ASC',
	'meta_query'     => array(
		array( 'key' => '_comedyadv_show_date', 'value' => $today, 'compare' => '>=' ),
	),
) );

// Groepeer per type/format
$grouped = array();
foreach ( $shows as $show ) {
	$type = get_post_meta( $show->ID, '_comedyadv_show_type', true );
	if ( ! $type ) $type = 'Overig';
	$grouped[ $type ][] = $show;
}
?>

<!-- BREADCRUMBS -->
<div class="workshop-breadcrumbs">
	<div class="container">
		<?php echo comedyadv_breadcrumbs( array( array( 'Home', $home_url ), 'Boek een show' ) ); ?>
	</div>
</div>

<!-- HERO -->
<div class="boeken-hero">
	<div class="container boeken-hero__inner">
		<div class="boeken-hero__body">
			<span class="workshop-hero__tag">🎟 Kies jouw show</span>
			<h1 class="boeken-hero__title">Boek een<br><span>onvergetelijke</span><br>avond</h1>
			<p class="boeken-hero__lead">Kies een aankomende show of comedy diner bij jou in de buurt. Reserveer eenvoudig via het contactformulier.</p>
			<div class="boeken-hero__steps">
				<div class="boeken-step">
					<span class="boeken-step__num">1</span>
					<span>Kies je show</span>
				</div>
				<div class="boeken-step__arrow">→</div>
				<div class="boeken-step">
					<span class="boeken-step__num">2</span>
					<span>Stuur een aanvraag</span>
				</div>
				<div class="boeken-step__arrow">→</div>
				<div class="boeken-step">
					<span class="boeken-step__num">3</span>
					<span>Bevestiging binnen 24u</span>
				</div>
			</div>
		</div>
		<div class="boeken-hero__deco">
			<span>HA</span>
			<span>HA</span>
			<span>HA</span>
		</div>
	</div>
</div>

<!-- SHOWS GRID -->
<div class="boeken-main">
	<div class="container">

		<?php if ( $shows ) : ?>

		<!-- Filter tabs -->
		<div class="boeken-filters" id="boeken-filters">
			<button class="boeken-filter is-active" data-filter="all">Alle shows</button>
			<?php foreach ( array_keys( $grouped ) as $type ) : ?>
			<button class="boeken-filter" data-filter="<?php echo esc_attr( sanitize_title( $type ) ); ?>"><?php echo esc_html( $type ); ?></button>
			<?php endforeach; ?>
		</div>

		<!-- Show kaarten -->
		<div class="boeken-grid">
			<?php foreach ( $shows as $show ) :
				$date        = get_post_meta( $show->ID, '_comedyadv_show_date', true );
				$time        = get_post_meta( $show->ID, '_comedyadv_show_time', true );
				$price       = get_post_meta( $show->ID, '_comedyadv_show_price', true );
				$venue       = get_post_meta( $show->ID, '_comedyadv_show_location', true );
				$inclusive   = get_post_meta( $show->ID, '_comedyadv_show_inclusive', true );
				$type        = get_post_meta( $show->ID, '_comedyadv_show_type', true ) ?: 'Overig';
				$city_id     = (int) get_post_meta( $show->ID, '_comedyadv_show_city', true );
				$city_name   = $city_id ? get_the_title( $city_id ) : '';
				$city_url    = $city_id ? get_permalink( $city_id ) : $contact_url;
				$img         = $city_id ? comedyadv_image_url( $city_id, 'large' ) : '';
				$date_fmt    = $date ? comedyadv_show_long_date( $date ) : '';
				$day         = $date ? comedyadv_show_day( $date ) : '';
				$month       = $date ? comedyadv_show_month( $date ) : '';
			?>
			<a class="boeken-card reveal" href="<?php echo esc_url( $city_url ); ?>" data-type="<?php echo esc_attr( sanitize_title( $type ) ); ?>">
				<div class="boeken-card__media">
					<?php if ( $img ) : ?>
					<img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $show->post_title ); ?>" />
					<?php else : ?>
					<div class="boeken-card__media-placeholder"></div>
					<?php endif; ?>
					<div class="boeken-card__date-badge">
						<span class="boeken-card__day"><?php echo esc_html( $day ); ?></span>
						<span class="boeken-card__month"><?php echo esc_html( $month ); ?></span>
					</div>
					<?php if ( $type && $type !== 'Overig' ) : ?>
					<span class="boeken-card__type-tag"><?php echo esc_html( $type ); ?></span>
					<?php endif; ?>
				</div>
				<div class="boeken-card__body">
					<h3 class="boeken-card__title"><?php echo esc_html( $show->post_title ); ?></h3>
					<div class="boeken-card__meta">
						<?php if ( $venue ) : ?><span>📍 <?php echo esc_html( $venue ); ?></span><?php endif; ?>
						<?php if ( $time ) : ?><span>🕐 <?php echo esc_html( $time ); ?></span><?php endif; ?>
					</div>
					<?php if ( $inclusive ) :
						$inc_items = array_filter( array_map( 'trim', explode( "\n", $inclusive ) ) );
						$inc_items = array_slice( $inc_items, 0, 3 );
					?>
					<ul class="boeken-card__inclusive">
						<?php foreach ( $inc_items as $item ) : ?>
						<li><svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg><?php echo esc_html( $item ); ?></li>
						<?php endforeach; ?>
					</ul>
					<?php endif; ?>
					<div class="boeken-card__footer">
						<?php if ( $price ) : ?>
						<span class="boeken-card__price"><?php echo esc_html( $price ); ?></span>
						<?php endif; ?>
						<span class="boeken-card__cta">Bekijk & reserveer <svg viewBox="0 0 16 16" fill="none" width="14" height="14"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
					</div>
				</div>
			</a>
			<?php endforeach; ?>
		</div>

		<?php else : ?>
		<div class="boeken-empty">
			<p>Er zijn momenteel geen aankomende shows gepland.</p>
			<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--primary">Vraag een show op maat aan <span class="btn__arrow">&rarr;</span></a>
		</div>
		<?php endif; ?>

	</div>
</div>

<!-- CTA ONDERAAN -->
<section class="boeken-cta">
	<div class="container boeken-cta__inner">
		<div>
			<span class="section-head__eyebrow" style="color:#FF5500;">Op maat</span>
			<h2>Geen show die past?</h2>
			<p>Wij verzorgen ook shows volledig op maat — op jouw datum, locatie en wensen.</p>
		</div>
		<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--primary">Vraag offerte aan <span class="btn__arrow">&rarr;</span></a>
	</div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
	const filters = document.querySelectorAll('.boeken-filter');
	const cards   = document.querySelectorAll('.boeken-card');
	filters.forEach(function(btn) {
		btn.addEventListener('click', function() {
			filters.forEach(function(b) { b.classList.remove('is-active'); });
			btn.classList.add('is-active');
			const f = btn.dataset.filter;
			cards.forEach(function(card) {
				card.style.display = (f === 'all' || card.dataset.type === f) ? '' : 'none';
			});
		});
	});
});
</script>

<?php get_footer(); ?>
