<?php
/**
 * Default fallback template — used for any request not matched by a more specific template.
 */
get_header();
?>

<section class="page-hero">
	<div class="container page-hero__inner">
		<?php if ( is_singular() ) : ?>
			<div class="page-hero__breadcrumbs"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / <?php the_title(); ?></div>
			<h1 class="page-hero__title"><?php the_title(); ?></h1>
		<?php else : ?>
			<h1 class="page-hero__title">Comedy <span>Adventure</span></h1>
			<p class="page-hero__lead">Het ultieme entertainment voor elk event.</p>
		<?php endif; ?>
	</div>
</section>

<section class="section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<article <?php post_class(); ?>>
					<?php the_content(); ?>
				</article>
			<?php endwhile; ?>
		<?php else : ?>
			<p>Geen content gevonden.</p>
		<?php endif; ?>
	</div>
</section>

<?php get_footer(); ?>
