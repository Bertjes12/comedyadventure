<?php
/**
 * Archive: Comedians — Gen Z Roster redesign.
 */
get_header();

$contact_url = comedyadv_url( 'contact' );
$home_url    = home_url( '/' );
?>

<!-- BREADCRUMBS -->
<div class="workshop-breadcrumbs">
	<div class="container">
		<?php echo comedyadv_breadcrumbs( array( array( 'Home', $home_url ), 'Comedians' ) ); ?>
	</div>
</div>

<!-- HERO -->
<div class="comedians-hero">
	<div class="container comedians-hero__inner">
		<div class="comedians-hero__body">
			<span class="workshop-hero__tag">🎤 Het roster</span>
			<h1 class="comedians-hero__title">Onze<br><span>comedians</span></h1>
			<p class="comedians-hero__lead">Nederlandse topcomedians klaar om jouw evenement onvergetelijk te maken. Van observatiehumor tot interactieve roasts.</p>
		</div>
		<div class="comedians-hero__deco">
			<span>HA</span>
			<span>HA</span>
			<span>HA</span>
		</div>
	</div>
</div>

<!-- COMEDIAN CARDS -->
<div class="comedians-grid-section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
		<div class="comedians-grid">
			<?php while ( have_posts() ) : the_post();
				$id        = get_the_ID();
				$tag       = comedyadv_meta( $id, '_comedyadv_tag' );
				$image_url = comedyadv_image_url( $id, 'large' );
				$first     = explode( ' ', get_the_title() )[0];
			?>
			<a class="comedian-card reveal" href="<?php the_permalink(); ?>">
				<div class="comedian-card__photo">
					<?php if ( $image_url ) : ?>
						<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php the_title_attribute(); ?>" />
					<?php endif; ?>
					<div class="comedian-card__overlay"></div>
				</div>
				<div class="comedian-card__body">
					<?php if ( $tag ) : ?>
						<span class="comedian-card__tag"><?php echo esc_html( $tag ); ?></span>
					<?php endif; ?>
					<h3 class="comedian-card__name"><?php the_title(); ?></h3>
					<span class="comedian-card__cta">Boek <?php echo esc_html( $first ); ?> →</span>
				</div>
			</a>
			<?php endwhile; ?>
		</div>
		<?php else : ?>
			<p>Nog geen comedians toegevoegd.</p>
		<?php endif; ?>
	</div>
</div>

<!-- CTA -->
<section class="comedians-cta">
	<div class="container comedians-cta__inner">
		<div>
			<span class="section-head__eyebrow">Niet zeker wie past?</span>
			<h2>Wij matchen de<br><span>perfecte comedian.</span></h2>
			<p>Vertel ons over je evenement en we adviseren de beste match voor jouw publiek.</p>
		</div>
		<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--primary comedians-cta__btn">
			Vraag advies aan <span class="btn__arrow">&rarr;</span>
		</a>
	</div>
</section>

<?php get_footer(); ?>
