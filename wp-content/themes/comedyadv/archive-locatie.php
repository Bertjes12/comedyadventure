<?php
/**
 * Archive: Locaties.
 */
get_header();

$contact_url = comedyadv_url( 'contact' );
$home_url    = home_url( '/' );
?>

<section class="page-hero">
	<div class="container page-hero__inner">
		<div class="page-hero__breadcrumbs"><?php echo comedyadv_breadcrumbs( array( array( 'Home', $home_url ), 'Locaties' ) ); ?></div>
		<h1 class="page-hero__title">Comedy door <span>heel Nederland</span></h1>
		<p class="page-hero__lead">Wij organiseren shows in alle grote steden van Nederland. Klik op een stad voor specifieke informatie en boekingsmogelijkheden in jouw regio.</p>
	</div>
</section>

<section class="section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<div class="city-grid">
				<?php while ( have_posts() ) : the_post();
					$bg = comedyadv_image_url( get_the_ID(), 'large' );
				?>
					<a href="<?php the_permalink(); ?>" class="city-card">
						<?php if ( $bg ) : ?>
							<div class="city-card__bg" style="background-image:url('<?php echo esc_url( $bg ); ?>')"></div>
						<?php endif; ?>
						<div class="city-card__inner">
							<div class="city-card__name"><?php the_title(); ?></div>
							<div class="city-card__cta">Bekijk shows</div>
						</div>
					</a>
				<?php endwhile; ?>
			</div>
		<?php else : ?>
			<p>Nog geen locaties toegevoegd.</p>
		<?php endif; ?>
	</div>
</section>

<section class="section section--dark">
	<div class="container">
		<div class="section-head">
			<span class="section-head__eyebrow">Niet jouw stad erbij?</span>
			<h2>Wij komen overal in Nederland.</h2>
			<p class="section-head__lead">Bovenstaande steden zijn onze meest geboekte locaties, maar wij organiseren shows in heel Nederland. Van Maastricht tot Leeuwarden &mdash; neem contact op voor de mogelijkheden.</p>
		</div>
		<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--primary">Plan jouw show <span class="btn__arrow">&rarr;</span></a>
	</div>
</section>

<?php get_footer(); ?>
