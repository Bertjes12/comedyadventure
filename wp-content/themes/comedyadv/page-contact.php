<?php
/**
 * Contact page — Gen Z redesign.
 */
get_header();

$home_url = home_url( '/' );
?>

<!-- BREADCRUMBS -->
<div class="workshop-breadcrumbs">
	<div class="container">
		<?php echo comedyadv_breadcrumbs( array( array( 'Home', $home_url ), 'Contact' ) ); ?>
	</div>
</div>

<!-- HERO -->
<div class="contact-hero">
	<div class="container contact-hero__inner">
		<div class="contact-hero__body">
			<span class="workshop-hero__tag">⚡ Reactie binnen 24 uur</span>
			<h1 class="contact-hero__title">Plan jouw<br><span>show</span></h1>
			<p class="contact-hero__lead">Vertel ons over je evenement. Wij sturen binnen 24 uur een voorstel op maat — inclusief comedians, prijs en opties.</p>
		</div>
		<div class="comedians-hero__deco">
			<span>HA</span>
			<span>HA</span>
			<span>HA</span>
		</div>
	</div>
</div>

<!-- TRUST STRIP -->
<div class="contact-trust">
	<div class="container contact-trust__inner">
		<div class="contact-trust__item">
			<span class="contact-trust__stat">24u</span>
			<span class="contact-trust__label">Reactie gegarandeerd</span>
		</div>
		<div class="contact-trust__divider"></div>
		<div class="contact-trust__item">
			<span class="contact-trust__stat">100<sup>+</sup></span>
			<span class="contact-trust__label">Shows verzorgd</span>
		</div>
		<div class="contact-trust__divider"></div>
		<div class="contact-trust__item">
			<span class="contact-trust__stat">100%</span>
			<span class="contact-trust__label">Op maat gemaakt</span>
		</div>
		<div class="contact-trust__divider"></div>
		<div class="contact-trust__item">
			<span class="contact-trust__stat">0</span>
			<span class="contact-trust__label">Verplichtingen</span>
		</div>
	</div>
</div>

<!-- FORM + CONTACT INFO -->
<div class="contact-main">
	<div class="container contact-main__inner">

		<!-- FORM -->
		<div class="contact-form-wrap reveal">
			<div class="contact-form-head">
				<span class="contact-form-head__eyebrow">Boekingsformulier</span>
				<h2>Vertel ons over<br>je evenement</h2>
				<p>Hoe meer je deelt, hoe beter we kunnen adviseren.</p>
			</div>
			<form class="contact-form" data-contact-form novalidate>
				<div class="contact-form__row">
					<div class="contact-form__group">
						<label class="contact-form__label" for="naam">Naam</label>
						<input class="contact-form__input" type="text" id="naam" name="naam" placeholder="Jouw naam" required />
					</div>
					<div class="contact-form__group">
						<label class="contact-form__label" for="bedrijf">Bedrijf <span>optioneel</span></label>
						<input class="contact-form__input" type="text" id="bedrijf" name="bedrijf" placeholder="Bedrijfsnaam" />
					</div>
				</div>
				<div class="contact-form__row">
					<div class="contact-form__group">
						<label class="contact-form__label" for="email">E-mailadres</label>
						<input class="contact-form__input" type="email" id="email" name="email" placeholder="jij@bedrijf.nl" required />
					</div>
					<div class="contact-form__group">
						<label class="contact-form__label" for="telefoon">Telefoon</label>
						<input class="contact-form__input" type="tel" id="telefoon" name="telefoon" placeholder="06-12345678" />
					</div>
				</div>
				<div class="contact-form__row">
					<div class="contact-form__group">
						<label class="contact-form__label" for="type">Type evenement</label>
						<select class="contact-form__input contact-form__select" id="type" name="type">
							<option value="">Kies een type...</option>
							<option>Bedrijfsfeest</option>
							<option>Diner</option>
							<option>Bruiloft</option>
							<option>Jubileum</option>
							<option>Personeelsuitje</option>
							<option>Anders</option>
						</select>
					</div>
					<div class="contact-form__group">
						<label class="contact-form__label" for="datum">Gewenste datum</label>
						<input class="contact-form__input" type="date" id="datum" name="datum" />
					</div>
				</div>
				<div class="contact-form__row">
					<div class="contact-form__group">
						<label class="contact-form__label" for="gasten">Aantal gasten</label>
						<select class="contact-form__input contact-form__select" id="gasten" name="gasten">
							<option value="">Kies een aantal...</option>
							<option>1 – 25 personen</option>
							<option>26 – 50 personen</option>
							<option>51 – 100 personen</option>
							<option>101 – 250 personen</option>
							<option>250+ personen</option>
						</select>
					</div>
					<div class="contact-form__group">
						<label class="contact-form__label" for="stad">Stad / locatie</label>
						<input class="contact-form__input" type="text" id="stad" name="stad" placeholder="bv. Amsterdam, Rotterdam..." />
					</div>
				</div>
				<div class="contact-form__group">
					<label class="contact-form__label" for="format">Gewenst format <span>optioneel</span></label>
					<select class="contact-form__input contact-form__select" id="format" name="format">
						<option value="">Nog niet zeker / laat ons adviseren</option>
						<option>Big Comedy Show</option>
						<option>Comedy Diner</option>
						<option>Workshop</option>
						<option>Roast</option>
						<option>Op maat</option>
					</select>
				</div>
				<div class="contact-form__group">
					<label class="contact-form__label" for="bericht">Vertel ons meer <span>optioneel</span></label>
					<textarea class="contact-form__input contact-form__textarea" id="bericht" name="bericht" placeholder="Sfeer, bijzondere wensen, thema..."></textarea>
				</div>
				<button type="submit" class="btn btn--primary contact-form__submit">
					Verstuur aanvraag <span class="btn__arrow">&rarr;</span>
				</button>
				<p class="contact-form__disclaimer">✓ Vrijblijvend &nbsp;·&nbsp; ✓ Geen spam &nbsp;·&nbsp; ✓ Reactie binnen 24 uur</p>
				<p data-form-status class="contact-form__status" role="status" aria-live="polite"></p>
			</form>
		</div>

		<!-- CONTACT INFO -->
		<div class="contact-info reveal">

			<div class="contact-info__card">
				<span class="contact-info__icon">📞</span>
				<div>
					<span class="contact-info__label">Telefoon</span>
					<a class="contact-info__value" href="tel:0207009439">020-700 94 39</a>
				</div>
			</div>

			<div class="contact-info__card">
				<span class="contact-info__icon">✉️</span>
				<div>
					<span class="contact-info__label">E-mail</span>
					<a class="contact-info__value" href="mailto:info@comedyadventure.nl">info@comedyadventure.nl</a>
				</div>
			</div>

			<div class="contact-info__card">
				<span class="contact-info__icon">📍</span>
				<div>
					<span class="contact-info__label">Adres</span>
					<span class="contact-info__value">Van Boshuizenstraat 549<br>1082 XR Amsterdam</span>
				</div>
			</div>

			<div class="contact-info__hours">
				<h3>Openingstijden</h3>
				<div class="contact-info__hour-row">
					<span>Ma &ndash; vr</span><span>09:00 – 18:00</span>
				</div>
				<div class="contact-info__hour-row">
					<span>Zaterdag</span><span>10:00 – 16:00</span>
				</div>
				<div class="contact-info__hour-row contact-info__hour-row--closed">
					<span>Zondag</span><span>Gesloten</span>
				</div>
			</div>

			<div class="contact-info__promise">
				<span class="contact-info__promise-icon">⚡</span>
				<div>
					<strong>Reactie binnen 24 uur</strong>
					<p>Werkdagen reageren we altijd binnen één werkdag met een voorstel op maat.</p>
				</div>
			</div>

		</div>

	</div>
</div>

<!-- REVIEWS -->
<?php
$page_reviews = (array) get_post_meta( get_the_ID(), '_comedyadv_page_reviews', true );
$page_reviews = array_values( array_filter( $page_reviews, function( $r ) { return ! empty( $r['text'] ); } ) );
if ( $page_reviews ) :
?>
<section class="contact-reviews">
	<div class="container">
		<div class="contact-reviews__head">
			<span class="section-head__eyebrow">Wat klanten zeggen</span>
			<h2>Tevreden opdrachtgevers</h2>
		</div>
		<div class="contact-reviews__grid">
			<?php foreach ( $page_reviews as $review ) : ?>
			<div class="contact-review-card reveal">
				<div class="contact-review-card__stars">★★★★★</div>
				<p>"<?php echo esc_html( $review['text'] ); ?>"</p>
				<?php if ( ! empty( $review['author'] ) ) :
					$parts = explode( ' — ', $review['author'], 2 );
				?>
				<div class="contact-review-card__author">
					<strong><?php echo esc_html( $parts[0] ); ?></strong>
					<?php if ( isset( $parts[1] ) ) : ?><span><?php echo esc_html( $parts[1] ); ?></span><?php endif; ?>
				</div>
				<?php endif; ?>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php get_footer(); ?>
