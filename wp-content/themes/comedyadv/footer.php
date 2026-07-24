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
<span>Powered by <a href="https://www.getevents.nl" target="_blank" rel="noopener noreferrer" style="color:inherit;text-decoration:underline;">Get Events B.V.</a></span>
		</div>
	</div>
</footer>

<!-- RESERVEER MODAL -->
<div class="reservation-modal" data-reserveer-modal aria-hidden="true">
	<div class="reservation-modal__overlay" data-reserveer-close></div>
	<div class="reservation-modal__dialog" role="dialog" aria-modal="true" aria-label="Reserveren">
		<button type="button" class="reservation-modal__close" data-reserveer-close aria-label="Sluiten">&times;</button>
		<span class="workshop-hero__tag">🎟 Reserveren</span>
		<h2 class="reservation-modal__title">Reserveer je plek</h2>
		<p class="reservation-modal__summary" data-reserveer-summary></p>
		<form class="contact-form" data-reserveer-form novalidate>
			<input type="hidden" name="show"  data-reserveer-field="show" />
			<input type="hidden" name="stad"  data-reserveer-field="stad" />
			<input type="hidden" name="datum" data-reserveer-field="datum" />
			<input type="hidden" name="tijd"  data-reserveer-field="tijd" />
			<div class="contact-form__row">
				<div class="contact-form__group">
					<label class="contact-form__label" for="reserveer-naam">Naam</label>
					<input class="contact-form__input" type="text" id="reserveer-naam" name="naam" placeholder="Jouw naam" required />
				</div>
				<div class="contact-form__group">
					<label class="contact-form__label" for="reserveer-personen">Aantal personen</label>
					<select class="contact-form__input contact-form__select" id="reserveer-personen" name="personen">
						<option value="">Kies een aantal...</option>
						<option>1 persoon</option>
						<option>2 personen</option>
						<option>3 – 4 personen</option>
						<option>5 – 9 personen</option>
						<option>10+ personen</option>
					</select>
				</div>
			</div>
			<div class="contact-form__row">
				<div class="contact-form__group">
					<label class="contact-form__label" for="reserveer-email">E-mailadres</label>
					<input class="contact-form__input" type="email" id="reserveer-email" name="email" placeholder="jij@voorbeeld.nl" required />
				</div>
				<div class="contact-form__group">
					<label class="contact-form__label" for="reserveer-telefoon">Telefoon <span>optioneel</span></label>
					<input class="contact-form__input" type="tel" id="reserveer-telefoon" name="telefoon" placeholder="06-12345678" />
				</div>
			</div>
			<div class="contact-form__group">
				<label class="contact-form__label" for="reserveer-bericht">Opmerking <span>optioneel</span></label>
				<textarea class="contact-form__input contact-form__textarea" id="reserveer-bericht" name="bericht" placeholder="Bijzondere wensen, dieetwensen..."></textarea>
			</div>
			<button type="submit" class="btn btn--primary contact-form__submit">
				Verstuur reservering <span class="btn__arrow">&rarr;</span>
			</button>
			<p class="contact-form__disclaimer">✓ Vrijblijvend &nbsp;·&nbsp; ✓ Bevestiging binnen 24 uur</p>
			<p data-form-status class="contact-form__status" role="status" aria-live="polite"></p>
		</form>
	</div>
</div>

<?php wp_footer(); ?>
</body>
</html>
