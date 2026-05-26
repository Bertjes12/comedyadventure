<?php
/**
 * Archive: Comedians.
 */
get_header();

$contact_url = comedyadv_url( 'contact' );
$home_url    = home_url( '/' );
?>

<section class="page-hero">
	<div class="container page-hero__inner">
		<div class="page-hero__breadcrumbs"><?php echo comedyadv_breadcrumbs( array( array( 'Home', $home_url ), 'Comedians' ) ); ?></div>
		<h1 class="page-hero__title">Onze <span>comedians</span></h1>
		<p class="page-hero__lead">Een netwerk van Nederlandse topcomedians, klaar om jouw evenement onvergetelijk te maken. Van scherpe observatiehumor tot interactieve roasts &mdash; wij matchen de juiste comedian aan jouw publiek.</p>
	</div>
</section>

<section class="section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<div class="card-grid">
				<?php while ( have_posts() ) : the_post();
					$tag       = comedyadv_meta( get_the_ID(), '_comedyadv_tag' );
					$image_url = comedyadv_image_url( get_the_ID(), 'medium_large' );
					$first     = explode( ' ', get_the_title() )[0];
				?>
					<a class="card reveal" href="<?php the_permalink(); ?>">
						<div class="card__media">
							<?php if ( $image_url ) : ?>
								<img src="<?php echo esc_url( $image_url ); ?>" alt="Comedian <?php the_title_attribute(); ?>" />
							<?php endif; ?>
						</div>
						<div class="card__body">
							<?php if ( $tag ) : ?>
								<span class="card__tag"><?php echo esc_html( $tag ); ?></span>
							<?php endif; ?>
							<h3 class="card__title"><?php the_title(); ?></h3>
							<p class="card__text"><?php echo wp_trim_words( wp_strip_all_tags( get_the_content() ), 28, '...' ); ?></p>
							<span class="card__link">Boek <?php echo esc_html( $first ); ?></span>
						</div>
					</a>
				<?php endwhile; ?>
			</div>
		<?php else : ?>
			<p>Nog geen comedians toegevoegd.</p>
		<?php endif; ?>
	</div>
</section>

<section class="section section--grey">
	<div class="container">
		<div class="cta-banner">
			<h2>Niet zeker welke comedian past?</h2>
			<p>Geen probleem. Vertel ons over je evenement en we adviseren de perfecte match.</p>
			<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--dark">Vraag advies aan <span class="btn__arrow">&rarr;</span></a>
		</div>
	</div>
</section>

<?php get_footer(); ?>
