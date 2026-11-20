<?php
/**
 * Pill: a small rounded label, e.g. "New", a category or a status. A link when it has an href.
 *
 * Colors are palette slugs and use WordPress's own has-{slug}-background-color and
 * has-{slug}-color classes, so a child theme's palette works without extra CSS.
 *
 * Usage: wpst_pill( array( 'text' => 'Nyhet' ) );
 *        wpst_pill( array( 'text' => 'Event', 'size' => 'md', 'background' => 'primary', 'color' => 'white', 'href' => '/event/' ) );
 *
 * @package WP Starter Theme
 */

if ( ! function_exists( 'wpst_pill' ) ) {
	/**
	 * Renders a pill.
	 *
	 * @param array $args {
	 *     @type string $text       Visible text. Nothing is output when empty.
	 *     @type string $size       sm (default), md or lg.
	 *     @type string $background Palette slug for the background, e.g. 'primary'. Default: the pill token.
	 *     @type string $color      Palette slug for the text, e.g. 'white'. Default: the pill token.
	 *     @type string $href       Makes the pill a link.
	 *     @type string $class      Extra classes.
	 *     @type bool   $display    Echo (default) or return the HTML.
	 * }
	 * @return string|null The HTML when display is false.
	 */
	function wpst_pill( $args = array() ) {
		$a = wp_parse_args(
			$args,
			array(
				'text'       => '',
				'size'       => 'sm',
				'background' => '',
				'color'      => '',
				'href'       => '',
				'class'      => '',
				'display'    => true,
			)
		);

		$text = trim( (string) $a['text'] );

		if ( '' === $text ) {
			return $a['display'] ? null : '';
		}

		$size    = in_array( $a['size'], array( 'sm', 'md', 'lg' ), true ) ? $a['size'] : 'sm';
		$classes = array( 'pill', 'pill--' . $size );

		if ( $a['background'] ) {
			$classes[] = 'has-background';
			$classes[] = 'has-' . sanitize_html_class( $a['background'] ) . '-background-color';
		}
		if ( $a['color'] ) {
			$classes[] = 'has-text-color';
			$classes[] = 'has-' . sanitize_html_class( $a['color'] ) . '-color';
		}
		if ( $a['class'] ) {
			$classes = array_merge( $classes, array_map( 'sanitize_html_class', preg_split( '/\s+/', (string) $a['class'] ) ) );
		}

		$class = implode( ' ', array_filter( $classes ) );

		$html = $a['href']
			? sprintf( '<a class="%s" href="%s">%s</a>', esc_attr( $class ), esc_url( $a['href'] ), esc_html( $text ) )
			: sprintf( '<span class="%s">%s</span>', esc_attr( $class ), esc_html( $text ) );

		if ( $a['display'] ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $html;
			return null;
		}

		return $html;
	}
}
