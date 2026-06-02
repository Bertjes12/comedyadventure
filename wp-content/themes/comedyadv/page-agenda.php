<?php
/**
 * Agenda page — Gen Z Festival Lineup redesign.
 */
get_header();

$contact_url = comedyadv_url( 'contact' );
$home_url    = home_url( '/' );
$today       = date( 'Y-m-d' );

$upcoming = get_posts( array(
	'post_type'      => 'show',
	'posts_per_page' => -1,
	'meta_key'       => '_comedyadv_show_date',
	'orderby'        => 'meta_value',
	'order'          => 'ASC',
	'meta_query'     => array(
		array( 'key' => '_comedyadv_show_date', 'value' => $today, 'compare' => '>=' ),
	),
) );
$past = get_posts( array(
	'post_type'      => 'show',
	'posts_per_page' => -1,
	'meta_key'       => '_comedyadv_show_date',
	'orderby'        => 'meta_value',
	'order'          => 'DESC',
	'meta_query'     => array(
		array( 'key' => '_comedyadv_show_date', 'value' => $today, 'compare' => '<' ),
	),
) );
?>

<!-- BREADCRUMBS -->
<div class="workshop-breadcrumbs">
	<div class="container">
		<?php echo comedyadv_breadcrumbs( array( array( 'Home', $home_url ), 'Agenda' ) ); ?>
	</div>
</div>

<!-- HERO -->
<div class="agenda-hero">
	<div class="container agenda-hero__inner">
		<div>
			<span class="workshop-hero__tag">
				<span class="agenda-hero__live"></span> <?php echo count( $upcoming ); ?> aankomende shows
			</span>
			<h1 class="agenda-hero__title">Aankomende<br><span>shows</span></h1>
			<p class="agenda-hero__lead">Comedy door heel Nederland. Klik op een show voor locatie en boekingsmogelijkheden.</p>
		</div>
		<div class="comedians-hero__deco">
			<span>HA</span>
			<span>HA</span>
			<span>HA</span>
		</div>
	</div>
</div>

<!-- UPCOMING SHOWS -->
<div class="agenda-section">
	<div class="container">
		<?php if ( $upcoming ) : ?>
		<div class="agenda-lineup">
			<?php foreach ( $upcoming as $show ) :
				$id       = $show->ID;
				$date     = get_post_meta( $id, '_comedyadv_show_date', true );
				$time     = get_post_meta( $id, '_comedyadv_show_time', true );
				$venue    = get_post_meta( $id, '_comedyadv_show_location', true );
				$price    = get_post_meta( $id, '_comedyadv_show_price', true );
				$city_id  = (int) get_post_meta( $id, '_comedyadv_show_city', true );
				$city     = $city_id ? get_post( $city_id ) : null;
				$city_lbl = $city ? $city->post_title : '';
				$city_url = $city ? get_permalink( $city ) : '';
				$meta     = array_filter( array( $city_lbl ?: $venue, $time, $price ) );
			?>
			<article class="lineup-card reveal">
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
						<p class="lineup-card__meta"><?php echo esc_html( implode( ' · ', $meta ) ); ?></p>
					<?php endif; ?>
				</div>
				<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--primary lineup-card__btn">Tickets</a>
			</article>
			<?php endforeach; ?>
		</div>
		<?php else : ?>
			<div class="agenda-empty">
				<span>😶</span>
				<p>Geen shows gepland. <a href="<?php echo esc_url( $contact_url ); ?>">Boek een privéshow</a>.</p>
			</div>
		<?php endif; ?>
	</div>
</div>

<!-- PAST SHOWS -->
<?php if ( $past ) : ?>
<div class="agenda-past">
	<div class="container">
		<div class="agenda-past__head">
			<span class="section-head__eyebrow">Archief</span>
			<h2>Eerdere shows</h2>
		</div>
		<div class="agenda-lineup agenda-lineup--past">
			<?php foreach ( $past as $show ) :
				$id       = $show->ID;
				$date     = get_post_meta( $id, '_comedyadv_show_date', true );
				$time     = get_post_meta( $id, '_comedyadv_show_time', true );
				$venue    = get_post_meta( $id, '_comedyadv_show_location', true );
				$city_id  = (int) get_post_meta( $id, '_comedyadv_show_city', true );
				$city     = $city_id ? get_post( $city_id ) : null;
				$city_lbl = $city ? $city->post_title : '';
				$city_url = $city ? get_permalink( $city ) : '';
				$meta     = array_filter( array( $city_lbl ?: $venue, $time ) );
			?>
			<article class="lineup-card lineup-card--past reveal">
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
						<p class="lineup-card__meta"><?php echo esc_html( implode( ' · ', $meta ) ); ?> · geweest</p>
					<?php endif; ?>
				</div>
				<?php if ( $city_url ) : ?>
					<a href="<?php echo esc_url( $city_url ); ?>" class="btn btn--ghost lineup-card__btn">Bekijk</a>
				<?php endif; ?>
			</article>
			<?php endforeach; ?>
		</div>
	</div>
</div>
<?php endif; ?>

<!-- CTA -->
<section class="section">
	<div class="container">
		<div class="cta-banner reveal">
			<h2>Liever een priv&eacute;show?</h2>
			<p>Wij organiseren ook besloten shows op jouw locatie. Vraag een offerte aan en wij regelen het.</p>
			<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--dark">Plan een priv&eacute;show <span class="btn__arrow">&rarr;</span></a>
		</div>
	</div>
</section>

<?php get_footer(); ?>
