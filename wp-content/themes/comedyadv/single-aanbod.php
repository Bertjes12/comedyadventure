<?php
/**
 * Single aanbod page — Gen Z redesign (zelfde stijl als workshop).
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

<!-- BREADCRUMBS -->
<div class="workshop-breadcrumbs">
	<div class="container">
		<?php echo comedyadv_breadcrumbs( array( array( 'Home', $home_url ), array( 'Aanbod', $archive_url ), get_the_title() ) ); ?>
	</div>
</div>

<!-- HERO: titel + foto split -->
<div class="workshop-hero">
	<div class="container">
		<div class="workshop-hero__inner">

			<!-- LINKS: tekst -->
			<div class="workshop-hero__body">
				<span class="workshop-hero__tag">Over deze activiteit</span>
				<h1 class="workshop-hero__title"><?php the_title(); ?></h1>
				<?php if ( $lead ) : ?>
					<p class="workshop-hero__lead"><?php echo esc_html( $lead ); ?></p>
				<?php endif; ?>
				<div class="workshop-hero__content">
					<?php the_content(); ?>
				</div>
				<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--primary workshop-hero__cta">
					Vraag offerte aan <span class="btn__arrow">&rarr;</span>
				</a>
			</div>

			<!-- RECHTS: foto -->
			<?php if ( $image ) : ?>
			<div class="workshop-hero__photo reveal">
				<div class="workshop-hero__photo-wrap">
					<img src="<?php echo esc_url( $image ); ?>" alt="<?php the_title_attribute(); ?>" />
				</div>
			</div>
			<?php endif; ?>

		</div>
	</div>
</div>

<!-- SPECS -->
<?php if ( $price_pp || $duration || $min ) : ?>
<section class="workshop-specs">
	<div class="container">
		<div class="workshop-specs__inner">

			<!-- Prijs + specs kaart -->
			<div class="workshop-specs__card reveal">
				<?php if ( $price_pp ) : ?>
				<div class="workshop-price">
					<span class="workshop-price__label">Prijs per persoon</span>
					<span class="workshop-price__amount"><?php echo esc_html( $price_pp ); ?></span>
				</div>
				<?php endif; ?>
				<ul class="workshop-specs__list">
					<?php if ( $duration ) : ?>
					<li class="workshop-specs__item">
						<span class="workshop-specs__item-label">Tijdsduur</span>
						<span class="workshop-specs__item-value"><?php echo esc_html( $duration ); ?></span>
					</li>
					<?php endif; ?>
					<?php if ( $min ) : ?>
					<li class="workshop-specs__item">
						<span class="workshop-specs__item-label">Minimum personen</span>
						<span class="workshop-specs__item-value"><?php echo esc_html( $min ); ?></span>
					</li>
					<?php endif; ?>
				</ul>
				<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--primary" style="width:100%;justify-content:center;">
					Vraag offerte aan <span class="btn__arrow">&rarr;</span>
				</a>
			</div>

			<!-- Wat kun je verwachten -->
			<div class="workshop-learn reveal">
				<span class="workshop-learn__eyebrow">Praktische info</span>
				<h2 class="workshop-learn__title">Wat kun je<br>verwachten?</h2>
				<p class="workshop-learn__closing">Heb je vragen of wil je een offerte op maat? Neem gerust contact met ons op — we reageren binnen 24 uur met een voorstel.</p>
				<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--primary" style="margin-top:24px;">
					Neem contact op <span class="btn__arrow">&rarr;</span>
				</a>
			</div>

		</div>
	</div>
</section>
<?php endif; ?>

<!-- CTA -->
<section class="section">
	<div class="container">
		<div class="cta-banner reveal">
			<h2>Klaar om te boeken?</h2>
			<p>Vraag een vrijblijvende offerte aan. Binnen 24 uur ontvang je een voorstel op maat.</p>
			<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--dark">Plan jouw show <span class="btn__arrow">&rarr;</span></a>
		</div>
	</div>
</section>

<?php endwhile; ?>
<?php get_footer(); ?>
