<?php
/**
 * Archive: Aanbod — Gen Z redesign.
 */
get_header();

$contact_url = comedyadv_url( 'contact' );
$home_url    = home_url( '/' );
?>

<!-- BREADCRUMBS -->
<div class="workshop-breadcrumbs">
	<div class="container">
		<?php echo comedyadv_breadcrumbs( array( array( 'Home', $home_url ), 'Aanbod' ) ); ?>
	</div>
</div>

<!-- HERO -->
<div class="aanbod-hero">
	<div class="container aanbod-hero__inner">
		<div class="aanbod-hero__body">
			<span class="workshop-hero__tag">🎭 Kies jouw format</span>
			<h1 class="aanbod-hero__title">Ons<br><span>aanbod</span></h1>
			<p class="aanbod-hero__lead">Van een korte set tijdens het diner tot een volledig avondvullend programma — kies het format dat het beste past bij jouw evenement.</p>
		</div>
		<div class="comedians-hero__deco">
			<span>HA</span>
			<span>HA</span>
			<span>HA</span>
		</div>
	</div>
</div>

<!-- AANBOD CARDS -->
<div class="workshops-archive-grid">
	<div class="container">
		<?php if ( have_posts() ) : ?>
		<div class="workshops-grid">
			<?php while ( have_posts() ) : the_post();
				$id         = get_the_ID();
				$image      = comedyadv_image_url( $id, 'large' );
				$lead       = comedyadv_meta( $id, '_comedyadv_aanbod_lead' );
				$price_pp   = comedyadv_meta( $id, '_comedyadv_aanbod_price_pp' );
			?>
			<a class="workshop-card reveal" href="<?php the_permalink(); ?>">
				<?php if ( $image ) : ?>
				<div class="workshop-card__image">
					<img src="<?php echo esc_url( $image ); ?>" alt="<?php the_title_attribute(); ?>" />
				</div>
				<?php endif; ?>
				<div class="workshop-card__body">
					<h3 class="workshop-card__title"><?php the_title(); ?></h3>
					<?php if ( $lead ) : ?>
						<p class="workshop-card__lead"><?php echo esc_html( wp_trim_words( $lead, 20, '…' ) ); ?></p>
					<?php endif; ?>
					<?php if ( $price_pp ) : ?>
					<div class="workshop-card__footer">
						<span class="workshop-card__price"><?php echo esc_html( $price_pp ); ?><small>p.p.</small></span>
					</div>
					<?php endif; ?>
				</div>
			</a>
			<?php endwhile; ?>
		</div>
		<?php else : ?>
			<p>Nog geen aanbod-items toegevoegd.</p>
		<?php endif; ?>
	</div>
</div>

<!-- CTA -->
<section class="comedians-cta">
	<div class="container comedians-cta__inner">
		<div>
			<span class="section-head__eyebrow">Op maat</span>
			<h2>Iets anders<br><span>in gedachten?</span></h2>
			<p>Wij maken graag een voorstel op maat. Vertel ons over je evenement en we komen met een plan.</p>
		</div>
		<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--primary comedians-cta__btn">
			Neem contact op <span class="btn__arrow">&rarr;</span>
		</a>
	</div>
</section>

<?php get_footer(); ?>
