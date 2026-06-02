<?php
/**
 * Single workshop page — Gen Z Poster redesign.
 */
get_header();

$contact_url = comedyadv_url( 'contact' );
$home_url    = home_url( '/' );
$archive_url = get_post_type_archive_link( 'workshop' );

while ( have_posts() ) : the_post();
	$id         = get_the_ID();
	$breadcrumb = comedyadv_meta( $id, '_comedyadv_breadcrumb', get_the_title() );
	$title_html = comedyadv_meta( $id, '_comedyadv_title_html', get_the_title() );
	$lead       = comedyadv_meta( $id, '_comedyadv_lead' );
	$image_url  = comedyadv_image_url( $id, 'large' );
	$eyebrow1   = comedyadv_meta( $id, '_comedyadv_eyebrow1', 'Over deze workshop' );
	$h2_1       = comedyadv_meta( $id, '_comedyadv_h2_1' );
	$price      = comedyadv_meta( $id, '_comedyadv_price' );
	$price_sub  = comedyadv_meta( $id, '_comedyadv_price_sub' );
	$specs      = (array) comedyadv_meta( $id, '_comedyadv_specs', array() );
	$eyebrow2   = comedyadv_meta( $id, '_comedyadv_eyebrow2', 'Wat leer je?' );
	$h2_2       = comedyadv_meta( $id, '_comedyadv_h2_2' );
	$pills      = (array) comedyadv_meta( $id, '_comedyadv_pills', array() );
	$closing    = comedyadv_meta( $id, '_comedyadv_closing' );
	$cta_h2     = comedyadv_meta( $id, '_comedyadv_cta_h2', 'Klaar om te boeken?' );
?>

<!-- BREADCRUMBS -->
<div class="workshop-breadcrumbs">
	<div class="container">
		<?php echo comedyadv_breadcrumbs( array( array( 'Home', $home_url ), array( 'Workshops', $archive_url ), $breadcrumb ) ); ?>
	</div>
</div>

<!-- HERO: titel + foto split -->
<div class="workshop-hero">
	<div class="container">
		<div class="workshop-hero__inner">

			<!-- LINKS: tekst -->
			<div class="workshop-hero__body">
				<?php if ( $eyebrow1 ) : ?>
					<span class="workshop-hero__tag"><?php echo esc_html( $eyebrow1 ); ?></span>
				<?php endif; ?>
				<h1 class="workshop-hero__title"><?php echo wp_kses_post( $title_html ); ?></h1>
				<?php if ( $lead ) : ?>
					<p class="workshop-hero__lead"><?php echo esc_html( $lead ); ?></p>
				<?php endif; ?>
				<?php if ( $h2_1 ) : ?>
					<div class="workshop-hero__label"><?php echo esc_html( $h2_1 ); ?></div>
				<?php endif; ?>
				<div class="workshop-hero__content">
					<?php the_content(); ?>
				</div>
				<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--primary workshop-hero__cta">
					Boek deze workshop <span class="btn__arrow">&rarr;</span>
				</a>
			</div>

			<!-- RECHTS: foto -->
			<?php if ( $image_url ) : ?>
			<div class="workshop-hero__photo reveal">
				<div class="workshop-hero__photo-wrap">
					<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $breadcrumb ); ?>" />
				</div>
			</div>
			<?php endif; ?>

		</div>
	</div>
</div>

<!-- SPECS + WAT LEER JE -->
<section class="workshop-specs">
	<div class="container">
		<div class="workshop-specs__inner">

			<!-- Prijs + specs kaart -->
			<?php if ( $price || $specs ) : ?>
			<div class="workshop-specs__card reveal">
				<?php if ( $price ) : ?>
				<div class="workshop-price">
					<span class="workshop-price__label">Prijs per persoon</span>
					<span class="workshop-price__amount"><?php echo esc_html( $price ); ?></span>
					<?php if ( $price_sub ) : ?>
						<span class="workshop-price__sub"><?php echo esc_html( $price_sub ); ?></span>
					<?php endif; ?>
				</div>
				<?php endif; ?>
				<?php if ( $specs ) : ?>
				<ul class="workshop-specs__list">
					<?php foreach ( $specs as $row ) :
						if ( empty( $row['label'] ) && empty( $row['value'] ) ) continue;
					?>
					<li class="workshop-specs__item">
						<span class="workshop-specs__item-label"><?php echo esc_html( $row['label'] ); ?></span>
						<span class="workshop-specs__item-value"><?php echo esc_html( $row['value'] ); ?></span>
					</li>
					<?php endforeach; ?>
				</ul>
				<?php endif; ?>
				<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--primary" style="width:100%;justify-content:center;">
					Vraag offerte aan <span class="btn__arrow">&rarr;</span>
				</a>
			</div>
			<?php endif; ?>

			<!-- Wat leer je -->
			<div class="workshop-learn reveal">
				<?php if ( $eyebrow2 ) : ?>
					<span class="workshop-learn__eyebrow"><?php echo esc_html( $eyebrow2 ); ?></span>
				<?php endif; ?>
				<?php if ( $h2_2 ) : ?>
					<h2 class="workshop-learn__title"><?php echo esc_html( $h2_2 ); ?></h2>
				<?php endif; ?>
				<?php if ( $pills ) : ?>
				<ul class="workshop-pills">
					<?php foreach ( $pills as $pill ) : ?>
						<li class="workshop-pill"><?php echo esc_html( $pill ); ?></li>
					<?php endforeach; ?>
				</ul>
				<?php endif; ?>
				<?php if ( $closing ) : ?>
					<p class="workshop-learn__closing"><?php echo esc_html( $closing ); ?></p>
				<?php endif; ?>
			</div>

		</div>
	</div>
</section>

<!-- CTA -->
<section class="section">
	<div class="container">
		<div class="cta-banner reveal">
			<h2><?php echo esc_html( $cta_h2 ); ?></h2>
			<p>Vraag een vrijblijvende offerte aan. Binnen 24 uur ontvang je een voorstel op maat.</p>
			<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--dark">Boek een workshop <span class="btn__arrow">&rarr;</span></a>
		</div>
	</div>
</section>

<?php endwhile; ?>
<?php get_footer(); ?>
