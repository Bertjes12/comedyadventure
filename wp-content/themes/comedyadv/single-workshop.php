<?php
/**
 * Single workshop page.
 */
get_header();

$contact_url   = comedyadv_url( 'contact' );
$home_url      = home_url( '/' );
$archive_url   = get_post_type_archive_link( 'workshop' );

while ( have_posts() ) : the_post();
	$id          = get_the_ID();
	$breadcrumb  = comedyadv_meta( $id, '_comedyadv_breadcrumb', get_the_title() );
	$title_html  = comedyadv_meta( $id, '_comedyadv_title_html', get_the_title() );
	$lead        = comedyadv_meta( $id, '_comedyadv_lead' );
	$image_url   = comedyadv_image_url( $id, 'large' );
	$eyebrow1    = comedyadv_meta( $id, '_comedyadv_eyebrow1', 'Over deze workshop' );
	$h2_1        = comedyadv_meta( $id, '_comedyadv_h2_1' );
	$price       = comedyadv_meta( $id, '_comedyadv_price' );
	$price_sub   = comedyadv_meta( $id, '_comedyadv_price_sub' );
	$specs       = (array) comedyadv_meta( $id, '_comedyadv_specs', array() );
	$eyebrow2    = comedyadv_meta( $id, '_comedyadv_eyebrow2', 'Wat leer je?' );
	$h2_2        = comedyadv_meta( $id, '_comedyadv_h2_2' );
	$pills       = (array) comedyadv_meta( $id, '_comedyadv_pills', array() );
	$closing     = comedyadv_meta( $id, '_comedyadv_closing' );
	$cta_h2      = comedyadv_meta( $id, '_comedyadv_cta_h2', 'Klaar om te boeken?' );
?>

<section class="page-hero">
	<div class="container page-hero__inner">
		<div class="page-hero__breadcrumbs"><?php echo comedyadv_breadcrumbs( array( array( 'Home', $home_url ), array( 'Workshops', $archive_url ), $breadcrumb ) ); ?></div>
		<h1 class="page-hero__title"><?php echo wp_kses_post( $title_html ); ?></h1>
		<?php if ( $lead ) : ?>
			<p class="page-hero__lead"><?php echo esc_html( $lead ); ?></p>
		<?php endif; ?>
	</div>
</section>

<section class="section">
	<div class="container">
		<div class="feature-split">
			<?php if ( $image_url ) : ?>
				<div class="feature-split__media reveal">
					<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $breadcrumb ); ?> in actie" />
				</div>
			<?php endif; ?>
			<div class="reveal">
				<?php if ( $eyebrow1 ) : ?>
					<span class="section-head__eyebrow"><?php echo esc_html( $eyebrow1 ); ?></span>
				<?php endif; ?>
				<?php if ( $h2_1 ) : ?>
					<h2><?php echo esc_html( $h2_1 ); ?></h2>
				<?php endif; ?>
				<?php the_content(); ?>
				<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--primary">Boek deze workshop <span class="btn__arrow">&rarr;</span></a>
			</div>
		</div>
	</div>
</section>

<section class="section section--grey">
	<div class="container">
		<div class="feature-split feature-split--reverse">
			<div class="feature-split__media reveal" style="aspect-ratio:1/1;">
				<div class="specs">
					<?php if ( $price ) : ?>
						<div class="specs__price">
							<div class="specs__price-label">Prijs per persoon</div>
							<div class="specs__price-amount"><?php echo esc_html( $price ); ?></div>
							</div>
					<?php endif; ?>
					<?php if ( $specs ) : ?>
						<ul class="specs__list">
							<?php foreach ( $specs as $row ) :
								if ( empty( $row['label'] ) && empty( $row['value'] ) ) {
									continue;
								}
							?>
								<li class="specs__item"><span class="specs__item-label"><?php echo esc_html( $row['label'] ); ?></span><span class="specs__item-value"><?php echo esc_html( $row['value'] ); ?></span></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
					<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--primary">Vraag offerte aan <span class="btn__arrow">&rarr;</span></a>
				</div>
			</div>
			<div class="reveal">
				<?php if ( $eyebrow2 ) : ?>
					<span class="section-head__eyebrow"><?php echo esc_html( $eyebrow2 ); ?></span>
				<?php endif; ?>
				<?php if ( $h2_2 ) : ?>
					<h2><?php echo esc_html( $h2_2 ); ?></h2>
				<?php endif; ?>
				<?php if ( $pills ) : ?>
					<ul class="pill-list">
						<?php foreach ( $pills as $pill ) : ?>
							<li class="pill"><?php echo esc_html( $pill ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<?php if ( $closing ) : ?>
					<p><?php echo esc_html( $closing ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>

<section class="section">
	<div class="container">
		<div class="cta-banner">
			<h2><?php echo esc_html( $cta_h2 ); ?></h2>
			<p>Vraag een vrijblijvende offerte aan. Binnen 24 uur ontvang je een voorstel op maat.</p>
			<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--dark">Boek een workshop <span class="btn__arrow">&rarr;</span></a>
		</div>
	</div>
</section>

<?php endwhile; ?>

<?php get_footer(); ?>
