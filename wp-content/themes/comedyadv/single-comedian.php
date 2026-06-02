<?php
/**
 * Single comedian profile — Gen Z Poster redesign.
 */
get_header();

$contact_url = comedyadv_url( 'contact' );
$home_url    = home_url( '/' );
$archive_url = get_post_type_archive_link( 'comedian' );

while ( have_posts() ) : the_post();
	$id        = get_the_ID();
	$tag       = comedyadv_meta( $id, '_comedyadv_tag' );
	$image_url = comedyadv_image_url( $id, 'large' );
	$firstname = explode( ' ', get_the_title() )[0];
?>

<!-- BREADCRUMBS -->
<div class="comedian-breadcrumbs">
	<div class="container">
		<?php echo comedyadv_breadcrumbs( array( array( 'Home', $home_url ), array( 'Comedians', $archive_url ), get_the_title() ) ); ?>
	</div>
</div>

<!-- HERO: concert-poster layout -->
<div class="comedian-hero">
	<div class="container">
		<div class="comedian-hero__inner">

			<!-- LINKS: naam + info + bio + CTA -->
			<div class="comedian-hero__body">
				<?php if ( $tag ) : ?>
					<span class="comedian-hero__tag"><?php echo esc_html( $tag ); ?></span>
				<?php endif; ?>
				<h1 class="comedian-hero__name"><?php the_title(); ?></h1>
				<div class="comedian-hero__label">Comedian</div>
				<div class="comedian-hero__bio">
					<?php the_content(); ?>
				</div>
				<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--primary comedian-hero__cta">
					Boek <?php echo esc_html( $firstname ); ?> <span class="btn__arrow">&rarr;</span>
				</a>
			</div>

			<!-- RECHTS: foto -->
			<?php if ( $image_url ) : ?>
			<div class="comedian-hero__photo reveal">
				<div class="comedian-hero__photo-wrap">
					<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php the_title_attribute(); ?>" />
				</div>
			</div>
			<?php endif; ?>

		</div>
	</div>
</div>

<!-- AANKOMENDE SHOWS -->
<?php
$upcoming = get_posts( array(
	'post_type'      => 'show',
	'posts_per_page' => 4,
	'meta_key'       => '_comedyadv_show_date',
	'orderby'        => 'meta_value',
	'order'          => 'ASC',
	'meta_query'     => array(
		array(
			'key'     => '_comedyadv_show_comedians',
			'value'   => '"' . $id . '"',
			'compare' => 'LIKE',
		),
	),
) );
?>

<?php if ( $upcoming ) : ?>
<section class="comedian-shows">
	<div class="container">
		<div class="comedian-shows__head">
			<span class="section-head__eyebrow">Live optredens</span>
			<h2>Aankomende shows</h2>
		</div>
		<div class="comedian-shows__grid">
			<?php foreach ( $upcoming as $show ) :
				$sid      = $show->ID;
				$date     = get_post_meta( $sid, '_comedyadv_show_date', true );
				$location = get_post_meta( $sid, '_comedyadv_show_location', true );
				$time     = get_post_meta( $sid, '_comedyadv_show_time', true );
				$price    = get_post_meta( $sid, '_comedyadv_show_price', true );
				$date_fmt = $date ? comedyadv_show_long_date( $date ) : '';
				$city_id  = (int) get_post_meta( $sid, '_comedyadv_show_city', true );
				$show_img = comedyadv_image_url( $sid, 'large' );
				if ( ! $show_img && $city_id ) {
					$show_img = comedyadv_image_url( $city_id, 'large' );
				}
			?>
			<article class="comedian-show-card reveal">
				<?php if ( $show_img ) : ?>
					<div class="comedian-show-card__bg" style="background-image:url('<?php echo esc_url( $show_img ); ?>')"></div>
				<?php endif; ?>
				<div class="comedian-show-card__inner">
					<div class="comedian-show-card__date">
						<span class="comedian-show-card__day"><?php echo esc_html( comedyadv_show_day( $date ) ); ?></span>
						<span class="comedian-show-card__month"><?php echo esc_html( comedyadv_show_month( $date ) ); ?></span>
					</div>
					<div class="comedian-show-card__info">
						<h3><?php echo esc_html( $show->post_title ); ?></h3>
						<p><?php echo esc_html( implode( ' · ', array_filter( array( $location, $time, $price ) ) ) ); ?></p>
					</div>
					<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--primary">Tickets</a>
				</div>
			</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php endwhile; ?>
<?php get_footer(); ?>
