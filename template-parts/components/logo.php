<?php
/**
 * Template part for displaying SVG logos.
 *
 * @package WP Starter Theme
 */

$defaults = array(
	'logo_path'  => get_theme_file_path( 'assets/svg/logo.svg' ),
	'class'      => 'site-logo',
	'aria_label' => get_bloginfo( 'name' ),
	'link_url'   => home_url( '/' ),
);

$args = wp_parse_args( $args ?? array(), $defaults );

$svg       = '';
$logo_path = isset( $args['logo_path'] ) ? $args['logo_path'] : '';
$site_name = isset( $args['aria_label'] ) ? $args['aria_label'] : get_bloginfo( 'name' );

if ( is_string( $logo_path ) && is_readable( $logo_path ) ) {
	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	$svg = file_get_contents( $logo_path );

	if ( false !== $svg && '' !== $svg ) {
		$svg = preg_replace( '#<(script|foreignObject)\b[^>]*>.*?</\1>#is', '', $svg );

		$svg_class = sanitize_html_class( $args['class'] );
		if ( ! preg_match( '/<svg[^>]*\bclass=/i', $svg ) ) {
			$svg = preg_replace( '/<svg\b([^>]*)>/i', '<svg class="' . esc_attr( $svg_class ) . '"$1>', $svg, 1 );
		} else {
			$svg = preg_replace( '/<svg\b([^>]*?)class="([^"]*)"/i', '<svg$1class="' . esc_attr( $svg_class ) . ' $2"', $svg, 1 );
		}
		if ( ! preg_match( '/<svg[^>]*\brole=/i', $svg ) ) {
			$svg = preg_replace( '/<svg\b([^>]*)>/i', '<svg role="img"$1>', $svg, 1 );
		}
		if ( ! preg_match( '/<svg[^>]*\bfocusable=/i', $svg ) ) {
			$svg = preg_replace( '/<svg\b([^>]*)>/i', '<svg focusable="false"$1>', $svg, 1 );
		}
		if ( false === strpos( $svg, '<title' ) ) {
			$svg = preg_replace( '/(<svg\b[^>]*>)/i', '$1<title id="logo-title">' . esc_html( $site_name ) . '</title>', $svg, 1 );
			if ( ! preg_match( '/<svg[^>]*\baria-labelledby=/i', $svg ) ) {
				$svg = preg_replace( '/<svg\b([^>]*)>/i', '<svg$1 aria-labelledby="logo-title">', $svg, 1 );
			}
		}

		$has_viewbox = (bool) preg_match( '/<svg[^>]*\bviewBox\s*=\s*"[^\"]*"/i', $svg );
		if ( $has_viewbox ) {
			$svg = preg_replace( '/\s(?:width|height)="[^"]*"/i', '', $svg );
		}

		if ( false === strpos( $svg, '<style' ) ) {
			$style = '<style>*[fill]{fill:currentColor !important} *[stroke]{stroke:currentColor !important}</style>';
			$svg   = preg_replace( '/(<svg\b[^>]*>)/i', '$1' . $style, $svg, 1 );
		}
	} else {
		$svg = '';
	}
} else {
	echo '<!-- SVG not readable: ' . esc_html( (string) $logo_path ) . ' -->';
}

$link_url   = isset( $args['link_url'] ) ? $args['link_url'] : '';
$aria_label = $site_name;

if ( ! empty( $link_url ) ) {
	echo '<a href="' . esc_url( $link_url ) . '" class="site-logo__link" aria-label="' . esc_attr( $aria_label ) . '">';
	echo ( '' !== $svg ) ? $svg : esc_html( $site_name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '</a>';
} else {
	echo '<div aria-label="' . esc_attr( $aria_label ) . '">';
	echo ( '' !== $svg ) ? $svg : esc_html( $site_name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '</div>';
}
