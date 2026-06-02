<?php
/**
 * Archive: Workshops — Gen Z redesign.
 */
get_header();

$contact_url = comedyadv_url( 'contact' );
$home_url    = home_url( '/' );
?>

<!-- BREADCRUMBS -->
<div class="workshop-breadcrumbs">
	<div class="container">
		<?php echo comedyadv_breadcrumbs( array( array( 'Home', $home_url ), 'Workshops' ) ); ?>
	</div>
</div>

<!-- HERO -->
<div class="workshops-archive-hero">
	<div class="container">
		<div class="workshops-archive-hero__inner">
			<div class="workshops-archive-hero__body">
				<span class="workshop-hero__tag">Teambuilding &amp; uitjes</span>
				<h1 class="workshops-archive-hero__title">Onze<br><span>workshops</span></h1>
				<p class="workshops-archive-hero__lead">Beleef comedy van de andere kant. Perfect voor teambuilding, personeelsuitjes of een verrassend bedrijfsuitje — onder begeleiding van echte comedians.</p>
			</div>
			<div class="workshops-archive-hero__deco">
				<span>HA</span>
				<span>HA</span>
				<span>HA</span>
			</div>
		</div>
	</div>
</div>

<!-- WORKSHOP CARDS -->
<div class="workshops-archive-grid">
	<div class="container">
		<?php if ( have_posts() ) : ?>
		<div class="workshops-grid">
			<?php while ( have_posts() ) : the_post();
				$id        = get_the_ID();
				$image_url = comedyadv_image_url( $id, 'large' );
				$lead      = comedyadv_meta( $id, '_comedyadv_lead' );
				$price     = comedyadv_meta( $id, '_comedyadv_price' );
				$eyebrow1  = comedyadv_meta( $id, '_comedyadv_eyebrow1', 'Workshop' );
			?>
			<a class="workshop-card reveal" href="<?php the_permalink(); ?>">
				<?php if ( $image_url ) : ?>
				<div class="workshop-card__image">
					<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php the_title_attribute(); ?>" />
				</div>
				<?php endif; ?>
				<div class="workshop-card__body">
						<h3 class="workshop-card__title"><?php the_title(); ?></h3>
					<?php if ( $lead ) : ?>
						<p class="workshop-card__lead"><?php echo esc_html( wp_trim_words( $lead, 20, '…' ) ); ?></p>
					<?php endif; ?>
					<?php if ( $price ) : ?>
					<div class="workshop-card__footer">
						<span class="workshop-card__price"><?php echo esc_html( $price ); ?><small>p.p.</small></span>
					</div>
					<?php endif; ?>
				</div>
			</a>
			<?php endwhile; ?>
		</div>
		<?php else : ?>
			<p>Nog geen workshops toegevoegd.</p>
		<?php endif; ?>
	</div>
</div>

<!-- HOE HET WERKT -->
<section class="workshops-steps">
	<div class="container">
		<div class="workshops-steps__head">
			<span class="section-head__eyebrow">Hoe het werkt</span>
			<h2>Workshop op jouw locatie</h2>
			<p>Wij komen naar jou. Op kantoor, in een feestlocatie of op een teambuildingsdag.</p>
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
				<p>Onze comedian leidt de workshop bij jou — interactief, energiek en vol humor.</p>
			</div>
			<div class="step reveal">
				<h4>Afsluitende show</h4>
				<p>De meeste workshops eindigen met een korte presentatie: deelnemers pakken zelf het podium.</p>
			</div>
		</div>
	</div>
</section>

<!-- CTA -->
<section class="section">
	<div class="container">
		<div class="cta-banner reveal">
			<h2>Klaar om je team op het podium te zetten?</h2>
			<p>Vertel ons over je groep en gelegenheid. Binnen 24 uur ontvang je een voorstel op maat.</p>
			<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--dark">Boek een workshop <span class="btn__arrow">&rarr;</span></a>
		</div>
	</div>
</section>

<?php get_footer(); ?>
