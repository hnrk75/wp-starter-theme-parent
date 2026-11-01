<?php
/**
 * Privacy policy page setup and Policy Guide suggestion.
 *
 * On theme activation the privacy policy page is created (or updated) with
 * our GDPR-compliant Swedish template as native Gutenberg blocks.
 * Replace all [HAKPARENTES] placeholders with project-specific info before going live.
 * Links between the two policy pages (href="#wpst-privacy-policy" / "#wpst-cookie-policy")
 * are resolved automatically and should be left as they are.
 *
 * @package WP Starter Theme
 */

/**
 * Creates a policy page from our template, or fills an existing one.
 *
 * An existing page is only filled once, and only while it is still a draft
 * (e.g. the page WordPress creates on install). Pages that already have our
 * template, or have been published, are left untouched so that edited
 * content is never overwritten when the theme is reactivated.
 *
 * @param string $option  Option that stores the page ID.
 * @param string $title   Title for a new page.
 * @param string $content Block content.
 */
function wpst_setup_policy_page( $option, $title, $content ) {
	$page = get_post( (int) get_option( $option ) );

	if ( $page ) {
		if ( 'draft' !== $page->post_status || get_post_meta( $page->ID, '_wpst_policy_template', true ) ) {
			return;
		}

		wp_update_post(
			array(
				'ID'           => $page->ID,
				'post_content' => $content,
			)
		);
		update_post_meta( $page->ID, '_wpst_policy_template', 1 );
		return;
	}

	$page_id = wp_insert_post(
		array(
			'post_title'   => $title,
			'post_status'  => 'draft',
			'post_type'    => 'page',
			'post_content' => $content,
		)
	);

	if ( $page_id ) {
		update_option( $option, $page_id );
		update_post_meta( $page_id, '_wpst_policy_template', 1 );
	}
}

function wpst_setup_privacy_policy_page() {
	wpst_setup_policy_page( 'wp_page_for_privacy_policy', __( 'Privacy Policy', 'wp-starter-theme' ), wpst_get_privacy_policy_template() );
}
add_action( 'after_switch_theme', 'wpst_setup_privacy_policy_page' );

function wpst_setup_cookie_policy_page() {
	wpst_setup_policy_page( 'wpst_page_for_cookie_policy', __( 'Cookie Policy', 'wp-starter-theme' ), wpst_get_cookie_policy_template() );
}
add_action( 'after_switch_theme', 'wpst_setup_cookie_policy_page' );

function wpst_get_cookie_policy_url() {
	$page_id = (int) get_option( 'wpst_page_for_cookie_policy' );

	return $page_id && 'publish' === get_post_status( $page_id ) ? get_permalink( $page_id ) : '';
}

/**
 * The templates link to each other with placeholder hrefs that are resolved on output,
 * so the links follow the pages' current URLs. A link to an unpublished page becomes plain text.
 *
 * @param string $content Post content.
 * @return string
 */
function wpst_resolve_policy_links( $content ) {
	if ( ! str_contains( $content, 'href="#wpst-' ) ) {
		return $content;
	}

	$urls = array(
		'wpst-privacy-policy' => get_privacy_policy_url(),
		'wpst-cookie-policy'  => wpst_get_cookie_policy_url(),
	);

	foreach ( $urls as $token => $url ) {
		if ( $url ) {
			$content = str_replace( 'href="#' . $token . '"', 'href="' . esc_url( $url ) . '"', $content );
		} else {
			$content = preg_replace( '~<a\s[^>]*href="#' . $token . '"[^>]*>(.*?)</a>~s', '$1', $content );
		}
	}

	return $content;
}
add_filter( 'the_content', 'wpst_resolve_policy_links' );

function wpst_privacy_policy_content() {
	if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
		return;
	}

	wp_add_privacy_policy_content( get_bloginfo( 'name' ), wp_kses_post( wpst_get_privacy_policy_template() ) );
}
add_action( 'admin_init', 'wpst_privacy_policy_content' );

function wpst_get_privacy_policy_template() {
	return '<!-- wp:paragraph -->
<p>Vi på [FÖRETAGSNAMN] värnar om din personliga integritet. Den här policyn förklarar vilka personuppgifter vi behandlar, varför vi behandlar dem och vilka rättigheter du har.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>Senast uppdaterad:</strong> [DATUM]</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">1. Personuppgiftsansvarig</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>[FÖRETAGSNAMN]<br>[GATUADRESS], [POSTNUMMER] [STAD]<br>Org.nr: [ORGANISATIONSNUMMER]<br>E-post: <a href="mailto:[E-POST]">[E-POST]</a><br>Telefon: [TELEFONNUMMER]</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">2. Vilka uppgifter vi samlar in och varför</h3>
<!-- /wp:heading -->

<!-- wp:heading {"level":4} -->
<h4 class="wp-block-heading">Kontaktformulär</h4>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>När du fyller i ett kontaktformulär behandlar vi ditt namn, din e-postadress och innehållet i ditt meddelande. Rättslig grund: berättigat intresse (att besvara din förfrågan). Uppgifterna raderas när ärendet är avslutat.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":4} -->
<h4 class="wp-block-heading">Nyhetsbrev</h4>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Om du prenumererar på vårt nyhetsbrev behandlar vi din e-postadress. Rättslig grund: samtycke. Du kan när som helst avregistrera dig via länken i varje utskick. Uppgifterna raderas vid avregistrering.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":4} -->
<h4 class="wp-block-heading">Cookies och analysverktyg</h4>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Vi använder cookies för att webbplatsen ska fungera korrekt och för att förstå hur den används. Se vår <a href="#wpst-cookie-policy">cookiepolicy</a> för fullständig information. Rättslig grund: samtycke (analytiska och marknadsföringscookies) respektive berättigat intresse (nödvändiga cookies).</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">3. Hur länge vi sparar dina uppgifter</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Vi sparar aldrig uppgifter längre än nödvändigt för det ändamål de samlades in för, och aldrig längre än vad lag kräver. Specifika lagringstider framgår av avsnittet ovan.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">4. Delning av personuppgifter</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Vi lämnar aldrig ut dina personuppgifter till tredje part utan ditt samtycke, om det inte krävs enligt lag eller är nödvändigt för att fullgöra ett avtal med dig. Vi delar uppgifter med:</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li>[LEVERANTÖR] – [SYFTE], server placerad inom EU/EES</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:paragraph -->
<p>Alla våra personuppgiftsbiträden har ingått biträdesavtal med oss och behandlar data i enlighet med GDPR.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">5. Överföring utanför EU/EES</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Vi strävar efter att all behandling sker inom EU/EES. Om uppgifter överförs till tredjeland sker det med stöd av lämpliga skyddsåtgärder (t.ex. EU:s standardavtalsklausuler).</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">6. Dina rättigheter</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Du har rätt att:</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li><strong>Få tillgång</strong> till de uppgifter vi har om dig (registerutdrag).</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Begära rättelse</strong> av felaktiga eller ofullständiga uppgifter.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Begära radering</strong> ("rätten att bli bortglömd") när uppgifterna inte längre behövs.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Begära begränsning</strong> av behandlingen medan ett klagomål utreds.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Invända</strong> mot behandling som grundar sig på berättigat intresse.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Dataportabilitet</strong> – få ut dina uppgifter i ett maskinläsbart format.</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><strong>Återkalla samtycke</strong> när som helst, utan att det påverkar lagligheten av behandling som skett dessförinnan.</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:paragraph -->
<p>Kontakta oss på <a href="mailto:[E-POST]">[E-POST]</a> för att utöva dina rättigheter. Vi svarar inom 30 dagar.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">7. Rätt att lämna klagomål</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Om du anser att vi behandlar dina personuppgifter på ett felaktigt sätt har du rätt att lämna klagomål till <strong>Integritetsskyddsmyndigheten (IMY)</strong>, <a href="https://www.imy.se" target="_blank" rel="noopener noreferrer">www.imy.se</a>.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">8. Ändringar i denna policy</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Vi kan komma att uppdatera denna policy. Den senaste versionen finns alltid på denna sida. Vid väsentliga ändringar meddelar vi dig via e-post eller ett tydligt meddelande på webbplatsen.</p>
<!-- /wp:paragraph -->';
}

function wpst_get_cookie_policy_template() {
	return '<!-- wp:paragraph -->
<p>Den här policyn förklarar vad cookies är, vilka cookies vi använder på [WEBBPLATSNAMN] och hur du kan hantera dem.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>Senast uppdaterad:</strong> [DATUM]</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Vad är cookies?</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Cookies är små textfiler som lagras i din webbläsare när du besöker en webbplats. De används för att webbplatsen ska fungera korrekt, för att komma ihåg dina inställningar och för att förstå hur besökare använder webbplatsen.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Vilka cookies använder vi?</h3>
<!-- /wp:heading -->

<!-- wp:heading {"level":4} -->
<h4 class="wp-block-heading">Nödvändiga cookies</h4>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Dessa cookies krävs för att webbplatsen ska fungera och kan inte stängas av. De sätts vanligtvis bara som svar på åtgärder du utför, som att logga in eller fylla i ett formulär. Rättslig grund: berättigat intresse.</p>
<!-- /wp:paragraph -->

<!-- wp:table {"className":"is-style-stripes"} -->
<figure class="wp-block-table is-style-stripes"><table><thead><tr><th>Cookie</th><th>Syfte</th><th>Giltighetstid</th></tr></thead><tbody><tr><td>wordpress_*</td><td>Hanterar inloggningssession i WordPress</td><td>Session</td></tr><tr><td>wordpress_logged_in_*</td><td>Håller dig inloggad i WordPress</td><td>14 dagar</td></tr><tr><td>wp-settings-*</td><td>Sparar layoutinställningar i adminpanelen</td><td>1 år</td></tr></tbody></table></figure>
<!-- /wp:table -->

<!-- wp:heading {"level":4} -->
<h4 class="wp-block-heading">Analytiska cookies</h4>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Vi använder [ANALYSVERKTYG, t.ex. Google Analytics / Matomo] för att förstå hur besökare använder vår webbplats. Informationen är anonym och aggregerad. Rättslig grund: samtycke.</p>
<!-- /wp:paragraph -->

<!-- wp:table {"className":"is-style-stripes"} -->
<figure class="wp-block-table is-style-stripes"><table><thead><tr><th>Cookie</th><th>Syfte</th><th>Giltighetstid</th></tr></thead><tbody><tr><td>[COOKIE-NAMN]</td><td>[SYFTE]</td><td>[TID]</td></tr></tbody></table></figure>
<!-- /wp:table -->

<!-- wp:heading {"level":4} -->
<h4 class="wp-block-heading">Marknadsföringscookies</h4>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Dessa cookies används för att visa anpassad reklam och för att spåra effektiviteten av marknadsföringskampanjer. Rättslig grund: samtycke.</p>
<!-- /wp:paragraph -->

<!-- wp:table {"className":"is-style-stripes"} -->
<figure class="wp-block-table is-style-stripes"><table><thead><tr><th>Cookie</th><th>Syfte</th><th>Giltighetstid</th></tr></thead><tbody><tr><td>[COOKIE-NAMN]</td><td>[SYFTE]</td><td>[TID]</td></tr></tbody></table></figure>
<!-- /wp:table -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Hur hanterar du cookies?</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Du kan när som helst återkalla ditt samtycke eller ändra dina cookieinställningar via vår cookiebanner. Du kan också ställa in din webbläsare så att den blockerar eller raderar cookies. Observera att vissa delar av webbplatsen kanske inte fungerar om du inaktiverar nödvändiga cookies.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Läs mer om hur du hanterar cookies i din webbläsare:</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li><a href="https://support.google.com/chrome/answer/95647" target="_blank" rel="noopener noreferrer">Google Chrome</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="https://support.mozilla.org/sv/kb/aktivera-och-inaktivera-kakor-webbplatser" target="_blank" rel="noopener noreferrer">Mozilla Firefox</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="https://support.apple.com/sv-se/guide/safari/sfri11471/mac" target="_blank" rel="noopener noreferrer">Apple Safari</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="https://support.microsoft.com/sv-se/microsoft-edge/ta-bort-cookies-i-microsoft-edge-63947406-40ac-c3b8-57b9-2a946a29ae09" target="_blank" rel="noopener noreferrer">Microsoft Edge</a></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Kontakt</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Har du frågor om hur vi använder cookies? Kontakta oss på <a href="mailto:[E-POST]">[E-POST]</a> eller läs vår <a href="#wpst-privacy-policy">integritetspolicy</a>.</p>
<!-- /wp:paragraph -->';
}
