<?php
/**
 * Footer template — shared across all pages.
 */
?>
</main>

<footer class="footer">
	<div class="container">
		<div class="footer__grid">
			<div class="footer__brand">
				<div class="logo">
					<span class="logo__dot"></span>
					Comedy<span style="color:var(--orange)">Adventure</span>
				</div>
				<p class="footer__tag">Het ultieme entertainment voor elk event. Stand-up comedyshows door heel Nederland.</p>
			</div>
			<div>
				<h4>Menu</h4>
				<ul class="footer__list">
					<li><a href="<?php echo esc_url( comedyadv_url( 'comedians' ) ); ?>">Comedians</a></li>
					<li><a href="<?php echo esc_url( comedyadv_url( 'aanbod' ) ); ?>">Aanbod</a></li>
					<li><a href="<?php echo esc_url( comedyadv_url( 'workshops' ) ); ?>">Workshops</a></li>
					<li><a href="<?php echo esc_url( comedyadv_url( 'agenda' ) ); ?>">Agenda</a></li>
					<li><a href="<?php echo esc_url( comedyadv_url( 'locaties' ) ); ?>">Locaties</a></li>
					<li><a href="<?php echo esc_url( comedyadv_url( 'contact' ) ); ?>">Contact</a></li>
				</ul>
			</div>
			<div>
				<h4>Steden</h4>
				<ul class="footer__list">
					<li><a href="<?php echo esc_url( comedyadv_url( 'amsterdam' ) ); ?>">Amsterdam</a></li>
					<li><a href="<?php echo esc_url( comedyadv_url( 'rotterdam' ) ); ?>">Rotterdam</a></li>
					<li><a href="<?php echo esc_url( comedyadv_url( 'utrecht' ) ); ?>">Utrecht</a></li>
					<li><a href="<?php echo esc_url( comedyadv_url( 'den-haag' ) ); ?>">Den Haag</a></li>
					<li><a href="<?php echo esc_url( comedyadv_url( 'eindhoven' ) ); ?>">Eindhoven</a></li>
					<li><a href="<?php echo esc_url( comedyadv_url( 'groningen' ) ); ?>">Groningen</a></li>
				</ul>
			</div>
			<div class="footer__contact">
				<h4>Contact</h4>
				<p>020-700 94 39</p>
				<p>info@comedyadventure.nl</p>
				<p>Van Boshuizenstraat 549<br />Amsterdam</p>
			</div>
		</div>
		<div class="footer__bottom">
			<span>&copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> Comedy Adventure. Alle rechten voorbehouden.</span>
			<span>KvK 12345678 &bull; BTW NL000000000B00</span>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
