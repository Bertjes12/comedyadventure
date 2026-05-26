<?php
/**
 * Contact page.
 */
get_header();

$home_url = home_url( '/' );
?>

<section class="page-hero">
	<div class="container page-hero__inner">
		<div class="page-hero__breadcrumbs"><?php echo comedyadv_breadcrumbs( array( array( 'Home', $home_url ), 'Contact' ) ); ?></div>
		<h1 class="page-hero__title">Plan jouw <span>show</span></h1>
		<p class="page-hero__lead">Vertel ons over je evenement. Wij sturen binnen 24 uur een voorstel op maat &mdash; inclusief beschikbare comedians, prijsindicatie en aanvullende opties.</p>
	</div>
</section>

<section class="section">
	<div class="container">
		<div class="feature-split">
			<div class="reveal">
				<span class="section-head__eyebrow">Boekingsformulier</span>
				<h2>Vertel ons over je event</h2>
				<p>Hoe meer je deelt, hoe beter wij kunnen adviseren. We reageren binnen &eacute;&eacute;n werkdag.</p>
				<form class="form" data-contact-form novalidate>
					<div class="form__row">
						<div class="form__group">
							<label class="form__label" for="naam">Naam</label>
							<input class="form__input" type="text" id="naam" name="naam" required />
						</div>
						<div class="form__group">
							<label class="form__label" for="bedrijf">Bedrijf (optioneel)</label>
							<input class="form__input" type="text" id="bedrijf" name="bedrijf" />
						</div>
					</div>
					<div class="form__row">
						<div class="form__group">
							<label class="form__label" for="email">E-mailadres</label>
							<input class="form__input" type="email" id="email" name="email" required />
						</div>
						<div class="form__group">
							<label class="form__label" for="telefoon">Telefoon</label>
							<input class="form__input" type="tel" id="telefoon" name="telefoon" />
						</div>
					</div>
					<div class="form__row">
						<div class="form__group">
							<label class="form__label" for="type">Type evenement</label>
							<select class="form__select" id="type" name="type">
								<option>Bedrijfsfeest</option>
								<option>Diner</option>
								<option>Bruiloft</option>
								<option>Jubileum</option>
								<option>Personeelsuitje</option>
								<option>Anders</option>
							</select>
						</div>
						<div class="form__group">
							<label class="form__label" for="datum">Gewenste datum</label>
							<input class="form__input" type="date" id="datum" name="datum" />
						</div>
					</div>
					<div class="form__group">
						<label class="form__label" for="bericht">Bericht</label>
						<textarea class="form__textarea" id="bericht" name="bericht" placeholder="Locatie, aantal gasten, sfeer, eventuele wensen..."></textarea>
					</div>
					<button type="submit" class="btn btn--primary">Verstuur aanvraag <span class="btn__arrow">&rarr;</span></button>
					<p data-form-status role="status" aria-live="polite"></p>
				</form>
			</div>
			<div class="reveal">
				<h3 style="margin-bottom:24px;">Direct contact</h3>
				<p style="margin-bottom:8px;"><strong>Telefoon</strong><br><a href="tel:0207009439">020-700 94 39</a></p>
				<p style="margin-bottom:8px;"><strong>E-mail</strong><br><a href="mailto:info@comedyadventure.nl">info@comedyadventure.nl</a></p>
				<p style="margin-bottom:24px;"><strong>Bezoekadres</strong><br>Van Boshuizenstraat 549<br>1082 XR Amsterdam</p>
				<h3 style="margin-bottom:16px;margin-top:32px;">Openingstijden</h3>
				<p style="margin-bottom:4px;">Maandag t/m vrijdag: 09:00 &ndash; 18:00</p>
				<p style="margin-bottom:4px;">Zaterdag: 10:00 &ndash; 16:00</p>
				<p>Zondag: gesloten</p>
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?>
