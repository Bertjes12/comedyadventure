<?php
/**
 * Single aanbod (activity) page.
 */
get_header();

$contact_url = comedyadv_url( 'contact' );
$home_url    = home_url( '/' );
$archive_url = get_post_type_archive_link( 'aanbod' );

while ( have_posts() ) : the_post();
	$id       = get_the_ID();
	$lead     = comedyadv_meta( $id, '_comedyadv_aanbod_lead' );
	$image    = comedyadv_image_url( $id, 'large' );
	$duration = comedyadv_meta( $id, '_comedyadv_aanbod_duration' );
	$price_pp = comedyadv_meta( $id, '_comedyadv_aanbod_price_pp' );
	$min      = comedyadv_meta( $id, '_comedyadv_aanbod_min_personen' );
?>

<section class="page-hero">
	<div class="container page-hero__inner">
		<div class="page-hero__breadcrumbs"><?php echo comedyadv_breadcrumbs( array( array( 'Home', $home_url ), array( 'Aanbod', $archive_url ), get_the_title() ) ); ?></div>
		<h1 class="page-hero__title"><?php the_title(); ?></h1>
		<?php if ( $lead ) : ?>
			<p class="page-hero__lead"><?php echo esc_html( $lead ); ?></p>
		<?php endif; ?>
	</div>
</section>

<section class="section">
	<div class="container">
		<div class="feature-split">
			<?php if ( $image ) : ?>
				<div class="feature-split__media reveal">
					<img src="<?php echo esc_url( $image ); ?>" alt="<?php the_title_attribute(); ?>" />
				</div>
			<?php endif; ?>
			<div class="reveal">
				<span class="section-head__eyebrow">Over deze activiteit</span>
				<h2><?php the_title(); ?></h2>
				<?php the_content(); ?>
				<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--primary">Vraag offerte aan <span class="btn__arrow">&rarr;</span></a>
			</div>
		</div>
	</div>
</section>

<?php if ( $price_pp || $duration || $min ) : ?>
<section class="section section--grey">
	<div class="container">
		<div class="feature-split feature-split--reverse">
			<div class="feature-split__media reveal" style="aspect-ratio:1/1;">
				<div class="specs">
					<?php if ( $price_pp ) : ?>
						<div class="specs__price">
							<div class="specs__price-label">Prijs per persoon</div>
							<div class="specs__price-amount"><?php echo esc_html( $price_pp ); ?></div>
						</div>
					<?php endif; ?>
					<?php if ( $duration || $min ) : ?>
						<ul class="specs__list">
							<?php if ( $duration ) : ?>
								<li class="specs__item">
									<span class="specs__item-label">Tijdsduur</span>
									<span class="specs__item-value"><?php echo esc_html( $duration ); ?></span>
								</li>
							<?php endif; ?>
							<?php if ( $min ) : ?>
								<li class="specs__item">
									<span class="specs__item-label">Minimum personen</span>
									<span class="specs__item-value"><?php echo esc_html( $min ); ?></span>
								</li>
							<?php endif; ?>
						</ul>
					<?php endif; ?>
					<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--primary">Vraag offerte aan <span class="btn__arrow">&rarr;</span></a>
				</div>
			</div>
			<div class="reveal">
				<span class="section-head__eyebrow">Praktische info</span>
				<h2>Wat kun je verwachten?</h2>
				<p>Hieronder vind je de praktische details voor deze activiteit. Heb je vragen of wil je een offerte op maat? Neem gerust contact met ons op.</p>
			</div>
		</div>
	</div>
</section>
<?php endif; ?>

<section class="section">
	<div class="container">
		<div class="cta-banner">
			<h2>Klaar om te boeken?</h2>
			<p>Vraag een vrijblijvende offerte aan. Binnen 24 uur ontvang je een voorstel op maat.</p>
			<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--dark">Plan jouw show <span class="btn__arrow">&rarr;</span></a>
		</div>
	</div>
</section>

<?php endwhile; ?>

<?php get_footer(); ?>
