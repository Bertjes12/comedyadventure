<?php
/**
 * Archive: Locaties — Gen Z Tour Poster redesign.
 */
get_header();

$contact_url = comedyadv_url( 'contact' );
$home_url    = home_url( '/' );

// Collect all cities for the ticker
$all_cities = get_posts( array(
	'post_type'      => 'locatie',
	'posts_per_page' => -1,
	'orderby'        => 'title',
	'order'          => 'ASC',
	'fields'         => 'ids',
) );
$city_names = array_map( 'get_the_title', $all_cities );
?>

<!-- BREADCRUMBS -->
<div class="workshop-breadcrumbs">
	<div class="container">
		<?php echo comedyadv_breadcrumbs( array( array( 'Home', $home_url ), 'Locaties' ) ); ?>
	</div>
</div>

<!-- HERO -->
<div class="locaties-hero">
	<div class="container">
		<div class="locaties-hero__inner">
			<div class="locaties-hero__body">
				<span class="workshop-hero__tag">🗺️ Door heel Nederland</span>
				<h1 class="locaties-hero__title">Kies<br><span>jouw stad</span></h1>
				<p class="locaties-hero__lead">Comedy shows & diners in alle grote steden. Klik op een stad voor shows, info en boekingsmogelijkheden in jouw regio.</p>
			</div>
			<div class="locaties-hero__arrow">↓</div>
		</div>
	</div>
</div>

<!-- CITY GRID -->
<div class="locaties-grid-section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
		<div class="city-grid locaties-city-grid">
			<?php while ( have_posts() ) : the_post();
				$bg = comedyadv_image_url( get_the_ID(), 'large' );
			?>
			<a href="<?php the_permalink(); ?>" class="city-card reveal">
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
</div>

<!-- NIET JOUW STAD -->
<section class="locaties-cta">
	<div class="container">
		<div class="locaties-cta__inner">
			<div class="locaties-cta__text">
				<span class="section-head__eyebrow">Niet jouw stad erbij?</span>
				<h2>Wij komen<br><span>overal.</span></h2>
				<p>Van Maastricht tot Leeuwarden — we organiseren shows in heel Nederland. Neem contact op voor de mogelijkheden.</p>
			</div>
			<div class="locaties-cta__action">
				<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--primary locaties-cta__btn">
					Plan jouw show <span class="btn__arrow">&rarr;</span>
				</a>
				<span class="locaties-cta__sub">Reactie binnen 24 uur</span>
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?>
