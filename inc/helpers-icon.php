<?php
/**
 * SVG icon helper
 *
 * Loads inline SVG from assets/svg/.
 * Usage: wpst_icon( 'arrow-right' );
 *        wpst_icon( 'arrow-right', [ 'class' => 'icon icon--sm', 'title' => 'Go forward' ] );
 *
 * @package WP Starter Theme
 */

if ( ! function_exists( 'wpst_icon' ) ) {
	function wpst_icon( string $name, array $args = array() ): string {
		$args = wp_parse_args(
			$args,
			array(
				'class' => 'icon',
				'title' => '',
				'echo'  => true,
			)
		);

		// Allow only a-z, 0-9, hyphens and underscores in filename.
		$safe_name = preg_replace( '/[^a-z0-9\-_]/', '', strtolower( $name ) );
		$path      = get_theme_file_path( 'assets/svg/' . $safe_name . '.svg' );

		if ( ! file_exists( $path ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
				trigger_error( esc_html( "wpst_icon: icon '{$safe_name}.svg' not found." ), E_USER_NOTICE );
			}
			return '';
		}

		$svg = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		if ( false === $svg || '' === trim( $svg ) ) {
			return '';
		}

		// The file may use single or double quotes.
		$class = esc_attr( $args['class'] );
		if ( preg_match( '/<svg\b[^>]*\bclass=(["\'])/', $svg ) ) {
			$svg = preg_replace( '/(<svg\b[^>]*\bclass=)(["\'])[^"\']*\2/', '$1"' . $class . '"', $svg, 1 );
		} else {
			$svg = preg_replace( '/<svg\b/', '<svg class="' . $class . '"', $svg, 1 );
		}

		if ( ! empty( $args['title'] ) ) {
			$title     = esc_html( $args['title'] );
			$title_tag = '<title>' . $title . '</title>';
			$svg       = preg_replace( '/(<svg[^>]*>)/', '$1' . $title_tag, $svg );
			$svg       = preg_replace( '/<svg/', '<svg aria-label="' . esc_attr( $args['title'] ) . '" role="img"', $svg );
		} else {
			// Decorative icon — hide from screen readers.
			$svg = preg_replace( '/<svg/', '<svg aria-hidden="true" focusable="false"', $svg );
		}

		// Lowercase names: wp_kses() compares them that way.
		$paint = array(
			'id'                => true,
			'class'             => true,
			'fill'              => true,
			'fill-rule'         => true,
			'fill-opacity'      => true,
			'clip-rule'         => true,
			'clip-path'         => true,
			'mask'              => true,
			'opacity'           => true,
			'transform'         => true,
			'stroke'            => true,
			'stroke-width'      => true,
			'stroke-linecap'    => true,
			'stroke-linejoin'   => true,
			'stroke-opacity'    => true,
			'stroke-dasharray'  => true,
			'stroke-dashoffset' => true,
		);

		$gradient = array(
			'id'                => true,
			'x1'                => true,
			'y1'                => true,
			'x2'                => true,
			'y2'                => true,
			'cx'                => true,
			'cy'                => true,
			'r'                 => true,
			'fx'                => true,
			'fy'                => true,
			'gradientunits'     => true,
			'gradienttransform' => true,
		);

		$output = wp_kses(
			$svg,
			array(
				'svg'            => array(
					'class'           => true,
					'aria-hidden'     => true,
					'aria-label'      => true,
					'focusable'       => true,
					'role'            => true,
					'xmlns'           => true,
					'width'           => true,
					'height'          => true,
					'viewbox'         => true,
					'fill'            => true,
					'stroke'          => true,
					'stroke-width'    => true,
					'stroke-linecap'  => true,
					'stroke-linejoin' => true,
				),
				'title'          => array(),
				'path'           => $paint + array( 'd' => true ),
				'circle'         => $paint + array(
					'cx' => true,
					'cy' => true,
					'r'  => true,
				),
				'ellipse'        => $paint + array(
					'cx' => true,
					'cy' => true,
					'rx' => true,
					'ry' => true,
				),
				'rect'           => $paint + array(
					'x'      => true,
					'y'      => true,
					'width'  => true,
					'height' => true,
					'rx'     => true,
					'ry'     => true,
				),
				'line'           => $paint + array(
					'x1' => true,
					'y1' => true,
					'x2' => true,
					'y2' => true,
				),
				'polyline'       => $paint + array( 'points' => true ),
				'polygon'        => $paint + array( 'points' => true ),
				'g'              => $paint,
				'defs'           => array(),
				'lineargradient' => $gradient,
				'radialgradient' => $gradient,
				'stop'           => array(
					'offset'       => true,
					'stop-color'   => true,
					'stop-opacity' => true,
				),
				'clippath'       => array(
					'id'            => true,
					'clippathunits' => true,
				),
				'mask'           => array(
					'id'     => true,
					'x'      => true,
					'y'      => true,
					'width'  => true,
					'height' => true,
				),
				'use'            => array(
					'href'       => true,
					'xlink:href' => true,
				),
			)
		);

		if ( $args['echo'] ) {
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sanitised via wp_kses
			return '';
		}

		return $output;
	}
}
