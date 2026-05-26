<?php
/**
 * Agenda page — pulls from the show CPT (data-only) and renders an upcoming + past list.
 * Each show title links to its linked locatie page.
 */
get_header();

$contact_url = comedyadv_url( 'contact' );
$home_url    = home_url( '/' );
$today       = date( 'Y-m-d' );

$upcoming = get_posts( array(
	'post_type'      => 'show',
	'posts_per_page' => -1,
	'meta_key'       => '_comedyadv_show_date',
	'orderby'        => 'meta_value',
	'order'          => 'ASC',
	'meta_query'     => array(
		array( 'key' => '_comedyadv_show_date', 'value' => $today, 'compare' => '>=' ),
	),
) );
$past = get_posts( array(
	'post_type'      => 'show',
	'posts_per_page' => -1,
	'meta_key'       => '_comedyadv_show_date',
	'orderby'        => 'meta_value',
	'order'          => 'DESC',
	'meta_query'     => array(
		array( 'key' => '_comedyadv_show_date', 'value' => $today, 'compare' => '<' ),
	),
) );

/**
 * Render one show as an agenda-item article.
 */
function comedyadv_render_agenda_item( $show, $is_past = false ) {
	$contact_url = comedyadv_url( 'contact' );
	$id          = $show->ID;
	$date        = get_post_meta( $id, '_comedyadv_show_date', true );
	$time        = get_post_meta( $id, '_comedyadv_show_time', true );
	$venue       = get_post_meta( $id, '_comedyadv_show_location', true );
	$price       = get_post_meta( $id, '_comedyadv_show_price', true );
	$city_id     = (int) get_post_meta( $id, '_comedyadv_show_city', true );
	$city        = $city_id ? get_post( $city_id ) : null;
	$city_lbl    = $city ? $city->post_title : '';
	$city_url    = $city ? get_permalink( $city ) : '';

	$meta_parts = array_filter( array( $city_lbl ? $city_lbl : $venue, $time, ( $is_past ? '' : $price ) ) );
	?>
	<article class="agenda-item reveal" <?php if ( $is_past ) echo 'style="opacity:0.7;"'; ?>>
		<div class="agenda-item__date">
			<div class="agenda-item__day"><?php echo esc_html( comedyadv_show_day( $date ) ); ?></div>
			<div class="agenda-item__month"><?php echo esc_html( comedyadv_show_month( $date ) ); ?></div>
		</div>
		<div class="agenda-item__info">
			<h4>
			<?php if ( $city_url ) : ?>
				<a href="<?php echo esc_url( $city_url ); ?>" style="color:inherit;text-decoration:none;border-bottom:1px solid transparent;" onmouseover="this.style.borderColor='var(--orange)'" onmouseout="this.style.borderColor='transparent'"><?php echo esc_html( $show->post_title ); ?></a>
			<?php else : echo esc_html( $show->post_title ); endif; ?>
			</h4>
			<?php if ( $meta_parts ) : ?>
				<p class="agenda-item__meta"><?php echo esc_html( implode( ' • ', $meta_parts ) ); ?><?php if ( $is_past ) echo ' &bull; geweest'; ?></p>
			<?php endif; ?>
		</div>
		<?php if ( $is_past ) : ?>
			<?php if ( $city_url ) : ?>
				<a href="<?php echo esc_url( $city_url ); ?>" class="btn btn--dark">Bekijk locatie</a>
			<?php endif; ?>
		<?php else : ?>
			<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--primary">Tickets</a>
		<?php endif; ?>
	</article>
	<?php
}
?>

<section class="page-hero">
	<div class="container page-hero__inner">
		<div class="page-hero__breadcrumbs"><?php echo comedyadv_breadcrumbs( array( array( 'Home', $home_url ), 'Agenda' ) ); ?></div>
		<h1 class="page-hero__title">Aankomende <span>shows</span></h1>
		<p class="page-hero__lead">Onze publieke shows door heel Nederland. Klik op een show voor de bijbehorende locatie en boekingsmogelijkheden.</p>
	</div>
</section>

<section class="section">
	<div class="container">
		<?php if ( $upcoming ) : ?>
			<div class="agenda-list">
				<?php foreach ( $upcoming as $show ) {
					comedyadv_render_agenda_item( $show, false );
				} ?>
			</div>
		<?php else : ?>
			<p>Er staan op dit moment geen shows gepland. Neem <a href="<?php echo esc_url( $contact_url ); ?>">contact</a> op voor een privéshow.</p>
		<?php endif; ?>
	</div>
</section>

<?php if ( $past ) : ?>
<section class="section section--grey">
	<div class="container">
		<div class="section-head">
			<span class="section-head__eyebrow">Archief</span>
			<h2>Eerdere shows</h2>
			<p class="section-head__lead">Een terugblik op recente edities. Klik door naar de locatie voor meer informatie over die stad.</p>
		</div>
		<div class="agenda-list">
			<?php foreach ( $past as $show ) {
				comedyadv_render_agenda_item( $show, true );
			} ?>
		</div>
	</div>
</section>
<?php endif; ?>

<section class="section">
	<div class="container">
		<div class="cta-banner">
			<h2>Liever een priv&eacute;show?</h2>
			<p>Wij organiseren ook besloten shows op jouw locatie. Vraag een offerte aan en wij regelen het.</p>
			<a href="<?php echo esc_url( $contact_url ); ?>" class="btn btn--dark">Plan een priv&eacute;show <span class="btn__arrow">&rarr;</span></a>
		</div>
	</div>
</section>

<?php get_footer(); ?>
