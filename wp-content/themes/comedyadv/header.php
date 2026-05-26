<?php
/**
 * Header template — shared across all pages.
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="header">
	<div class="container header__inner">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo" aria-label="Comedy Adventure home">
			<span class="logo__dot"></span>
			Comedy<span style="color:var(--orange)">Adventure</span>
		</a>
		<button class="nav-toggle" aria-label="Menu" aria-expanded="false" aria-controls="primary-nav">
			<span class="nav-toggle__bar"></span>
		</button>
		<nav class="nav" id="primary-nav" aria-label="Hoofdnavigatie">
			<?php comedyadv_primary_nav(); ?>
		</nav>
	</div>
</header>

<main>
