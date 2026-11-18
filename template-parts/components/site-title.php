<?php
/**
 * Template part for the site name as text, when there is no logo.
 *
 * The tagline (Settings → General) is shown under the name when theme.json sets
 * settings.custom.logo.showTagline to true.
 *
 * @package WP Starter Theme
 */

$show_tagline = (bool) wp_get_global_settings( array( 'custom', 'logo', 'showTagline' ) );
$tagline      = $show_tagline ? get_bloginfo( 'description' ) : '';
?>
<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="custom-logo-link" rel="home">
	<span class="site-title"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
	<?php if ( '' !== $tagline ) : ?>
		<span class="site-tagline"><?php echo esc_html( $tagline ); ?></span>
	<?php endif; ?>
</a>
