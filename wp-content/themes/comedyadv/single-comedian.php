<?php
/**
 * Single comedian profile.
 */
get_header();

$contact_url   = comedyadv_url( 'contact' );
$home_url      = home_url( '/' );
$archive_url   = get_post_type_archive_link( 'comedian' );

while ( have_posts() ) : the_post();
	$id        = get_the_ID();
	$tag       = comedyadv_meta( $id, '_comedyadv_tag' );
	$image_url = comedyadv_image_url( $id, 'large' );
?>

<section class="page-hero">
	<div class="container page-hero__inner">
		<div class="page-hero__breadcrumbs"><?php echo comedyadv_breadcrumbs( array( array( 'Home', $home_url ), array( 'Comedians', $archive_url ), get_the_title() ) ); ?></div>
		<h1 class="page-hero__title"><span><?php the_title(); ?></span></h1>
		<?php if ( $tag ) : ?>
			<p class="page-hero__lead"><?php echo esc_html( $tag ); ?></p>
		<?php endif; ?>
	</div>
</section>

<section class="section">
	<div class="container">
		<div class="feature-split">
			<?php if ( $image_url ) : ?>
				<div class="feature-split__media reveal">
					<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php the_title_attribute(); ?>" />
				</div>
			<?php endif; ?>
			<div class="reveal">
				<?php if ( $tag ) : ?>
					<span class="section-head__eyebrow"><?php echo esc_html( $tag ); ?></span>
				<?php endif; ?>
				<h2><?php the_title(); ?></h2>
				<?php the_content(); ?>
				<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--primary">Boek <?php echo esc_html( explode( ' ', get_the_title() )[0] ); ?> <span class="btn__arrow">&rarr;</span></a>
			</div>
		</div>
	</div>
</section>

<?php
	// Show upcoming shows this comedian is booked for.
	$upcoming = get_posts( array(
		'post_type'      => 'show',
		'posts_per_page' => 5,
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
<section class="section section--grey">
	<div class="container">
		<div class="section-head">
			<span class="section-head__eyebrow">Live optredens</span>
			<h2>Aankomende shows</h2>
		</div>
		<div class="agenda-list">
			<?php foreach ( $upcoming as $show ) :
				$sid      = $show->ID;
				$date     = get_post_meta( $sid, '_comedyadv_show_date', true );
				$location = get_post_meta( $sid, '_comedyadv_show_location', true );
				$time     = get_post_meta( $sid, '_comedyadv_show_time', true );
				$price    = get_post_meta( $sid, '_comedyadv_show_price', true );
			?>
				<article class="agenda-item">
					<div class="agenda-item__date">
						<div class="agenda-item__day"><?php echo esc_html( comedyadv_show_day( $date ) ); ?></div>
						<div class="agenda-item__month"><?php echo esc_html( comedyadv_show_month( $date ) ); ?></div>
					</div>
					<div class="agenda-item__info">
						<h4><?php echo esc_html( $show->post_title ); ?></h4>
						<p class="agenda-item__meta"><?php echo esc_html( implode( ' • ', array_filter( array( $location, $time, $price ) ) ) ); ?></p>
					</div>
					<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--primary">Tickets</a>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php endwhile; ?>

<?php get_footer(); ?>
