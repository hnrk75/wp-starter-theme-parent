<?php
/**
 * Enqueue scripts and styles
 *
 * @package WP Starter Theme
 */

/**
 * Optional Google Fonts URL. Empty by default: the fonts are self-hosted.
 *
 * @return string
 */
function wpst_get_google_fonts_url() {
	return (string) apply_filters( 'wpst_google_fonts_url', '' );
}

if ( ! function_exists( 'wpst_scripts' ) ) :
	function wpst_scripts() {
		$fonts_url = wpst_get_google_fonts_url();

		if ( $fonts_url ) {
			wp_enqueue_style(
				'wpst-google-fonts',
				$fonts_url,
				array(),
				null // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion -- external URL, no version needed
			);
		}

		// Always the parent's files; a child theme enqueues its own on top.
		$css_rel  = 'assets/css/style.min.css';
		$css_path = get_parent_theme_file_path( $css_rel );
		$css_uri  = get_parent_theme_file_uri( $css_rel );
		$css_ver  = file_exists( $css_path ) ? (string) filemtime( $css_path ) : null;

		wp_enqueue_style( 'wpst-style', $css_uri, array(), $css_ver );

		$js_rel  = 'assets/js/dist/scripts.min.js';
		$js_path = get_parent_theme_file_path( $js_rel );
		$js_uri  = get_parent_theme_file_uri( $js_rel );
		$js_ver  = file_exists( $js_path ) ? (string) filemtime( $js_path ) : null;

		wp_enqueue_script(
			'wpst-scripts',
			$js_uri,
			array(),
			$js_ver,
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);
	}
endif;
add_action( 'wp_enqueue_scripts', 'wpst_scripts' );
