<?php
/**
 * Render a themed button or link with unified .btn styling.
 *
 * @package WP Starter Theme
 */

if ( ! function_exists( 'wpst_button' ) ) {
	function wpst_button( $args = array() ) {
		$defaults = array(
			'label'      => '',
			'variant'    => 'primary',
			'size'       => '',
			'href'       => '',
			'type'       => 'button',
			'disabled'   => false,
			'target'     => '',
			'external'   => false,
			'rel'        => '',
			'id'         => '',
			'class'      => array(),
			'attrs'      => array(),
			'aria_label' => '',
			'icon_html'  => '',
			'icon_pos'   => 'before',
			'display'    => true,
		);
		$a = wp_parse_args( $args, $defaults );

		$classes = array( 'btn', 'btn-' . sanitize_html_class( $a['variant'] ) );
		if ( ! empty( $a['size'] ) ) {
			$classes[] = sanitize_html_class( $a['size'] );
		}
		if ( ! empty( $a['class'] ) ) {
			$classes = array_merge(
				$classes,
				is_array( $a['class'] ) ? $a['class'] : preg_split( '/\s+/', (string) $a['class'] )
			);
		}
		$classes = array_map( 'sanitize_html_class', array_filter( $classes ) );

		$allowed_icon_tags = array(
			'span' => array(
				'class'       => true,
				'aria-hidden' => true,
			),
			'i'    => array(
				'class'       => true,
				'aria-hidden' => true,
			),
			'svg'  => array(
				'class'        => true,
				'width'        => true,
				'height'       => true,
				'viewBox'      => true,
				'viewbox'      => true,
				'fill'         => true,
				'stroke'       => true,
				'xmlns'        => true,
				'xmlns:xlink'  => true,
				'role'         => true,
				'aria-hidden'  => true,
				'focusable'    => true,
			),
			'path' => array(
				'd'               => true,
				'fill'            => true,
				'stroke'          => true,
				'stroke-width'    => true,
				'stroke-linecap'  => true,
				'stroke-linejoin' => true,
				'fill-rule'       => true,
				'clip-rule'       => true,
			),
			'g' => array(
				'fill'   => true,
				'stroke' => true,
			),
		);

		$icon = '';
		if ( ! empty( $a['icon_html'] ) ) {
			$icon_raw = wp_kses( $a['icon_html'], $allowed_icon_tags );

			if ( false === strpos( $icon_raw, 'aria-hidden' ) ) {
				$icon_raw = preg_replace( '/^<(svg|i|span)/', '<$1 aria-hidden="true"', $icon_raw );
			}

			$icon = '<span class="icon" aria-hidden="true">' . $icon_raw . '</span>';
		}

		$label_text    = (string) $a['label'];
		$visible_label = esc_html( $label_text );

		$content_parts = array();
		if ( 'before' === $a['icon_pos'] && $icon ) {
			$content_parts[] = $icon;
		}
		$content_parts[] = $visible_label;
		if ( 'after' === $a['icon_pos'] && $icon ) {
			$content_parts[] = $icon;
		}
		$content_html = implode( ' ', $content_parts );

		$attr = array();
		if ( ! empty( $a['id'] ) ) {
			$attr['id'] = $a['id'];
		}
		$attr['class'] = trim( implode( ' ', $classes ) );

		if ( ! empty( $a['aria_label'] ) ) {
			$attr['aria-label'] = $a['aria_label'];
		}

		if ( ! empty( $a['attrs'] ) && is_array( $a['attrs'] ) ) {
			foreach ( $a['attrs'] as $k => $v ) {
				if ( '' === $v && 'aria-' !== substr( $k, 0, 5 ) ) {
					continue;
				}
				$attr[ $k ] = $v;
			}
		}

		$html = '';

		if ( ! empty( $a['href'] ) ) {
			$attr['href'] = esc_url( $a['href'] );

			if ( $a['disabled'] ) {
				$attr['aria-disabled'] = 'true';
				$attr['tabindex']      = '-1';
			}

			if ( ! empty( $a['target'] ) ) {
				$attr['target'] = $a['target'];
			}
			$rel = (string) $a['rel'];
			if ( $a['external'] || ( isset( $attr['target'] ) && '_blank' === $attr['target'] ) ) {
				$rel = trim( $rel . ' noopener noreferrer' );
			}
			if ( $rel ) {
				$attr['rel'] = $rel;
			}

			$html = '<a' . wpst_html_attributes( $attr ) . '>' . $content_html . '</a>';
		} else {
			$attr['type'] = in_array( $a['type'], array( 'button', 'submit', 'reset' ), true ) ? $a['type'] : 'button';

			if ( $a['disabled'] ) {
				$attr['disabled'] = 'disabled';
			}

			$html = '<button' . wpst_html_attributes( $attr ) . '>' . $content_html . '</button>';
		}

		if ( $a['display'] ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $html;
			return null;
		}

		return $html;
	}
}

if ( ! function_exists( 'wpst_html_attributes' ) ) {
	function wpst_html_attributes( $attr ) {
		if ( empty( $attr ) || ! is_array( $attr ) ) {
			return '';
		}
		$compiled = '';
		foreach ( $attr as $name => $value ) {
			if ( '' === $value && 'value' !== $name ) {
				continue;
			}
			$compiled .= ' ' . esc_attr( $name ) . '="' . esc_attr( (string) $value ) . '"';
		}
		return $compiled;
	}
}
