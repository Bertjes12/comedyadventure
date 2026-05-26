<?php
/**
 * Archive: Workshops.
 */
get_header();

$contact_url = comedyadv_url( 'contact' );
$home_url    = home_url( '/' );
?>

<section class="page-hero">
	<div class="container page-hero__inner">
		<div class="page-hero__breadcrumbs"><?php echo comedyadv_breadcrumbs( array( array( 'Home', $home_url ), 'Workshops' ) ); ?></div>
		<h1 class="page-hero__title">Onze <span>workshops</span></h1>
		<p class="page-hero__lead">Beleef comedy van de andere kant. Onze workshops zijn perfect voor teambuilding, personeelsuitjes of een verrassend bedrijfsuitje. Onder begeleiding van ervaren comedians staat jouw team binnen no-time zelf op het podium.</p>
	</div>
</section>

<section class="section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<div class="card-grid">
				<?php while ( have_posts() ) : the_post();
					$id        = get_the_ID();
					$image_url = comedyadv_image_url( $id, 'medium_large' );
					$lead      = comedyadv_meta( $id, '_comedyadv_lead' );
					$specs     = (array) comedyadv_meta( $id, '_comedyadv_specs', array() );
					$tag = ! empty( $specs ) && isset( $specs[0]['value'] ) ? $specs[0]['value'] : '';
				?>
					<a class="card reveal" href="<?php the_permalink(); ?>">
						<div class="card__media">
							<?php if ( $image_url ) : ?>
								<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php the_title_attribute(); ?>" />
							<?php endif; ?>
						</div>
						<div class="card__body">
							<?php if ( $tag ) : ?>
								<span class="card__tag"><?php echo $tag; ?></span>
							<?php endif; ?>
							<h3 class="card__title"><?php the_title(); ?></h3>
							<?php if ( $lead ) : ?>
								<p class="card__text"><?php echo esc_html( wp_trim_words( $lead, 32, '...' ) ); ?></p>
							<?php endif; ?>
							<span class="card__link">Bekijk workshop</span>
						</div>
					</a>
				<?php endwhile; ?>
			</div>
		<?php else : ?>
			<p>Nog geen workshops toegevoegd.</p>
		<?php endif; ?>
	</div>
</section>

<section class="section section--grey">
	<div class="container">
		<div class="section-head">
			<span class="section-head__eyebrow">Hoe het werkt</span>
			<h2>Workshop op jouw locatie</h2>
			<p class="section-head__lead">Wij komen naar jou. Op kantoor, in een feestlocatie of op een teambuildingsdag &mdash; onze comedians passen zich aan jouw setting aan.</p>
		</div>
		<div class="steps">
			<div class="step reveal">
				<h4>Kies een workshop</h4>
				<p>Welke past bij jouw groep? Wij adviseren graag, of je kiest zelf uit ons aanbod.</p>
			</div>
			<div class="step reveal">
				<h4>Plan &amp; briefing</h4>
				<p>We stemmen datum, locatie en groepsgrootte af en bereiden ons voor op jouw publiek.</p>
			</div>
			<div class="step reveal">
				<h4>Workshop op locatie</h4>
				<p>Onze comedian leidt de workshop bij jou &mdash; interactief, energiek en met humor als rode draad.</p>
			</div>
			<div class="step reveal">
				<h4>Afsluitende show</h4>
				<p>De meeste workshops eindigen met een korte presentatie waarin deelnemers zelf het podium pakken.</p>
			</div>
		</div>
	</div>
</section>

<section class="section">
	<div class="container">
		<div class="cta-banner">
			<h2>Klaar om je team op het podium te zetten?</h2>
			<p>Vertel ons over je groep en gelegenheid. Binnen 24 uur ontvang je een voorstel op maat.</p>
			<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--dark">Boek een workshop <span class="btn__arrow">&rarr;</span></a>
		</div>
	</div>
</section>

<?php get_footer(); ?>
