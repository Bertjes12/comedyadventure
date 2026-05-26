<?php
/**
 * Archive: Aanbod (activiteiten/services).
 */
get_header();

$contact_url = comedyadv_url( 'contact' );
$home_url    = home_url( '/' );
?>

<section class="page-hero">
	<div class="container page-hero__inner">
		<div class="page-hero__breadcrumbs"><?php echo comedyadv_breadcrumbs( array( array( 'Home', $home_url ), 'Aanbod' ) ); ?></div>
		<h1 class="page-hero__title">Ons <span>aanbod</span></h1>
		<p class="page-hero__lead">Van een korte set tijdens het diner tot een volledig avondvullend programma &mdash; kies het format dat het beste past bij jouw evenement.</p>
	</div>
</section>

<section class="section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<div class="card-grid">
				<?php while ( have_posts() ) : the_post();
					$id         = get_the_ID();
					$image      = comedyadv_image_url( $id, 'medium_large' );
					$lead       = comedyadv_meta( $id, '_comedyadv_aanbod_lead' );
					$card_extra = get_post_meta( $id, '_comedyadv_aanbod_card_extra', true );
				?>
					<a class="card reveal" href="<?php the_permalink(); ?>">
						<div class="card__media">
							<?php if ( $image ) : ?>
								<img src="<?php echo esc_url( $image ); ?>" alt="<?php the_title_attribute(); ?>" />
							<?php endif; ?>
						</div>
						<div class="card__body">
							<?php if ( $card_extra ) : ?>
								<span class="card__tag"><?php echo esc_html( $card_extra ); ?></span>
							<?php endif; ?>
							<h3 class="card__title"><?php the_title(); ?></h3>
							<?php if ( $lead ) : ?>
								<p class="card__text"><?php echo esc_html( wp_trim_words( $lead, 28, '...' ) ); ?></p>
							<?php endif; ?>
							<span class="card__link">Meer info</span>
						</div>
					</a>
				<?php endwhile; ?>
			</div>
		<?php else : ?>
			<p>Nog geen aanbod-items toegevoegd.</p>
		<?php endif; ?>
	</div>
</section>

<section class="section section--grey">
	<div class="container">
		<div class="cta-banner">
			<h2>Iets anders in gedachten?</h2>
			<p>Wij maken graag een voorstel op maat. Vertel ons over je evenement en we komen met een plan.</p>
			<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--dark">Neem contact op <span class="btn__arrow">&rarr;</span></a>
		</div>
	</div>
</section>

<?php get_footer(); ?>
